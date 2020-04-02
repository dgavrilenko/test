<?php

namespace ddmp\common\data\models\requestConfidant;

use ddmp\common\data\models\BaseDao;
use ddmp\common\data\models\request\RequestDao;
use ddmp\common\enums\UserTypeEnum;
use ddmp\common\exceptions\InternalException;
use ddmp\common\exceptions\NotAllowedException;
use ddmp\common\extend\yii\validators\DefaultIntegerValidator;

/**
 * This is the model class for table "request_confidant".
 *
 * @property int        $id          Идентификатор комментария к заявке
 * @property int        $request_id  Идентификатор заявки
 * @property string     $name        Имя
 * @property string     $last_name   Фамилия
 * @property string     $phone       Телефон
 * @property string     $create_time Время создания записи
 * @property string     $update_time Время обновления записи
 *
 * @property RequestConfidantDao $request
 */
class RequestConfidantDao extends BaseDao
{
    const DISABILITY_LIST = [
        1 => 'Нет инвалидности',
        2 => '1-я группа',
        3 => '2-я группа',
        4 => '3-я группа'
    ];

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'request_confidant';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                [['id', 'request_id'], 'integer'],
                [['name', 'last_name', 'phone'], 'string'],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id'          => 'Идентификатор комментария к заявке',
            'request_id'  => 'Идентификатор заявки',
            'name'        => 'Имя',
            'last_name'   => 'Фамилия',
            'phone'       => 'Телефон',
            'create_time' => 'Время создания записи',
            'update_time' => 'Время обновления записи',
        ];
    }

    /**
     * @inheritdoc
     * @return RequestConfidantDaoQuery the active query used by this AR class.
     */
    public static function find(): RequestConfidantDaoQuery
    {
        return RequestConfidantDaoQuery::build(static::class);
    }

    /**
     * @return \yii\db\ActiveQuery|RequestConfidantDaoQuery
     */
    public function getRequest(): RequestConfidantDaoQuery
    {
        return $this->hasOne(RequestDao::class, ['id' => 'request_id']);
    }
}
