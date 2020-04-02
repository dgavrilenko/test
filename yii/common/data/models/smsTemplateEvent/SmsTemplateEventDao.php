<?php

namespace ddmp\common\data\models\smsTemplateEvent;

use ddmp\common\data\models\smsTemplate\SmsTemplateDao;
use ddmp\common\models\sms\template\event\SmsTemplateEventDao as BaseSmsTemplateEventDao;
use ddmp\common\models\common\NameCases;
use ddmp\common\base\data\models\AdminDaoInterface;
use ddmp\common\data\models\partner\PartnerDao;

/**
 * Class SmsTemplateEventDao
 * Привязка шаблонов сообщений к типам событий
 *
 * @package ddmp\common\models\sms\template\event
 *
 * @property integer           $id              Id
 * @property string            $event_name      Название события, по которому отправляется сообщение
 * @property integer|null      $entity_id       Идентификатор связанной сущности (если требуется)
 * @property integer           $template_id     Идентификатор шаблона сообщения
 * @property integer           $partner_id      Идентификатор партнера
 */
class SmsTemplateEventDao extends BaseSmsTemplateEventDao
{
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return array_merge(parent::rules() ?: [], [
			[['partner_id'], 'exist', 'targetClass' => PartnerDao::class, 'targetAttribute' => ['partner_id' => 'id'], 'on' => AdminDaoInterface::SCENARIO_ADMIN_EDIT],
			[['template_id'], 'exist', 'targetClass' => SmsTemplateDao::class, 'targetAttribute' => ['template_id' => 'id'], 'on' => AdminDaoInterface::SCENARIO_ADMIN_EDIT],
		]);
	}

	/**
	 * @return NameCases
	 */
	public function getNameCases(): NameCases
	{
		$nameCases = parent::getNameCases();
		$nameCases->setNominative('События sms-шаблонов');
		$nameCases->setNominativePlural('События sms-шаблонов');
		$nameCases->setGenitivePlural('События sms-шаблонов');
		$nameCases->setAccusative('События sms-шаблонов');

		return $nameCases;
	}

	/**
	 * @return bool
	 */
	public function isAdministrating(): bool
	{
		return true;
	}
}