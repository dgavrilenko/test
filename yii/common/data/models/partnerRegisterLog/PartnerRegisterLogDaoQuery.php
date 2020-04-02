<?php

namespace ddmp\common\data\models\partnerRegisterLog;

use ddmp\common\data\models\BaseDaoQuery;

/**
 * This is the ActiveQuery class for [[PartnerRegisterLogDao]].
 *
 * @see PartnerRegisterLogDao
 */
class PartnerRegisterLogDaoQuery extends BaseDaoQuery
{
	/**
	 * @inheritdoc
	 * @return PartnerRegisterLogDao[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return PartnerRegisterLogDao|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}
}
