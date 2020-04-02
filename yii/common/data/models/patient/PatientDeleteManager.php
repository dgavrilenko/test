<?php

namespace ddmp\common\data\models\patient;

use ddmp\common\commands\delete\PatientDeleteHandler;
use ddmp\common\data\models\chat\ChatDao;
use ddmp\common\data\models\contract\ContractDao;
use ddmp\common\data\models\emailLog\EmailLogDao;
use ddmp\common\data\models\externalPatient\ExternalPatientDao;
use ddmp\common\data\models\file\FileDao;
use ddmp\common\data\models\medCard\MedCardAppointmentDao;
use ddmp\common\data\models\partnerRegisterLog\PartnerRegisterLogDao;
use ddmp\common\data\models\patientParam\PatientParamDao;
use ddmp\common\data\models\payLog\PayLogDao;
use ddmp\common\data\models\request\RequestDao;
use ddmp\common\data\models\serviceLog\ServiceLogDao;
use ddmp\common\data\models\sms\SmsLogDao;
use ddmp\common\exceptions\DomainException;
use ddmp\common\models\event\DeleteEventModel;
use ddmp\common\models\shortLink\ShortLinkDao;

/**
 * Class PatientDeleteManager
 * Для удаления пациентов в нашей системе и в системах партнеров
 *
 * @package ddmp\common\data\models\patient
 */
class PatientDeleteManager {

	/**
	 * @var PatientDeleteHandler
	 */
	private $patientDeleteHandler;

	/**
	 * PatientDeleteManager constructor.
	 *
	 * @param PatientDeleteHandler $patientDeleteHandler
	 */
	public function __construct(PatientDeleteHandler $patientDeleteHandler)
	{
		$this->patientDeleteHandler = $patientDeleteHandler;
	}

	/**
	 * @param PatientDao $patient
	 *
	 * @return DeleteEventModel|null
	 * @throws \Throwable
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\db\Exception
	 * @throws \yii\di\NotInstantiableException
	 */
	public function deletePatient(PatientDao $patient) : ?DeleteEventModel
	{
		$delModel = $this->deleteInPartnersSystems($patient);
		$this->deleteInOurSystem($patient);
		return $delModel;
	}


	/**
	 * @param PatientDao $patient
	 *
	 * @return DeleteEventModel
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\di\NotInstantiableException
	 */
	public function deleteInPartnersSystems(PatientDao $patient) : DeleteEventModel
	{
		/** @var DeleteEventModel $deleteEventModel */
		$deleteEventModel = \Yii::$container->get(DeleteEventModel::class);
		$deleteEventModel->setPatient($patient);
		return $this->patientDeleteHandler->handleEvent($deleteEventModel);
	}


	/**
	 * @param PatientDao $patient
	 *
	 * @return bool
	 * @throws \Throwable
	 * @throws \yii\db\Exception
	 */
	protected function deleteInOurSystem(PatientDao $patient) : bool
	{

		try {
			$transaction = \Yii::$app->db->beginTransaction();

			$condition = ['patient_id' => $patient->id];
			ShortLinkDao::deleteAll($condition);
			PatientParamDao::deleteAll($condition);
			SmsLogDao::deleteAll($condition);
			PartnerRegisterLogDao::deleteAll($condition);
			PayLogDao::deleteAll($condition);
			RequestDao::deleteAll($condition);
			ServiceLogDao::deleteAll($condition);
			ChatDao::deleteAll($condition);
			MedCardAppointmentDao::deleteAll($condition);
			ExternalPatientDao::deleteAll($condition);
			EmailLogDao::deleteAll($condition);
			FileDao::deleteAll($condition);
			ContractDao::deleteAll($condition);

			$delCount = $patient->delete();
			if (1 != $delCount) {
				throw new DomainException(DomainException::ERROR_INTERNAL_ERROR, "Вместо удаления одного пациента попытка удалить {$delCount} пациентов");
			}
			$transaction->commit();
			\Yii::info("Удален пациент {$patient->phone}");
			return true;
		} catch (\Exception $e) {
			$transaction->rollBack();
			throw $e;
		}
	}
}