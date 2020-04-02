<?php

namespace ddmp\common\data\models\payLog;

use ddmp\common\data\models\BaseDaoQuery;

/**
 * This is the ActiveQuery class for [[PayLogDao]].
 *
 * @see PayLogDao
 */
class PayLogDaoQuery extends BaseDaoQuery
{
	/**
	 * @inheritdoc
	 * @return PayLogDao[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return PayLogDao|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}

	/**
	 * Выборка оплат по контракту
	 *
	 * @param $contractId
	 *
	 * @return $this
	 */
	public function byContractId($contractId)
	{
		return $this
			->andWhere(['contract_id' => $contractId])
		;
	}

	/**
	 * Выборка оплаты по инвойсу платёжной системы
	 *
	 * @param $paySystem
	 * @param $invoice
	 *
	 * @return $this
	 */
	public function byInvoice($paySystem, $invoice)
	{
		return $this
			->andWhere(['pay_system' => $paySystem])
			->andWhere(['invoice' => $invoice])
		;
	}
}