<?php
namespace ddmp\common\data\models\medCard;

use ddmp\common\data\models\BaseDaoQuery;
use ddmp\common\data\models\medCard\enums\MedCardAppointmentStateEnum;
use ddmp\common\enums\MedCardAppointmentSourceTypeEnum;
use ddmp\common\utils\formatters\DateTimeFormat;

/**
 * This is the ActiveQuery class for [[MedCardAppointmentDao]].
 *
 * @see MedCardAppointmentDao
 */
class MedCardAppointmentDaoQuery extends BaseDaoQuery
{
	/**
	 * @inheritdoc
	 * @return MedCardAppointmentDao[]
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return MedCardAppointmentDao|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}

	/**
	 * Выборка по консультаций с врачами
	 *
	 * @param int $patientId
	 *
	 * @return $this
	 */
	public function byPatientIdWithDoctors($patientId): BaseDaoQuery
	{
		return $this->byPatient((int)$patientId)->with('doctor');
	}

	/**
	 * Выборка по пациенту
	 *
	 * @param int $patientId
	 * @return MedCardAppointmentDaoQuery
	 */
	public function byPatient(int $patientId): MedCardAppointmentDaoQuery
	{
		return $this->andWhere(['patient_id' => $patientId, 'is_hidden' => false]);
	}

	/**
	 * @param int $patientId
	 * @param int $productId
	 *
	 * @return MedCardAppointmentDaoQuery
	 */
	public function byPatientbyProduct(int $patientId, int $productId): MedCardAppointmentDaoQuery
	{
		return $this->andWhere(['patient_id' => $patientId, 'product_id' => $productId, 'is_hidden' => false]);
	}

    /**
     * Выборка консультаций по статусу
     *
     * @param int $state
     * @return MedCardAppointmentDaoQuery
     */
	public function byState(int $state): MedCardAppointmentDaoQuery
	{
		return $this->andWhere([$this->getAlias() .'.state' => $state]);
	}

    /**
     * Выборка консультаций по продукту
     *
     * @param int $productId
     * @return MedCardAppointmentDaoQuery
     */
	public function byProduct(int $productId): MedCardAppointmentDaoQuery
	{
		return $this->andWhere([$this->getAlias() .'.product_id' => $productId]);
	}

	/**
	 * @param int $partnerId
	 *
	 * @return MedCardAppointmentDaoQuery
	 */
	public function byPartnerId(int $partnerId): MedCardAppointmentDaoQuery
	{
		return $this->andWhere([$this->getAlias() .'.partner_id' => $partnerId]);
	}

	/**
	 * @param string $dateBeforeStr
	 *
	 * @return MedCardAppointmentDaoQuery
	 */
	public function createdBefore(string $dateBeforeStr): MedCardAppointmentDaoQuery
	{
		return $this->andWhere('create_time <= :dateBefore', ['dateBefore' => $dateBeforeStr]);
	}

	/**
	 * @param string $dateAfterStr
	 *
	 * @return MedCardAppointmentDaoQuery
	 */
	public function updatedAfter(string $dateAfterStr): MedCardAppointmentDaoQuery
	{
		return $this->andWhere('update_time <= :dateAfter', ['dateAfter' => $dateAfterStr]);
	}


	/**
	 * @return MedCardAppointmentDaoQuery
	 */
	public function byActiveState(): self
	{
		return $this->andWhere(['between', 'state', MedCardAppointmentStateEnum::STATE_SCHEDULED, MedCardAppointmentStateEnum::STATE_QUEUED_CHILD]);
	}

	/**
	 * @param int    $partnerId
	 * @param int    $patientId
	 * @param string $externalId
	 *
	 * @return MedCardAppointmentDaoQuery
	 */
	public function byPartnerIdAndPatientIdAndExternalId(int $partnerId, int $patientId, string $externalId): MedCardAppointmentDaoQuery
	{
		return $this
			->andWhere(['partner_id' => $partnerId])
			->andWhere(['patient_id' => $patientId])
			->andWhere(['system_id' => $externalId]);
	}

	/**
	 * @param int                $partnerId
	 * @param \DateInterval|null $intervalBeforeNow
	 * @param int|null           $excludingPatientId
	 *
	 * @return MedCardAppointmentDaoQuery
	 */
	public function queuedByPartnerId(int $partnerId, ?\DateInterval $intervalBeforeNow = null, ?int $excludingPatientId = null): self
	{
		if ($intervalBeforeNow !== null) {
			$now = DateTimeFormat::now();
			$dateBefore = DateTimeFormat::parse(DateTimeFormat::now()->getDateTime()->sub($intervalBeforeNow));
			$this->andWhere(['between', 'partner_create_time', $dateBefore->mySqlTime(), $now->mySqlTime()]);
		}

		$this
			->andWhere(['partner_id' => $partnerId])
			->byActiveState()
			->andWhere(['source_type' => MedCardAppointmentSourceTypeEnum::QUEUE]);

		if ($excludingPatientId !== null) {
			$this->andWhere(['!=', 'patient_id', $excludingPatientId]);
		}

		return $this;
	}

	/**
	 * @param int                $partnerId
	 * @param \DateInterval|null $intervalBeforeNow
	 *
	 * @return MedCardAppointmentDaoQuery
	 */
	public function avgQueueWaitingTimeByPartnerId(int $partnerId, ?\DateInterval $intervalBeforeNow = null): self
	{
		if ($intervalBeforeNow !== null) {
			$now = DateTimeFormat::now();
			$dateBefore = DateTimeFormat::parse(DateTimeFormat::now()->getDateTime()->sub($intervalBeforeNow));
			$this->andWhere(['between', 'partner_create_time', $dateBefore->mySqlTime(), $now->mySqlTime()]);
		}

		$this
			->andWhere('`started_time` IS NOT NULL')
			->andWhere(['partner_id' => $partnerId])
			->andWhere(['source_type' => MedCardAppointmentSourceTypeEnum::QUEUE])
		;

		return $this->select('AVG(TIME_TO_SEC(TIMEDIFF(`started_time`, `partner_create_time`)))');
	}

	/**
	 * @param int                $partnerId
	 * @param \DateInterval|null $intervalBeforeNow
	 *
	 * @return MedCardAppointmentDaoQuery
	 */
	public function countStartedByPartnerId(int $partnerId, ?\DateInterval $intervalBeforeNow = null): self
	{
		if ($intervalBeforeNow !== null) {
			$now = DateTimeFormat::now();
			$dateBefore = DateTimeFormat::parse(DateTimeFormat::now()->getDateTime()->sub($intervalBeforeNow));
			$this->andWhere(['between', 'started_time', $dateBefore->mySqlTime(), $now->mySqlTime()]);
		}

		$this
			->andWhere('`partner_create_time` IS NOT NULL')
			->andWhere(['partner_id' => $partnerId])
			->andWhere(['source_type' => MedCardAppointmentSourceTypeEnum::QUEUE])
		;

		return $this->select('COUNT(*)');
	}
}