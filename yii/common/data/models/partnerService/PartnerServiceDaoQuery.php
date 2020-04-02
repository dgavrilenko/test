<?php

namespace ddmp\common\data\models\partnerService;

use ddmp\common\data\models\BaseDaoQuery;
use ddmp\common\data\models\productService\ProductServiceDao;
use ddmp\common\enums\ServiceTypeEnum;

/**
 * This is the ActiveQuery class for [[PartnerServiceDao]].
 *
 * @see PartnerServiceDao
 */
class PartnerServiceDaoQuery extends BaseDaoQuery
{
	/**
	 * @inheritdoc
	 * @return PartnerServiceDao[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return PartnerServiceDao|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}

	/**
	 * @param int $id
	 *
	 * @return PartnerServiceDao|null
	 */
	public function oneById(int $id): ?PartnerServiceDao
	{
		return $this->byId($id)->one();
	}

	/**
	 * Выборка по коду
	 *
	 * @param string $code
	 *
	 * @return $this
	 */
	public function byCode(string $code) : PartnerServiceDaoQuery
	{
		return $this
			->andWhere(['code' => $code]);
	}

	/**
	 * @param int $partnerId
	 *
	 * @return PartnerServiceDaoQuery
	 */
	public function byPartnerId(int $partnerId) : PartnerServiceDaoQuery
	{
		return $this->andWhere(['partner_id' => $partnerId]);
	}

	/**
	 * @param string $code
	 *
	 * @return PartnerServiceDaoQuery
	 */
	public function byPartnerCode(string $code) : PartnerServiceDaoQuery
	{
		return $this->andWhere(['code' => $code]);
	}

	/**
	 * @param int $type
	 *
	 * @return PartnerServiceDaoQuery
	 */
	public function byType(int $type) : PartnerServiceDaoQuery
	{
		return $this->andWhere(['type' => $type]);
	}

	/**
	 * @return PartnerServiceDaoQuery
	 */
	public function byTypeTelemed() : PartnerServiceDaoQuery
	{
		return $this->byType(ServiceTypeEnum::TELEMED);
	}

	/**
	 * @param int $partnerId
	 * @param int $productId
	 *
	 * @return PartnerServiceDaoQuery
	 */
	public function byPartnerByProductForTelemed(int $partnerId, int $productId) : PartnerServiceDaoQuery
	{
		return $this
			->byTypeTelemed()
			->byPartnerId($partnerId)
			->innerJoin(ProductServiceDao::tableName() . ' as prod_s', $this->getAlias() . '.id=prod_s.partner_service_id')
			->andWhere('prod_s.product_id='.$productId)
			;
	}

	/**
	 * Выборка по идентификатору в bmp
	 *
	 * @param $bpmId
	 *
	 * @return $this
	 */
	public function byBpmId($bpmId)
	{
		return $this
			->andWhere(['bpm_id' => $bpmId]);
	}

	/**
	 * @param int $bitrixId
	 *
	 * @return PartnerServiceDao|null
	 */
	public function oneByBitrixId(int $bitrixId): ?PartnerServiceDao
	{
		return $this
			->andWhere(['bitrix_id' => $bitrixId])
			->one();
	}

	/**
	 * @param string $code
	 *
	 * @return PartnerServiceDao|null
	 */
	public function oneByCode(string $code): ?PartnerServiceDao
	{
		return $this
			->andWhere(['code' => $code])
			->one();
	}
}