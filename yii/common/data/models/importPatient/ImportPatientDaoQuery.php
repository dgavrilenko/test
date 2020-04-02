<?php

namespace ddmp\common\data\models\importPatient;

use ddmp\common\data\models\BaseDaoQuery;
use ddmp\common\data\models\patient\PatientDao;
use ddmp\common\enums\ImportPatientStatusEnum;

/**
 * This is the ActiveQuery class for [[ImportPatientDao]].
 *
 * @see ImportPatientDao
 */
class ImportPatientDaoQuery extends BaseDaoQuery
{

	/**
	 * @inheritdoc
	 * @return ImportPatientDao[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

    /**
	 * @inheritdoc
	 * @return ImportPatientDao|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}

	/**
	 * Выборка не импортированных пациентов
	 *
	 * @return $this
	 */
	public function notImported()
	{
		$query = $this->andWhere(
			[
				'status' => ImportPatientStatusEnum::NOT_IMPORTED
			]
		);

		return $query;
	}

	/**
	 * @param PatientDao $patient
	 *
	 * @return $this
	 */
	public function byPatientFio(PatientDao $patient)
	{
		$query = $this->andWhere(
			[
				'and',
				['last_name' => $patient->last_name],
				['first_name' => $patient->first_name],
				['birthday' => $patient->birthday],
			]
		);

		return $query;
	}

	/**
	 * Возвращает импорт пациента по фио пациента
	 *
	 * @param PatientDao $patient
	 *
	 * @return array|ImportPatientDao|null
	 */
	public function oneByPatientFio(PatientDao $patient)
	{
		return $this->byPatientFio($patient)->one();
	}

	/**
	 * Выборка по пациенту
	 * Связка: фамилия, имя, дата рождения
	 *
	 * @param PatientDao $patient
	 *
	 * @return $this
	 */
	public function byPatient($patient)
	{
		if ($patient->isEmpty(PatientDao::SCENARIO_VALIDATE_EMPTY)) {
			$this->where(['phone' => $patient->phone]);
		} else {
			$this->where(
				[
					'or',
					['phone' => $patient->phone],
					[
						'and',
						['last_name' => $patient->last_name],
						['first_name' => $patient->first_name],
						['birthday' => $patient->birthday],
					]
				]
			);
		}

		return $this;
	}
}
