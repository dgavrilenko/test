<?php

namespace ddmp\common\data\models\serviceFeature;

use ddmp\common\data\models\BaseDaoQuery;

/**
 * This is the ActiveQuery class for [[ServiceFeatureDao]].
 *
 * @see ServiceFeatureDao
 */
class ServiceFeatureDaoQuery extends BaseDaoQuery
{
	/**
	 * @inheritdoc
	 * @return ServiceFeatureDao[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return ServiceFeatureDao|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}
}

