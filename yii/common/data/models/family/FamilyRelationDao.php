<?php

namespace ddmp\common\data\models\family;

use ddmp\common\data\models\BaseDao;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "family_relation".
 *
 * @property string           $id                Id контракта
 * @property string           $owner_id          Владелец семейного кабинета
 * @property string           $relative_id       Id представителя
 * @property string           $name              Имя представителя
 * @property string           $surname           Фамилия представителя
 * @property string           $birthday          Д.р. представителя
 * @property string           $is_child          Представитель - ребенок
 * @property string           is_deleted         Удалено
 * @property string           $create_time       Время создания
 * @property string           $update_time       Время обновления
 */
class FamilyRelationDao extends BaseDao
{
	/**
	 * @return array
	 */
	public function attributeLabels()
	{
		return [
			'id'            => 'Id контракта',
			'owner_id'      => 'Владелец семейного кабинета',
			'relative_id'   => 'Id представителя',
			'name'          => 'Имя представителя',
			'surname'       => 'Фамилия представителя',
			'birthday'      => 'Д.р. представителя',
			'is_child'      => 'Представитель - ребенок',
			'is_deleted'    => 'Удалено',
			'create_time'   => 'Время создания',
			'update_time'   => 'Время обновления',
		];
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'id'            => 'id',
			'owner_id'      => 'owner_id',
			'relative_id'   => 'relative_id',
			'name'          => 'name',
			'surname'       => 'surname',
			'birthday'      => 'birthday',
			'is_child'      => 'is_child',
			'is_deleted'    => 'is_deleted',
			'create_time'   => 'create_time',
			'update_time'   => 'update_time',
		];
	}

	/**
	 * @return FamilyRelationDaoQuery|ActiveQuery
	 */
	public static function find()
	{
		return FamilyRelationDaoQuery::build(static::class);
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'family_relation';
	}

}
