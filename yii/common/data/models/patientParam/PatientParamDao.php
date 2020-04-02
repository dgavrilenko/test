<?php

namespace ddmp\common\data\models\patientParam;

use ddmp\common\data\models\BaseDao;
use ddmp\common\data\models\patient\PatientDao;
use ddmp\common\enums\PatientParamEnum;
use ddmp\common\external\medlinesoft\api\params\MedlinePatient;
use ddmp\common\external\mpc\api\params\MpcPatient;
use ddmp\common\utils\formatters\ArrayFormat;

/**
 * This is the model class for table "patient_param".
 *
 * @property string     $patient_id  Id пациента
 * @property string     $name        Название параметра
 * @property string     $value       Значение параметра
 * @property string     $create_time Время создания записи
 * @property string     $update_time Время обновления записи
 * @property string     $value_text
 *
 * @property PatientDao $patient
 */
class PatientParamDao extends BaseDao
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'patient_param';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['patient_id', 'name'], 'required'],
			[['patient_id'], 'integer'],
			[['create_time', 'update_time'], 'safe'],
			[['value_text'], 'string'],
			[['name', 'value'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'patient_id'  => 'Id пациента',
			'name'        => 'Название параметра',
			'value'       => 'Значение параметра',
			'create_time' => 'Время создания записи',
			'update_time' => 'Время обновления записи',
			'value_text'  => 'Value Text',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPatient()
	{
		return $this->hasOne(PatientDao::className(), ['id' => 'patient_id']);
	}

	/**
	 * @inheritdoc
	 * @return PatientParamDaoQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return PatientParamDaoQuery::build(static::class);
	}

	/**
	 * Возвращает поле value с приведением к типу @see ArrayFormat
	 *
	 * @return ArrayFormat
	 */
	public function getArrayValue()
	{
		return new ArrayFormat($this->value);
	}

	/**
	 * Преобразует к модели @see MedlinePatient
	 *
	 * @return MedlinePatient
	 */
	public function asMedlineParams()
	{
		$unserialized = json_decode($this->value_text, true);
		$params = new MedlinePatient();
		$params->load($unserialized);

		return $params;
	}

	/**
	 * Преобразует к модели @see MpcPatient
	 *
	 * @return MpcPatient
	 */
	public function asMpcParams(): MpcPatient
	{
		$unserialized = json_decode($this->value_text, true);
		$params = new MpcPatient();
		$params->load($unserialized);

		return $params;
	}

	/**
	 * Заполняет модель из профиля пациента в ммт
	 *
	 * @param PatientDao $patient
	 */
	public function loadMmtProfileParams($patient, ?string $phoneReplacement = null)
	{
		$this->patient_id = $patient->id;
		$this->name = PatientParamEnum::MMT_PATIENT_PROFILE;
		$this->value_text = $patient->asMmtAddUserParams($phoneReplacement)->toJson();
	}

	/**
	 * @param PatientDao $patient
	 * @param int        $medcardId
	 */
	public function loadMmtMedcardParams($patient, $medcardId)
	{
		$this->patient_id = $patient->id;
		$this->name       = PatientParamEnum::MMT_PATIENT_MEDCARD_ID;
		$this->value      = (string) $medcardId;
	}
}