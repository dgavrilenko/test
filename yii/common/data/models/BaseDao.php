<?php

namespace ddmp\common\data\models;

use ddmp\common\base\data\models\AdminDaoInterface;
use ddmp\common\base\models\common\NameCasesInterface;
use ddmp\common\base\utils\factory\IDaoFactory;
use ddmp\common\base\utils\serialize\SerializableModelTrait;
use ddmp\common\exceptions\DomainException;
use ddmp\common\exceptions\ValidationException;
use ddmp\common\models\common\NameCases;
use ddmp\common\utils\formatters\DateTimeFormat;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

/**
 * Class BaseDao
 *
 * @property int $id
 *
 * @package ddmp\common\data\models
 */
abstract class BaseDao extends ActiveRecord implements \JsonSerializable, \Serializable, AdminDaoInterface
{
	use SerializableModelTrait {
		toArray as toArrayTrait;
	}

	/**
	 * @inheritdoc
	 */
	protected function getAccessibleProperties()
	{
		$attributes = array_keys($this->attributes);

		return array_combine($attributes, $attributes);
	}

	/**
	 * Specify data which should be serialized to JSON
	 *
	 * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	public function jsonSerialize()
	{
		return $this->toArray();
	}

	/**
	 * @inheritdoc
	 */
	public function toArray(array $fields = [], array $expand = [], $recursive = true)
	{
		return parent::toArray($fields, $expand, $recursive);
	}

	/**
	 * Сериализует модель в json
	 *
	 * @return string
	 */
	public function toJson()
	{
		return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
	}

	/**
	 * Сериализует модель в json с разрывами
	 *
	 * @return string
	 */
	public function toJsonPretty()
	{
		return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	}

	/**
	 * Сохраняет и выкидывает исключение, если есть ошибки валидации
	 *
	 * @param bool $runValidation
	 *
	 * @return $this
	 * @throws ValidationException
	 */
	public function trySave($runValidation = true)
	{
		$result = static::save($runValidation);

		if ($result === false) {
			throw (new ValidationException(
				DomainException::ERROR_INVALID_PARAMS, 'Ошибка во время сохранения записи ' . static::tableName()
			))->withEntity($this);
		}

		return $this;
	}

	/**
	 * Возвращает ошибки валидации в json-формате
	 *
	 * @return string
	 */
	public function errorsToJson()
	{
		return json_encode($this->errors, JSON_UNESCAPED_UNICODE);
	}

	/**
	 * @param Model $model
	 */
	public function loadFromModel($model)
	{
		$this->load($model->getAttributes($model->safeAttributes()), '');
	}

	/**
	 * Приводит модель к массиву вида
	 * [ label => value ]
	 *
	 * @param $scenario
	 *
	 * @return array
	 */
	public function toScenarioLabels($scenario)
	{
		$attributes = $this->toScenarioArray($scenario);
		$result = [];

		foreach ($attributes as $key => $value) {
			$result[$this->getAttributeLabel($key)] = $value;
		}

		return $result;
	}

	/**
	 * Приводит модель к массиву из активных атрибутов
	 * [ attribute => value]
	 *
	 * @param string $scenario
	 *
	 * @return array
	 */
	public function toScenarioArray($scenario)
	{
		$attributeNames = $this->getScenarioAttributes($scenario);

		$result = $this->getAttributes($attributeNames);

		return $result;
	}

	/**
	 * Возвращает массив полей, которые необходимо преобразовать в текст
	 *
	 * @param $scenario
	 *
	 * @return string[]
	 */
	protected function getScenarioAttributes($scenario)
	{
		$prevScenario = $this->getScenario();
		$this->setScenario($scenario);

		$attributes = $this->activeAttributes();
		$this->setScenario($prevScenario);

		return $attributes;
	}

	/**
	 * Возвращает объект сущности
	 *
	 * @return static
	 */
	public static function create()
	{
		/** @var IDaoFactory $factory */
		$factory = \Yii::$container->get(IDaoFactory::class);

		return $factory->createDao(static::class);
	}

	/**
	 * Заполняет модель непустыми значениями
	 *
	 * @param $data
	 *
	 * @return $this
	 */
	public function loadNotEmpty(array $data)
	{
		foreach ($data as $key => $value) {
			if ($value === null || $value === '') {
				unset($data[$key]);
			}
		}

		$this->load($data, '');
		return $this;
	}

	/**
	 * String representation of object
	 * @link http://php.net/manual/en/serializable.serialize.php
	 * @return string the string representation of the object or null
	 * @since 5.1.0
	 */
	public function serialize()
	{
		return $this->toJson();
	}

	/**
	 * Constructs the object
	 * @link http://php.net/manual/en/serializable.unserialize.php
	 * @param string $serialized <p>
	 * The string representation of the object.
	 * </p>
	 * @return void
	 * @since 5.1.0
	 */
	public function unserialize($serialized)
	{
		$data = json_decode($serialized, true);
		$this->setAttributes($data, false);
	}

	/**
	 * @return static
	 */
	public function setCreateTimeNow(): self
	{
		$this->create_time = DateTimeFormat::now()->mySqlTime();

		return $this;
	}

	/**
	 * @return int|null
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * Администрируется ли сущность
	 *
	 * @return bool
	 */
	public function isAdministrating(): bool
	{
		return false;
	}

	/**
	 * @return NameCases
	 */
	public function getNameCases(): NameCases
	{
		$className = static::class;
		$parts = explode('\\', $className);
		$className = end($parts);

		if (mb_substr($className, -3, 3) === 'Dao') {
			$className = mb_substr($className, 0, -3);
		}

		$name = Inflector::camel2words($className);

		return new NameCases(
			$name, $name, $name, $name, $name, $name, $name
		);
	}
}