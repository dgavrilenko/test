<?php
namespace ddmp\common\data\models\medCard;

use ddmp\common\data\models\BaseDao;
use ddmp\common\external\medlinesoft\api\models\MedlineAppointment;

/**
 * This is the model class for table "medcard_clinic".
 *
 * @property integer               $id                    Id
 * @property string                $system_id             Cистемный идентификатор клиники
 * @property string                $partner_id
 * @property string                $name
 * @property string                $description
 * @property string                $photo_url
 * @property string                $site
 * @property string                $city
 * @property string                $address
 * @property string                $phone
 * @property string                $partner_create_time   Время создания в системе партнера
 * @property string                $partner_update_time   Время последнего изменения в системе партнера
 * @property string                $create_time           Дата создания
 * @property string                $update_time           Дата обновления
 * @property MedlineAppointment[]  $appointments          Связанные консультации
 * */
class MedCardClinicDao extends BaseDao
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'medcard_clinic';
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'                    => 'Id',
			'partner_id'            => 'id партнера',
			'name'                  => 'Название клиники',
			'description'           => 'Описание клиники',
			'photo_url'             => 'Url изображения',
			'site'                  => 'Адрес сайта клиники',
			'city'                  => 'Город клиник',
			'address'               => 'Адрес клиники',
			'phone'                 => 'Телефон клиники',
			'partner_create_time'   => 'Время создания в системе партнера',
			'partner_update_time'   => 'Время обновления в системе партнера',
			'create_time'           => 'Время создания',
			'update_time'           => 'Время обновления',
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
			'id'                    => 'Id',
			'partner_id'            => 'partner_id',
			'name'                  => 'name',
			'description'           => 'description',
			'photo_url'             => 'photo_url',
			'site'                  => 'site',
			'city'                  => 'city',
			'address'               => 'address',
			'phone'                 => 'phone',
			'partner_create_time'   => 'partner_create_time',
			'partner_update_time'   => 'partner_update_time',
			'create_time'           => 'create_time',
			'update_time'           => 'update_time',
		];
	}
}