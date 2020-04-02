<?php

namespace ddmp\common\data\models\serviceFeature;

use ddmp\common\data\models\BaseDaoQuery;

/**
 * This is the ActiveQuery class for [[PartnerServiceFeatureDao]].
 *
 * @see PartnerServiceFeatureDao
 */
class PartnerServiceFeatureDaoQuery extends BaseDaoQuery
{
	/**
	 * @inheritdoc
	 * @return PartnerServiceFeatureDao[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return PartnerServiceFeatureDao|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}
}

