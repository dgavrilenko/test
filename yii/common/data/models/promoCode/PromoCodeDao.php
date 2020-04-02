<?php

namespace ddmp\common\data\models\promoCode;

use DateInterval;
use DateTime;
use ddmp\common\base\data\models\AdminDaoInterface;
use ddmp\common\data\models\BaseDao;
use ddmp\common\data\models\partner\PartnerDao;
use ddmp\common\data\models\product\ProductDao;
use ddmp\common\enums\PromoCodeStatusEnum;
use ddmp\common\exceptions\PromoCodeException;
use ddmp\common\extend\yii\validators\DateValidator;
use ddmp\common\extend\yii\validators\DefaultIntegerValidator;
use ddmp\common\extend\yii\validators\EnumValidator;
use ddmp\common\models\common\NameCases;
use ddmp\common\utils\formatters\DateTimeFormat;
use ddmp\common\utils\PhpUtils;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "promo_code".
 *
 * @property int        $id                   Идентификатор промо-кода
 * @property string     $product_id           Идентификатор продукта
 * @property string     $contract_partner_id  Партнёр для контракта
 * @property string     $code                 Промо-код
 * @property int        $use_count_max        Количество использований кода
 * @property int        $product_period_days  Количество дней на которые даётся продукт по промо-коду
 * @property int        $status               Статус состояния промо-кода
 * @property string     $end_date             Дата окончания действия промо-кода
 * @property int        $bitrix_id            Идентификатор в битрикс
 *
 * @property PartnerDao $contractPartner
 * @property ProductDao $product
 */
class PromoCodeDao extends BaseDao
{
	/**
	 * @inheritdoc
	 */
	public static function tableName(): string
	{
		return 'promo_code';
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array
	{
		return [
			[['product_id', 'code'], 'required'],
			[['product_id', 'contract_partner_id', 'bitrix_id'], DefaultIntegerValidator::class],
			[['product_id', 'contract_partner_id', 'use_count_max', 'product_period_days', 'status', 'bitrix_id'], 'integer'],
			[['code'], 'string', 'max' => 20],
			[['end_date'], DateValidator::class],
			[['product_id'], 'exist', 'targetClass' => ProductDao::class, 'targetAttribute' => ['product_id' => 'id'], 'on' => AdminDaoInterface::SCENARIO_ADMIN_EDIT],
			[['contract_partner_id'], 'exist', 'targetClass' => PartnerDao::class, 'targetAttribute' => ['contract_partner_id' => 'id'], 'on' => AdminDaoInterface::SCENARIO_ADMIN_EDIT],
			[['status'], EnumValidator::class, 'enumClass' => PromoCodeStatusEnum::class, 'on' => AdminDaoInterface::SCENARIO_ADMIN_EDIT],
			[['end_date'], DateValidator::class],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array
	{
		return [
			'id'                  => 'Идентификатор промо-кода',
			'product_id'          => 'Идентификатор продукта',
			'contract_partner_id' => 'Партнёр для контракта',
			'code'                => 'Промо-код',
			'use_count_max'       => 'Количество использований кода',
			'product_period_days' => 'Количество дней на которые даётся продукт по промо-коду',
			'status'              => 'Статус состояния промо-кода',
			'create_time'         => 'Время создания',
			'update_time'         => 'Время изменения',
			'end_date'            => 'Действителен до',
		];
	}

	/**
	 * @inheritdoc
	 * @return PromoCodeDaoQuery the active query used by this AR class.
	 */
	public static function find(): PromoCodeDaoQuery
	{
		return PromoCodeDaoQuery::build(static::class);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getContractPartner(): ActiveQuery
	{
		return $this->hasOne(PartnerDao::class, ['id' => 'contract_partner_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getProduct(): ActiveQuery
	{
		return $this->hasOne(ProductDao::class, ['id' => 'product_id']);
	}

	/**
	 * Добавляет период в днях к указанной дате
	 *
	 * @param DateTime $periodStart
	 *
	 * @return DateTime|null
	 */
	public function getPeriodEndDate(DateTime $periodStart): ?DateTime
	{
		if ($this->product_period_days === null) {
			return null;
		}

		return $periodStart
			->add(new DateInterval("P{$this->product_period_days}D"));
	}

	/**
	 * Зафиксировать использование промокода
	 * В данный момент поддерживается только
	 * одноразовое и бесконечное использование
	 */
	public function use(): self
	{
		if ($this->use_count_max !== null) {
			$this->status = PromoCodeStatusEnum::INACTIVE;
		}

		return $this;
	}

	/**
	 * @return PromoCodeDao
	 * @throws PromoCodeException
	 */
	public function throwIfExpired(): self
	{
		if ($this->end_date !== null) {
			/** @var PhpUtils $phpUtils */
			$phpUtils = \Yii::$container->get(PhpUtils::class);
			$endDate = DateTimeFormat::parse($this->end_date);
			$now = DateTimeFormat::parse($phpUtils->time());

			if ($now->getDateTime() >= $endDate->getDateTime()) {
				$this->status = PromoCodeStatusEnum::INACTIVE;
				$this->trySave();
				throw PromoCodeException::codeExpired($this->code);
			}
		}

		return $this;
	}

	/**
	 * @return NameCases
	 */
	public function getNameCases(): NameCases
	{
		$nameCases = parent::getNameCases();
		$nameCases->setNominative('Промо код');
		$nameCases->setNominativePlural('Промо коды');
		$nameCases->setGenitivePlural('промо кодов');
		$nameCases->setAccusative('промо код');

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
