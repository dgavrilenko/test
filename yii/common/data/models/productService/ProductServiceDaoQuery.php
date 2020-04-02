<?php

namespace ddmp\common\data\models\productService;

use ddmp\common\data\models\BaseDaoQuery;
use ddmp\common\data\models\partnerService\PartnerServiceDao;

/**
 * This is the ActiveQuery class for [[ProductServiceDao]].
 *
 * @see ProductServiceDao
 */
class ProductServiceDaoQuery extends BaseDaoQuery
{
	/**
	 * @inheritdoc
	 * @return ProductServiceDao[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return ProductServiceDao|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}

	/**
	 * Выборка по id продукта
	 *
	 * @param int   $productId
	 *
	 * @return $this
	 */
	public function byProductId($productId)
	{
		$query = $this->andWhere(["{$this->getAlias()}.product_id" => $productId]);

		return $query;
	}

	/**
	 * @param $partnerServiceId
	 *
	 * @return ProductServiceDaoQuery
	 */
	public function byPartnerServiceId($partnerServiceId): ProductServiceDaoQuery
	{
		return $this->andWhere(["{$this->getAlias()}.partner_service_id" => $partnerServiceId]);
	}

	/**
	 * @param int $typeId
	 *
	 * @return ProductServiceDaoQuery
	 */
	public function byPartnerServiceType(int $typeId): ProductServiceDaoQuery
	{
		return $this
			->innerJoin(PartnerServiceDao::tableName(), PartnerServiceDao::tableName().'.id='.ProductServiceDao::tableName().'.partner_service_id')
			->andWhere([PartnerServiceDao::tableName().'.type' => $typeId]);
	}

	/**
	 * Выборка по унику
	 *
	 * @param int $productId
	 * @param int $partnerServiceId
	 *
	 * @return $this
	 */
	public function byPrimaryKey($productId, $partnerServiceId)
	{
		$query = $this
			->andWhere([$this->getAlias() . '.product_id' => $productId])
			->andWhere([$this->getAlias() . '.partner_service_id' => $partnerServiceId])
		;

		return $query;
	}

	/**
	 * @param int $productId
	 * @param int $partnerId
	 *
	 * @return BaseDaoQuery
	 */
	public function byProductIdAndPartnerId(int $productId, int $partnerId): BaseDaoQuery
	{
		$query = $this
			->innerJoin(PartnerServiceDao::tableName(), $this->getAlias() . '.partner_service_id = ' . PartnerServiceDao::tableName() . '.id')
			->andWhere([$this->getAlias() . '.product_id' => $productId])
			->andWhere([PartnerServiceDao::tableName() . '.partner_id' => $partnerId]);

		return $query;
	}
}

