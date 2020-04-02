<?php

namespace ddmp\common\data\models\productService;

use ddmp\common\data\models\BaseDao;
use ddmp\common\data\models\contract\ContractDao;
use ddmp\common\data\models\partnerService\PartnerServiceDao;
use ddmp\common\data\models\product\ProductDao;
use ddmp\common\enums\ProductServiceStatusEnum;
use ddmp\common\extend\yii\validators\EnumValidator;
use ddmp\common\models\common\NameCases;
use ddmp\common\params\telemed\TelemedParams;
use ddmp\common\utils\formatters\DateTimeFormat;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "product_service".
 *
 * @property int               $id                 Id
 * @property string            $product_id         Id продукта
 * @property int               $partner_service_id Идентификатор услуги партнёра
 * @property string            $title              Заголовок услуги
 * @property string            $title_accusative   Заголовок услуги в винительном падеже
 * @property string            $action_name        Название кнопки запускающей услугу
 * @property string            $icon_name          Название иконки
 * @property string            $description        Описание услуги продукта
 * @property int               $status             Статус услуги продукта
 * @property int               $limit_count        Количество использований за период (NULL - не ограничено)
 * @property int               $limit_period       Период использования в месяцах (NULL - весь срок контракта)
 * @property int               $limit_repeat       Повторять лимит после первого завершения
 * @property string            $create_time        Время создания записи
 * @property string            $update_time        Время изменения записи
 * @property string            $available_in       Интервал через который услуга доступна (в днях от даты покупки)
 * @property string            $bpm_id             ID в BPM
 *
 * @property ProductDao        $product
 * @property PartnerServiceDao $partnerService
 */
class ProductServiceDao extends BaseDao
{

	const ICON_DEFAULT = 'bank-plus';

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'product_service';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['product_id'], 'required'],
			[['product_id', 'partner_service_id', 'status'], 'integer'],
			[['create_time', 'action_name', 'icon_name'], 'safe'],
			[['status'], EnumValidator::class, 'enumClass' => ProductServiceStatusEnum::class],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'product_id'         => 'Продукт',
			'partner_service_id' => 'Услуга партнёра',
			'create_time'        => 'Время создания записи',
			'action_name'        => 'Название кнопки запускающей услугу',
			'icon_name'          => 'Название иконки',
			'status'             => 'Статус услуги продукта',
			'limit_count'        => 'Ограничение по кол-ву использований',
			'limit_period'       => 'Период (мес.) на который ограничено кол-во использований',
			'limit_repeat'       => 'Сбрасывать лимит спустя период',
			'title'              => 'Название',
			'title_accusative'   => 'Название в винительном п. (Создать Кого? Что?)',
			'description'        => 'Описание',
			'available_in'       => 'Через сколько дней станет доступна после подключения?',
			'bpm_id'             => 'ID в BPM',
			'update_time'        => 'Время обновления',
		];
	}

	/**
	 * @inheritdoc
	 * @return ProductServiceDaoQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return ProductServiceDaoQuery::build(get_called_class());
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getProduct()
	{
		return $this->hasOne(ProductDao::class, ['id' => 'product_id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getPartnerService()
	{
		return $this->hasOne(PartnerServiceDao::class, ['id' => 'partner_service_id']);
	}

	/**
	 * Активны статус у услуги продукта
	 *
	 * @return bool
	 */
	public function isActive()
	{
		return $this->status == ProductServiceStatusEnum::ACTIVE;
	}

	/**
	 * Возвращает заголовок услуги
	 * в винительном падеже
	 *
	 * @return string
	 */
	public function getTitleAccusative()
	{
		return $this->partner_service_id === null
			? $this->title_accusative
			: $this->partnerService->title_accusative;
	}

	/**
	 * Возвращает заголовок услуги
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->partner_service_id === null
			? $this->title
			: $this->partnerService->title;
	}

	/**
	 * Возвращает описание услуги
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->partner_service_id === null
			? $this->description
			: $this->partnerService->description;
	}

	/**
	 * Возвращает названия действия оказания услуги
	 *
	 * @return string
	 */
	public function getActionName()
	{
		return $this->partner_service_id === null
			? $this->action_name
			: $this->partnerService->action_name;
	}

	/**
	 * Возвращает наименование иконки для текущей услуги партнёра
	 *
	 * @return string
	 */
	public function getIconName()
	{
		if ($this->icon_name) {
			return $this->icon_name;
		}
		/** @var PartnerServiceDao $partnerService */
		if ($this->partner_service_id && $partnerService = $this->getPartnerService()->one()) {
			return $partnerService->getIconName();
		}
		return self::ICON_DEFAULT;
	}

	/**
	 * @return int
	 */
	public function getType()
	{
		return $this->partnerService ? $this->partnerService->type : null;
	}

	/**
	 * @return string
	 */
	public function getCode()
	{
		return $this->partnerService->code;
	}

	/**
	 * Возвращает дату доступности
	 *
	 * @param ContractDao $contract
	 *
	 * @return \DateTime
	 */
	public function getAvailableDate($contract)
	{
		if ($this->available_in === null) {
			return new \DateTime();
		}

		return DateTimeFormat::parse($contract->purchase_date)
			->getDateTime()
			->add(new \DateInterval("P{$this->available_in}D"));
	}

	/**
	 * @return bool
	 */
	public function isDisabled(): bool
	{
		return $this->status === ProductServiceStatusEnum::DISABLED;
	}

	/**
	 * @return null|string
	 */
	public function getExternalPartnerServiceId(): ?string
	{
		if ($this->partnerService->external_id) {
			return $this->partnerService->external_id;
		}

		$telemedParams = TelemedParams::build();
		$partnerParams = $telemedParams->getParamsOfPartner($this->partnerService->partner_id);
		$externalPartnerServiceId = $partnerParams->getPartnerProductIdByMarketProductId($this->product_id);

		return $externalPartnerServiceId;
	}

	/**
	 * Возвращает услугу заменитель для услуги продукта
	 *
	 * На данный момент доступно только для продукта Сбербанк 1
	 * Для услуг партнёров 1 и 2
	 *
	 * @return PartnerServiceDao|null
	 */
	public function getReplacingPartnerService(): ?PartnerServiceDao
	{
		if ($this->product_id !== ProductDao::PRODUCT_PERS_MED_SBER) {
			return null;
		}

		switch ($this->partner_service_id) {
			case 1:
				return PartnerServiceDao::find()->oneById(2);
			case 2:
				return PartnerServiceDao::find()->oneById(1);
			default:
				return null;
		}
	}

	/**
	 * @return NameCases
	 */
	public function getNameCases(): NameCases
	{
		$nameCases = parent::getNameCases();
		$nameCases->setNominative('Услуга продукта');
		$nameCases->setNominativePlural('Услуги продуктов');
		$nameCases->setGenitivePlural('услуг продуктов');
		$nameCases->setAccusative('услугу продукта');

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