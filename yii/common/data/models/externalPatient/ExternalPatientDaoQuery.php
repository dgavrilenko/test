<?php

namespace ddmp\common\data\models\externalPatient;

use ddmp\common\data\models\BaseDaoQuery;

/**
 * This is the ActiveQuery class for [[ExternalPatientDao]].
 *
 * @see ExternalPatientDao
 */
class ExternalPatientDaoQuery extends BaseDaoQuery
{
	/**
	 * @inheritdoc
	 * @return ExternalPatientDao[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return ExternalPatientDao|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}
	
	/**
	 * @param int $patientId
	 *
	 * @return ExternalPatientDaoQuery
	 */
	public function byPatientId(int $patientId)
	{
		return $this->andWhere(['patient_id' => $patientId]);
	}

	/**
	 * @param int $partnerId
	 *
	 * @return ExternalPatientDaoQuery
	 */
	public function byPartnerId(int $partnerId)
	{
		return $this->andWhere(['partner_id' => $partnerId]);
	}

	/**
	 * Выборка по внешнему id пациента и партнёру
	 *
	 * @param string $externalPatientId
	 * @param int    $partnerId
	 *
	 * @return $this
	 */
	public function byExternalPatientIdAndPartnerId(string $externalPatientId, int $partnerId)
	{
		return $this
			->andWhere(['external_patient_id' => $externalPatientId])
			->andWhere(['partner_id' => $partnerId]);
	}

	/**
	 * Уникальная запись по внешнему id пациента и партнёру
	 *
	 * @param string $externalPatientId
	 * @param int    $partnerId
	 *
	 * @return ExternalPatientDao|null
	 */
	public function oneByExternalPatientIdAndPartnerId(string $externalPatientId, int $partnerId) : ?ExternalPatientDao
	{
		if (empty($externalPatientId) || empty($partnerId)) {
			return null;
		}

		return $this->byExternalPatientIdAndPartnerId($externalPatientId, $partnerId)->one();
	}
}
