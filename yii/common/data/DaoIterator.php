<?php

namespace ddmp\common\data;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Класс помогающий итеративно возвращать по одной записи для запроса
 * Ограничивая выборку из БД $batchSize кол-вом записей
 *
 * @package ddmp\common\data
 */
class DaoIterator
{
	/**
	 * @var ActiveQuery
	 */
	protected $query;

	/**
	 * @var int
	 */
	protected $batchSize;

	/**
	 * @var string
	 */
	protected $idColName = 'id';

	/**
	 * @var int
	 */
	protected $lastId = 0;

	/**
	 * DaoIterator constructor.
	 *
	 * @param ActiveQuery $query
	 * @param int         $batchSize
	 */
	public function __construct(ActiveQuery $query, int $batchSize = 1000)
	{
		$this->query = $query;
		$this->batchSize = $batchSize;
		$this->reset();
	}

	/**
	 * Итарация по результату запроса
	 *
	 * @return iterable
	 */
	public function iterate(): iterable
	{
		while (\count($rows = $this->nextRows($this->lastId)) > 0) {
			$this->lastId = $rows[\count($rows) - 1][$this->idColName];

			foreach ($rows as $row) {
				yield $row;
			}

			if (\count($rows) < $this->batchSize) {
				break;
			}
		}
	}

	/**
	 * @return iterable
	 */
	public function next(): iterable
	{
		$nextData = $this->nextRows($this->lastId);

		if (\count($nextData) === 0) {
			return null;
		}

		$this->lastId = $nextData[\count($nextData) - 1][$this->idColName];

		foreach ($nextData as $row) {
			yield $row;
		}

		return 1;
	}

	/**
	 *
	 */
	public function reset(): void
	{
		$this->lastId = 0;
	}

	/**
	 * Возвращает следующую порцию данных
	 *
	 * @param int $lastId
	 *
	 * @return ActiveRecord[]
	 */
	private function nextRows(int $lastId): array
	{
		$query = clone $this->query;

		$query
			->andWhere("`{$this->idColName}` > :lastId", [':lastId' => $lastId])
			->orderBy([$this->idColName => SORT_ASC])
			->limit($this->batchSize);

		return $query->all();
	}
}
