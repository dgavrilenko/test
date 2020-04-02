<?php

namespace ddmp\common\data\models\serviceFeature;

use ddmp\common\data\models\BaseDao;
use ddmp\common\data\models\partnerService\PartnerServiceDao;

/**
 * Модель услуг партнёрских продуктов
 *
 * @property int               $id                 Id
 * @property string            $title              Заголовок услуги
 * @property string            $action_name        Название кнопки запускающей услугу
 * @property string            $icon_name          Название иконки
 * @property string            $description        Описание услуги
 *
 * @property PartnerServiceDao[] $partnerServices
 * @property PartnerServiceFeatureDao[] $partnerServiceFeatures
 */
class ServiceFeatureDao extends BaseDao
{

	const ICON_DEFAULT = 'telemed';

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'service_feature';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
        return [
            [['title', 'action_name'], 'required', 'max' => 255],
            [['title', 'action_name', 'icon_name', 'description'], 'string'],
        ];
	}

    /**
     * @inheritdoc
     * @return ServiceFeatureDaoQuery the active query used by this AR class.
     */
    public static function find()
    {
        return ServiceFeatureDaoQuery::build(get_called_class());
    }

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'title'       => 'Наименование услуги',
			'description' => 'Описание',
			'create_time' => 'Время создания записи',
			'action_name' => 'Название кнопки запускающей услугу',
			'icon_name'   => 'Название иконки',
		];
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerServices()
    {
        return $this->hasMany(PartnerServiceDao::class, ['id' => 'partner_service_id'])
            ->via('partnerServiceFeatures');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerServiceFeatures()
    {
        return $this->hasMany(PartnerServiceFeatureDao::class, ['service_feature_id' => 'id']);
    }

	/**
	 * Возвращает заголовок услуги
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Возвращает описание услуги
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Возвращает названия действия оказания услуги
	 *
	 * @return string
	 */
	public function getActionName()
	{
		return $this->action_name;
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

		return self::ICON_DEFAULT;
	}
}