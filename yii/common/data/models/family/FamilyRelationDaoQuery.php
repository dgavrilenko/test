<?php

namespace ddmp\common\data\models\family;

use ddmp\common\data\models\BaseDaoQuery;

/**
 * This is the ActiveQuery class for [[FamilyRelationDao]].
 *
 * @see PatientDao
 */
class FamilyRelationDaoQuery extends BaseDaoQuery
{
	/**
	 * @param int $ownerId
	 * @param int $relativeId
	 *
	 * @return FamilyRelationDaoQuery
	 */
	public function byOwnerIdAndRelativeId(int $ownerId, int $relativeId): FamilyRelationDaoQuery
	{
		$this->where(['owner_id' => $ownerId])
			->andWhere(['relative_id' => $relativeId])
			->andWhere(['is_deleted' => false]);

		return $this;
	}

	/**
	 * @param int $ownerId
	 *
	 * @return FamilyRelationDaoQuery
	 */
	public function byOwnerId(int $ownerId): FamilyRelationDaoQuery
	{
		$this->where(['owner_id' => $ownerId])->andWhere(['is_deleted' => false]);
		return $this;
	}
}

