<?php
namespace ddmp\common\data\models\import;

use ddmp\common\data\models\BaseDao;

/**
 * This is the model class for table "api_import_log".
 *
 * @property integer           $id                    Id
 * @property string            $phone                 Телефон
 * @property integer           $contract_status       Статус контракта(1-активирован, 0-деактивирован)
 * @property integer           $status                Статус импорта
 * @property string            $create_time           Время создания записи
 * @property string            $update_time           Время обновления записи
 * @property json              $request_body          Тело запроса
 */
class ApiImportLogDao extends BaseDao
{
    /**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'api_import_log';
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'              => 'Id',
			'phone'           => 'Телефон',
			'contract_status' => 'Статус контракта',
			'status'          => 'Статус импорта',
			'create_time'     => 'Время создания записи',
			'update_time'     => 'Время обновления записи',
			'request_body'    => 'Тело запроса',
		];
	}

	/**
	 * Возвращает массив полей ассоциированных с атрибутами для сериализации
	 *
	 * @return array
	 */
	public function fields()
	{
		return [
			'id'              => 'id',
			'phone'           => 'phone',
			'contract_status' => 'contract_status',
			'status'          => 'status',
			'create_time'     => 'create_time',
			'update_time'     => 'update_time',
			'request_body'    => 'request_body',
		];
	}
}

