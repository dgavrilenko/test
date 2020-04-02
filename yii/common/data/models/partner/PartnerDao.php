<?php

namespace ddmp\common\data\models\partner;

use ddmp\common\data\models\BaseDao;
use ddmp\common\data\models\contract\ContractDao;
use ddmp\common\data\models\product\ProductDao;
use ddmp\common\extend\yii\validators\DateTimeValidator;
use ddmp\common\models\common\NameCases;

/**
 * This is the model class for table "partner".
 *
 * @property string        $id            Id партнера
 * @property string        $name          Наименование партнёра
 * @property string        $email         Email партнёра
 * @property string        $email_support Email поддержки партнёра
 * @property string        $create_time   Время создания записи
 * @property string        $update_time   Время обновления записи
 * @property string        $bpm_id        ID в системе BPM
 *
 * @property ContractDao[] $contracts
 * @property ProductDao[]  $products
 */
class PartnerDao extends BaseDao
{
	/** ДокДок */
	const PARTNER_ID_DOCDOC = 1;

	/** Сбербанк Страхование */
	const PARTNER_ID_SBERBANK_STRAHOVANIE = 2;

	/** Доктор рядом */
	const PARTNER_ID_DOCTOR_RYADOM = 3;

	/** Доктор рядом */
	const PARTNER_CODE_DOCTOR_RYADOM = "doctorRyadom";

	/** Сбербанк добровольное страхование жизни */
	const PARTNER_ID_SBERBANK_DSZ = 4;

	/** ММТ */
	const PARTNER_ID_MMT = 5;

	/** ММТ */
	const PARTNER_CODE_MMT = 'mmt';

	/** НМС */
	const PARTNER_ID_NMS = 6;

	/** Маданес */
	const PARTNER_ID_MADANES = 7;

	/** Мать и дитя || Doctis */
	const PARTNER_ID_MAT_I_DITYA = 8;

	/** Мать и дитя || Doctis */
	const PARTNER_CODE_MAT_I_DITYA = "doctis";

	/** Medigo */
	const PARTNER_ID_MEDIGO = 9;

    /** MPC */
    const PARTNER_ID_MPC = 10;

	/** MPC */
	const PARTNER_CODE_MPC = "mpc";

	/** Kot Zdorov */
	const PARTNER_ID_KOT_ZDOROV = 11;

	/** Kot Tele2 */
	const PARTNER_ID_TELE2 = 12;

	/** Атлас */
	const PARTNER_ID_ATLAS = 13;

	/** Doc+ */
	const PARTNER_ID_DOC_PLUS = 14;

	/** Doc+ code */
	const PARTNER_CODE_DOC_PLUS = 'docPlus';

	/** Ibolit */
	const PARTNER_ID_IBOLIT = 15;

	/** Ibolit code */
	const PARTNER_CODE_IBOLIT = 'ibolit';

	/** Сбербанк Онлайн */
	const PARTNER_ID_SBOL = 20;

	const PARTNER_ID_LOYALITI_SPASIBO = 16;

	/** Кнопка жизни */
	const PARTNER_ID_LIFE_BUTTON = 30;

    /**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'partner';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['name'], 'required'],
			[['create_time', 'update_time'], 'safe'],
			[['name', 'bpm_id', 'email'], 'string', 'max' => 255],
			[['email_support'], 'email'],
			[['create_time', 'update_time'], DateTimeValidator::class],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'          => 'Id партнера',
			'name'        => 'Наименование партнёра',
			'create_time' => 'Время создания записи',
			'email'       => 'Email партнёра',
			'update_time' => 'Время обновления записи',
			'bpm_id'      => 'ID партнера в системе BPM'
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getContracts()
	{
		return $this->hasMany(ContractDao::className(), ['partner_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getProducts()
	{
		return $this->hasMany(ProductDao::className(), ['partner_id' => 'id']);
	}

	/**
	 * @inheritdoc
	 * @return PartnerDaoQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return PartnerDaoQuery::build(get_called_class());
	}

	/**
	 * Возвращает email для заявок
	 *
	 * @return array
	 */
	public function getRequestEmail()
	{
		$emails = explode(',', $this->email);

		if (empty($emails)) {
			return [];
		}

		return $emails[0];
	}

	/**
	 * Возвращает email cc для заявок
	 *
	 * @return array
	 */
	public function getRequestEmailCc()
	{
		$emails = explode(',', $this->email);

		if (empty($emails) || count($emails) === 1) {
			return [];
		}

		unset($emails[0]);

		return array_values($emails);
	}

	/**
	 * @return bool
	 */
	public function isAdministrating(): bool
	{
		return true;
	}

	/**
	 * @return NameCases
	 */
	public function getNameCases(): NameCases
	{
		$nameCases = parent::getNameCases();
		$nameCases->setNominative('Партнёр');
		$nameCases->setNominativePlural('Партнёры');
		$nameCases->setGenitivePlural('партнёров');
		$nameCases->setAccusative('партнёра');

		return $nameCases;
	}
}
