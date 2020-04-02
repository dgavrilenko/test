<?php

namespace ddmp\common\data\models\partnerService;

use ddmp\common\base\data\models\AdminDaoInterface;
use ddmp\common\data\models\BaseDao;
use ddmp\common\data\models\partner\PartnerDao;
use ddmp\common\data\models\productService\ProductServiceDao;
use ddmp\common\data\models\serviceFeature\PartnerServiceFeatureDao;
use ddmp\common\data\models\serviceFeature\ServiceFeatureDao;
use ddmp\common\data\models\request\RequestDao;
use ddmp\common\data\models\serviceLog\ServiceLogDao;
use ddmp\common\enums\ServiceTypeEnum;
use ddmp\common\extend\yii\validators\DateTimeValidator;
use ddmp\common\models\common\NameCases;

/**
 * This is the model class for table "partner_service".
 *
 * @property int                 $id               Идентификатор услуги партнёра
 * @property string              $partner_id       Идентификатор партнёра
 * @property string              $external_id      Идентификатор на стороне партнёра
 * @property string              $title            Наименование услуги
 * @property string              $code             Код услуги
 * @property string              $title_accusative Наименование в винительном падеже
 * @property int                 $type             Тип услуги
 * @property string              $description      Описание услуги
 * @property string              $action_name      Название кнопки запускающей услугу
 * @property string              $icon_name        Название иконки
 * @property string              $bpm_id           ID в системе BPM
 * @property int                 $bitrix_id        Идентификатор в системе bitrix
 * @property string              $create_time      Время создания записи
 * @property string              $update_time      Время обновления записи
 *
 * @property PartnerDao          $partner
 * @property ProductServiceDao[] $productServices
 * @property RequestDao[]        $requests
 * @property ServiceLogDao[]     $serviceLogs
 * @property ServiceFeatureDao[] $serviceFeatures
 * @property PartnerServiceFeatureDao[] $partnerServiceFeatures
 */
class PartnerServiceDao extends BaseDao
{
	const ICON_DEFAULT = 'bank-plus';

	const ICON_TELEMED = 'telemed';

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'partner_service';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[$this->attributes(), 'trim'],
			[$this->attributes(), 'default'],
			[['partner_id', 'type'], 'integer'],
			[['title', 'type'], 'required'],
			[['description'], 'string'],
			[['create_time', 'update_time', 'external_id', 'bitrix_id'], 'safe'],
			[['title', 'title_accusative', 'bpm_id'], 'string', 'max' => 255],
			[['code', 'action_name', 'icon_name'], 'string', 'max' => 100],
			[['create_time', 'update_time'], DateTimeValidator::class],
			[['partner_id', 'code'], 'required', 'on' => AdminDaoInterface::SCENARIO_ADMIN_EDIT],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'               => 'Идентификатор услуги партнёра',
			'partner_id'       => 'Идентификатор партнёра',
			'title'            => 'Наименование услуги',
			'code'             => 'Код услуги',
			'title_accusative' => 'Наименование в винительном падеже',
			'type'             => 'Тип услуги',
			'description'      => 'Описание услуги',
			'action_name'      => 'Название кнопки запускающей услугу',
			'icon_name'        => 'Название иконки',
			'bpm_id'           => 'ID в системе BPM',
			'create_time'      => 'Время создания записи',
			'update_time'      => 'Время обновления записи',
			'external_id'      => 'Идентификатор в системе партнёра',
		];
	}

	/**
	 * @inheritdoc
	 * @return PartnerServiceDaoQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return PartnerServiceDaoQuery::build(get_called_class());
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
	public function getProductServices()
	{
		return $this->hasMany(ProductServiceDao::class, ['partner_service_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRequests()
	{
		return $this->hasMany(RequestDao::class, ['partner_service_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getServiceLogs()
	{
		return $this->hasMany(ServiceLogDao::class, ['partner_service_id' => 'id']);
	}

    /**
     * @return \yii\db\ActiveQuery
     */
	public function getServiceFeatures()
    {
        return $this->hasMany(ServiceFeatureDao::class, ['id' => 'service_feature_id'])
            ->via('partnerServiceFeatures');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerServiceFeatures()
    {
        return $this->hasMany(PartnerServiceFeatureDao::class, ['partner_service_id' => 'id']);
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
		return ServiceTypeEnum::TELEMED == $this->type ? self::ICON_TELEMED : self::ICON_DEFAULT;
	}

	/**
	 * @return int|null
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getPartnerId(): string
	{
		return $this->partner_id;
	}

	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * @return string
	 */
	public function getCode(): string
	{
		return $this->code;
	}

	/**
	 * @return string
	 */
	public function getTitleAccusative(): string
	{
		return $this->title_accusative;
	}

	/**
	 * @return int
	 */
	public function getType(): int
	{
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function getActionName(): string
	{
		return $this->action_name;
	}

	/**
	 * @return string
	 */
	public function getBpmId(): string
	{
		return $this->bpm_id;
	}

	/**
	 * @return string
	 */
	public function getCreateTime(): string
	{
		return $this->create_time;
	}

	/**
	 * @return string
	 */
	public function getUpdateTime(): string
	{
		return $this->update_time;
	}

	/**
	 * @return string
	 */
	public function getExternalPartnerServiceId(): string
	{
		return $this->external_id;
	}

	/**
	 * @return NameCases
	 */
	public function getNameCases(): NameCases
	{
		$nameCases = parent::getNameCases();
		$nameCases->setNominative('Услуга партнёра');
		$nameCases->setNominativePlural('Услуги партнёров');
		$nameCases->setGenitivePlural('услуг партнёров');
		$nameCases->setAccusative('услугу партнёра');

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