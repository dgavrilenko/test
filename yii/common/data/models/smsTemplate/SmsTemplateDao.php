<?php

namespace ddmp\common\data\models\smsTemplate;

use ddmp\common\models\sms\template\SmsTemplateDao as BaseSmsTemplateDao;
use ddmp\common\models\common\NameCases;

/**
 * This is the model class for table "sms_template".
 *
 * @property integer           $id              Id
 * @property string            $template        Сам шаблон сообщения
 *
 **/
class SmsTemplateDao extends BaseSmsTemplateDao
{
	/**
	 * @return NameCases
	 */
	public function getNameCases(): NameCases
	{
		$nameCases = parent::getNameCases();
		$nameCases->setNominative('Sms шаблон');
		$nameCases->setNominativePlural('Sms шаблоны');
		$nameCases->setGenitivePlural('Sms шаблоны');
		$nameCases->setAccusative('Sms шаблона');

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