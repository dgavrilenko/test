<?php
namespace ddmp\common\data\models\serviceHint;

use ddmp\common\data\models\contract\ContractDao;
use ddmp\common\data\models\partnerService\PartnerServiceDao;
use ddmp\common\data\models\product\ProductDao;

/**
 * Точка доступа для получения подсказок к услугам (текст под кнопокой получения услуги)
 *
 * Class ServiceHintManager
 *
 * @package ddmp\common\data\models\serviceHint
 */
class ServiceHintManager
{
	private const PRODUCT_HINT_MAP = [
		ProductDao::PRODUCT_TELE_CHECKUP => TelecheckupServiceHint::class
	];

	/**
	 * @param null|PartnerServiceDao $partnerService
	 * @param ContractDao $contract
	 *
	 * @return string
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\di\NotInstantiableException
	 */
	public function getHintText(?PartnerServiceDao $partnerService, ContractDao $contract): string
	{
		if (!$partnerService || !$contract) {
			return '';
		}
		$hintClassName = self::PRODUCT_HINT_MAP[$contract->product_id] ?? null;
		if (!$hintClassName) {
			return '';
		}
		/** @var BaseServiceHint $hintClass */
		$hintClass = \Yii::$container->get($hintClassName);
		$hintClass->setPartnerService($partnerService);
		$hintClass->setContract($contract);
		return $hintClass->getHintText();
	}
}