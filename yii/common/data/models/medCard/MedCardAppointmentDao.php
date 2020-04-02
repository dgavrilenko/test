<?php
namespace ddmp\common\data\models\medCard;

use ddmp\common\base\commands\telemed\CreateSessionCommandInterface;
use ddmp\common\base\models\telemed\CreateSessionModel;
use ddmp\common\commands\telemed\CreateSessionCommandFactory;
use ddmp\common\data\models\BaseDao;
use ddmp\common\data\models\contract\ContractDao;
use ddmp\common\data\models\medCard\enums\MedCardAppointmentStateEnum;
use ddmp\common\data\models\partner\PartnerDao;
use ddmp\common\data\models\partnerService\PartnerServiceDao;
use ddmp\common\data\models\patient\PatientDao;
use ddmp\common\data\models\product\ProductDao;
use ddmp\common\data\models\productService\ProductServiceDao;
use ddmp\common\enums\MedCardAppointmentSourceTypeEnum;
use ddmp\common\enums\PayStatus;
use ddmp\common\enums\ServiceTypeEnum;
use ddmp\common\external\medlinesoft\adapters\MedlineSourceTypeAdapter;
use ddmp\common\models\contract\ContractPayInfo;
use ddmp\common\params\telemed\MedlineTelemedParams;
use ddmp\common\params\telemed\MpcTelemedParams;
use ddmp\common\utils\helpers\Url;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "medcard_appointment".
 *
 * @property integer             $id                     Id
 * @property string              $system_id              Cистемный идентификатор консультации
 * @property integer             $state                  Состояние консультации (активна, завершена и т.п)
 * @property string              $patient                Информация о пациенте для публичной части интерфейса
 * @property integer             $source_type            Исходный тип консультации
 * @property int                 $queue_type             Тип очереди
 * @property string              $service_name           Название услуги
 * @property string              $service_price          Цена услуги
 * @property float               $service_sale_price     Цена услуги со скидкой
 * @property string              $planned_time           Запланированное время начала
 * @property string              $started_time           Время начала
 * @property string              $finished_time          Время окончания
 * @property string              $updated_time           Время последнего обновления
 * @property string              $patient_left_time      Время покидания консультации пациентом
 * @property integer             $has_report             Признак наличия готового заключения
 * @property string              $report_files           Файлы заключений
 * @property string              $partner_create_time
 * @property string              $partner_update_time
 * @property string              $video_call_system_info
 * @property string              $tarif_code
 * @property string              $duration_max
 * @property string              $chat_id                Идентификтаор сесии разговора для данной консультации
 * @property string              $video_id               Идентификатор видеосеанса для данной консультации
 * @property string              $invoice_external_id    Внешний идентификатор счета
 * @property integer             $invoice_created        Дата выставления счета
 * @property integer             $invoice_amount_total   Стоимость услуг по данному счету
 * @property float               $invoice_amount_paid    Сумма проведенных платежей по данному счету
 * @property integer             $invoice_item_count     Количество услуг в данном счете
 * @property integer             $is_paid                Оплачена ли консультация
 * @property integer             $payment_url
 * @property integer             $is_visited_by_patient
 * @property integer             $chat_session_state     Состояние сеанса
 * @property string              $chat_session_members   Информация об участниках сеанса
 * @property string              $chat_session_files     Прикрепленные в данном сеансе файлы
 * @property integer             $video_session_state    Состояние сеанса
 * @property string              $video_session_caller   Информация об инициаторе вызовов
 * @property string              $video_session_callee   Информация об адресате вызовов
 * @property array               $appointment_short_view Лог ответа - cущность AppointmentShortView
 * @property array               $appointment_view       Лог ответа - сущность AppointmentView
 * @property integer             $doctor_id
 * @property string              $report_comment         Заключение
 * @property string              $create_time            Время создания в маркетплэйс
 * @property string              $update_time            Время оновления в маркетплэйс
 * @property integer             $product_id             id продукта в маркетплэйсе
 * @property integer             $partner_id             id партнера в маркетплэйсе
 * @property integer             $clinic_id              id клиники
 * @property MedCardDoctorDao    $doctor                 Связанный врач
 * @property integer             $patient_id             ID пациента
 * @property boolean             $is_hidden              Консультация скрыта
 *
 * @property PartnerDao $partner
 */
class MedCardAppointmentDao extends BaseDao
{
	public const BITRIX_BLOCK_ID = 2;
	/**
	 * Список параметров, которые мы требуем от всех партнеров
	 * Они необходимы для отрисовки мед.карты
	 * По факту, некоторые партнеры ещё не передавают все обязательные параметры
	 */
	const REQUIRED_PARAMS = [
		'state',
		'product_id',
		'source_type',
		'service_name',
		'planned_time',
		'started_time',
		'finished_time',
		'updated_time',
		'has_report',
		'report_files',
		'chat_id',
		'video_id',
		'chat_session_state',
		'chat_session_files',
		'video_session_state'
	];

	public const SOURCE = ['QUEUED' => 1, 'SCHEDULED' => 2, 'URGENT' => 3];
	public const CHAT_SESSION_STATE = ['NOT_ACTIVE' => 1, 'ACTIVE' => 2, 'FINISHED' => 3, 'CANCELLED' => 4];

	public const STATUSES = [
		'PROCESSING' => 1, // запись в обработке
		'APPROVED' => 2, // запись подтверждена
		'FINISHED' => 3, // завершено
		'CANCELLED' => 4, //отменено
	];

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'medcard_appointment';
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'                     => 'Id',
			'system_id'              => 'Cистемный идентификатор консультации',
			'state'                  => 'Состояние консультации (активна, завершена и т.п)',
			'patient'                => 'Информация о пациенте для публичной части интерфейса',
			'source_type'            => 'Исходный тип консультации',
			'service_name'           => 'Название услуги',
			'service_price'          => 'Цена услуги',
			'service_sale_price'     => 'Цена услуги со скидкой',
			'planned_time'           => 'Запланированное время начала',
			'started_time'           => 'Время начала',
			'finished_time'          => 'Время окончания',
			'updated_time'           => 'Время последнего обновления',
			'patient_left_time'      => 'Время покидания консультации пациентом',
			'has_report'             => 'Признак наличия готового заключения',
			'report_files'           => 'Файлы заключений',
			'partner_create_time'    => 'Время создания в системе партнера',
			'partner_update_time'    => 'Время обновления в системе партнера',
			'video_call_system_info' => 'Системная информация о видео вызове',
			'tarif_code'             => 'Код тарифа консультации',
			'duration_max'           => 'Максимальная разрешенная продолжительность консультации',
			'chat_id'                => 'Идентификтаор сесии разговора для данной консультации',
			'video_id'               => 'Идентификатор видеосеанса для данной консультации',
			'invoice_external_id'    => 'Внешний идентификатор счета',
			'invoice_created'        => 'Дата выставления счета',
			'invoice_amount_total'   => 'Стоимость услуг по данному счету',
			'invoice_amount_paid'    => 'Сумма проведенных платежей по данному счету',
			'invoice_item_count'     => 'Количество услуг в данном счете',
			'is_paid'                => 'Оплачена ли консультация',
			'payment_url'            => 'Url для оплаты консультации',
			'is_visited_by_patient'  => 'Заходил ли хоть раз пациент в эту консультацию',
			'chat_session_state'     => 'Состояние сеанса',
			'chat_session_members'   => 'Информация об участниках сеанс',
			'chat_session_files'     => 'Прикрепленные в данном сеансе',
			'video_session_state'    => 'Состояние сеанса',
			'video_session_caller'   => 'Информация об инициаторе вызовов',
			'video_session_callee'   => 'Информация об адресате вызовов',
			'appointment_short_view' => 'Лог ответа - cущность AppointmentShortView',
			'appointment_view'       => 'Лог ответа - сущность AppointmentView',
			'create_time'            => 'Дата создания',
			'update_time'            => 'Дата обновления',
			'product_id'             => 'Id продукта',
			'partner_id'             => 'Id партнера',
			'clinic_id'              => 'Id клиники',
			'is_hidden'              => 'Консультация скрыта',
		];
	}

	/**
	 * Возвращает массив полей ассоциированных с атрибутами для сериализации
	 *
	 * @return array
	 */
	public function fields()
	{
		return [
			'id'                     => 'Id',
			'system_id'              => 'system_id',
			'state'                  => 'state',
			'patient'                => 'patient',
			'source_type'            => 'source_type',
			'service_name'           => 'service_name',
			'service_price'          => 'service_price',
			'service_sale_price'     => 'service_sale_price',
			'planned_time'           => 'planned_time',
			'started_time'           => 'started_time',
			'finished_time'          => 'finished_time',
			'updated_time'           => 'updated_time',
			'patient_left_time'      => 'patient_left_time',
			'has_report'             => 'has_report',
			'report_files'           => 'report_files',
			'partner_create_time'    => 'partner_create_time',
			'partner_update_time'    => 'partner_update_time',
			'video_call_system_info' => 'video_call_system_info',
			'tarif_code'             => 'tarif_code',
			'duration_max'           => 'duration_max',
			'chat_id'                => 'chat_id',
			'video_id'               => 'video_id',
			'invoice_external_id'    => 'invoice_external_id',
			'invoice_created'        => 'invoice_created',
			'invoice_amount_total'   => 'invoice_amount_total',
			'invoice_amount_paid'    => 'invoice_amount_paid',
			'invoice_item_count'     => 'invoice_item_count',
			'is_paid'                => 'is_paid',
			'payment_url'            => 'payment_url',
			'is_visited_by_patient'  => 'is_visited_by_patient',
			'chat_session_state'     => 'chat_session_state',
			'chat_session_members'   => 'chat_session_members',
			'chat_session_files'     => 'chat_session_files',
			'video_session_state'    => 'video_session_state',
			'video_session_caller'   => 'video_session_caller',
			'video_session_callee'   => 'video_session_callee',
			'appointment_short_view' => 'appointment_short_view',
			'appointment_view'       => 'appointment_view',
			'create_time'            => 'create_time',
			'update_time'            => 'update_time',
			'product_id'             => 'product_id',
			'partner_id'             => 'partner_id',
			'clinic_id'              => 'clinic_id',
			'is_hidden'              => 'is_hidden',
		];
	}

	/**
	 * @inheritdoc
	 * @return MedCardAppointmentDaoQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return MedCardAppointmentDaoQuery::build(get_called_class());
	}

	/**
	 * @return ActiveQuery
	 */
	public function getPartner(): ActiveQuery
	{
		return $this->hasOne(PartnerDao::class, ['id' => 'partner_id']);
	}

	/**
	 * Метод получения связанных данных из medcard_doctor
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getDoctor()
	{
		return $this->hasOne(MedCardDoctorDao::class, ['id' => 'doctor_id']);
	}

	/**
	 * Метод получения связанных данных из medcard_clinic
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getClinic()
	{
		return $this->hasOne(MedCardClinicDao::class, ['id' => 'clinic_id']);
	}

	/**
	 * Создать ссылку на консультацию.
	 *
	 * В случае если не найден активный контракт, сервис или продукт возвращает false
	 *
	 * @return string
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\di\NotInstantiableException
	 */
	public function getConsultationLink(): ?string
	{
		$contract = ContractDao::find()
			->byTelemedPartner($this->partner_id)
			->byPatientId($this->patient_id)
			->one();

		if (empty($contract)) {
			return null;
		}

		$product = $contract->product;

		if (!$product) {
			return null;
		}

		$partnerServiceTelemed = $product->findPartnerService($this->partner_id, ServiceTypeEnum::TELEMED);
		if (!$partnerServiceTelemed) {
			return null;
		}

		return Url::toServiceConsultationAbsolute($product->code, $partnerServiceTelemed->code, $this->system_id);
	}


	/**
	 * @return string
	 */
	public function getServiceIframeLink(): string
	{
		$activeContract = ContractDao::find()->withProductAndServices()->activeByPatientByTelemedPartner($this->patient_id, $this->partner_id)->one();

		if ($activeContract === null) {
			$activeContracts = ContractDao::find()->withProductAndServices()->byPatientId($this->patient_id)->activeNow()->all();
			// Если не найден активный контракт партнёра, даём ссылку на телемедицину другого партнёра
			foreach ($activeContracts as $contract) {
				$productService = $contract->product->findActiveProductService(ServiceTypeEnum::TELEMED);
				if ($productService) {
					$activeContract = $contract;
					break;
				}
			}
		}

		if ($activeContract === null) {
			return '';
		}

		$product = $activeContract->product;

		$productService = $product->findProductService($this->partner_id, ServiceTypeEnum::TELEMED);

		if ($productService === null) {
			return '';
		}

		if ($productService->isDisabled()) {
			$productService = $product->findActiveProductService(ServiceTypeEnum::TELEMED);
		}

		if ($productService === null) {
			return '';
		}

		return Url::toServiceAbsolute($product->code, $productService->partnerService->code);
	}

	/**
	 * Получить ссылку на страницу консультации
	 *
	 * @return bool|string
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\di\NotInstantiableException
	 */
	public function getIframeConsultationLink()
	{
		$factory = \Yii::$container->get(CreateSessionCommandFactory::class);
		/* @var CreateSessionCommandInterface $command */
		$command = $factory->create($this->partner_id);

		if (!empty($this->product_id)) {
			//Проверяем есть ли активный контракт.
			$activeContract = ContractDao::find()->activeByPatientByProduct($this->patient_id, $this->product_id)->one();
		} else {
			//Находим любой активный контракт, т.к. пока мпц и медлайн не передают product_id.
			$activeContract = ContractDao::find()->activeByPatientByTelemedPartner($this->patient_id, $this->partner_id)->one();
		}

		if (empty($activeContract)) {
			return false;
		}

		$patient = PatientDao::find()->byId($this->patient_id)->one();
		$createSessionModel = new CreateSessionModel($patient, $activeContract);

		$result = $command->run($createSessionModel, true);
		$token = $result->getToken();

		switch ($this->partner_id)
		{
			case PartnerDao::PARTNER_ID_MPC:
				/* @var MpcTelemedParams $params */
				$params = \Yii::$container->get(MpcTelemedParams::class);
				$url = $params->getTelemedIFrameConsultationSrc($token, $this->system_id);
				break;
			case PartnerDao::PARTNER_ID_DOCTOR_RYADOM:
				/* @var MedlineTelemedParams $params */
				$params = \Yii::$container->get(MedlineTelemedParams::class);
				$url = $params->getTelemedIFrameConsultationSrc($token, $this->system_id);
				break;
			case PartnerDao::PARTNER_ID_MMT:
				$url = Url::toMmtConsultationFrame($token, $this->system_id);
				break;
		}

		return $url;
	}

	/**
	 * @return array
	 */
	public function getReportFilesList(): array
	{
		$files = [];

		if (empty($this->report_files)) {
			return [];
		}

		$reportFiles = json_decode( $this->report_files, true);

		if (is_null($reportFiles)) {
			\Yii::error("Ошибка декодирования файлов заключений: " . json_last_error() . ". [appointment_id = " . $this->id . "]");
			return [];
		}

		if (is_array($reportFiles)) {
			foreach ($reportFiles as $file) {
				if (isset($file['id']) && isset($file['filename']) && isset($file['mimeType']))
				{
					$files[$file['id']] = $file;
				}
			}
		}
		return $files;

	}

	/*
	* Маппинг статусов
	*
	* @return string
	*/
	public function mapViewStatus(): ?int
	{
		$statuses = [
			MedCardAppointmentStateEnum::STATE_SCHEDULED => self::STATUSES['APPROVED'],
			MedCardAppointmentStateEnum::STATE_QUEUED_ADULT => self::STATUSES['APPROVED'],
			MedCardAppointmentStateEnum::STATE_QUEUED_CHILD => self::STATUSES['APPROVED'],
			MedCardAppointmentStateEnum::STATE_PROGRESS => self::STATUSES['APPROVED'],
			MedCardAppointmentStateEnum::STATE_FINISHED => self::STATUSES['FINISHED'],
			MedCardAppointmentStateEnum::STATE_CANCELLED => self::STATUSES['CANCELLED'],
			MedCardAppointmentStateEnum::STATE_EXPIRED => self::STATUSES['CANCELLED'],
			MedCardAppointmentStateEnum::STATE_CANCELLED_PATIENT => self::STATUSES['CANCELLED'],
			MedCardAppointmentStateEnum::STATE_CANCELLED_DOCTOR => self::STATUSES['CANCELLED'],
		];

		return $statuses[$this->state] ?? null;
	}

	/**
	 * @return bool|null
	 */
	public function isPaid()
	{
		if (empty($this->product_id)) {
			return null;
		}

		$contract = ContractDao::find()->oneByPatientAndProduct($this->patient_id, $this->product_id);

		if (empty($contract)) {
			return null;
		}

		if ($contract->pay_status == PayStatus::PAID) {
			return true;
		}

		return false;
	}

	/**
	 * Консультация для разовой услуги - например, профорентация для детей.
	 *
	 * @return bool|null
	 */
	public function isOneTimeUsing(): ?bool
	{
		if (empty($this->product_id)) {
			return null;
		}

		/* @var ProductServiceDao $productService */
		$productService = ProductServiceDao::find()->byProductIdAndPartnerId($this->product_id, $this->partner_id)->one();

		if (empty($productService)) {
			return null;
		}

		if ($productService->limit_count === 1 && $productService->limit_repeat === 0) {
			return true;
		}

		return false;
	}

	/**
	 * Сумма оплаченная за консультацию для разовой услуги.
	 *
	 * @return null|float
	 * @throws \Bitrix\Main\ArgumentNullException
	 * @throws \Bitrix\Main\NotImplementedException
	 */
	public function getOneTimeUsingAppointmentPaidSum(): ?float
	{
		if (empty($this->product_id)) {
			return null;
		}

		$contract = ContractDao::find()->oneByPatientAndProduct($this->patient_id, $this->product_id);
		if (empty($contract)) {
			return null;
		}

		$contractPayInfo  = new ContractPayInfo($contract);
		return $contractPayInfo->getSumPaid();
	}

	/**
	 * @return bool
	 */
	public function isUrgent(): bool
	{
		return $this->source_type === MedCardAppointmentSourceTypeEnum::QUEUE;
	}

	/**
	 * @return string
	 */
	public function getPartnerCreatedTime(): string
	{
		return $this->partner_create_time ?? $this->create_time;
	}

	/**
	 * Назначение в очереди
	 *
	 * @return bool
	 */
	public function isInQueue(): bool
	{
		return $this->source_type === MedCardAppointmentSourceTypeEnum::QUEUE &&
			($this->state === MedCardAppointmentStateEnum::STATE_SCHEDULED ||
			$this->state === MedCardAppointmentStateEnum::STATE_QUEUED_ADULT ||
			$this->state === MedCardAppointmentStateEnum::STATE_QUEUED_CHILD);
	}
}
