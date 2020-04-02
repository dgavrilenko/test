<?php

namespace ddmp\common\data\models\externalPatient;

use ddmp\common\data\models\BaseDao;
use ddmp\common\data\models\partner\PartnerDao;
use ddmp\common\data\models\patient\PatientDao;
use ddmp\common\extend\yii\validators\DateTimeValidator;
use ddmp\common\utils\formatters\DateTimeFormat;

/**
 * This is the model class for table "external_patient".
 *
 * @property int        $id                  Идентификатор записи
 * @property string     $external_patient_id Идентификатор пациента у партнёра
 * @property string     $partner_id          Идентификатор партнёра
 * @property string     $patient_id          Идентификатор пациента MP
 * @property string     $create_time         Время создания записи
 * @property string     $update_time         Время обновления записи
 *
 * @property PartnerDao $partner
 * @property PatientDao $patient
 */
class ExternalPatientDao extends BaseDao
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'external_patient';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['external_patient_id', 'partner_id', 'patient_id'], 'required'],
			[['partner_id', 'patient_id'], 'integer'],
			[['create_time', 'update_time'], DateTimeValidator::class],
			[['external_patient_id'], 'string', 'max' => 50],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'                  => 'Идентификатор записи',
			'external_patient_id' => 'Идентификатор пациента у партнёра',
			'partner_id'          => 'Идентификатор партнёра',
			'patient_id'          => 'Идентификатор пациента MP',
			'create_time'         => 'Время создания записи',
			'update_time'         => 'Время обновления записи',
		];
	}

	/**
	 * @inheritdoc
	 * @return ExternalPatientDaoQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return ExternalPatientDaoQuery::build(get_called_class());
	}

	/**
	 * @return int
	 */
	public function getId(): ?int
	{
		return (int)$this->id ?: null;
	}

	/**
	 * @return string
	 */
	public function getExternalPatientId(): ?string
	{
		return (string)$this->external_patient_id ?: null;
	}

	/**
	 * @return int
	 */
	public function getPartnerId(): ?int
	{
		return (int)$this->partner_id ?: null;
	}

	/**
	 * @return int
	 */
	public function getPatientId(): ?int
	{
		return (int)$this->patient_id ?: null;
	}

	/**
	 * @return DateTimeFormat
	 */
	public function getCreateTime(): DateTimeFormat
	{
		return DateTimeFormat::parse($this->create_time);
	}

	/**
	 * @return DateTimeFormat
	 */
	public function getUpdateTime(): DateTimeFormat
	{
		return DateTimeFormat::parse($this->update_time);
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
