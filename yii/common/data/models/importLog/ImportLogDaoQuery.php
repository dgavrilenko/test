<?php

namespace ddmp\common\data\models\importLog;

use ddmp\common\data\models\BaseDaoQuery;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[ImportLogDao]].
 *
 * @see ImportLogDao
 */
class ImportLogDaoQuery extends BaseDaoQuery
{
	/**
	 * @inheritdoc
	 * @return ImportLogDao[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return ImportLogDao|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}

	/**
	 * Выборка по времени транзакции
	 *
	 * @param string $transactionTime
	 *
	 * @return ActiveQuery
	 */
	public function byTransactionTime(string $transactionTime) : ActiveQuery
	{
		return $this->andWhere(['transaction_time' => $transactionTime]);
	}

	/**
	 * Все записи на указанное время транзакции
	 *
	 * @param string $transactionTime
	 *
	 * @return ImportLogDao[]
	 */
	public function allByTransactionTime(string $transactionTime) : array
	{
		return $this->byTransactionTime($transactionTime)->all();
	}
}
