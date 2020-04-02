<?php
namespace ddmp\common\data\models\serviceHint;

use ddmp\common\data\models\contract\ContractDao;
use ddmp\common\data\models\partnerService\PartnerServiceDao;

/**
 * Базовый класс для генерации подсказок к услугам (мелкий текст под кнопкой получения услуги)
 *
 * Class BaseServiceHint
 *
 * @package ddmp\common\data\models\serviceHint
 */
abstract class BaseServiceHint
{
	/** @var PartnerServiceDao */
	protected $partnerService;

	/** @var ContractDao */
	protected $contract;

	/**
	 * @return string
	 */
	abstract function getHintText(): string;

	/**
	 * @param PartnerServiceDao $partnerService
	 */
	public function setPartnerService(PartnerServiceDao $partnerService): void
	{
		$this->partnerService = $partnerService;
	}

	/**
	 * @param ContractDao $contract
	 */
	public function setContract(ContractDao $contract): void
	{
		$this->contract = $contract;
	}
}