<?php
namespace ddmp\common\data\models\medCard;

use ddmp\common\data\models\BaseDao;
use ddmp\common\external\medlinesoft\api\models\MedlineAppointment;

/**
 * This is the model class for table "medcard_doctor".
 *
 * @property integer               $id                    Id
 * @property string                $system_id             Cистемный идентификатор доктора
 * @property string                $first_name
 * @property string                $last_name
 * @property string                $middle_name
 * @property string                $is_active
 * @property string                $phone
 * @property string                $email
 * @property integer               $sex                   Пол
 * @property string                $photo_url             url фотографии
 * @property string                $birthday              Дата рождения
 * @property integer               $is_anonimous          Анонимный ли пользователь
 * @property boolean               $is_adult_on           Обслуживает ли врач взрослых
 * @property boolean               $is_child_on           Обслуживает ли врач детей
 * @property boolean               $is_animal_on          Обслуживает ли врач животных
 * @property boolean               $is_duty               Дежурный ли врач
 * @property string                $profession_name       Название основной специальности
 * @property string                $degree_name           Учёная степень
 * @property string                $comment               Общее описание
 * @property float                 $rating                Рейтинг врача
 * @property integer               $review_count          Количество отзывов
 * @property string                $free_slot_datetime
 * @property string                $code
 * @property integer               $last_enter_time    Дата ближайшей доступной для записи ячейки расписания
 * @property string                $specialties           Список специальностей врача
 * @property array                 $response_log          Лог ответов - сущность DoctorShortPublicView
 * @property string                $partner_create_time   Время создания в системе партнера
 * @property string                $partner_update_time   Время последнего изменения в системе партнера
 * @property string                $create_time           Дата создания
 * @property string                $update_time           Дата обновления
 * @property integer               $partner_id            Идентификатор партнера
 * @property MedlineAppointment[]  $appointments          Связанные консультации
 * */
class MedCardDoctorDao extends BaseDao
{
	/**
	 * Список параметров, которые мы требуем от всех партнеров
	 * Они необходимы для отрисовки мед.карты
	 * По факту, некоторые партнеры ещё не передавают все обязательные параметры
	 */
	const REQUIRED_PARAMS = [
		'first_name',
		'last_name',
		'middle_name',
		'photo_url',
		'profession_name',
		'degree_name',
		'comment',
		'rating',
		'review_count',
		'free_slot_datetime'
	];
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'medcard_doctor';
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'                    => 'Id',
			'partner_id'            => 'Идентификатор партнера',
			'system_id'             => 'Cистемный идентификатор доктора',
			'first_name'            => 'Имя',
			'last_name'             => 'Фамилия',
			'middle_name'           => 'Отчество',
			'is_active'             => 'Активен ли профиль в системе партнера',
			'phone'                 => 'Телефон',
			'email'                 => 'Email',
			'sex'                   => 'Пол',
			'photo_url'             => 'url фотографии',
			'birthday'              => 'Дата рождения',
			'is_anonimous'          => 'Анонимный ли пользователь',
			'is_adult_on'           => 'Обслуживает лм врач взрослых',
			'is_child_on'           => 'Обслуживает ли врач детей',
			'is_animal_on'          => 'Обслуживает ли врач животных',
			'is_duty'               => 'Дежурный ли врач',
			'profession_name'       => 'Название основной специальности',
			'degree_name'           => 'Учёная степень',
			'comment'               => 'Общее описание',
			'rating'                => 'Рейтинг врача',
			'review_count'          => 'Количество отзывов',
			'free_slot_datetime'    => 'Дата ближайшей доступной для записи ячейки расписания',
			'code'                  => 'Код врача в системе партнера',
			'last_enter_time'       => 'Время последнего входа в систему',
			'response_log'          => 'Лог ответов - сущность DoctorShortPublicView',
			'partner_create_time'   => 'Время создания в системе партнера',
			'partner_update_time'   => 'Время последнего изменения в системе партнера',
			'create_time'           => 'Дата создания',
			'update_time'           => 'Дата обновления',
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
			'system_id'             => 'system_id',
			'first_name'            => 'first_name',
			'last_name'             => 'last_name',
			'middle_name'           => 'middle_name',
			'is_active'             => 'is_active',
			'phone'                 => 'phone',
			'email'                 => 'email',
			'sex'                   => 'sex',
			'photo_url'             => 'photo_url',
			'birthday'              => 'birthday',
			'is_anonimous'          => 'is_anonimous',
			'is_adult_on'           => 'is_adult_on',
			'is_child_on'           => 'is_child_on',
			'is_animal_on'          => 'is_animal_on',
			'is_duty'               => 'is_duty',
			'profession_name'       => 'profession_name',
			'degree_name'           => 'degree_name',
			'comment'               => 'comment',
			'rating'                => 'rating',
			'review_count'          => 'review_count',
			'free_slot_datetime'    => 'free_slot_datetime',
			'code'                  => 'code',
			'last_enter_time'       => 'last_enter_time',
			'response_log'          => 'response_log',
			'partner_create_time'   => 'partner_create_time',
			'partner_update_time'   => 'partner_update_time',
			'create_time'           => 'create_time',
			'update_time'           => 'update_time',
		];
	}

	/**
	 * Метод получения связанных данных из medcard_appointment
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getAppointments()
	{
		// same as above
		return $this->hasMany(MedCardAppointmentDao::class, ['id' => 'doctor_id']);
	}
}
