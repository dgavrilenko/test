<?php
namespace ddmp\common\data\models\medCard;
use \ddmp\common\data\models\BaseDao;

/**
 * This is the model class for table "medcard_review".
 *
 * @property integer             $id                      Id
 * @property integer             $appointment_id          идентификатор консультации
 * @property integer             $appointment_system_id   системный идентификтор консультации
 * @property string              $patient_name            имя пациента
 * @property string              $date                    дата
 * @property float               $rating                  оценка
 * @property string              $comment                 комментарий
 * @property string              $create_time
 * @property string              $update_time
 *.
 */
class MedCardReviewDao extends BaseDao
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'medcard_review';
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'                    => 'Id',
			'appointment_id'        => 'идентификатор консультации',
			'appointment_system_id' => 'системный идентификтор консультации',
			'patient_name'          => 'имя пациента',
			'date'                  => 'дата',
			'rating'                => 'оценка',
			'comment  '             => 'комментарий',
			'create_time'            => 'Дата создания',
			'update_time'            => 'Дата обновления',
		];
	}

	/**
	 * Возвращает массив полей ассоциированных с атрибутами для сериализации
	 *
	 * @return array
	 */
	public function fields()
	{
		return [
			'id'                    => 'id',
			'appointment_id'        => 'appointment_id',
			'appointment_system_id' => 'appointment_system_id',
			'patient_name'          => 'patient_name',
			'date'                  => 'date',
			'rating'                => 'rating',
			'comment'               => 'comment',
			'create_time'            => 'Дата создания',
			'update_time'            => 'Дата обновления',

		];
	}
}
