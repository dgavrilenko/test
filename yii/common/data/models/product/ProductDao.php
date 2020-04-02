<?php

namespace ddmp\common\data\models\product;

use ddmp\common\data\models\BaseDao;
use ddmp\common\data\models\contract\ContractDao;
use ddmp\common\data\models\partner\PartnerDao;
use ddmp\common\data\models\partnerService\PartnerServiceDao;
use ddmp\common\data\models\partnerService\PartnerServiceDaoQuery;
use ddmp\common\data\models\productService\ProductServiceDao;
use ddmp\common\extend\yii\validators\DateTimeValidator;
use ddmp\common\models\common\NameCases;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "product".
 *
 * @property string              $id                   Id продукта
 * @property string              $name                 Наименование продукта
 * @property string              $partner_id           Id партнера
 * @property string              $create_time          Время создания записи
 * @property string              $update_time          Время обновления записи
 * @property string              $code                 Код продукта
 * @property string              $title                Заголовок продукта
 * @property string              $description          Описание продукта
 * @property string              $bpm_id               Идентификатор продукта в системе BPM
 * @property number              $price_sbol           Цена для платформы Сбербанк Онлайн
 * @property int                 $period_days          Срок действия контракта на продукт в днях
 * @property boolean             $is_family_available  Доступен в семейном кабинете
 *
 * @property ContractDao[]       $contracts
 * @property ProductServiceDao[] $productServices
 * @property PartnerServiceDao[] $partnerServices
 */
class ProductDao extends BaseDao
{
	const PRODUCT_KAPITAL_ZDOROVIA_INDIVIDUAL = 1;
	const PRODUCT_KAPITAL_ZDOROVIA_SEMEYNIY = 2;
	const PRODUCT_DSZH = 3;
	const PRODUCT_TELEMED_DLYA_KORP_KLIENTOV_1 = 4;
	const PRODUCT_TELEMED_DLYA_KORP_KLIENTOV_2 = 5;
	const PRODUCT_TELEMED_DLYA_KORP_KLIENTOV_3 = 6;
	const PRODUCT_TELEMED_DLYA_KORP_KLIENTOV_4 = 7;
	const PRODUCT_TELEMEDICINA_MMT = 8;
	const PRODUCT_CHECKUP_NMS = 9;
	const PRODUCT_PERS_MED_SBER_PREMIER = 10;
	/** Сбербанк Первый */
	const PRODUCT_PERS_MED_SBER = 11;
	const PRODUCT_PERS_MED_SBER_PRIVATE = 12;
	const PRODUCT_DMS_PERS_MED_STRAH_1 = 13;
	const PRODUCT_DMS_PERS_MED_STRAH_2 = 14;
	const PRODUCT_FOND_ZDOROVYA = 15;
	const PRODUCT_ONLINE_CONSULT_TERAPEVT = 16;
	const PRODUCT_ONLINE_CONSULT_PEDIATOR = 17;
	const PRODUCT_ONLINE_CONSULT_PROFILE = 18;
	const PRODUCT_ONLINE_CONSULT_TERAPEVT_PEDIATOR = 19;
	const PRODUCT_ONLINE_CONSULT_SEMEINI = 20;
	const PRODUCT_TESTOVY = 21;
	const PRODUCT_OBSHAYA_DESPANSERIZACIA = 22;
	const PRODUCT_PROFIL_DESPANSERIZACIA = 23;
	const PRODUCT_MUZSKOE_ZDOROVIE = 24;
	const PRODUCT_ZHENSKOE_ZDOROVIE = 25;
	const PRODUCT_TELE2 = 26;
	const PRODUCT_VETTELEMEDICINA = 27;
	const PRODUCT_MPC_TELEMED = 28;
	const PRODUCT_TELE2_TERAPEVT = 29;
	const PRODUCT_SBOL_TERAPEVT_3_MONTHS = 30;
	const PRODUCT_SBOL_TERAPEVT_6_MONTHS = 31;
	const PRODUCT_SBOL_TERAPEVT_YEAR = 32;
	const PRODUCT_KOT_ZDOROV_ONE_TIME_TELEMED = 36;
	const PRODUCT_KOT_ZDOROV_YEAR_TELEMED = 38;
	const PRODUCT_DOC_PLUS_TELEMED = 53;

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'product';
	}

	/**
	 * @inheritdoc
	 * @return ProductDaoQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return ProductDaoQuery::build(get_called_class());
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['name', 'partner_id'], 'required'],
			[['partner_id'], 'integer'],
			[['create_time', 'update_time'], DateTimeValidator::class],
			[['name', 'code', 'title', 'bpm_id'], 'string', 'max' => 255],
			[['description'], 'string'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'                  => 'Id продукта',
			'name'                => 'Наименование продукта',
			'partner_id'          => 'Id партнера',
			'create_time'         => 'Время создания записи',
			'update_time'         => 'Время обновления записи',
			'code'                => 'Код продукта',
			'title'               => 'Заголовок',
	-		'description'         => 'Описание',
			'bpm_id'              => 'ID продукта в системе BPM',
			'is_family_available' => 'Доступен в семейном кабинете',
			'price_sbol'          => 'Цена СБОЛ',
			'period_days'         => 'Срок активности продукта для СБОЛ',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getContracts()
	{
		return $this->hasMany(ContractDao::class, ['product_id' => 'id']);
	}

	/**
	 * Возвращает код продукта
	 *
	 * @return string
	 */
	public function getProductCode()
	{
		return $this->code;
	}

	/**
	 * Возвращает первый контракт из массива
	 *
	 * @return ContractDao|null
	 */
	public function getFirstContract()
	{
		if (empty($this->contracts)) {
			return null;
		}

		return $this->contracts[0];
	}

	/**
	 * Возвращает заголовок продукта
	 *
	 * @return string
	 */
	public function getProductTitle()
	{
		return $this->title;
	}

	/**
	 * Возвращает описание продукта
	 *
	 * @return string
	 */
	public function getProductDescription()
	{
		return $this->description;
	}

	/**
	 * Количество услуг
	 *
	 * @return int
	 * @throws \Exception
	 */
	public function getServicesCount()
	{
		return count($this->productServices);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getProductServices()
	{
		return $this->hasMany(ProductServiceDao::class, ['product_id' => 'id']);
	}

	/**
	 * @return PartnerServiceDaoQuery
	 */
	public function getPartnerServices(): PartnerServiceDaoQuery
	{
		return $this->hasMany(PartnerServiceDao::class, ['id' => 'partner_service_id'])->viaTable(
			'product_service',
			['product_id' => 'id']
		);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPartner()
	{
		return $this->hasOne(PartnerDao::class, ['id' => 'partner_id']);
	}

	/**
	 * Возвращает услугу указанного типа
	 *
	 * @param int $partnerServiceId Тип услуги
	 *
	 * @return ProductServiceDao|null
	 */
	public function getProductServiceOfPartnerService(int $partnerServiceId): ?ProductServiceDao
	{
		$typeIndexedServices = ArrayHelper::index($this->productServices, 'partner_service_id');

		return $typeIndexedServices[$partnerServiceId] ?? null;
	}

	/**
	 * Возвращает услугу в составе продукта по коду
	 *
	 * @param $partnerServiceCode
	 *
	 * @return ProductServiceDao
	 */
	public function getServiceByCode($partnerServiceCode)
	{
		$codeIndexedServices = ArrayHelper::index($this->productServices, 'partnerService.code');

		if (!isset($codeIndexedServices[$partnerServiceCode])) {
			return null;
		}

		return $codeIndexedServices[$partnerServiceCode];
	}

	/**
	 * @param int $partnerId
	 * @param int $serviceType
	 *
	 * @return PartnerServiceDao|null
	 */
	public function findPartnerService(int $partnerId, int $serviceType): ?PartnerServiceDao
	{
		$partnerServices = array_filter($this->partnerServices, function (PartnerServiceDao $partnerServiceDao) use ($partnerId) {
			return $partnerServiceDao->partner_id === $partnerId;
		});

		$partnerServices = ArrayHelper::index($partnerServices, 'type');

		return $partnerServices[$serviceType] ?? null;
	}

	/**
	 * @param int $partnerId
	 * @param int $serviceType
	 *
	 * @return ProductServiceDao|null
	 *
	 */
	public function findProductService(int $partnerId, int $serviceType): ?ProductServiceDao
	{
		$productServices = array_filter($this->productServices, function (ProductServiceDao $productServiceDao) use ($partnerId) {
			return $productServiceDao->partnerService->partner_id === $partnerId;
		});

		$productServices = ArrayHelper::index($productServices, 'partnerService.type');

		return $productServices[$serviceType] ?? null;
	}

	/**
	 * @return ProductServiceDao[]
	 */
	public function getNotDisabledProductServices(): array
	{
		return array_filter(
			$this->productServices,
			function (ProductServiceDao $productService) {
				return !$productService->isDisabled();
			}
		);
	}

	/**
	 * @param $serviceType
	 *
	 * @return ProductServiceDao|null
	 */
	public function findActiveProductService(int $serviceType): ?ProductServiceDao
	{
		foreach ($this->productServices as $productService) {
			if ($productService->isActive() && $productService->partnerService->type === $serviceType) {
				return $productService;
			}
		}

		return null;
	}

	/**
	 * @return NameCases
	 */
	public function getNameCases(): NameCases
	{
		$nameCases = parent::getNameCases();
		$nameCases->setNominative('Продукт');
		$nameCases->setNominativePlural('Продукты');
		$nameCases->setGenitivePlural('продуктов');
		$nameCases->setAccusative('продукт');

		return $nameCases;
	}

	/**
	 * @return bool
	 */
	public function isAdministrating(): bool
	{
		return true;
	}
}
