<?php

namespace ddmp\common\data\models\promoCode;

use ddmp\common\data\models\BaseDaoQuery;
use ddmp\common\data\models\product\ProductDao;
use ddmp\common\enums\PromoCodeStatusEnum;

/**
 * This is the ActiveQuery class for [[PromoCodeDao]].
 *
 * @see PromoCodeDao
 */
class PromoCodeDaoQuery extends BaseDaoQuery
{
	/**
	 * @inheritdoc
	 * @return PromoCodeDao[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return PromoCodeDao|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}

	/**
	 * Выборка по коду и статусу
	 *
	 * @param string   $code
	 * @param int|null $status
	 *
	 * @return $this
	 */
	public function byCode(string $code, ?int $status = PromoCodeStatusEnum::ACTIVE): PromoCodeDaoQuery
	{
		return $this
			->andWhere(['code' => $code])
			->inStatus($status);
	}

	/**
	 * @param string   $code
	 * @param int|null $status
	 *
	 * @return PromoCodeDao|null
	 */
	public function oneByCode(string $code, ?int $status = PromoCodeStatusEnum::ACTIVE): ?PromoCodeDao
	{
		return $this->byCode($code, $status)->one();
	}

	/**
	 * @param $bitrixId
	 *
	 * @return PromoCodeDaoQuery
	 */
	public function byBitrixId(int $bitrixId): self
	{
		return $this
			->andWhere(['bitrix_id' => $bitrixId]);
	}

	/**
	 * @param int $bitrixId
	 *
	 * @return PromoCodeDao|null
	 */
	public function oneByBitrixId(int $bitrixId): ?PromoCodeDao
	{
		return $this->byBitrixId($bitrixId)->one();
	}

	/**
	 * @param int $id
	 *
	 * @return PromoCodeDao|null
	 */
	public function oneById(int $id) : ?PromoCodeDao
	{
		return parent::oneById($id);
	}
}
