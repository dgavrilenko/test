<?php

namespace ddmp\common\data\models\request;
use ddmp\common\data\models\partnerService\PartnerServiceDao;
use ddmp\common\data\models\authToken\AuthTokenDao;
use ddmp\common\data\models\BaseDaoQuery;
use ddmp\common\enums\RequestStatusEnum;

/**
 * This is the ActiveQuery class for [[RequestDao]].
 *
 * @see RequestDao
 */
class RequestDaoQuery extends BaseDaoQuery
{
	/**
	 * @inheritdoc
	 * @return RequestDao[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return RequestDao|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}

	/**
	 * @param $id
	 *
	 * @return RequestDao|null
	 */
	public function oneById(int $id): ?RequestDao
	{
		return $this->byId($id)->one();
	}

	/**
	 * Выборка по id партнёра
	 *
	 * @param $partnerId
	 *
	 * @return $this
	 */
	public function byPartnerId($partnerId)
	{
		$this->andWhere(['partner_id' => $partnerId]);

		return $this;
	}

	/**
	 * Выборка по токену доступа
	 *
	 * @param AuthTokenDao $authToken
	 *
	 * @return $this
	 */
	public function byAuthToken($authToken)
	{
		return $this
			->byId($authToken->row_id)
			->byPartnerId($authToken->user_id);
	}

	/**
	 * Выборка по типу заявки
	 *
	 * @param $partnerServiceId
	 *
	 * @return $this
	 */
	public function byPartnerServiceId($partnerServiceId)
	{
		return $this
			->andWhere(['partner_service_id' => $partnerServiceId]);
	}

	/**
	 * Выборка по пациенту
	 *
	 * @param $patientId
	 *
	 * @return $this
	 */
	public function byPatientId($patientId)
	{
		return $this
			->andWhere(['patient_id' => $patientId]);
	}

	/**
	 * Сортировка по времени изменения
	 * Недавно обновлённые сначала
	 *
	 * @return $this
	 */
	public function sortUpdatedFirst()
	{
		return $this
			->orderBy(['create_time' => SORT_DESC]);
	}

	/**
	 * Выборка по id контракта
	 *
	 * @param $contractId
	 *
	 * @return $this
	 */
	public function byContractId($contractId)
	{
		return $this->where(['contract_id' => $contractId]);
	}

	/**
	 * Имеется ли заявка для контракта
	 *
	 * @param $contractId
	 *
	 * @return bool
	 */
	public function existsByContractId($contractId) : bool
	{
		return $this
			->byContractId($contractId)
			->exists();
	}

	/**
	 * @param $patientId
	 *
	 * @return BaseDaoQuery
	 */
	public function byPatientIdWithFiles($patientId): BaseDaoQuery
	{
		return $this->where(['patient_id' => $patientId])
			->with('filesPatient')
			->with('filesPartner');
	}

	/**
	 * @param integer $patientId
	 * @param integer $type
	 *
	 * @return BaseDaoQuery
	 */
	public function newByPatientIdAndType(int $patientId, int $type): BaseDaoQuery
	{
		return $this
			->innerJoin(PartnerServiceDao::tableName() . ' as t', RequestDao::tableName() . '.partner_service_id=t.id')
			->where(
				[
					'patient_id' => $patientId,
					'status'     => RequestStatusEnum::NEW,
					't.type'     => $type
				]
			);
	}

}
