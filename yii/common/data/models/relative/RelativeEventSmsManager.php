<?php

namespace ddmp\common\data\models\relative;


use ddmp\common\data\models\contract\ContractDao;
use ddmp\common\data\models\family\FamilyRelationDao;
use ddmp\common\data\models\patient\PatientDao;
use ddmp\common\data\models\product\ProductDao;
use ddmp\common\data\models\sms\SmsLogDao;
use ddmp\common\data\models\sms\SmsLogManager;
use ddmp\common\enums\SmsTemplateEventEnum;
use ddmp\common\family\FamilyRelationsService;
use ddmp\common\models\shortLink\ShortLinkManager;
use ddmp\common\models\sms\template\event\SmsTemplateEventManager;
use ddmp\common\utils\formatters\Phone;
use ddmp\common\utils\helpers\Url;

/**
 * Отвечает за отправку смс сообщений в результате событий,
 * которые пользователь производит с родственниками (Семейный кабинет)
 *
 * Class RelativeEventSmsManager
 *
 * @package ddmp\common\data\models\relative
 */
class RelativeEventSmsManager
{
	/**
	 * Сколько минут смс должно "отлежаться" до отправки
	 */
	private const INTERVAL_SMS_WAITS_MINUTES = 5;

	/** @var SmsLogManager */
	private $smsLogManager;

	/** @var SmsTemplateEventManager  */
	private $smsTemplateEventManager;

	/**
	 * RelativeEventSmsManager constructor.
	 *
	 * @param SmsLogManager $smsLogManager
	 * @param SmsTemplateEventManager $smsTemplateEventManager
	 */
	public function __construct(SmsLogManager $smsLogManager, SmsTemplateEventManager $smsTemplateEventManager)
	{
		$this->smsLogManager = $smsLogManager;
		$this->smsTemplateEventManager = $smsTemplateEventManager;
	}

	/**
	 * Обработка событи: пользователь добавил родственника
	 *
	 * @param PatientDao $mainUser
	 * @param PatientDao $relative
	 */
	public function addRelativeHandler(PatientDao $mainUser, PatientDao $relative): void
	{
		$data = [
			'patient' => $mainUser
		];
		try {
			$smsText = $this->smsTemplateEventManager->buildText(
				SmsTemplateEventEnum::RELATIVE_ADDED,
				null,
				$this->prepareData($data)
			);
			$this->smsLogManager->planSms($relative, $smsText, $this->getPlannedTime(), $this->getEventId($mainUser->id));
		} catch (\Throwable $e) {
			\Yii::exception($e);
		}
	}

	/**
	 * Обрабатываем событие: пользователь удалил родственника
	 *
	 * @param PatientDao $mainUser
	 * @param PatientDao $relative
	 */
	public function removeRelativeHandler(PatientDao $mainUser, PatientDao $relative): void
	{
		try {
			$existPlannedSms = SmsLogDao::find()
				->plannedForEvent($relative->id, $this->getEventId($mainUser->id))
				->one();
			if ($existPlannedSms) {
				$this->smsLogManager->cancel($existPlannedSms);
			}
		} catch (\Throwable $e) {
			\Yii::exception($e);
		}
	}

	/**
	 * Вызывать как при добавление, так и при удаление продукта к родственнику
	 *
	 * @param PatientDao $mainUser
	 * @param PatientDao $relative
	 */
	public function relativeProductsEventHandler(PatientDao $mainUser, PatientDao $relative): void
	{
		try {
			$newSmsData = $this->prepareRelativeProductSmsText($mainUser, $relative);
			$newSmsEvent = $newSmsData['event'];
			$smsText = $newSmsData['text'];

			$existPlannedSms = SmsLogDao::find()
				->plannedForEvent($relative->id, $this->getEventId($mainUser->id))
				->one();
			if ($existPlannedSms) {
				//если добавлен родственник без продуктов и такая смс уже отправлялась, не шлем ее ещё раз
				if (SmsTemplateEventEnum::RELATIVE_ADDED == $newSmsEvent && $this->alreadySendSmsExists($mainUser, $relative)) {
					$this->smsLogManager->cancel($existPlannedSms);
				} else {
					$this->smsLogManager->reNewPlannedSms($existPlannedSms, $smsText, $this->getPlannedTime());
				}
			} else {
				//если сообщение о том что родственик добавлен (без продуктов)
				if (SmsTemplateEventEnum::RELATIVE_ADDED == $newSmsEvent && $this->alreadySendSmsExists($mainUser, $relative)) {
					//то если такая смс хоть раз была отправлена ранее, ее слать уже не нужно
					return;
				}
				$this->smsLogManager->planSms($relative, $smsText, $this->getPlannedTime(), $this->getEventId($mainUser->id));
			}

		} catch (\Throwable $e) {
			\Yii::exception($e);
		}
	}

	/**
	 * @param PatientDao $mainUser
	 * @param PatientDao $relative
	 *
	 * @return bool
	 */
	protected function alreadySendSmsExists(PatientDao $mainUser,PatientDao $relative): bool
	{
		$smsBeforeEventSms = SmsLogDao::find()
			->sendForEvent($relative->id, $this->getEventId($mainUser->id))
			->one();
		return $smsBeforeEventSms ? true : false;
	}

	/**
	 * Подготавливает текст. Возвращает в массиве такст сообщения и событие, по которому текст сформирован
	 *  ['event'] - название событие, которое актуально на данный момент
	 *  ['text'] - текст смс сообщения, которое было сформировано для события event
	 *
	 * @param PatientDao $mainUser
	 * @param PatientDao $relative
	 * @param null|string $host
	 *
	 * @return array
	 * @throws \ddmp\common\exceptions\ValidationException
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\di\NotInstantiableException
	 */
	private function prepareRelativeProductSmsText(PatientDao $mainUser, PatientDao $relative, ?string $host = null): array
	{
		$products = $this->getOwnerToRelativeProducts($mainUser, $relative);
		//если удалили последний привязаный продукт, остается только отправить смс о том, что пользователь добавлен
		if (empty($products)) {
			$data = [
				'patient' => $mainUser
			];
			return [
				'event' => SmsTemplateEventEnum::RELATIVE_ADDED,
				'text' => $this->smsTemplateEventManager->buildText(
					SmsTemplateEventEnum::RELATIVE_ADDED,
					null,
					$this->prepareData($data)
				)
			];
		}

		$productNames = [];
		foreach ($products as $product) {
			$productNames[] = "'" . $product->name . "'";
		}
		$productNamesList = implode(', ', $productNames);

		$data = [
			'patient' => $mainUser,
			'product_names_list' => $productNamesList,
			'productsAuthLink' => $this->getAuthLink($relative, $host)
		];

		return [
			'event' => SmsTemplateEventEnum::RELATIVE_PRODUCTS_ADDED,
			'text' => $this->smsTemplateEventManager->buildText(
				SmsTemplateEventEnum::RELATIVE_PRODUCTS_ADDED,
				null,
				$this->prepareData($data)
			)
		];
	}

	/**
	 * @param PatientDao $relative
	 * @param null|string $host
	 *
	 * @return string
	 * @throws \ddmp\common\exceptions\ValidationException
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\di\NotInstantiableException
	 */
	protected function getAuthLink(PatientDao $relative, ?string $host = null): string
	{
		/** @var ShortLinkManager $shortLinkManager */
		$shortLinkManager = \Yii::$container->get(ShortLinkManager::class);
		$hash = $shortLinkManager->createAuthShortLink('/cabinet/products', $relative)->hash;
		if (!$host) {
			$host = Url::MAIN_HOST;
		}
		return $host . Url::toShortLinkPage($hash);
	}

	/**
	 * @param int $mainUserId
	 *
	 * @return string
	 */
	private function getEventId(int $mainUserId): string
	{
		return 'relative_' . $mainUserId;
	}

	/**
	 * Возвращает отложеное время для отправки
	 *
	 * @return \DateTime
	 * @throws \Exception
	 */
	protected function getPlannedTime(): \DateTime
	{
		$plannedTime = new \DateTime();
		return $plannedTime->add(new \DateInterval('PT' . self::INTERVAL_SMS_WAITS_MINUTES . 'M'));
	}

	/**
	 * Подготавливает данные перед отправкой в генератор текста смс
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	private function prepareData(array $data): array
	{
		if (isset($data['patient'])) {
			$data['patient']->phone = (new Phone($data['patient']->phone))->secreted();
		}
		return $data;
	}

	/**
	 * @param PatientDao $mainUser
	 * @param PatientDao $relative
	 *
	 * @return array
	 * @throws \Exception
	 */
	protected function getOwnerToRelativeProducts(PatientDao $mainUser, PatientDao $relative): array
	{
		$familyRelation = FamilyRelationDao::find()->byOwnerIdAndRelativeId($mainUser->id, $relative->id)->one();
		if ($familyRelation === null) {
			throw new \Exception("Не найден родственник");
		}

		$contracts = ContractDao::find()
			->byRelationId($familyRelation->id)
			->withProduct()
			->active()
			->all();

		$products = [];
		foreach ($contracts as $contract)
		{
			$products[] = $contract->product;
		}
		return $products;
	}

}