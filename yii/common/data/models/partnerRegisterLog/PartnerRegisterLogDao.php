<?php

namespace ddmp\common\data\models\partnerRegisterLog;

use ddmp\common\data\models\BaseDao;

/**
 * This is the model class for table "partner_register_log".
 *
 * @property int    $id               Идентификтор лога
 * @property string $transaction_time Время транзакции
 * @property int    $partner_id       Идентификатор партнёра
 * @property int    $patient_id       Идентификатор пациента
 * @property int    $contract_id      Идентификатор контракта
 * @property int    $status           Статус экспорта
 * @property string $error_text       Текст ошибки
 */
class PartnerRegisterLogDao extends BaseDao
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'partner_register_log';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['transaction_time'], 'safe'],
			[['partner_id', 'patient_id', 'contract_id', 'status'], 'integer'],
			[['error_text'], 'string'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'               => 'Идентификтор лога',
			'transaction_time' => 'Время транзакции',
			'partner_id'       => 'Идентификатор партнёра',
			'patient_id'       => 'Идентификатор пациента',
			'contract_id'      => 'Идентификатор контракта',
			'status'           => 'Статус экспорта',
			'error_text'       => 'Текст ошибки',
		];
	}

	/**
	 * @inheritdoc
	 * @return PartnerRegisterLogDaoQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new PartnerRegisterLogDaoQuery(get_called_class());
	}
}
