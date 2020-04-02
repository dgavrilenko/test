<?php

namespace ddmp\common\data\models\promoCode;

use ddmp\common\base\utils\generators\PromoCodeGeneratorInterface;
use ddmp\common\enums\PromoCodeStatusEnum;

/**
 * Class PromoCodeManager
 *
 * @package ddmp\common\data\models\promoCode
 */
class PromoCodeManager
{
	private const DEFAULT_USE_MAX_COUNT = 1;

	/**
	 * Пытается создать n промокодов. Возвращает числоо m - сколько промокодов удалось создать
	 *
	 * @param int                         $n
	 * @param int                         $productId
	 * @param int                         $partnerId
	 * @param int                         $useMaxCount
	 * @param string|null                 $endDate
	 * @param int|null                    $productPeriodDays
	 * @param PromoCodeGeneratorInterface $generator
	 *
	 * @return int
	 */
	public function generateNCodes(
		int $n,
		int $productId,
		int $partnerId,
		int $useMaxCount = self::DEFAULT_USE_MAX_COUNT,
		?string $endDate = null,
		?int $productPeriodDays = null,
		PromoCodeGeneratorInterface $generator
	): int
	{
		$createdNum = 0;
		$attemptsNum = 0;
		do {
			$attemptsNum++;
			try {
				$this->createOneTimeCode($productId, $partnerId, $useMaxCount, $endDate, $productPeriodDays, $generator);
			} catch (\Exception $e) {
				continue;
			}
			$createdNum++;
		} while ($createdNum < $n && $attemptsNum < $n * 10);

		return $createdNum;
	}

	/**
	 * @param int                         $productId
	 * @param int                         $partnerId
	 * @param int                         $useMaxCount
	 * @param null|string                 $endDate
	 * @param int|null                    $productPeriodDays
	 * @param PromoCodeGeneratorInterface $generator
	 */
	public function createOneTimeCode(
		int $productId,
		int $partnerId,
		int $useMaxCount = self::DEFAULT_USE_MAX_COUNT,
		?string $endDate = null,
		?int $productPeriodDays = null,
		PromoCodeGeneratorInterface $generator
	): void
	{
		$code = new PromoCodeDao();
		$code->product_id = $productId;
		$code->contract_partner_id = $partnerId;
		$code->code = $generator->generate();
		$code->use_count_max = $useMaxCount;
		$code->product_period_days = $productPeriodDays;
		$code->status = PromoCodeStatusEnum::ACTIVE;
		$code->end_date = $endDate;

		$code->trySave();
	}
}