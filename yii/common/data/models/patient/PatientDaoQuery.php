<?php

namespace ddmp\common\data\models\patient;

use ddmp\app\integration\sbol\models\chat\SbolChatMessage;
use ddmp\common\data\models\BaseDaoQuery;
use ddmp\common\data\models\contract\ContractDao;
use ddmp\common\data\models\contract\ContractDaoQuery;
use ddmp\common\data\models\importPatient\ImportPatientDao;
use ddmp\common\data\models\patientParam\PatientParamDao;
use ddmp\common\data\models\patientParam\PatientParamDaoQuery;
use ddmp\common\enums\PatientParamEnum;
use ddmp\common\utils\formatters\Phone;

/**
 * This is the ActiveQuery class for [[PatientDao]].
 *
 * @see PatientDao
 */
class PatientDaoQuery extends BaseDaoQuery
{
	/**
	 * @inheritdoc
	 * @return PatientDao[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return PatientDao|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}

	/**
	 * @param $id
	 *
	 * @return PatientDao|null
	 */
	public function oneById(int $id): ?PatientDao
	{
		return parent::oneById($id);
	}

	/**
	 * Выборка по Id
	 *
	 * @param $patientId
	 *
	 * @return $this
	 */
	public function byId($patientId)
	{
		$query = $this->andWhere(['id' => $patientId]);

		return $query;
	}

	/**
	 * Выборка по номеру телефона
	 *
	 * @param $phone
	 *
	 * @return $this
	 */
	public function byPhone($phone)
	{
		$phoneObj = new Phone($phone);
		$query = $this->where(['phone' => $phoneObj->getNumber()]);

		return $query;
	}

	/**
	 * @param $datetime
	 *
	 * @return PatientDaoQuery
	 */
	public function createdAfter($datetime)
	{
		return $this
			->andWhere('`create_time` >= :datetime', ['datetime' => $datetime]);
	}

	/**
	 * Возвращает пациента по телефону
	 *
	 * @param $phone
	 * @param bool $isShouldCreate
	 *
	 * @return array|PatientDao|null
	 * @throws \ddmp\common\exceptions\ValidationException
	 */
	public function oneByPhone($phone, $isShouldCreate = false)
	{
		$patient = $this->byPhone($phone)->one();

		if ($patient === null && $isShouldCreate) {
			$patient = new PatientDao();
			$patient->phone = $phone;
			$patient->trySave();
		}

		return $patient;
	}

	/**
	 * Выборка пациентов не зарегистрированных в api партнёров телемедицины
	 *
	 * @param $partnerId
	 *
	 * @return $this
	 */
	public function needRegisterProfileInPartnerTelemed($partnerId): PatientDaoQuery
	{
		$query = $this
			->andWhere(
				[
					'exists',
					ContractDao::find()
						->where('`contract`.`patient_id` = `patient`.`id`')
						->active()
						->byTelemedPartner($partnerId)
				]
			)
			->andWhere(
				[
					'not exists',
					PatientParamDao::find()
						->where('patient_id = `patient`.`id`')
						->andWhere(
							['name' => PatientParamEnum::getTelemedProfileParam($partnerId)]
						)
				]
			);

		return $query;
	}

	/**
	 * Выборка пациентов у которых нет полисов в телемед api партнёра
	 *
	 * @param $partnerId
	 *
	 * @return $this
	 */
	public function needRegisterProductInPartnerTelemed($partnerId)
	{
		$query = $this
			->innerJoinWith(
				[
					'contracts' => function (ContractDaoQuery $query) use ($partnerId) {
						return $query
							->active()
							->byTelemedPartner($partnerId);
					}
				]
			)
			->andWhere(
				[
					'not exists',
					PatientParamDao::find()
						->where('patient_id = `patient`.`id`')
						->andWhere(
							['name' => PatientParamEnum::getTelemedProductsParam($partnerId)]
						)
				]
			);

		return $query;
	}

	/**
	 * Выборка по импортируемому пациенту
	 *
	 * @param ImportPatientDao $patientToImport
	 *
	 * @return $this
	 */
	public function byImportPatient($patientToImport)
	{
		$this->where(['last_name' => $patientToImport->last_name])
			->andWhere(['first_name' => $patientToImport->first_name])
			->andWhere(['birthday' => $patientToImport->birthday]);

		return $this;
	}

	/**
	 * Выборка с параметрами пациента
	 *
	 * @param string[] $patientParamNames Наименования параметров
	 *
	 * @return $this
	 */
	public function withParams($patientParamNames)
	{
		$this->with(
			[
				'patientParams' => function (PatientParamDaoQuery $query) use ($patientParamNames) {
					$query->where(['name' => $patientParamNames]);

					return $query;
				}
			]
		);

		return $this;
	}

	/*
	 * Выборка пациентов с непустым значением bpm_id (экспортированных в BPM)
	 *
	 * @return $this
	 */
	public function importedToBpm()
	{
		$query = $this->where(['not', ['bpm_id' => null]]);

		return $query;
	}

	/**
	 * Выборка пациентов с пустым значением bpm_id (не экспортированных в BPM)
	 *
	 * @return $this
	 */
	public function notImportedToBpm()
	{
		$query = $this->where(['bpm_id' => null]);

		return $query;
	}

	/**
	 * Выборка по идентификатору в bitrix
	 *
	 * @param $bitrixId
	 *
	 * @return $this
	 */
	public function byBitrixId($bitrixId)
	{
		return $this
			->andWhere(['bitrix_id' => $bitrixId]);
	}

	/**
	 * Пациент
	 *
	 * @param SbolChatMessage $message
	 *
	 * @return array|PatientDao|null
	 */
	public function oneBySbolMessage(SbolChatMessage $message)
	{
		return $this
			->byPhone($message->getSender()->getId())
			->one();
	}

	/**
	 * Выборка пациентов по ФИО + ДР
	 *
	 * @param $lastName
	 * @param $firstName
	 * @param $middleName
	 * @param $birthday
	 *
	 * @return $this
	 */
	public function byFio($lastName, $firstName, $middleName, $birthday)
	{
		$query = $this->andWhere(
			[
				'and',
				['last_name' => $lastName],
				['first_name' => $firstName],
				['birthday' => $birthday],
			]
		);

		if (!empty($middleName)) {
			$query = $query->andWhere(['middle_name' => $middleName]);
		}

		return $query;
	}

	/**
	 * Поиск пациента по ФИО + Д.Р.
	 *
	 * @param string      $lastName
	 * @param string      $firstName
	 * @param string|null $middleName
	 * @param string      $birthday
	 *
	 * @return PatientDao|null
	 */
	public function oneByFio(string $lastName, string $firstName, ?string $middleName, string $birthday): ?PatientDao
	{
		return $this->byFio($lastName, $firstName, $middleName, $birthday)->one();
	}

	/**
	 * @param string      $lastName
	 * @param string      $firstName
	 * @param null|string $middleName
	 * @param string      $birthday
	 *
	 * @return PatientDao[]
	 */
	public function allByFio(string $lastName, string $firstName, ?string $middleName, string $birthday): array
	{
		return $this->byFio($lastName, $firstName, $middleName, $birthday)->all();
	}

	/**
	 * @return PatientDaoQuery
	 */
	public function patientsForSync(): PatientDaoQuery
	{
		return $this->andWhere(['synchronized' => 0]);
	}
}
