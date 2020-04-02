<?php

namespace ddmp\common\data\models\serviceLog;

use ddmp\common\data\models\BaseDao;
use ddmp\common\data\models\contract\ContractDao;
use ddmp\common\data\models\contract\ContractDaoQuery;
use ddmp\common\data\models\partnerService\PartnerServiceDao;
use ddmp\common\data\models\patient\PatientDao;
use ddmp\common\data\models\request\RequestDao;
use ddmp\common\enums\ServiceLogStatusEnum;
use yii\caching\TagDependency;

/**
 * This is the model class for table "service_log".
 *
 * @property int               $id                  Идентификатор
 * @property string            $patient_id          Идентификатор пациента
 * @property string            $contract_id         Идентификатор контракта
 * @property string            $partner_service_id  Идентификатор услуги партнёра
 * @property string            $time                Время использования услуги
 * @property int               $status              Статус оказания услуги
 *
 * @property ContractDao       $contract
 * @property PatientDao        $patient
 * @property PartnerServiceDao $partnerService
 */
class ServiceLogDao extends BaseDao
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'service_log';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['patient_id', 'contract_id', 'partner_service_id'], 'required'],
			[['patient_id', 'contract_id', 'partner_service_id', 'status'], 'integer'],
			[['time'], 'safe']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'                 => 'Идентификатор',
			'patient_id'         => 'Идентификатор пациента',
			'contract_id'        => 'Идентификатор контракта',
			'partner_service_id' => 'Идентификатор услуги партнёра',
			'time'               => 'Время использования услуги',
			'status'             => 'Статус оказания услуги',
		];
	}

	/**
	 * @inheritdoc
	 * @return ServiceLogDaoQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return ServiceLogDaoQuery::build(static::class);
	}

	public function afterSave($insert, $changedAttributes)
	{
		parent::afterSave($insert, $changedAttributes);

		TagDependency::invalidate(\Yii::$app->cache, ServiceLogDaoQuery::getCacheTag($this->patient_id));
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getContract(): ContractDaoQuery
	{
		return $this->hasOne(ContractDao::class, ['id' => 'contract_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPatient()
	{
		return $this->hasOne(PatientDao::class, ['id' => 'patient_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPartnerService()
	{
		return $this->hasOne(PartnerServiceDao::class, ['id' => 'partner_service_id']);
	}

	/**
	 * @param RequestDao $request
	 */
	public function loadFromRequest($request)
	{
		$this->contract_id = $request->contract_id;
		$this->partner_service_id = $request->partner_service_id;
		$this->patient_id = $request->patient_id;
		$this->time = $request->create_time;
	}

	/**
	 * Услуга запрошена
	 */
	public function setRequested()
	{
		$this->status = ServiceLogStatusEnum::REQUESTED;
	}

	/**
	 * Услуга не была запрошена
	 */
	public function setNotRequested()
	{
		$this->status = ServiceLogStatusEnum::NOT_REQUESTED;
	}

	/**
	 * Услуга была успешно оказана
	 */
	public function setFinished()
	{
		$this->status = ServiceLogStatusEnum::FINISHED;
	}

	/**
	 * Что-то пошло не так
	 */
	public function setInactivated()
	{
		$this->status = ServiceLogStatusEnum::INACTIVATED;
	}


}