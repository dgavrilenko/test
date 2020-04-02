<?php

namespace ddmp\common\data\models\patient;

use ddmp\common\enums\PatientStatusEnum;
use ddmp\common\params\system\SystemParams;
use ddmp\common\utils\formatters\Phone;

class PatientManager
{
	const TELEMED_LOGIN_PREFIX = 11;

	const EMAIL_HOST = '#';

	/**
	 * @param PatientDao $patient
	 *
	 * @return string
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\di\NotInstantiableException
	 */
	public function getPasswordOnSalt(PatientDao $patient)
	{
		/** @var SystemParams $systemParams */
		$systemParams = \Yii::$container->get(SystemParams::class);
		return $patient->generatePasswordOnSalt($systemParams->getPasswordSalt());
	}

	/**
	 * @param string $phone
	 * @param null|string $scenario
	 *
	 * @return PatientDao
	 * @throws \ddmp\common\exceptions\ValidationException
	 */
	public function create(string $phone, ?string $scenario = null): PatientDao
	{
		$patient = PatientDao::create();
		if ($scenario) {
			$patient->setScenario($scenario);
		}
		$patient->status = PatientStatusEnum::ACTIVE;
		$patient->phone = $phone;
		return $patient->trySave();
	}

	/**
	 * @param int $id
	 *
	 * @return array|PatientDao|null
	 */
	public function getById(int $id) {
		return PatientDao::find()->byId($id)->one();
	}

	/**
	 * @param string $phone
	 *
	 * @return string
	 */
	public function generateTelemedLogin(string $phone): string
	{
		$numberObject = new Phone($phone);
		return self::TELEMED_LOGIN_PREFIX . $numberObject->getNumber();
	}

	/**
	 * @param string $login
	 *
	 * @return string
	 */
	public function revertLoginToPhone(string $login): string
	{
		$prefix = mb_substr($login, 0, 2);
		if ($prefix == self::TELEMED_LOGIN_PREFIX) {
			$phone = mb_substr($login, 2);
		} else {
			$phone = $login;
		}
		return $phone;
	}

	/**
	 * Генерирует email
	 *
	 * @return string
	 */
	public function generateEmail(string $phone)
	{
		return 'u+' . $phone. self::EMAIL_HOST;
	}
}