<?php

namespace ddmp\common\data\models\authToken;

use ddmp\common\base\data\models\AdminDaoInterface;
use ddmp\common\data\models\BaseDao;
use ddmp\common\data\models\partner\PartnerDao;
use ddmp\common\enums\UserTypeEnum;
use ddmp\common\extend\yii\validators\DateTimeValidator;
use ddmp\common\extend\yii\validators\EnumValidator;
use ddmp\common\models\common\NameCases;
use ddmp\common\utils\formatters\ArrayFormat;
use ddmp\common\utils\formatters\DateTimeFormat;
use ddmp\common\utils\generators\HashGenerator;

/**
 * This is the model class for table "auth_token".
 *
 * @property string $id        Id
 * @property string $token     Токен доступа
 * @property string $user_id   Id пользователя
 * @property int    $user_type Тип пользователя
 * @property string $action    Действие на которое выдаётся разрешение
 * @property string $row_id    Id записи на которую выдаётся разрешение
 * @property string $expired   Время истечения
 */
class AuthTokenDao extends BaseDao
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'auth_token';
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array
	{
		return [
			[['token'], 'required'],
			[['user_id', 'user_type'], 'integer'],
			[['token', 'action', 'row_id'], 'string', 'max' => 255],
			[['user_type'], EnumValidator::class, 'enumClass' => UserTypeEnum::class],
			[['expired'], DateTimeValidator::class],
			[['user_id'], 'exist', 'targetClass' => PartnerDao::class, 'targetAttribute' => ['user_id' => 'id'], 'on' => AdminDaoInterface::SCENARIO_ADMIN_EDIT],
			[['token'], 'unique', 'on' => AdminDaoInterface::SCENARIO_ADMIN_EDIT],
			[['user_type'], 'default', 'value' => UserTypeEnum::PARTNER, 'on' => AdminDaoInterface::SCENARIO_ADMIN_EDIT],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'        => 'Id',
			'token'     => 'Токен доступа',
			'user_id'   => 'Id пользователя',
			'user_type' => 'Тип пользователя',
			'action'    => 'Действие на которое выдаётся разрешение',
			'row_id'    => 'Id записи на которую выдаётся разрешение',
			'expired'   => 'Время истечения',
		];
	}

	/**
	 * @inheritdoc
	 * @return AuthTokenDaoQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return AuthTokenDaoQuery::build(get_called_class());
	}

	/**
	 * Генерация токена
	 *
	 * @param int             $userId
	 * @param string          $userType
	 * @param string|string[] $action
	 * @param int             $rowId
	 * @param int             $intervalSec
	 *
	 * @return static
	 */
	public static function generateAuthToken($userId, $userType, $action, $rowId, $intervalSec)
	{
		$model = new static();
		$model->token = HashGenerator::generateToken();
		$model->expired = DateTimeFormat::parse(time() + $intervalSec)->mySqlTime();
		$model->user_id = $userId;
		$model->user_type = $userType;
		$model->action = ArrayFormat::parse($action)->getString();
		$model->row_id = strval($rowId);

		$model->save();

		return $model;
	}

	/**
	 * @return string
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getToken(): string
	{
		return $this->token;
	}

	/**
	 * @return string
	 */
	public function getUserId(): ?string
	{
		return $this->user_id;
	}

	/**
	 * @return int
	 */
	public function getUserType(): ?int
	{
		return $this->user_type;
	}

	/**
	 * @return string
	 */
	public function getAction(): ?string
	{
		return $this->action;
	}

	/**
	 * @return ArrayFormat
	 */
	public function getActionList(): ArrayFormat
	{
		return ArrayFormat::parse($this->getAction());
	}

	/**
	 * @return string|array
	 */
	public function getRowId()
	{
		return $this->row_id;
	}

	/**
	 * @return ArrayFormat
	 */
	public function getRowIdList()
	{
		return ArrayFormat::parse($this->getRowId());
	}

	/**
	 * @return string
	 */
	public function getExpired(): ?string
	{
		return $this->expired;
	}

	/**
	 * @return NameCases
	 */
	public function getNameCases(): NameCases
	{
		$nameCases = parent::getNameCases();
		$nameCases->setNominative('Токен доступа');
		$nameCases->setNominativePlural('Токены доступа');
		$nameCases->setGenitivePlural('токенов доступа');
		$nameCases->setAccusative('токен доступа');

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