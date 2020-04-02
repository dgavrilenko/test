<?php

namespace ddmp\common\data\models\serviceFeature;

use ddmp\common\data\models\BaseDao;
use ddmp\common\data\models\partnerService\PartnerServiceDao;

/**
 * Связующая модель ServiceFeature <-> PartnerService
 *
 * @property int               $id                      Id
 * @property string            $service_feature_id      Идентификатор услуги
 * @property string            $partner_service_id      Идентификатор услуги партнёра
 * @property string            $partner_frame_url       URL фрейма партнёра
 *
 * @property PartnerServiceDao $partnerService
 * @property ServiceFeatureDao $serviceFeature
 */
class PartnerServiceFeatureDao extends BaseDao
{
    /**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'partner_service_feature';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
	    return [
            [['service_feature_id', 'partner_service_id'], 'integer'],
            [['partner_frame_url'], 'string'],
        ];
	}

    /**
     * @inheritdoc
     * @return PartnerServiceFeatureDaoQuery the active query used by this AR class.
     */
    public static function find()
    {
        return PartnerServiceFeatureDaoQuery::build(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerService()
    {
        return $this->hasOne(PartnerServiceDao::class, ['id' => 'partner_service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceFeature()
    {
        return $this->hasOne(ServiceFeatureDao::class, ['id' => 'service_feature_id']);
    }

    /**
     * @return string
     */
    public function getPartnerFrameUrl()
    {
        return $this->partner_frame_url;
    }
}