<?php

namespace ddmp\common\data\models\patientParam;

use ddmp\common\data\models\partner\PartnerDao;
use ddmp\common\data\models\partner\PartnerManager;
use ddmp\common\enums\PatientParamEnum;
use ddmp\common\utils\formatters\ArrayFormat;
use http\Exception\InvalidArgumentException;

/**
 * Class PatientParamManager
 *
 * @package ddmp\common\data\models\patientParam
 */
class PatientParamManager
{
	/** @var PartnerManager  */
	private $partnerManager;

	public function __construct(PartnerManager $partnerManager)
	{
		$this->partnerManager = $partnerManager;
	}

	/**
	 * @param int $patientId
	 * @param string $name
	 * @param string $value
	 * @param null $valueText
	 *
	 * @return bool
	 */
	public function upsert(int $patientId, string $name, ?string $value, ?string $valueText = null): PatientParamDao
	{
		$param = PatientParamDao::find()->byPatientIdAndName($patientId, $name)->one();
		if (!$param) {
			$param = new PatientParamDao();
			$param->patient_id = $patientId;
			$param->name = $name;
		}
		if ($value) {
			$param->value = $value;
		}
		if ($valueText) {
			$param->value_text = $valueText;
		}
		$param->trySave();
		return $param;
	}

	/**
	 * Регистрируем факт привязки продукта к пациенту
	 *
	 * @param int $patientId
	 * @param int $partnerId
	 * @param string $productCode
	 *
	 * @return bool
	 */
	public function registerPatientProductRelation(int $patientId, int $partnerId, string $productCode) : bool
	{

		$patientParam = $this->getProductRelationsRecord($patientId, $partnerId);
		if (!$patientParam) {
			$patientParam = $this->getNewRecord();
			$patientParam->patient_id = $patientId;
			$patientParam->name = $this->getProductRelationKey($partnerId);
		}
		$products = ArrayFormat::parse($patientParam->value);
		if (!$products->contains($productCode)) {
			$products->addValue($productCode);
		}
		$patientParam->value = $products->getString();

		return $patientParam->save();
	}

	/**
	 * @return PatientParamDao
	 */
	protected function getNewRecord() : PatientParamDao
	{
		return new PatientParamDao();
	}

	/**
	 * Привязан ли продукт к пациенту?
	 *
	 * @param int $patientId
	 * @param int $partnerId
	 * @param string $productCode
	 *
	 * @return bool
	 */
	public function isRegisterdPatientProductRelation(int $patientId, int $partnerId, string $productCode) : bool
	{
		$record = $this->getProductRelationsRecord($patientId, $partnerId);
		if (!$record) {
			return false;
		}
		$productCodeList = ArrayFormat::parse($record->value);
		return in_array($productCode, $productCodeList->getArray()) ? true : false;
	}

	/**
	 * @param int $patientId
	 * @param int $partnerId
	 *
	 * @return array
	 */
	public function getRegisterdPatientProductRelations(int $patientId, int $partnerId): array
	{
		$record = $this->getProductRelationsRecord($patientId, $partnerId);
		if (!$record) {
			return [];
		}
		return ArrayFormat::parse($record->value)->getArray();
	}

	/**
	 * @param int $patientId
	 * @param int $partnerId
	 *
	 * @return PatientParamDao|null
	 */
	protected function getProductRelationsRecord(int $patientId, int $partnerId) : ?PatientParamDao
	{
		return PatientParamDao::find()->byPatientIdAndName(
			$patientId,
			$this->getProductRelationKey($partnerId)
		)->one();
	}

	/**
	 * @param int $patientId
	 * @param int $partnerId
	 * @param array $patientData
	 *
	 * @return bool
	 */
	public function saveProfileInParams(int $patientId, int $partnerId, array $patientData): PatientParamDao
	{
		return $this->upsert(
			$patientId,
			$this->getPatientProfileKey($partnerId),
			null,
			ArrayFormat::parse($patientData)->getString()
		);
	}

	/**
	 * @param int $patientId
	 * @param int $partnerId
	 *
	 * @return PatientParamDao
	 */
	public function getProfileFromParams(int $patientId, int $partnerId): ?PatientParamDao
	{
		return PatientParamDao::find()
			->byPatientIdAndName($patientId, $this->getPatientProfileKey($partnerId))
			->one();
	}

	/**
	 * Обновляет информацию о профиле пациента в patient_param
	 *
	 * @param int $patientId
	 * @param int $partnerId
	 * @param array $newData
	 *
	 * @return PatientParamDao
	 * @throws \ddmp\common\exceptions\ValidationException
	 */
	public function upsertProfileInParams(int $patientId, int $partnerId, array $newData) : PatientParamDao
	{
		if (empty($newData)) {
			throw new InvalidArgumentException('Невозможно добавить пустой массив данных');
		}
		$profileParam = $this->getProfileFromParams($patientId, $partnerId);
		if (!$profileParam) {
			return $this->upsert(
				$patientId,
				$this->getPatientProfileKey($partnerId),
				null,
				ArrayFormat::parse($newData)->getString()
			);
		}
		$existData = ArrayFormat::parse($profileParam->value_text)->getArray();
		foreach ($newData as $key => $val) {
			$existData[$key] = $val;
		}
		$profileParam->value_text = ArrayFormat::parse($existData)->getString();
		$profileParam->trySave();
		return $profileParam;
	}

	/**
	 * @param int $partnerId
	 *
	 * @return string
	 */
	protected function getProductRelationKey(int $partnerId) : string
	{
		$partnerCode = $this->partnerManager->getCodeById($partnerId);
		return $partnerCode ? $partnerCode . PatientParamEnum::PATIENT_PRODUCTS_POSTFIX : null;
	}

	/**
	 * @param int $partnerId
	 *
	 * @return string
	 */
	public function getPatientProfileKey(int $partnerId) : string
	{
		if (PartnerDao::PARTNER_ID_MMT == $partnerId){
			return PatientParamEnum::MMT_PATIENT_PROFILE;
		}
		$partnerCode = $this->partnerManager->getCodeById($partnerId);
		if (PartnerDao::PARTNER_ID_DOCTOR_RYADOM == $partnerId) {
			$partnerCode = 'medline';
		}
		return $partnerCode ? $partnerCode . PatientParamEnum::PATIENT_PROFILE_POSTFIX : null;
	}
}