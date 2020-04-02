<?php

namespace ddmp\common\data\models\emailLog;

use ddmp\common\data\models\BaseDaoQuery;
use ddmp\common\enums\EmailLogStatusEnum;
use ddmp\common\params\shop\ShopEmailParams;

/**
 * This is the ActiveQuery class for [[EmailLogDao]].
 *
 * @see EmailLogDao
 */
class EmailLogDaoQuery extends BaseDaoQuery
{
	/**
	 * @inheritdoc
	 * @return EmailLogDao[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return EmailLogDao|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}

	/**
	 * Выборка сообщений, ожидающих отправки
	 *
	 * @return $this
	 */
	public function toSend()
	{
		$params = ShopEmailParams::build();

		return $this
			->andWhere([
				'or',
				['status' => EmailLogStatusEnum::NEW],
				[
					'and',
					['status' => EmailLogStatusEnum::FAIL],
					['<', 'resend_count', $params->getResendCountMax()]
				]
			]);
	}
}
