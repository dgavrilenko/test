<?php

namespace ddmp\common\data\models\requestComment;

use ddmp\common\base\exceptions\InternalExceptionInterface;
use ddmp\common\data\models\BaseDao;
use ddmp\common\data\models\request\RequestDao;
use ddmp\common\enums\UserTypeEnum;
use ddmp\common\exceptions\DomainException;
use ddmp\common\exceptions\NotAllowedException;
use ddmp\common\extend\yii\validators\DefaultIntegerValidator;

/**
 * This is the model class for table "request_comment".
 *
 * @property int        $id          Идентификатор комментария к заявке
 * @property string     $request_id  Идентификатор заявки
 * @property string     $user_id     Идентификатор пользователя, который написал комментарий
 * @property int        $user_type   Тип пользователя, который оставил комментарий
 * @property string     $text        Текст комментария
 * @property int        $status      Статус комментария
 * @property string     $create_time Время создания записи
 * @property string     $update_time Время обновления записи
 *
 * @property RequestDao $request
 */
class RequestCommentDao extends BaseDao
{
	public const CREATE = 'create';
	public const UPDATE = 'update';

	/**
	 * RequestCommentDao constructor.
	 *
	 * @param array $config
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);
		$this->scenario = self::CREATE;
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName(): string
	{
		return 'request_comment';
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios(): array
	{
		return [
			self::CREATE => [
				'request_id',
				'text',
			]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array
	{
		return [
			[$this->attributes(), 'trim'],
			[$this->attributes(), 'default'],
			[['id', 'request_id', 'user_id', 'user_type', 'status'], DefaultIntegerValidator::class],
			[['id', 'request_id', 'user_id', 'user_type', 'text'], 'required'],
			[['id', 'request_id', 'user_id', 'user_type', 'status'], 'integer'],
			[['text'], 'string'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array
	{
		return [
			'id'          => 'Идентификатор комментария к заявке',
			'request_id'  => 'Идентификатор заявки',
			'user_id'     => 'Идентификатор пользователя, который написал комментарий',
			'user_type'   => 'Тип пользователя, который оставил комментарий',
			'text'        => 'Текст комментария',
			'status'      => 'Статус комментария',
			'create_time' => 'Время создания записи',
			'update_time' => 'Время обновления записи',
		];
	}

	/**
	 * @inheritdoc
	 * @return RequestCommentDaoQuery the active query used by this AR class.
	 */
	public static function find(): RequestCommentDaoQuery
	{
		return RequestCommentDaoQuery::build(static::class);
	}

	/**
	 * @return \yii\db\ActiveQuery|RequestCommentDaoQuery
	 */
	public function getRequest(): RequestCommentDaoQuery
	{
		return $this->hasOne(RequestDao::class, ['id' => 'request_id']);
	}

	/**
	 * @param RequestDao $request
	 *
	 * @return RequestCommentDao
	 * @throws InternalExceptionInterface
	 */
	public function checkOwnerUser(RequestDao $request): self
	{
		if ((int)$this->request_id !== (int)$request->id) {
			throw DomainException::logicError('Заявка для комментария отличается от привязанной');
		}

		switch ($this->user_type) {
			case UserTypeEnum::PATIENT:
				$this->checkOwnerPatient($request, $this->user_id);
				break;
			case UserTypeEnum::PARTNER:
				$this->checkOwnerPartner($request, $this->user_id);
				break;
			default:
				throw DomainException::logicError("Не известный тип пользователя {$this->user_type}");
		}

		return $this;
	}

	/**
	 * @param RequestDao $request
	 * @param int        $patientId
	 *
	 * @throws NotAllowedException
	 */
	public function checkOwnerPatient(RequestDao $request, int $patientId): void
	{
		if ((int)$request->patient_id !== $patientId) {
			throw NotAllowedException::userNotOwner('Заявка');
		}
	}

	/**
	 * @param RequestDao $request
	 * @param int        $partnerId
	 *
	 * @throws NotAllowedException
	 */
	public function checkOwnerPartner(RequestDao $request, int $partnerId): void
	{
		if ((int)$request->partner_id !== $partnerId) {
			throw NotAllowedException::userNotOwner('Заявка');
		}
	}
}
