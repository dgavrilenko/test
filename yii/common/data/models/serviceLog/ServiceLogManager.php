<?php
namespace ddmp\common\data\models\serviceLog;

use ddmp\common\data\models\contract\ContractDao;
use ddmp\common\enums\ServiceLogStatusEnum;
use ddmp\common\enums\ServiceTypeEnum;

/**
 * Для работы с текущем статусом услуги
 *
 * Class ServiceLogManager
 *
 * @package ddmp\common\data\models\serviceLog
 */
class ServiceLogManager
{
	/**
	 * @param int $patientId
	 * @param int $contractId
	 * @param int $partnerServiceId
	 * @param int $status
	 *
	 * @return ServiceLogDao
	 * @throws \ddmp\common\exceptions\ValidationException
	 */
	public function create(int $patientId, int $contractId, int $partnerServiceId, int $status = ServiceLogStatusEnum::REQUESTED): ServiceLogDao
	{
		$serviceLog = ServiceLogDao::create();
		$serviceLog->patient_id = $patientId;
		$serviceLog->contract_id = $contractId;
		$serviceLog->partner_service_id = $partnerServiceId;
		$serviceLog->status = $status;
		$log->time = date("Y-m-d H:i:s");
		$serviceLog->trySave();
		return $serviceLog;
	}

	/**
	 * @param ContractDao $contract
	 * @param int $partnerServiceTypeId
	 *
	 * @return ServiceLogDao
	 */
	public function setRequestFinished(ContractDao $contract, int $partnerServiceTypeId): ServiceLogDao
	{
		$log = ServiceLogDao::find()
			->byContractId($contract->id)
			->byPartnerServiceType($partnerServiceTypeId)
			->sortUpdatedFirst()
			->one();
		$log->time = date("Y-m-d H:i:s");
		$log->status = ServiceLogStatusEnum::FINISHED;
		$log->save();
		return $log;
	}

	/**
	 * По контракту расшифрованы результаты анализов
	 *
	 * @param ContractDao $contract
	 *
	 * @return ServiceLogDao
	 */
	public function setAnalysesDecoded(ContractDao $contract): ServiceLogDao
	{
		$log = ServiceLogDao::find()
			->byContractId($contract->id)
			->byPartnerServiceType(ServiceTypeEnum::ANALYSES)
			->sortUpdatedFirst()
			->one();
		$log->time = date("Y-m-d H:i:s");
		$log->status = ServiceLogStatusEnum::INACTIVATED;
		$log->save();
		return $log;
	}
}