<?php

namespace ddmp\common\data\models\contract;

use ddmp\common\data\models\BaseDaoQuery;
use ddmp\common\data\models\partnerService\PartnerServiceDao;
use ddmp\common\data\models\patient\PatientDao;
use ddmp\common\data\models\product\ProductDao;
use ddmp\common\data\models\product\ProductDaoQuery;
use ddmp\common\data\models\productService\ProductServiceDao;
use ddmp\common\enums\ContractStatusEnum;
use ddmp\common\enums\PayStatus;
use ddmp\common\enums\ServiceTypeEnum;
use yii\db\Expression;

/**
 * This is the ActiveQuery class for [[ContractDao]].
 *
 * @see ContractDao
 */
class ContractDaoQuery extends BaseDaoQuery
{
	/**
	 * Возвращает тег кеша
	 *
	 * @param $patientId
	 *
	 * @return string
	 */
	public static function getCacheTag($patientId)
	{
		return "TAG_PATIENT_CONTRACTS_{$patientId}";
	}

	/**
	 * @inheritdoc
	 * @return ContractDao[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return ContractDao|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}

	/**
	 * Выборка по ID
	 *
	 * @param int $id
	 *
	 * @return $this
	 */
	public function byId($id)
	{
		return $this
			->andWhere(['id' => $id]);
	}

	/**
	 * Выборка контрактов, по которым необходимо отправить приветственное сообщение
	 *
	 * @param int $product
	 *
	 * @return $this
	 */
	public function needSmsForProduct($product)
	{
		$query = $this
			->andWhere(
				[
					'contract.is_sms_sent' => 0,
					'contract.product_id'  => $product
				]
			)
			->active();

		return $query;
	}

	/**
	 * Выборка активных по статусу контрактов
	 *
	 * @return $this
	 */
	public function active()
	{
		$query = $this->andWhere([$this->getAlias() . '.status' => ContractStatusEnum::ACTIVE]);

		return $query;
	}

	/**
	 * Выборка активных контрактов на текущий момент
	 *
	 * @return $this
	 */
	public function activeNow()
	{
		$query = $this->andWhere(['contract.status' => ContractDao::ACTIVE_STATUSES]);

		return $query;
	}

	/**
	 * Подгрузить пациентов
	 *
	 * @return $this
	 */
	public function withPatient()
	{
		$query = $this
			->innerJoinWith(PatientDao::tableName(), true);

		return $query;
	}

	/**
	 * Выборка по id пациента
	 *
	 * @param integer $patientId
	 *
	 * @return $this
	 */
	public function byPatientId($patientId)
	{
		$query = $this->andWhere(["{$this->getAlias()}.patient_id" => $patientId]);

		return $query;
	}

	/**
	 * @param $patientId
	 *
	 * @return ContractDaoQuery
	 */
	public function activeByPatientId($patientId)
	{
		$query = $this->andWhere(["{$this->getAlias()}.patient_id" => $patientId])->active();

		return $query;
	}

	/**
	 * @param $patientId
	 *
	 * @return ContractDaoQuery
	 */
	public function activeAndCancelledByPatientId($patientId)
	{
		$query = $this->andWhere(["{$this->getAlias()}.patient_id" => $patientId])
			->andWhere([$this->getAlias() . '.status' => [ContractStatusEnum::ACTIVE, ContractStatusEnum::CANCELLED]]);

		return $query;
	}

	/**
	 * Выборка по id партнёра
	 *
	 * @param $partnerId
	 *
	 * @return $this
	 */
	public function byPartnerId($partnerId) : ContractDaoQuery
	{
		$query = $this->andWhere(["{$this->getAlias()}.partner_id" => $partnerId]);

		return $query;
	}

	/**
	 * @param int $patientId
	 * @param int $servicePartnerId
	 *
	 * @return ContractDaoQuery
	 */
	public function byPatientIdAndServicePartnerId(int $patientId, int $servicePartnerId): ContractDaoQuery
	{
		return $this->alias('c')
			->leftJoin(ProductServiceDao::tableName().' as prodS', 'prodS.product_id=c.product_id')
			->leftJoin(PartnerServiceDao::tableName().' as partS', 'prodS.partner_service_id=partS.id')
			->andWhere('c.patient_id=:patientId', ['patientId' => $patientId])
			->andWhere('partS.partner_id=:partnerId', ['partnerId' => $servicePartnerId])
			;
	}

	/**
	 * Выборка по идентификатору импорт пациента
	 *
	 * @param $importPatientId
	 *
	 * @return $this
	 */
	public function byImportPatientId($importPatientId) : ContractDaoQuery
	{
		return $this->andWhere(['import_patient_id' => $importPatientId]);
	}

	/**
	 * Выборка по id продукта
	 *
	 * @param int $productId
	 *
	 * @return $this
	 */
	public function byProductId($productId) : ContractDaoQuery
	{
		$query = $this->andWhere(['product_id' => $productId]);

		return $query;
	}

	/**
	 * @param int[] $productIdList
	 *
	 * @return ContractDaoQuery
	 */
	public function byProductIdList(array $productIdList): ContractDaoQuery
	{
		return $this->andWhere(['product_id' => $productIdList]);
	}

	public function withProduct() : ContractDaoQuery
	{
		$query = $this->innerJoinWith(ProductDao::tableName());

		return $query;
	}

	/**
	 * Order by сначала активные
	 *
	 * @return $this
	 */
	public function activeFirst() : ContractDaoQuery
	{
		$statusesOrder =
			[
				ContractStatusEnum::ACTIVE,
				ContractStatusEnum::CANCELLED,
				ContractStatusEnum::INACTIVE,
				ContractStatusEnum::DUPLICATE,
				ContractStatusEnum::FAMILY_CANCELLED
			];
		$statusesOrderImploded = implode(',', $statusesOrder);
		$query = $this->addOrderBy(
			[
				new Expression("FIELD ({$this->getAlias()}.status, $statusesOrderImploded)")
			]
		);

		return $query;
	}

	/**
	 * Выборка наиболее актуальных контрактов пациента по продукту
	 *
	 * @param string $patientId
	 *
	 * @return $this
	 */
	public function firstActual($patientId)
	{
		$query = $this
			->byPatientId($patientId)
			->andWhere(
				[
					'=',
					"{$this->getAlias()}.id",
					ContractDao::find()
						->alias('c2')
						->andWhere('c2.product_id = contract.product_id')
						->andWhere('c2.patient_id = contract.patient_id')
						->activeFirst()
						->limit(1)
						->select('c2.id')
				]
			)
			->activeFirst()
			->addOrderBy(['id' => SORT_DESC]);

		return $query;
	}

	/**
	 * Выборка контрактов с непустым значением bpm_id (экспортированных в BPM)
	 *
	 * @return $this
	 */
	public function importedToBpm()
	{
		$query = $this->where(['not', ['bpm_id' => null]]);

		return $query;
	}

	/**
	 * Выборка контрактов с пустым значением bpm_id (не экспортированных в BPM)
	 *
	 * @return $this
	 */
	public function notImportedToBpm()
	{
		$query = $this->where(['bpm_id' => null]);

		return $query;
	}

	/**
	 * Выборка по идентификатору заказа в битрикс
	 *
	 * @param int $bitrixId
	 *
	 * @return $this
	 */
	public function byBitrixId($bitrixId)
	{
		return $this
			->andWhere(['bitrix_id' => $bitrixId]);
	}

	/**
	 * Получение контракта по идентификатору заказа в битрикс
	 *
	 * @param $bitrixId
	 *
	 * @return array|ContractDao|null
	 */
	public function oneByBitrixId($bitrixId)
	{
		return $this->byBitrixId($bitrixId)->one();
	}

	/**
	 * Выборка контрактов, ожидающих оплату
	 *
	 * @return $this
	 */
	public function toPay()
	{
		return $this
			->active()
			->andWhere(
				[
					'or',
					['pay_status' => null],
					['pay_status' => PayStatus::PAID],
				]
			)
			->andWhere('DATE(`next_pay`) <= CURDATE()')
			->andWhere('(`end_date` IS NULL) OR (`end_date` > NOW())');
	}

	/**
	 * Выборка контрактов, у которых
	 * не прошла оплата
	 *
	 * @return $this
	 */
	public function unpaid()
	{
		return $this
			->active()
			->andWhere(['pay_status' => PayStatus::FAILED])
			->andWhere('DATE(next_pay) < DATE(NOW())');
	}

	/**
	 * Выборка контрактов с окончившимся сроком действия
	 *
	 * @return $this
	 */
	public function expired()
	{
		/*
		 * Активные, срок действия до текущей даты
		 */
		return $this
			->activeNow()
			->andWhere('`end_date` < CURDATE()');
	}

	/**
	 * Запрос всех контрактов с активными услугами партнёра
	 *
	 * @param int $patientId
	 * @param int $partnerId
	 * @param int $serviceType
	 *
	 * @return $this
	 */
	public function activeContractsOfServicePartner($patientId, $partnerId, $serviceType = null): self
	{
		$query = ContractDao::find()
			->byPatientId($patientId)
			->activeNow()
			->innerJoin(
				ProductServiceDao::tableName(),
				'`contract`.`product_id` = `product_service`.`product_id`'
			)
			->innerJoin(
				PartnerServiceDao::tableName(),
				'`product_service`.`partner_service_id` = `partner_service`.`id`'
			)
			->andWhere("`partner_service`.`partner_id`= :partnerId", [':partnerId' => $partnerId]);

		if ($serviceType !== null) {
			$query = $query
				->andWhere("`partner_service`.`type`= :serviceType", [':serviceType' => $serviceType]);
		}

		return $query;
	}

	/**
	 * Выборка активных контрактов с партнёром телемедицины
	 *
	 * @param $partnerId
	 *
	 * @return $this
	 */
	public function byTelemedPartner($partnerId)
	{
		return $this
			->innerJoin(
				ProductDao::tableName(),
				"`product`.`id`=`product_id`"
			)
			->innerJoin(
				ProductServiceDao::tableName(),
				"`product_service`.`product_id`=`product`.`id`"
			)
			->innerJoin(
				PartnerServiceDao::tableName(),
				'`partner_service`.`id`=`product_service`.`partner_service_id` AND `partner_service`.`type`=:type AND `partner_service`.`partner_id` = :partnerId',
				[
					':type'      => ServiceTypeEnum::TELEMED,
					':partnerId' => $partnerId
				]
			);
	}

	/**
	 * Выборка дубликатов контрактов
	 *
	 * @return $this
	 */
	public function duplicates()
	{
		return $this
			->alias('c')
			->active()
			->andWhere(
				[
					'exists',
					ContractDao::find()
						->alias('cc')
						->active()
						->andWhere('cc.id != c.id')
						->andWhere('cc.patient_id = c.patient_id')
						->andWhere('cc.product_id = c.product_id')
				]
			);
	}

	/**
	 * Все дубликаты контрактов
	 *
	 * @return ContractDao[]
	 */
	public function allDuplicates(): array
	{
		return $this->duplicates()->all();
	}

	/**
	 * Выборка активный контрактов пациента по телемед пациенту и продукту
	 *
	 * @param int $patientId
	 * @param int $productId
	 *
	 * @return ContractDaoQuery
	 */
	public function activeByPatientByProduct(int $patientId, int $productId) : ContractDaoQuery
	{
		return $this
			->byProductId($productId)
			->byPatientId($patientId)
			->activeNow();
	}

	/**
	 * Выборка активный контрактов пациента по телемед пациенту и продукту
	 *
	 * @param int $patientId
	 * @param int $productId
	 *
	 * @return ContractDaoQuery
	 */
	public function inActiveByPatientByProduct(int $patientId, int $productId) : ContractDaoQuery
	{
		return $this
			->byProductId($productId)
			->byPatientId($patientId)
			->andWhere(['contract.status' => ContractStatusEnum::FAMILY_CANCELLED]);
	}

	/**
	 * Выборка контракта по продукту и фэмлирелэйшн
	 *
	 * @param int $patientId
	 * @param int $productId
	 *
	 * @return ContractDaoQuery
	 */
	public function ByProducByRelationt(int $productId, int $familyRelation) : ContractDaoQuery
	{
		return $this
			->byProductId($productId)
			->andWhere(['relation_id' => $familyRelation]);
	}


	/**
	 * @param int $patientId
	 * @param array $productIdList
	 *
	 * @return null|ContractDaoQuery
	 */
	public function activeByPatientByOneOfProducts(int $patientId, array $productIdList) : ?ContractDaoQuery
	{
		if (empty($productIdList)) {
			return null;
		}
		return $this
			->andWhere('product_id IN ('.implode(',', $productIdList).')')
			->byPatientId($patientId)
			->activeNow();
	}

	/**
	 * Выборка активного контракта пациента на продукт
	 *
	 * @param int $patientId
	 * @param int $partnerId
	 *
	 * @return ContractDaoQuery
	 */
	public function activeByPatientByTelemedPartner(int $patientId, int $partnerId) : ContractDaoQuery
	{
		return $this
			->byTelemedPartner($partnerId)
			->byPatientId($patientId)
			->activeNow();
	}

	/**
	 * Возвращает контракт по пациенту и продукту
	 *
	 * @param int            $patientId
	 * @param int            $productId
	 * @param int            $partnerId
	 * @param array|int|null $inStatus
	 *
	 * @return ContractDao|null
	 */
	public function oneByPatientAndProductAndPartner(
		$patientId,
		$productId,
		$partnerId,
		$inStatus = ContractStatusEnum::ACTIVE
	): ?ContractDao
	{
		return $this
			->byPatientId($patientId)
			->byProductId($productId)
			->byPartnerId($partnerId)
			->inStatus($inStatus)
			->one();
	}

	/**
	 * Возвращает контракт по пациенту и продукту
	 *
	 * @param int            $patientId
	 * @param int            $productId
	 * @param null|int|array $inStatus
	 *
	 * @return array|ContractDao|null
	 */
	public function oneByPatientAndProduct($patientId, $productId, $inStatus = ContractStatusEnum::ACTIVE)
	{
		$query = $this
			->byPatientId($patientId)
			->byProductId($productId);
		if ($inStatus) {
			$query = $query->inStatus($inStatus);
		}
		return $query->one();
	}

	/**
	 * Возвращает контракт по импорт пациенту и продукту
	 *
	 * @param int            $importPatientId
	 * @param int            $productId
	 * @param int            $partnerId
	 * @param array|int|null $inStatus
	 *
	 * @return ContractDao|null
	 */
	public function oneByImportPatientAndProductAndPartner(
		$importPatientId,
		$productId,
		$partnerId,
		$inStatus = ContractStatusEnum::ACTIVE
	) : ?ContractDao
	{
		return $this
			->byProductId($productId)
			->byImportPatientId($importPatientId)
			->byPartnerId($partnerId)
			->inStatus($inStatus)
			->one();
	}

	/**
	 * @param int $patientId
	 *
	 * @return $this
	 */
	public function cacheByPatientId(int $patientId): ContractDaoQuery
	{
		return $this
			->cache(3600, self::getTagDependency(self::getCacheTag($patientId)));
	}

	/**
	 * Первый контракт пациента по внешнему идентификатору услугу у партнёра
	 *
	 * @param int    $patientId
	 * @param int    $partnerId
	 * @param string $externalPartnerServiceId
	 *
	 * @return $this
	 */
	public function byPartnerExternalServiceId(int $patientId, int $partnerId, string $externalPartnerServiceId): ContractDaoQuery
	{
		return $this
			->activeContractsOfServicePartner($patientId, $partnerId, ServiceTypeEnum::TELEMED)
			->andWhere(['partner_service.external_id' => $externalPartnerServiceId]);
	}

	/**
	 * @param int    $patientId
	 * @param int    $partnerId
	 * @param string $externalPartnerServiceId
	 *
	 * @return ContractDao|null
	 */
	public function oneByExternalPartnerServiceId(int $patientId, int $partnerId, string $externalPartnerServiceId): ?ContractDao
	{
		return $this->byPartnerExternalServiceId($patientId, $partnerId, $externalPartnerServiceId)->one();
	}

	/**
	 * @param int $relationId
	 *
	 * @return ContractDaoQuery
	 */
	public function byRelationId(int $relationId): ContractDaoQuery
	{
		return $this->where(['relation_id' => $relationId]);
	}

	/**
	 * @param int $relationId
	 * @param int $productId
	 *
	 * @return ContractDaoQuery
	 */
	public function byRelationIdByProductIdByStatus(int $relationId, int $productId, int $status): ContractDaoQuery
	{
		return $this->where(['relation_id' => $relationId, 'product_id' => $productId, 'status' => $status]);
	}

	/*
	 * @return ContractDaoQuery
	 */
	public function withProductAndServices(): ContractDaoQuery
	{
		return $this
			->with([
				'product' => function (ProductDaoQuery $productDaoQuery) {
					return $productDaoQuery->withPartnerServices();
				}
			]);
	}
}