<?php

namespace ddmp\common\data\models\requestComment;

use ddmp\common\data\models\BaseDaoQuery;

/**
 * This is the ActiveQuery class for [[RequestCommentDao]].
 *
 * @see RequestCommentDao
 */
class RequestCommentDaoQuery extends BaseDaoQuery
{
	/**
	 * @inheritdoc
	 * @return RequestCommentDao[]|array
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return RequestCommentDao|array|null
	 */
	public function one($db = null): ?RequestCommentDao
	{
		return parent::one($db);
	}
}
