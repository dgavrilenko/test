<?php

namespace ddmp\common\data\models\doctor;

use ddmp\common\data\models\BaseDao;
use Yii;

/**
 * This is the model class for table "doctor_specialization".
 *
 * @property int $id Идентификатор врачебной специализации
 * @property string $name Название врачебной специализации
 * @property string $create_time Время создания записи
 * @property string $update_time Время обновления записи
 *
 */
class DoctorSpecializationDao extends BaseDao
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'doctor_specialization';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['name'], 'required'],
			[['create_time', 'update_time'], 'safe'],
			[['name'], 'string', 'max' => 255],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'Идентификатор врачебной специализации',
			'name' => 'Название врачебной специализации',
			'create_time' => 'Время создания записи',
			'update_time' => 'Время обновления записи',
		];
	}

	/**
	 * @inheritdoc
	 * @return DoctorSpecializationDaoQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return new DoctorSpecializationDaoQuery(get_called_class());
	}
}