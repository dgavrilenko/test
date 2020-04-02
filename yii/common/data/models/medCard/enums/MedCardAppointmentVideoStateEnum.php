<?php
namespace ddmp\common\data\models\medCard\enums;

use \ddmp\common\enums\AbstractEnum;

/**
 * Статусы видое сессий внутри консультаций, которые мы храним в базе (унифицированы для всех партнеров)
 *
 * Class MedCardAppointmentVideoSessionStateEnum
 *
 * @package ddmp\common\data\models\medCard\enums
 */
class MedCardAppointmentVideoStateEnum extends AbstractEnum
{
	/**
	 * Ещё не началась
	 */
	const NOT_ACTIVE = 1;

	/**
	 * Активная\в процессе
	 */
	const ACTIVE = 2;

	/**
	 * Завершена
	 */
	const FINISHED = 3;

	/**
	 * Просрочена
	 */
	const CANCELED = 4;

	/**
	 * Отменена
	 */
	const EXPIRED = 5;
}