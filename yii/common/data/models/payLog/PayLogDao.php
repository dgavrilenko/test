<?php

namespace ddmp\common\data\models\payLog;

use ddmp\common\data\models\BaseDao;
use ddmp\common\data\models\contract\ContractDao;
use ddmp\common\data\models\patient\PatientDao;

/**
 * This is the model class for table "pay_log".
 *
 * @property int         $id          Идентификатор оплаты
 * @property string      $contract_id Идентификатор оплачиваемого контракта
 * @property string      $patient_id  Идентификатор пациента
 * @property string      $sum         Сумма оплаты
 * @property int         $pay_system  Система, используемая для оплаты
 * @property string      $invoice     Номер счёта на стороне системы оплаты
 * @property string      $create_time Время создания записи
 * @property string      $update_time Время изменения записи
 *
 * @property ContractDao $contract
 * @property PatientDao  $patient
 */
class PayLogDao extends BaseDao
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'pay_log';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['contract_id', 'patient_id', 'sum', 'pay_system'], 'required'],
			[['contract_id', 'patient_id', 'pay_system'], 'integer'],
			[['sum'], 'number'],
			[['create_time', 'update_time'], 'safe'],
			[['invoice'], 'string', 'max' => 255],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'          => 'Идентификатор оплаты',
			'contract_id' => 'Идентификатор оплачиваемого контракта',
			'patient_id'  => 'Идентификатор пациента',
			'sum'         => 'Сумма оплаты',
			'pay_system'  => 'Система, используемая для оплаты',
			'invoice'     => 'Номер счёта на стороне системы оплаты',
			'create_time' => 'Время создания записи',
			'update_time' => 'Время изменения записи',
		];
	}

	/**
	 * @inheritdoc
	 * @return PayLogDaoQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new PayLogDaoQuery(get_called_class());
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getContract()
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
}