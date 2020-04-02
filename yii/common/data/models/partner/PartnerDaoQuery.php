<?php

namespace ddmp\common\data\models\partner;

use ddmp\common\data\models\BaseDaoQuery;

/**
 * This is the ActiveQuery class for [[PartnerDao]].
 *
 * @see PartnerDao
 */
class PartnerDaoQuery extends BaseDaoQuery
{
	/**
	 * @inheritdoc
	 * @return PartnerDao[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return PartnerDao|null
	 */
	public function one($db = null): ?PartnerDao
	{
		return parent::one($db);
	}

	/**
	 * @inheritdoc
	 * @return PartnerDao|null
	 */
	public function oneById(int $id): ?PartnerDao
	{
		return parent::oneById($id);
	}

	/**
	 * Выборка партнеров с непустым значением bpm_id (экспортированных в BPM)
	 *
	 * @return $this
	 */
	public function importedToBpm()
	{
		$query = $this->where(['not', ['bpm_id' => null]]);

		return $query;
	}

	/**
	 * Выборка партнеров с пустым значением bpm_id (не экспортированных в BPM)
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
}
