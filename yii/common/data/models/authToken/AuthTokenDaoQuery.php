<?php

namespace ddmp\common\data\models\authToken;

use ddmp\common\data\models\BaseDaoQuery;
use ddmp\common\enums\UserTypeEnum;
use ddmp\common\utils\formatters\DateTimeFormat;

/**
 * This is the ActiveQuery class for [[AuthTokenDao]].
 *
 * @see AuthTokenDao
 */
class AuthTokenDaoQuery extends BaseDaoQuery
{
	/**
	 * @inheritdoc
	 * @return AuthTokenDao[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return AuthTokenDao|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}

	/**
	 * Выборка по токену
	 *
	 * @param string $token
	 *
	 * @return $this
	 */
	public function byToken($token)
	{
		$this->andWhere(['token' => $token]);

		return $this;
	}

	/**
	 * Возвращает запись по токену
	 *
	 * @param $token
	 *
	 * @return AuthTokenDao|null
	 */
	public function oneByToken($token)
	{
		return $this->byToken($token)->one();
	}

	/**
	 * Возвращает токен по партнёру и действию
	 *
	 * @param      $partnerId
	 * @param null $action
	 *
	 * @return array|AuthTokenDao|null
	 */
	public function oneByPartner($partnerId, $action = null)
	{
		$query = $this
			->andWhere(['user_id' => $partnerId])
			->andWhere(['user_type' => UserTypeEnum::PARTNER])
			->andWhere(['action' => $action])
			->orderBy('`expired` IS NULL DESC, `expired` DESC')
			->limit(1)
		;

		return $query->one();
	}

	/**
	 * Выборка активного токена на указанный момент времени
	 *
	 * @param int $timestamp Время
	 *
	 * @return $this
	 */
	public function active($timestamp)
	{
		$this->andWhere(
			[
				'or',
				[
					'expired' => null
				],
				[
					'>',
					'expired',
					DateTimeFormat::parse($timestamp)->mySqlTime()
				]
			]);

		return $this;
	}
}
