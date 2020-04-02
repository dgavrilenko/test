<?php

namespace ddmp\common\data\models\request;

use ddmp\common\data\models\BaseDao;
use ddmp\common\data\models\contract\ContractDao;
use ddmp\common\data\models\file\FileDao;
use ddmp\common\data\models\partner\PartnerDao;
use ddmp\common\data\models\partnerService\PartnerServiceDao;
use ddmp\common\data\models\patient\PatientDao;
use ddmp\common\data\models\requestComment\RequestCommentDao;
use ddmp\common\data\models\requestConfidant\RequestConfidantDao;
use ddmp\common\enums\RequestStatusEnum;
use ddmp\common\exceptions\NotAllowedException;
use ddmp\common\exceptions\ValidationException;
use ddmp\common\extend\yii\validators\DateValidator;
use ddmp\common\enums\ServiceTypeEnum;
use yii\caching\TagDependency;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "request".
 *
 * @property string              $id                       Id заявки
 * @property int                 $patient_id               Id пациента
 * @property int                 $contract_id              Id контракта
 * @property int                 $partner_id               Id партнёра
 * @property int                 $partner_service_id       Идентификатор услуги партнёра
 * @property string              $city                     Город
 * @property string              $date_desired             Желаемая дата приёма
 * @property string              $time_desired             Желаемое время приёма
 * @property string              $comment                  Комментарий
 * @property string              $date_scheduled           Подтверждённая дата записи
 * @property string              $time_scheduled           Подтверждённое время записи
 * @property string              $clinic_name              Наименование клиники приёма
 * @property string              $clinic_address           Адрес клиники приёма
 * @property string              $comment_partner          Комментарий партнёра
 * @property int                 $status                   Статус заявки
 * @property string              $order_number             Номер заказа
 * @property string              $delivery_address         Адрес доставки
 * @property string              $delivery_date            Дата доставки
 * @property string              $delivery_time            Время доставки
 * @property string              $name                     Имя
 * @property string              $last_name                Фамилия
 * @property string              $middle_name              Отчество
 * @property string              $birthday                 Дата рождения
 * @property string              $phone                    Мобильный телефон
 * @property string              $home_phone               Домашний телефон
 * @property string              $disability               Инвалидность
 * @property string              $major_diseases           Основные заболевания
 * @property string              $medications_taken        Принимаемые препараты
 * @property string              $allergic_to              Аллергия
 *
 * @property string              $create_time              Время создания записи
 * @property string              $update_time              Время обновления записи
 *
 * @property ContractDao         $contract
 * @property PatientDao          $patient
 * @property PartnerDao          $partner
 * @property PartnerServiceDao   $partnerService
 * @property FileDao[]           $filesPatient
 * @property FileDao[]           $filesPartner
 * @property RequestCommentDao[] $requestComments
 * @property RequestConfidantDao[] $requestConfidants
 */
class RequestDao extends BaseDao
{
	public const STATUSES = [
		'PROCESSING' => 1, // запись в обработке
		'APPROVED' => 2, // запись подтверждена
		'FINISHED' => 3, // завершено
		'CANCELLED' => 4, //отменено
	];

	const SCENARIO_EMAIL_CHECKUP_REQUEST_CREATE = 'to_text_checkup_request';

	const APPROVE_EXPIRE_INTERVAL = 2592000;

	private $statusChangeMap = [
		RequestStatusEnum::NEW      => [
			RequestStatusEnum::AWAITING,
			RequestStatusEnum::APPROVED,
			RequestStatusEnum::REJECTED,
		],
		RequestStatusEnum::AWAITING => [
			RequestStatusEnum::APPROVED,
			RequestStatusEnum::REJECTED,
		],
		RequestStatusEnum::APPROVED => [
			RequestStatusEnum::APPROVED,
			RequestStatusEnum::REJECTED,
			RequestStatusEnum::CONCLUDED
		]
	];

	/** Файлы загруженные пациентом */
	const FILE_TARGET_PATIENT = 'request/patient';

	/** Файлы загруженные партнёром */
	const FILE_TARGET_PARTNER = 'request/partner';

	/** Тег для сброса кеша зявок пациента */
	const CACHE_TAG_PATIENT_REQUESTS = "cache_tag_patient_requests";

	/**
	 * Возвращает тег для сброса кеша заявок пациента
	 *
	 * @param string $patientId
	 *
	 * @return string
	 */
	public static function getPatientRequestsTag($patientId)
	{
		return self::CACHE_TAG_PATIENT_REQUESTS . "_{$patientId}";
	}

	public function scenarios()
	{
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_EMAIL_CHECKUP_REQUEST_CREATE] = [
			'id',
			'city',
			'time_desired',
			'date_desired',
			'comment',
			'create_time'
		];

		return $scenarios;
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'request';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return array_merge(
			parent::rules(),
			[
				[['patient_id', 'contract_id', 'partner_id', 'partner_service_id'], 'required'],
				[['patient_id', 'contract_id', 'partner_service_id', 'status'], 'integer'],
				[['date_desired', 'time_scheduled', 'create_time', 'update_time'], 'safe'],
				[['comment', 'comment_partner'], 'string'],
				[['city', 'time_desired', 'clinic_name'], 'string', 'max' => 255],
				[['delivery_address', 'delivery_time'], 'string', 'max' => 255],
				[['name', 'last_name', 'middle_name'], 'string', 'max' => 255],
				[['birthday', 'phone', 'home_phone'], 'string', 'max' => 255],
				[['major_diseases', 'medications_taken', 'allergic_to'], 'string', 'max' => 255],
				[['disability',], 'integer'],
				[['clinic_address'], 'string', 'max' => 500],
				[['date_desired', 'date_scheduled', 'delivery_date'], DateValidator::class],
				[['order_number'], 'string', 'max' => 255],
			]
		);
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'                 => 'Номер заявки',
			'patient_id'         => 'Id пациента',
			'contract_id'        => 'Id контракта',
			'partner_id'         => 'Партнёр',
			'partner_service_id' => 'Идентификатор услуги партнёра',
			'city'               => 'Город',
			'date_desired'       => 'Желаемая дата приёма',
			'time_desired'       => 'Желаемое время приёма',
			'comment'            => 'Комментарий',
			'date_scheduled'     => 'Подтверждённая дата записи',
			'time_scheduled'     => 'Подтверждённое время записи',
			'clinic_name'        => 'Наименование клиники приёма',
			'clinic_address'     => 'Адрес клиники приёма',
			'comment_partner'    => 'Комментарий партнёра',
			'status'             => 'Статус заявки',
            'delivery_address' => 'Адрес доставки',
            'delivery_date' => 'Дата доставки',
            'delivery_time' => 'Время доставки',
            'name' => 'Имя',
            'last_name' => 'Фамилия',
            'middle_name' => 'Отчество',
            'birthday' => 'Дата рождения',
            'phone' => 'Мобильный телефон',
            'home_phone' => 'Домашний телефон',
            'disability' => 'Инвалидность',
            'major_diseases' => 'Основные заболевания',
            'medications_taken' => 'Принимаемые препараты',
            'allergic_to' => 'Аллергия',

			'create_time'        => 'Время создания заявки',
			'update_time'        => 'Время обновления заявки',
			'order_number'      => 'Номер заказа',
		];
	}

	public function afterSave($insert, $changedAttributes)
	{
		TagDependency::invalidate(\Yii::$app->cache, self::getPatientRequestsTag($this->patient_id));

		parent::afterSave($insert, $changedAttributes);
	}

	/**
	 * @inheritdoc
	 * @return RequestDaoQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return RequestDaoQuery::build(get_called_class());
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getContract()
	{
		return $this->hasOne(ContractDao::class, ['id' => 'contract_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPatient()
	{
		return $this->hasOne(PatientDao::class, ['id' => 'patient_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPartner()
	{
		return $this->hasOne(PartnerDao::class, ['id' => 'partner_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getFilesPatient()
	{
		return $this->hasMany(FileDao::class, ['row_id' => 'id'])->andOnCondition(
			['target' => self::FILE_TARGET_PATIENT]
		);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getFilesPartner()
	{
		return $this->hasMany(FileDao::class, ['row_id' => 'id'])->andOnCondition(
			['target' => self::FILE_TARGET_PARTNER]
		);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPartnerService()
	{
		return $this->hasOne(PartnerServiceDao::class, ['id' => 'partner_service_id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRequestComments(): ActiveQuery
	{
		return $this->hasMany(RequestCommentDao::class, ['request_id' => 'id']);
	}

    /**
     * @return ActiveQuery
     */
	public function getRequestConfidants(): ActiveQuery
    {
        return $this->hasMany(RequestConfidantDao::class, ['request_id' => 'id']);
    }

	/**
	 * Для переопределения возможности переходов
	 *
	 * @param $array
	 */
    public function overrideChangeStatus($array)
	{
		$this->statusChangeMap = $array + $this->statusChangeMap;
	}

	/**
	 * @return array|null
	 */
	public function getStatusMapForType()
	{
		$map = null;
		/* даем возможность завершать заявку из Ожидает подтверждения */
		if ($this->partnerService->type == ServiceTypeEnum::LIFE_BUTTON) {
			$map = [
					RequestStatusEnum::AWAITING => [
						RequestStatusEnum::APPROVED,
						RequestStatusEnum::REJECTED,
						RequestStatusEnum::CONCLUDED
					]
				] + $this->statusChangeMap;
		}

		return $map;
	}

	/**
	 * Можно ли менять статус
	 *
	 * @param $status
	 * @param array $map
	 *
	 * @return bool
	 */
	public function canChangeStatus($status)
	{
		$map = $this->getStatusMapForType();

		if ($map === null) {
			$map = $this->statusChangeMap;
		}

		return
			isset($map[$this->status])
			&& in_array($status, $map[$this->status]);
	}

	/**
	 * Смена статуса
	 *
	 * @param int $status
	 *
	 * @throws NotAllowedException
	 */
	public function nextStatus($status)
	{
		if (!$this->canChangeStatus($status)) {
			throw NotAllowedException::raise(
				ValidationException::ERROR_INVALID_REQUEST,
				'Не разрешена смена статуса заявки'
			);
		}

		$this->status = $status;
	}

	/**
	 * Маппинг статусов
	 *
	 * @param int $status
	 * @return int
	 */
	public function mapViewStatus(): int
	{
		$statuses = [
			RequestStatusEnum::AWAITING => self::STATUSES['PROCESSING'],
			RequestStatusEnum::APPROVED => self::STATUSES['APPROVED'],
			RequestStatusEnum::CONCLUDED => self::STATUSES['FINISHED'],
			RequestStatusEnum::REJECTED => self::STATUSES['CANCELLED'],
			RequestStatusEnum::NEW => self::STATUSES['PROCESSING'],
		];
		return $statuses[$this->status];
	}

	/**
	 * Двухэтапная заявка
	 *
	 * @return bool
	 */
	public function isTwoStep(): bool
	{
		$twoStepRequests = [
			ServiceTypeEnum::LIFE_BUTTON,
		];

		return in_array($this->partnerService->type, $twoStepRequests);
	}
}
