<?php

namespace ddmp\common\data\models\requestConfidant;

use ddmp\common\data\models\BaseDaoQuery;

/**
 * This is the ActiveQuery class for [[RequestConfidantDao]].
 *
 * @see RequestConfidantDaoQuery
 */
class RequestConfidantDaoQuery extends BaseDaoQuery
{
    /**
     * @inheritdoc
     * @return RequestConfidantDao[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return RequestConfidantDao|array|null
     */
    public function one($db = null): ?RequestConfidantDao
    {
        return parent::one($db);
    }
}
