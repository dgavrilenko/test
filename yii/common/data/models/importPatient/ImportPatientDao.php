<?php

namespace ddmp\common\data\models\importPatient;

use ddmp\common\data\models\BaseDao;
use ddmp\common\data\models\contract\ContractDao;
use ddmp\common\enums\ImportPatientStatusEnum;
use ddmp\common\extend\yii\validators\DateValidator;
use ddmp\common\utils\helpers\CheckEmptyModelTrait;

/**
 * This is the model class for table "import_patient".
 *
 * @property string        $id          Id
 * @property string        $first_name  Имя пациента
 * @property string        $last_name   Фамилия пациента
 * @property string        $middle_name Отчество пациента
 * @property string        $phone       Номер телефона
 * @property string        $email       Электронная почта
 * @property string        $birthday    Дата рождения
 * @property int           $gender      Пол
 * @property int           $status      Статус импорта
 * @property string        $error_text  Текст ошибки
 * @property string        $create_time Время создания записи
 * @property string        $update_time Время обновления записи
 *
 * @property ContractDao[] $contracts
 */
class ImportPatientDao extends BaseDao
{
	use CheckEmptyModelTrait;

	/**
	 * При загрузке реестров сбера
	 */
	const SCENARIO_SBREG = 'sbreg';

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'import_patient';
	}

	/**
	 * @inheritdoc
	 * @return ImportPatientDaoQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return ImportPatientDaoQuery::build(get_called_class());
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['birthday', 'first_name', 'last_name'], 'required', 'on' => self::SCENARIO_SBREG],
			[['birthday', 'create_time', 'update_time'], 'safe'],
			[['gender', 'status'], 'integer'],
			[['error_text'], 'string'],
			[['first_name', 'last_name', 'middle_name', 'email'], 'string', 'max' => 255],
			[['phone'], 'string', 'max' => 20],
			['phone', 'default', 'value' => null],
			[['birthday'], DateValidator::class],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'          => 'Id',
			'first_name'  => 'Имя пациента',
			'last_name'   => 'Фамилия пациента',
			'middle_name' => 'Отчество пациента',
			'phone'       => 'Номер телефона',
			'email'       => 'Электронная почта',
			'birthday'    => 'Дата рождения',
			'gender'      => 'Пол',
			'status'      => 'Статус импорта',
			'error_text'  => 'Текст ошибки',
			'create_time' => 'Время создания записи',
			'update_time' => 'Время обновления записи',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getContracts()
	{
		return $this->hasMany(ContractDao::className(), ['import_patient_id' => 'id']);
	}

	/**
	 * Устанавливает статус Импортировано
	 */
	public function imported()
	{
		$this->status = ImportPatientStatusEnum::IMPORTED;
	}

	/**
	 * Устанавливает статус Ошибка импорта и записывает текст ошибки
	 *
	 * @param string $text
	 */
	public function importFailed($text)
	{
		$this->status = ImportPatientStatusEnum::FAILED;
		$this->error_text = $text;
	}
}
