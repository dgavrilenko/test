<?php

namespace ddmp\common\data\models\patient;

use ddmp\common\data\models\BaseDao;
use ddmp\common\data\models\chat\ChatDao;
use ddmp\common\data\models\contract\ContractDao;
use ddmp\common\data\models\emailLog\EmailLogDao;
use ddmp\common\data\models\externalPatient\ExternalPatientDao;
use ddmp\common\data\models\file\FileDao;
use ddmp\common\data\models\partner\PartnerDao;
use ddmp\common\data\models\patientParam\PatientParamDao;
use ddmp\common\data\models\payLog\PayLogDao;
use ddmp\common\data\models\request\RequestDao;
use ddmp\common\data\models\serviceLog\ServiceLogDao;
use ddmp\common\data\models\sms\SmsLogDao;
use ddmp\common\enums\GenderEnum;
use ddmp\common\enums\PatientStatusEnum;
use ddmp\common\enums\ServiceTypeEnum;
use ddmp\common\extend\yii\validators\DateTimeValidator;
use ddmp\common\extend\yii\validators\DateValidator;
use ddmp\common\extend\yii\validators\DefaultIntegerValidator;
use ddmp\common\extend\yii\validators\EnumValidator;
use ddmp\common\extend\yii\validators\MobilePhoneValidator;
use ddmp\common\extend\yii\validators\NameSymbolsValidator;
use ddmp\common\extend\yii\validators\PhoneValidator;
use ddmp\common\extend\yii\validators\UcFirstValidator;
use ddmp\common\external\medlinesoft\api\params\MedlinePatient;
use ddmp\common\external\mmt\api\params\AddPolicyParams;
use ddmp\common\external\mmt\api\params\AddUserParams;
use ddmp\common\models\common\NameCases;
use ddmp\common\models\common\PersonName;
use ddmp\common\params\telemed\MmtTelemedParams;
use ddmp\common\utils\formatters\DateTimeFormat;
use ddmp\common\utils\generators\HashGenerator;
use ddmp\common\utils\helpers\CheckEmptyModelTrait;
use yii\validators\RequiredValidator;

/**
 * This is the model class for table "patient".
 *
 * @property integer           $id              Id
 * @property string            $first_name      Имя пациента
 * @property string            $last_name       Фамилия пациента
 * @property string            $middle_name     Отчество пациента
 * @property string            $phone           Номер телефона
 * @property string            $email           Электронная почта
 * @property string            $birthday        Дата рождения
 * @property integer           $gender          Пол
 * @property string            $status          Статус пациента
 * @property string            $bpm_id          Идентификатор пациента в BPM
 * @property integer           $bitrix_id       Индентификатор пациента в bitrix
 * @property string            $create_time     Время создания записи
 * @property string            $update_time     Время обновления записи
 * @property string            $last_login_time Время последней авторизации
 * @property integer           $synchronized    Синхронизирован ли профиль
 *
 * @property ChatDao[] $chats
 * @property ContractDao[] $contracts
 * @property EmailLogDao[] $emailLogs
 * @property ExternalPatientDao[] $externalPatients
 * @property FileDao[] $files
 * @property PatientParamDao[] $patientParams
 * @property PayLogDao[] $payLogs
 * @property RequestDao[] $requests
 * @property ServiceLogDao[] $serviceLogs
 * @property SmsLogDao[] $smsLogs
 */
class PatientDao extends BaseDao
{
	const SCENARIO_FORM_UPDATE = 'form_update';
	const SCENARIO_VALIDATE_EMPTY = 'validate_empty';
	const SCENARIO_MMT = 'mmt';
	const SCENARIO_SYSTEM_AUTH = 'system_auth';

	/**
	 * При загрузке реестров сбера
	 */
	const SCENARIO_SBREG = 'sbreg';

	const SCENARIO_EMAIL_CHECKUP_REQUEST_CREATE = 'scenario_text_checkup_request';

	use CheckEmptyModelTrait;

	public function scenarios()
	{
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_MMT] =
			['id', 'phone', 'birthday', 'first_name', 'last_name', 'middle_name', 'email', 'gender'];
		$scenarios[self::SCENARIO_SYSTEM_AUTH] =
			['id', 'phone'];
		return $scenarios;
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'patient';
	}

	/**
	 * @inheritdoc
	 * @return PatientDaoQuery the active query used by this AR class.
	 */
	public static function find()
	{
		return PatientDaoQuery::build(static::class);
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return array_merge(
			parent::rules(),
			[
				[$this->attributes(), 'trim'],
				[$this->attributes(), 'default'],
				[['id', 'bitrix_id', 'gender'], DefaultIntegerValidator::class],
				[['phone'], RequiredValidator::class],
				[['birthday', 'first_name', 'last_name'], 'required', 'on' => self::SCENARIO_FORM_UPDATE],
				[['phone', 'birthday', 'first_name', 'last_name'], 'required', 'on' => self::SCENARIO_VALIDATE_EMPTY],
				[['phone', 'first_name', 'last_name', 'birthday'], 'required', 'on' => self::SCENARIO_MMT],
				[['birthday', 'first_name', 'last_name'], 'required', 'on' => self::SCENARIO_SBREG],
				// Validators
				[['synchronized'], 'default', 'value'=> 0],
				[['id', 'gender', 'status', 'bitrix_id', 'synchronized'], 'integer'],
				[['gender'], EnumValidator::class, 'enumClass' => GenderEnum::class],
				[['status'], EnumValidator::class, 'enumClass' => PatientStatusEnum::class],
				[['email', 'bpm_id', 'birthday'], 'string', 'max' => 255],
				[['first_name', 'last_name'], 'string', 'min' => 2, 'max' => 75, 'on' => self::SCENARIO_FORM_UPDATE],
				[['middle_name'], 'string', 'min' => 5, 'max' => 100, 'on' => self::SCENARIO_FORM_UPDATE],
				[['phone'], MobilePhoneValidator::class, 'on' => 'default'],
				[['phone'], PhoneValidator::class, 'on' => self::SCENARIO_FORM_UPDATE],
				[['phone'], PhoneValidator::class, 'on' => self::SCENARIO_VALIDATE_EMPTY],
				[['phone'], PhoneValidator::class, 'on' => self::SCENARIO_MMT],
				[['phone'], PhoneValidator::class, 'on' => self::SCENARIO_SBREG],
				[['phone'], PhoneValidator::class, 'on' => self::SCENARIO_SYSTEM_AUTH],
				[
					['birthday'],
					DateValidator::class,
					'from'                     => DateTimeFormat::now()
						->getDateTime()
						->sub(new \DateInterval("P110Y"))
						->getTimestamp(),
					'to'                       => DateTimeFormat::now()->getDateTime()->getTimestamp(),
					'includeTo' => true,
				],
				['email', 'email', 'on' => self::SCENARIO_FORM_UPDATE],
				[['first_name', 'last_name', 'middle_name'], NameSymbolsValidator::class, 'on' => self::SCENARIO_FORM_UPDATE],
				// Modifiers
				[['first_name', 'last_name', 'middle_name'], UcFirstValidator::class],
				[['create_time', 'update_time', 'last_login_time'], DateTimeValidator::class],
			]
		);
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'              => 'Id',
			'first_name'      => 'Имя',
			'last_name'       => 'Фамилия',
			'middle_name'     => 'Отчество',
			'phone'           => 'Номер телефона',
			'email'           => 'Электронная почта',
			'birthday'        => 'Дата рождения',
			'gender'          => 'Пол',
			'status'          => 'Статус пациента',
			'create_time'     => 'Время создания записи',
			'update_time'     => 'Время обновления записи',
			'last_login_time' => 'Время последней авторизации',
			'bpm_id'          => 'ID пациента в системе BPM',
			'synchronized'    => 'Синхронизирован ли профиль',
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
			'id'         => 'id',
			'firstName'  => 'first_name',
			'lastName'   => 'last_name',
			'middleName' => 'middle_name',
			'birthday'   => 'birthday',
			'gender'     => 'gender',
			'email'      => 'email',
			'phone'      => 'phone',
			'synchronized' => 'synchronized',
		];
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
		if ($scenario == self::SCENARIO_EMAIL_CHECKUP_REQUEST_CREATE) {
			return [
				'last_name',
				'first_name',
				'middle_name',
				'phone',
				'birthday'
			];
		}

		return parent::getScenarioAttributes($scenario);
	}

	/**
	 * Генерирует пароль пользователя
	 *
	 * @return float|int
	 */
	public function generatePassword()
	{
		return (new HashGenerator())->generateNumericByString($this->phone);
	}

	/**
	 * Генерирует пароль пользователя защищенный солью
	 *
	 * @param string $salt
	 *
	 * @return int
	 */
	public function generatePasswordOnSalt(string $salt) : int
	{
		$string = '-' . md5($this->phone) . ':' . $salt . '+' . $this->phone;
		return (new HashGenerator())->generateNumericByString($string, 10);
	}

	/**
	 * Приводит модель к @see AddUserParams
	 *
	 * @return AddUserParams
	 */
	public function asMmtAddUserParams(?string $phoneReplacement = null)
	{
		$mmtParams = new AddUserParams(
			$this->email,
			$this->generatePassword(),
			$phoneReplacement ? $phoneReplacement : $this->phone,
			$this->first_name,
			$this->last_name
		);

		return $mmtParams;
	}

	/**
	 * Приводит модель к @see AddPolicyParams
	 *
	 * @param ContractDao|null $contract
	 *
	 * @param null|string $phoneReplacement
	 *
	 * @return AddPolicyParams
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\di\NotInstantiableException
	 */
	public function asMmtAddPolicyParams($contract = null, ?string $phoneReplacement = null)
	{
		$contract = $contract ?? $this->contracts[0];

		$partnerProductId = $contract->findExternalPartnerServiceId(PartnerDao::PARTNER_ID_MMT);

		$mmtParams = new AddPolicyParams(
			$contract->getMmtContractNumber(),
			$this->first_name,
			$this->last_name,
			$this->middle_name,
			$this->gender,
			$this->birthday,
			$partnerProductId,
			$contract->getCurrentStartDate(),
			$contract->getCurrentEndDate(),
			$this->email,
			$phoneReplacement ? $phoneReplacement : $this->phone
		);

		return $mmtParams;
	}

	/**
	 * Преобразует к модели @see MedlinePatient
	 *
	 * @return MedlinePatient
	 */
	public function asMedlinePatient()
	{
		$params = new MedlinePatient(
			$this->first_name,
			$this->last_name,
			$this->phone,
			$this->birthday,
			$this->email,
			$this->middle_name
		);

		return $params;
	}

	/**
	 * @param PersonName $name
	 */
	public function fromName(PersonName $name)
	{
		$this->first_name = $name->getFirstName();
		$this->last_name = $name->getLastName();
		$this->middle_name = $name->getMiddleName();
	}

	/**
	 * @return PersonName
	 */
	public function getPersonName(): PersonName
	{
		return new PersonName($this->last_name, $this->first_name, $this->middle_name);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getChats()
	{
		return $this->hasMany(ChatDao::class, ['patient_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getContracts()
	{
		return $this->hasMany(ContractDao::class, ['patient_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getEmailLogs()
	{
		return $this->hasMany(EmailLogDao::class, ['patient_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getExternalPatients()
	{
		return $this->hasMany(ExternalPatientDao::class, ['patient_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getFiles()
	{
		return $this->hasMany(FileDao::class, ['patient_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPatientParams()
	{
		return $this->hasMany(PatientParamDao::class, ['patient_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPayLogs()
	{
		return $this->hasMany(PayLogDao::class, ['patient_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRequests()
	{
		return $this->hasMany(RequestDao::class, ['patient_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getServiceLogs()
	{
		return $this->hasMany(ServiceLogDao::class, ['patient_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getSmsLogs()
	{
		return $this->hasMany(SmsLogDao::class, ['patient_id' => 'id']);
	}

	/**
	 * @return string
	 */
	public function getFullName(): string
	{
		$fullName = $this->last_name . ' ' . $this->first_name;
		if ($this->middle_name) {
			$fullName .= ' ' . $this->middle_name;
		}
		return $fullName;
	}

	/**
	 * @return NameCases
	 */
	public function getNameCases(): NameCases
	{
		$nameCases = parent::getNameCases();
		$nameCases->setNominative('Пациент');
		$nameCases->setNominativePlural('Пациенты');
		$nameCases->setGenitivePlural('пациентов');
		$nameCases->setAccusative('пациента');

		return $nameCases;
	}

	/**
	 * @return bool
	 */
	public function hasContracts(): bool
	{
		return \count($this->contracts) > 0;
	}

	/**
	 * @return bool
	 */
	public function isAdministrating(): bool
	{
		return true;
	}

	public function isEmptyFioOrBirthday(): bool
	{
		return $this->isEmpty(self::SCENARIO_FORM_UPDATE);
	}
}