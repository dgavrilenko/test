<?php

namespace ddmp\common\data\models\externalPatient;

use ddmp\common\data\models\BaseDao;

class ExternalPatientManager extends BaseDao
{
	/**
	 * @param int $patientId
	 * @param int $partnerPatientId
	 * @param $partnerId
	 *
	 * @return array|ExternalPatientDao|null
	 * @throws \ddmp\common\exceptions\ValidationException
	 */
	public function upsert(int $patientId, int $partnerPatientId, $partnerId)
	{
		$record = ExternalPatientDao::find()
			->byPartnerId($partnerId)
			->byPatientId($patientId)
			->one();
		if (!$record) {
			$record = new ExternalPatientDao();
			$record->patient_id = $patientId;
			$record->partner_id = $partnerId;
		}
		$record->external_patient_id = (string)$partnerPatientId;
		return $record->save();
	}

	/**
	 * @param ExternalPatientDao $externalPatient
	 * @param string $newExternalId
	 *
	 * @return bool
	 */
	public function updateExternalPatientId(ExternalPatientDao $externalPatient, string $newExternalId) : bool
	{
		$externalPatient->external_patient_id = $newExternalId;
		return $externalPatient->save();
	}
}
