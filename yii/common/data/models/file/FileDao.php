<?php

namespace ddmp\common\data\models\file;

use ddmp\common\base\models\storage\FileIdInterface;
use ddmp\common\data\models\BaseDao;
use ddmp\common\models\storage\FileIdBuilder;
use ddmp\common\utils\helpers\Url;

/**
 * This is the model class for table "file".
 *
 * @property string  $id          Идентификатор файла
 * @property string  $name        Имя файла
 * @property string  $type        Тип файла
 * @property integer $size        Размер файла
 * @property string  $target      Назначение файла, например Заявка
 * @property string  $row_id      Идентификатор записи, к которой относится файл
 * @property int     $status      Статус файла
 * @property int     $patient_id  Идентификатор пациента, которому предназначен файл
 * @property string  $create_time Время создания записи
 * @property string  $update_time Время обновления записи
 */
class FileDao extends BaseDao
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'file';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['name'], 'required'],
			[['status', 'patient_id'], 'integer'],
			[['name', 'target', 'row_id'], 'string', 'max' => 255],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'          => 'Идентификатор файла',
			'name'        => 'Имя файла',
			'type'        => 'Тип файла',
			'size'        => 'Размер файла',
			'target'      => 'Назначение файла, например Заявка',
			'row_id'      => 'Идентификатор записи, к которой относится файл',
			'status'      => 'Статус файла',
			'patient_id'  => 'Идентификатор пациента, которому предназначен файл',
			'create_time' => 'Время создания записи',
			'update_time' => 'Время обновления записи',
		];
	}

	/**
	 * @inheritdoc
	 * @return FileDaoQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return FileDaoQuery::build();
	}

	/**
	 * Генерирует идентификатор файла для хранилищ
	 *
	 * @return FileIdInterface
	 */
	public function generateFileId()
	{
		return new FileIdBuilder(
			$this->id,
			$this->target,
			$this->name,
			$this->row_id,
			$this->patient_id
		);
	}

	/**
	 * @param null|string $token
	 *
	 * @return string
	 */
	public function getPath(?string $token = null): string
	{
		return Url::toFileDownload($this->id, $token, $this->row_id);
	}

	/**
	 * @param null|string $token
	 *
	 * @return string
	 */
	public function getPathAbsolute(?string $token): string
	{
		return \Yii::$app->request->getHostInfo() . $this->getPath($token);
	}
}