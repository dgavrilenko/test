<?php

namespace ddmp\common\data\models\contract;

use ddmp\common\data\models\productService\ProductServiceDao;
use ddmp\common\enums\ContractPurchaseTypeEnum;
use DateTime;
use ddmp\common\base\models\common\PayInfoInterface;
use ddmp\common\commands\contract\CalculateNextPaymentCommand;
use ddmp\common\data\models\BaseDao;
use ddmp\common\data\models\importPatient\ImportPatientDao;
use ddmp\common\data\models\partner\PartnerDao;
use ddmp\common\data\models\partnerService\PartnerServiceDao;
use ddmp\common\data\models\patient\PatientDao;
use ddmp\common\data\models\product\ProductDao;
use ddmp\common\data\models\request\RequestDao;
use ddmp\common\data\models\serviceLog\ServiceLogDao;
use ddmp\common\enums\ContractStatusEnum;
use ddmp\common\enums\PayStatus;
use ddmp\common\enums\ServiceLogStatusEnum;
use ddmp\common\enums\ServiceTypeEnum;
use ddmp\common\exceptions\ValidationException;
use ddmp\common\extend\yii\validators\DateTimeValidator;
use ddmp\common\extend\yii\validators\DateValidator;
use ddmp\common\extend\yii\validators\DefaultIntegerValidator;
use ddmp\common\extend\yii\validators\EnumValidator;
use ddmp\common\models\common\NameCases;
use ddmp\common\models\contract\CalculateNextPaymentModel;
use ddmp\common\models\contract\ContractPayInfo;
use ddmp\common\utils\formatters\DateTimeFormat;
use ddmp\common\utils\helpers\CheckEmptyModelTrait;
use ddmp\common\utils\helpers\ServiceActivatorFactory;
use ddmp\common\utils\PhpUtils;
use yii\caching\TagDependency;

/**
 * This is the model class for table "contract".
 *
 * @property string           $id                Id контракта
 * @property string           $number            Номер договора
 * @property string           $product_id        Id приобретенного продукта
 * @property string           $purchase_date     Дата покупки в маркете. NULL пока не оплачен
 * @property string           $start_date        Дата начала действия контракта
 * @property string           $end_date          Дата окончания действия продукта
 * @property string           $status            Статус контракта
 * @property string           $comment           Причина подключения/отключения
 * @property string           $partner_id        Id партнера
 * @property string           $patient_id        Id пациента
 * @property int              $import_patient_id Id ипортируемого пациента
 * @property string           $create_time       Время создания записи
 * @property string           $update_time       Время обновления записи
 * @property int              $is_sms_sent       Смс была отправлена
 * @property int              $bitrix_id         Идентификатор заказа в bitrix
 * @property string           $last_paid         Время последней оплаты
 * @property string           $next_pay          Время следующей оплаты
 * @property number           $next_sum          Сумма следующего платежа
 * @property string           $invoice_id        Номер счёта для повторного списания yandex
 * @property int              $pay_status        Статус оплаты
 * @property int              $pay_rule          Правило оплаты
 * @property int              $is_mp_watched     Мобильная платформа ознакомлена с контрактом
 * @property int              $relation_id       Представитель семейного кабинета
 *
 * @property PatientDao       $patient
 * @property ProductDao       $product
 * @property ImportPatientDao $importPatient
 * @property RequestDao[]     $requests
 * @property ServiceLogDao[]  $serviceLogs
 */
class ContractDao extends BaseDao
{
	use CheckEmptyModelTrait;

	/**
	 * Статусы при которых контракт действует и по нему можно получить услугу
	 */
	const ACTIVE_STATUSES = [
		ContractStatusEnum::ACTIVE,
		ContractStatusEnum::CANCELLED
	];

	const SCENARIO_MMT = 'mmt';

	/**
	 * При зарузке реестра Сбербанка
	 */
	const SCENARIO_SBREG = 'sbreg';

	/**
	 * Имейл о создании заявки на чекап
	 */
	const SCENARIO_EMAIL_CHECKUP_REQUEST_CREATE = 'email_checkup_request_create';

	/**
	 * @return array
	 */
	public static function getActiveStatuses()
	{
		return self::ACTIVE_STATUSES;
	}

	public function scenarios()
	{
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_EMAIL_CHECKUP_REQUEST_CREATE] = ['number'];
		$scenarios[self::SCENARIO_MMT] = ['id', 'product_id', 'partner_id', 'start_date', 'end_date', 'number', 'comment'];
		return $scenarios;
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'contract';
	}

	/**
	 * @inheritdoc
	 * @return ContractDaoQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return ContractDaoQuery::build(get_called_class());
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['import_patient_id'], DefaultIntegerValidator::class],
			[['product_id', 'partner_id'], 'required'],
			[['product_id', 'start_date', 'end_date'], 'required', 'on' => self::SCENARIO_MMT],
			[['product_id', 'start_date'], 'required', 'on' => self::SCENARIO_SBREG],
			[['product_id', 'status', 'partner_id', 'patient_id', 'is_sms_sent', 'pay_status', 'import_patient_id', 'bitrix_id', 'pay_rule', 'is_mp_watched'], 'integer'],
			[['purchase_date', 'create_time', 'update_time', 'last_paid', 'next_pay'], DateTimeValidator::class],
			[['start_date', 'end_date'], DateValidator::class],
			[['comment', 'invoice_id'], 'string'],
			[['number'], 'string', 'max' => 255],
			[['next_sum'], 'number'],
			[['status'], EnumValidator::class, 'enumClass' => ContractStatusEnum::class],
			[['pay_status'], EnumValidator::class, 'enumClass' => PayStatus::class],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'                => 'Идентификатор договора',
			'number'            => 'Номер договора',
			'product_id'        => 'Id приобретенного продукта',
			'purchase_date'     => 'Дата покупки в маркете. NULL пока не оплачен',
			'start_date'        => 'Дата начала действия контракта',
			'end_date'          => 'Дата окончания действия продукта',
			'status'            => 'Статус контракта',
			'comment'           => 'Причина подключения/отключения',
			'partner_id'        => 'Id партнера',
			'patient_id'        => 'Id пациента',
			'create_time'       => 'Время создания записи',
			'update_time'       => 'Время обновления записи',
			'is_sms_sent'       => 'Смс была отправлена',
			'relation_id'       => 'Представитель семейного кабинета',
			'last_paid'         => 'Время последней оплаты (подписка)',
			'next_pay'          => 'Дата следующей оплаты (подписка)',
			'next_sum'          => 'Сумма к оплате (подписка)',
			'invoice_id'        => 'Номер счёта (подписка)',
			'pay_rule'          => 'Правило для оплаты (подписка)',
			'pay_status'        => 'Статус оплаты',
			'is_mp_watched'     => 'Просмотрено на мобильном',
			'import_patient_id' => 'ID импорт пациента',
		];
	}

	/**
	 * @inheritdoc
	 *
	 * @param bool  $insert
	 * @param array $changedAttributes
	 */
	public function afterSave($insert, $changedAttributes)
	{
		parent::afterSave($insert, $changedAttributes);

		TagDependency::invalidate(\Yii::$app->cache, ContractDaoQuery::getCacheTag($this->patient_id));
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
	public function getProduct()
	{
		return $this->hasOne(ProductDao::class, ['id' => 'product_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getImportPatient()
	{
		return $this->hasOne(ImportPatientDao::class, ['id' => 'import_patient_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getServiceLogs()
	{
		return $this
			->hasMany(ServiceLogDao::class, ['contract_id' => 'id'])
			->andOnCondition('status != :status', ['status' => ServiceLogStatusEnum::NOT_REQUESTED]);
	}

	/**
	 * Возвращает текущую дату начала договора
	 * в зависимости от его типа
	 *
	 * @return string
	 */
	public function getCurrentStartDate()
	{
		if ($this->isSubscription()) {
			return $this->last_paid;
		}

		return $this->start_date;
	}

	/**
	 * Возвращает текущую дату окончания договора
	 * в зависимости от его типа
	 *
	 * @return string
	 */
	public function getCurrentEndDate()
	{
		if ($this->isSubscription()) {
			return $this->next_pay;
		}

		return $this->end_date;
	}

	/**
	 * Возвращает метку считающую бесконечной датой окончания полиса
	 *
	 * @return DateTime
	 */
	public function getInfiniteDate()
	{
		return new DateTime('2029-06-03');
	}

	/**
	 * Возвращает информацию об оплате контракта
	 *
	 * @return PayInfoInterface
	 */
	public function getPayInfo()
	{
		return new ContractPayInfo($this);
	}

	/**
	 * @return bool
	 */
	public function isActive() : bool
	{
		return in_array((int)$this->status, self::ACTIVE_STATUSES, true);
	}

	/**
	 * @return bool
	 */
	public function isInactive() : bool
	{
		return (int)$this->status === ContractStatusEnum::INACTIVE;
	}

	/**
	 * @return bool
	 */
	public function isCanceled() : bool
	{
		return (int)$this->status === ContractStatusEnum::CANCELLED;
	}

	/**
	 * Установить флаг приветственное смс отправлено
	 */
	public function setWelcomeSmsSent()
	{
		$this->is_sms_sent = 1;
	}

	/**
	 * Привязывает пациента к контракту
	 *
	 * @param PatientDao $patient
	 *
	 * @throws ValidationException
	 */
	public function linkPatient($patient)
	{
		if (!empty($this->patient_id) && $this->patient_id != $patient->id) {
			throw new ValidationException(
				ValidationException::ERROR_CONTRACT_HAS_PATIENT,
				'Контракт уже привязан к пациенту'
			);
		}

		$this->patient_id = $patient->id;
	}

	/**
	 * Загружает модель из модели @see ProductDao
	 *
	 * @param ProductDao $product
	 */
	public function loadFromProduct($product)
	{
		$this->product_id = $product->id;
	}

	/**
	 * Возвращает номер контракта ммт
	 *
	 * @return string
	 */
	public function getMmtContractNumber()
	{
		$result = $this->patient_id . '-' . $this->id;

		if (IS_DEBUG) {
			$result .= '-' . DateTimeFormat::parse($this->create_time)->solid();
		}

		return $result;
	}

	/**
	 * Активна ли указанная услуга
	 *
	 * @param int $partnerServiceId
	 *
	 * @return bool
	 */
	public function isServiceActive(int $partnerServiceId): bool
	{
		/** @var PhpUtils $phpUtils */
		$phpUtils = \Yii::$container->get(PhpUtils::class);
		$productService = $this->product->getProductServiceOfPartnerService($partnerServiceId);

		if ($productService === null) {
			return false;
		}

		if ($productService->available_in !== null) {
			$availableDate = $productService->getAvailableDate($this);
			$now = DateTimeFormat::parse($phpUtils->time())->getDateTime();

			if ($availableDate > $now) {
				return false;
			}
		}

		$serviceLogs = array_filter(
			$this->serviceLogs,
			function (ServiceLogDao $log) use ($partnerServiceId) {
				return $log->partner_service_id == $partnerServiceId;
			}
		);

		/** @var ServiceActivatorFactory $activatorFactory */
		$activatorFactory = \Yii::$container->get(ServiceActivatorFactory::class);
		$activator = $activatorFactory->getServiceActivator($this);
		return $activator
			->setContract($this)
			->setProductService($productService)
			->setServiceLogs($serviceLogs)
			->isActive();
	}

	/**
	 * Вычисляет и устанавливает статус контракта по определяющим его полям
	 */
	public function determineStatus()
	{
		$now = DateTimeFormat::now()->getDateTime();
		$endDate = DateTimeFormat::parse($this->end_date)->getDateTime();
		$nextPay = DateTimeFormat::parse($this->next_pay)->getDateTime();

		if ($endDate != null && $now >= $endDate) {
			$this->status = 0;

			return;
		}
		if ($nextPay != null && $this->pay_status != PayStatus::WAITING && $nextPay < $now) {
			$this->status = 0;

			return;
		}

		$this->status = 1;
	}

	/**
	 * Вычисляет и устанавливает время следующего платежа по контракту
	 *
	 * @param int $bitrixProductId
	 */
	public function determineNextPay($bitrixProductId)
	{
		/** @var PhpUtils $phpUtils */
		$phpUtils = \Yii::$container->get(PhpUtils::class);
		$this->last_paid = DateTimeFormat::parse($phpUtils->time())->mySqlTime();

		/** @var CalculateNextPaymentCommand $calculatePaymentCommand */
		$calculatePaymentCommand = \Yii::$container->get(CalculateNextPaymentCommand::class);
		$payModel = new CalculateNextPaymentModel($this, $bitrixProductId);
		$payParams = $calculatePaymentCommand->run($payModel);

		// Заполняем реквизиты следующей оплаты
		// только, если имеются правила
		if ($payParams !== null) {
			$this->next_pay = DateTimeFormat::parse($payParams->getNextPayTime())->mySqlTime();
			$this->next_sum = $payParams->getNextSum();
			$this->pay_rule = $payParams->getNextPayRuleId();
		}
	}

	/**
	 * Является ли контракт подпиской
	 */
	public function isSubscription()
	{
		return !empty($this->next_pay);
	}

	/**
	 * Возвращает услугу партнёра указанного типа
	 *
	 * @return PartnerServiceDao[]
	 */
	public function getPartnerServices()
	{
		return $this->product->partnerServices;
	}
	
	/**
	 * Устанавливает дату окончания без срока
	 */
	public function setEndDateInfinite()
	{
		$this->end_date = DateTimeFormat::parse($this->getInfiniteDate())->mySqlDate();
	}

	/**
	 * Способ приобретения контракта
	 * @see ContractPurchaseTypeEnum
	 *
	 * @return int
	 */
	public function getPurchaseType()
	{
		//TODO: Вынести в отдельное поле, убрать вычисления по комментарию
		$default = ContractPurchaseTypeEnum::PROMO;
		$associations = [
			'заказ из bitrix' => ContractPurchaseTypeEnum::SHOP,
			'куплен через витрину сбол' => ContractPurchaseTypeEnum::SBOL,
			'промо код' => ContractPurchaseTypeEnum::PROMO,
			'sbreg-' => ContractPurchaseTypeEnum::SBREG,
		];
		foreach ($associations as $key => $value) {
			if (mb_substr(mb_strtolower($this->comment), 0, mb_strlen($key)) === $key) {
				return $value;
			}
		}

		return $default;
	}

	/**
	 * Был приобретён за деньги
	 *
	 * @return bool
	 */
	public function isMoneyPaid()
	{
		return in_array($this->getPurchaseType(), [ContractPurchaseTypeEnum::SHOP, ContractPurchaseTypeEnum::SBOL]);
	}

    /**
     * Просмотрен в мобильной платформе
     *
     * @return bool
     */
    public function isMpWatched(): bool
    {
        return (bool)$this->is_mp_watched;
    }

	/**
	 * @param int $partnerId
	 * @param int $serviceType
	 *
	 * @return PartnerServiceDao|null
	 */
	public function findPartnerService(int $partnerId, int $serviceType): ?PartnerServiceDao
	{
		return $this->product->findPartnerService($partnerId, $serviceType);
	}

	/**
	 * @param int $partnerId
	 * @param int $serviceType
	 *
	 * @return ProductServiceDao
	 */
	public function findProductService(int $partnerId, int $serviceType): ProductServiceDao
	{
		return $this->product->findProductService($partnerId, $serviceType);
	}

	/**
	 * @param int $partnerId
	 * @param int $serviceType
	 *
	 * @return null|string
	 */
	public function findExternalPartnerServiceId(int $partnerId, int $serviceType = ServiceTypeEnum::TELEMED): ?string
	{
		return $this
			->findProductService($partnerId, $serviceType)
			->getExternalPartnerServiceId();
	}

    /**
     * Установить все контракты пациенту как просмотренные
     *
     * @param int $patientId
     *
     * @return int
     */
    public function watchedAllByPatient(int $patientId): int
    {
        return self::updateAll(['is_mp_watched' => 1], ['patient_id' => $patientId]);
    }

	/**
	 * @return NameCases
	 */
	public function getNameCases(): NameCases
	{
		$nameCases = parent::getNameCases();
		$nameCases->setNominative('Контракт');
		$nameCases->setNominativePlural('Контракты');
		$nameCases->setGenitivePlural('контрактов');
		$nameCases->setAccusative('контракт');

		return $nameCases;
	}

	/**
	 * @return bool
	 */
	public function isAdministrating(): bool
	{
		return true;
	}

	/**
	 * Копирует контракт другому пациенту
	 *
	 * @param int $patientId
	 *
	 * @return ContractDao
	 */
	public function copy(int $patientId): ContractDao
	{
		$copy = ContractDao::create();

		$copy->setAttributes($this->getAttributes(null, [
			'id',
			'patient_id',
			'create_time',
			'update_time',
		]));

		$copy->comment = "copy:{$this->id}";
		$copy->patient_id = $patientId;
		$copy->bitrix_id = null;
		$copy->last_paid = null;
		$copy->next_pay = null;
		$copy->next_sum = null;
		$copy->invoice_id = null;
		$copy->pay_rule = null;
		$copy->is_mp_watched = 0;

		return $copy;
	}
}