<?php

namespace ddmp\common\data\models\patientParam;

use ddmp\common\data\models\BaseDaoQuery;

/**
 * This is the ActiveQuery class for [[PatientParamDao]].
 *
 * @see PatientParamDao
 */
class PatientParamDaoQuery extends BaseDaoQuery
{

	/**
	 * @inheritdoc
	 * @return PatientParamDao[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return PatientParamDao|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}

	/**
	 * Добавляет value_text в список выборки
	 *
	 * @return $this
	 */
	public function selectText()
	{
		$query = $this->addSelect(['value_text']);

		return $query;
	}

	/**
	 * Выборка по пациенту и наименованию параметра
	 *
	 * @param integer $patientId
	 * @param string  $paramName
	 *
	 * @return PatientParamDaoQuery
	 */
	public function byPatientIdAndName($patientId, $paramName)
	{
		$query = $this->andWhere(
			[
				'patient_id' => $patientId,
				'name'       => $paramName
			]
		);

		return $query;
	}
}
