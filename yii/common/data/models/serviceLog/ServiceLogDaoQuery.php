<?php

namespace ddmp\common\data\models\serviceLog;

use ddmp\common\data\models\BaseDaoQuery;
use ddmp\common\data\models\partnerService\PartnerServiceDao;
use ddmp\common\data\models\request\RequestDao;

/**
 * This is the ActiveQuery class for [[ServiceLogDao]].
 *
 * @see ServiceLogDao
 */
class ServiceLogDaoQuery extends BaseDaoQuery
{
	const TAG_SERVICE_LOG_PREFIX = 'TAG_SERVICE_LOG_';

	/**
	 * Возвращает тег для кеширования
	 *
	 * @param $patientId
	 *
	 * @return string
	 */
	public static function getCacheTag($patientId)
	{
		return self::TAG_SERVICE_LOG_PREFIX . $patientId;
	}

	/**
	 * @inheritdoc
	 * @return ServiceLogDao[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return ServiceLogDao|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}

	/**
	 * @param RequestDao $request
	 *
	 * @return $this
	 */
	public function byRequest($request)
	{
		return $this
			->andWhere(['contract_id' => $request->contract_id])
			->andWhere(['partner_service_id' => $request->partner_service_id])
			->andWhere(['patient_id' => $request->patient_id])
			->andWhere(['time' => $request->create_time]);
	}

	/**
	 * @param int $typeId
	 *
	 * @return ServiceLogDaoQuery
	 */
	public function byPartnerServiceType(int $typeId): ServiceLogDaoQuery
	{
		return $this
			->innerJoin(PartnerServiceDao::tableName(), PartnerServiceDao::tableName().'.id='.ServiceLogDao::tableName().'.partner_service_id')
			->andWhere([PartnerServiceDao::tableName().'.type' => $typeId]);
	}

	/**
	 * @param int $contractId
	 *
	 * @return ServiceLogDaoQuery
	 */
	public function byContractId(int $contractId): ServiceLogDaoQuery
	{
		return $this
			->andWhere(['contract_id' => $contractId]);
	}

	/**
	 * @param int $partnerServiceId
	 *
	 * @return ServiceLogDaoQuery
	 */
	public function byPartnerServiceId(int $partnerServiceId): ServiceLogDaoQuery
	{
		return $this
			->andWhere(['partner_service_id' => $partnerServiceId]);
	}

	/**
	 * @param int $status
	 *
	 * @return ServiceLogDaoQuery
	 */
	public function byStatus(int $status): ServiceLogDaoQuery
	{
		return $this
			->andWhere(['status' => $status]);
	}

	/**
	 * Статус был установлен $daysNum дней назад и более
	 *
	 * @param int $daysNum
	 *
	 * @return ServiceLogDaoQuery
	 */
	public function byDaysAgo(int $daysNum): ServiceLogDaoQuery
	{
		$time = time() - $daysNum*24*60*60;
		return $this
			->andWhere('time <= :time', ['time' => date("Y-m-d H:i:s", $time)]);
	}

	/**
	 * @return ServiceLogDaoQuery
	 */
	public function sortUpdatedFirst(): ServiceLogDaoQuery
	{
		return $this
			->orderBy(['time' => SORT_DESC]);
	}
}
