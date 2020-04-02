<?php

namespace ddmp\common\data\models\product;

use ddmp\common\data\models\BaseDaoQuery;
use ddmp\common\data\models\contract\ContractDao;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[ProductDao]].
 *
 * @see ProductDao
 */
class ProductDaoQuery extends BaseDaoQuery
{
	/**
	 * @inheritdoc
	 * @return ProductDao[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return ProductDao|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}

	/**
	 * @param $id
	 *
	 * @return ProductDao|null
	 */
	public function oneById(int $id): ?ProductDao
	{
		return parent::oneById($id);
	}

	/**
	 * Выборка по id продукта
	 *
	 * @param int|int[] $productId
	 *
	 * @return $this
	 */
	public function byId($productId)
	{
		$query = $this->where(['product.id' => $productId]);

		return $query;
	}

	/**
	 * @param array $productIdList
	 *
	 * @return ProductDaoQuery
	 */
	public function byIdList(array $productIdList): ProductDaoQuery
	{
		$query = $this->where(['product.id' => $productIdList]);

		return $query;
	}

	public function byPatientId($patientId)
	{
		$query = $this
			->innerJoinWith(ContractDao::tableName())
			->andWhere(['contract.patient_id' => $patientId]);

		return $query;
	}

	/**
	 * Выборка продуктов с непустым значением bpm_id (экспортированных в BPM)
	 *
	 * @return $this
	 */
	public function importedToBpm()
	{
		$query = $this->where(['not', ['bpm_id' => null]]);

		return $query;
	}

	/**
	 * Выборка продуктов с пустым значением bpm_id (не экспортированных в BPM)
	 *
	 * @return $this
	 */
	public function notImportedToBpm()
	{
		$query = $this->where(['bpm_id' => null]);

		return $query;
	}


	/**
	 * Выборка по BPM ID
	 *
	 * @param string $bpmId
	 *
	 * @return $this
	 */
	public function byBpmId($bpmId)
	{
		return $this->andWhere(['bpm_id' => $bpmId]);
	}

	/**
	 * @param int $contractId
	 *
	 * @return ProductDaoQuery
	 */
	public function byContractId(int $contractId) {
		return $this
			->leftJoin(ContractDao::tableName() . ' as c', ProductDao::tableName() . '.id=c.product_id')
			->where(['c.id' => $contractId]);
	}

	/**
	 * @return ProductDaoQuery
	 */
	public function withPartnerServices(): ProductDaoQuery
	{
		return $this->with('partnerServices');
	}
}
