<?php

namespace ddmp\common\data\models\importLog;

use ddmp\common\data\models\BaseDao;

/**
 * This is the model class for table "import_log".
 *
 * @property int    $id               Идентификатор
 * @property string $name             Наименование лога
 * @property string $data             Данные импорта
 * @property int    $status           Статус
 * @property string $error_text       Текст ошибки
 * @property string $log_text         Лог выполнения операции
 * @property string $transaction_time Время транзакции
 */
class ImportLogDao extends BaseDao
{
	/**
	 * ImportLogDao constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'import_log';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['data', 'error_text', 'log_text'], 'string'],
			[['status'], 'integer'],
			[['transaction_time'], 'safe'],
			[['name'], 'string', 'max' => 100],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'               => 'Идентификатор',
			'name'             => 'Наименование лога',
			'data'             => 'Данные импорта',
			'status'           => 'Статус',
			'error_text'       => 'Текст ошибки',
			'log_text'         => 'Лог выполнения операции',
			'transaction_time' => 'Время транзакции',
		];
	}

	/**
	 * @inheritdoc
	 * @return ImportLogDaoQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return ImportLogDaoQuery::build(get_called_class());
	}

	/**
	 * Добавляет сообщение лога
	 *
	 * @param $text
	 */
	public function addMessage($text)
	{
		$this->log_text .= $text . PHP_EOL;
	}

	/**
	 * @param int $status @see ImportStatusEnum
	 *
	 * @return $this
	 */
	public function setStatus($status)
	{
		$this->status = $status;
		return $this;
	}

	/**
	 * @param string $text
	 *
	 * @return $this
	 */
	public function setErrorText($text)
	{
		$this->error_text = $text;
		return $this;
	}
}