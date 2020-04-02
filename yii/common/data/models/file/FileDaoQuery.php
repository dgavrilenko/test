<?php

namespace ddmp\common\data\models\file;

use ddmp\common\data\models\BaseDaoQuery;

/**
 * This is the ActiveQuery class for [[FileDao]].
 *
 * @see FileDao
 */
class FileDaoQuery extends BaseDaoQuery
{
	/**
	 * @inheritdoc
	 * @return FileDao[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return FileDao|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}

	/**
	 * Возвращает имя dao класса
	 *
	 * @return string
	 */
	public static function getDaoClassName()
	{
		return FileDao::class;
	}
}
