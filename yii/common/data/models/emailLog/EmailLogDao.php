<?php

namespace ddmp\common\data\models\emailLog;

use ddmp\common\data\models\BaseDao;
use ddmp\common\data\models\partner\PartnerDao;
use ddmp\common\data\models\patient\PatientDao;

/**
 * This is the model class for table "email_log".
 *
 * @property string     $id           Id лога
 * @property string     $email_from   Электронная почта отправителя
 * @property string     $email_to     Электронная почта получателя
 * @property string     $email_cc     Электронные почты для копии письма
 * @property string     $subject      Тема письма
 * @property string     $patient_id   Id пациента
 * @property string     $partner_id   Id партнёра, которому ушло письмо
 * @property string     $text         Текст письма
 * @property string     $error_text   Текст ошибки
 * @property int        $status       Статус отправки
 * @property string     $send_time    Время отправки
 * @property int        $resend_count Кол-во попыток отправки
 * @property string     $create_time  Время создания записи
 *
 * @property PartnerDao $partner
 * @property PatientDao $patient
 */
class EmailLogDao extends BaseDao
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'email_log';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['patient_id', 'partner_id', 'status'], 'integer'],
			[['text', 'error_text'], 'string'],
			[['status'], 'required'],
			[['send_time'], 'safe'],
			[['email_from', 'email_to'], 'string', 'max' => 255],
			[['email_cc'], 'string', 'max' => 500]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'         => 'Id лога',
			'email_from' => 'Электронная почта отправителя',
			'email_to'   => 'Электронная почта получателя',
			'email_cc'   => 'Электронные почты для копии письма',
			'patient_id' => 'Id пациента',
			'partner_id' => 'Id партнёра, которому ушло письмо',
			'text'       => 'Текст письма',
			'error_text' => 'Текст ошибки',
			'status'     => 'Статус отправки',
			'send_time'  => 'Время отправки',
		];
	}

	/**
	 * @inheritdoc
	 * @return EmailLogDaoQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return EmailLogDaoQuery::build(static::class);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPartner()
	{
		return $this->hasOne(PartnerDao::class, ['id' => 'partner_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPatient()
	{
		return $this->hasOne(PatientDao::class, ['id' => 'patient_id']);
	}
}