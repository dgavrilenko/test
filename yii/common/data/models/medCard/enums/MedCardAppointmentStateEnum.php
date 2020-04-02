<?php
namespace ddmp\common\data\models\medCard\enums;

use \ddmp\common\enums\AbstractEnum;

/**
 * Статусы консультаций, которые мы храним в базе (унифицированы для всех партнеров)
 *
 * Class MedCardAppointmentStateEnum
 *
 * @package ddmp\common\data\models\medCard\enums
 */
class MedCardAppointmentStateEnum extends AbstractEnum
{
	/** @var int
	 * Запланирована, ошидается
	 */
	const STATE_SCHEDULED = 1;

	/** @var int
	 * В процессе в данный момент
	 */
	const STATE_PROGRESS = 4;

	/** @var int
	 * Завершена
	 */
	const STATE_FINISHED = 5;

	/** @var int
	 * Отменена. Не важно\не известно кем
	 */
	const STATE_CANCELLED = 6;

	/** @var int
	 * Просрочена. Не была проведена
	 */
	const STATE_EXPIRED = 7;

	/** @var int  */
	const STATE_QUEUED_ADULT = 2;

	/** @var int  */
	const STATE_QUEUED_CHILD = 3;

	/** @var int
	 * Отменена пациентом
	 */
	const STATE_CANCELLED_PATIENT = 8;

	/** @var int
	 * Отменена врачем
	 */
	const STATE_CANCELLED_DOCTOR = 9;
}