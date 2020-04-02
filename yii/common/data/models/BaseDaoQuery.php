<?php

namespace ddmp\common\data\models;

use ddmp\common\base\utils\factory\IDaoFactory;
use ddmp\common\data\DaoIterator;
use ddmp\common\utils\formatters\DateTimeFormat;
use yii\caching\TagDependency;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\di\NotInstantiableException;

class BaseDaoQuery extends ActiveQuery
{
	/**
	 * @param $tag
	 *
	 * @return TagDependency
	 */
	public static function getTagDependency($tag)
	{
		return new TagDependency(['tags' => $tag]);
	}

	/**
	 * Возвращает текущее время
	 *
	 * @return false|string
	 */
	public function getNowTime()
	{
		$formatted = DateTimeFormat::parse(new \DateTime())->mySqlTime();

		return $formatted;
	}

	/**
	 * Возвращает псевдоним для выборки
	 *
	 *
	 * @return string
	 */
	public function getAlias()
	{
		$from = $this->from;
		if (empty($from)) {
			return $this->getPrimaryTableName();
		}

		if (is_array($from)) {
			$nameToAlias = array_flip($from);
			return $nameToAlias[$this->getPrimaryTableName()];
		}

		return $from;
	}

	/**
	 * Выборка по id
	 *
	 * @param $id
	 *
	 * @return $this
	 */
	public function byId($id)
	{
		$this->andWhere(['id' => $id]);

		return $this;
	}

	/**
	 * @param int $id
	 *
	 * @return ActiveRecord|array|BaseDao|mixed
	 */
	public function oneById(int $id)
	{
		return $this->byId($id)->one();
	}

	public static function getDaoClassName()
	{
		throw new NotInstantiableException(static::class);
	}

	/**
	 * Выборка по статус
	 *
	 * @param null|int|int[] $status Если null, то условие не добавляется
	 *                               Если int, то выбирается по указанному статусу
	 *                               Если array, то выбирает по вхождению в диапазон статусов
	 *
	 * @return $this
	 */
	public function inStatus($status)
	{
		if ($status === null || $status === '') {
			return $this;
		}

		return $this->andWhere(['status' => $status]);
	}

	/**
	 * Инициализирует объект класса
	 *
	 * @param string $className
	 *
	 * @return static
	 */
	public static function build($className = null)
	{
		/** @var IDaoFactory $factory */
		$factory = \Yii::$container->get(IDaoFactory::class);
		return $factory->createQuery(static::class, $className ?? static::getDaoClassName());
	}

	/**
	 * Выборка по BPM ID
	 *
	 * @param string $bpmId
	 *
	 * @return $this
	 */
	public function byBpmId($bpmId)
	{
		return $this->andWhere(['bpm_id' => $bpmId]);
	}

	/**
	 * @param int $batchSize
	 *
	 * @return DaoIterator
	 */
	public function getIterator(int $batchSize): DaoIterator
	{
		return new DaoIterator($this, $batchSize);
	}

	/**
	 * Итерация по записям из запроса
	 *
	 * @param int $batchSize Ограничение на кол-во запрашиваемых данных
	 *
	 * @return iterable
	 */
	public function iterate(int $batchSize) : iterable
	{
		$iterator = new DaoIterator($this, $batchSize);
		return $iterator->iterate();
	}
}