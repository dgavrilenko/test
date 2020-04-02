<?php
namespace ddmp\common\data\models\serviceHint;

use ddmp\common\data\models\medCard\enums\MedCardAppointmentStateEnum;
use ddmp\common\data\models\medCard\MedCardAppointmentDao;
use ddmp\common\data\models\request\RequestDao;
use ddmp\common\data\models\serviceLog\ServiceLogDao;
use ddmp\common\enums\RequestStatusEnum;
use ddmp\common\enums\ServiceLogStatusEnum;
use ddmp\common\enums\ServiceTypeEnum;
use ddmp\common\utils\formatters\DateTimeFormat;
use ddmp\common\utils\helpers\Url;

/**
 * Подсказки для услуг продукта Теле-чекап
 *
 * Class TelecheckupServiceHint
 *
 * @package ddmp\common\data\models\serviceHint
 */
class TelecheckupServiceHint extends BaseServiceHint
{
	function getHintText(): string
	{
		if (ServiceTypeEnum::TELEMED == $this->partnerService->getType()) {
			$serviceLog = $this->getAnalysesServiceLog();
			if (!$serviceLog) {
				return 'Необходимо сдать анализы';
			}
			switch ($serviceLog->status) {
				case ServiceLogStatusEnum::NOT_REQUESTED:
					return 'Необходимо сдать анализы';
				case ServiceLogStatusEnum::REQUESTED:
					return 'Необходимы результаты анализов. Вы уже оставили <a target="_blank" href="' . Url::toCard() . '">заявку</a>';
				case ServiceLogStatusEnum::FINISHED:
					$date = new \DateTime($serviceLog->time);
					$date->add(new \DateInterval('P10D'));
					$dateFormat = new DateTimeFormat($date);
					return 'Использовать до '. $dateFormat->prettyMonthDateNoYear();
				case ServiceLogStatusEnum::INACTIVATED:
					$isActive = $this->contract->isServiceActive($serviceLog->partner_service_id);
					if ($isActive) {
						return 'Необходимо сдать анализы';
					}
					$request = RequestDao::find()
						->byContractId($this->contract->id)
						->byPartnerServiceId($serviceLog->partner_service_id)
						->sortUpdatedFirst()
						->one();
					$finishedAppointment = MedCardAppointmentDao::find()
						->byPartnerId($this->contract->patient_id)
						->byProduct($this->contract->product_id)
						->byState(MedCardAppointmentStateEnum::STATE_FINISHED)
						->updatedAfter($request->create_time)
						->one();
					if ($finishedAppointment) {
						return 'К <a target="_blank" href="' . Url::toCardFinished() . '">заключению</a> врача по результатам анализов';
					}
					return 'Результаты анализов получены более 10 дней назад и не актуальны.';
			}
		} elseif (ServiceTypeEnum::ANALYSES == $this->partnerService->getType()) {
			$request = RequestDao::find()
				->byContractId($this->contract->id)
				->byPartnerServiceId($this->partnerService->id)
				->sortUpdatedFirst()
				->one();
			if (!$request) {
				return '';
			}
			switch ($request->status) {
				case RequestStatusEnum::NEW:
					return 'Вы оставили <a target="_blank" href="' . Url::toCard() . '">заявку</a> на сдача анализов';
				case RequestStatusEnum::AWAITING:
					return 'Ваша <a target="_blank" href="' . Url::toCard() . '">заявка</a> ожидает подтверждения';
				case RequestStatusEnum::APPROVED:
					return 'Ваша <a target="_blank" href="' . Url::toCard() . '">заявка</a> подтверждена';
				case RequestStatusEnum::REJECTED:
					return 'Ваша заявка отклонена';
				case RequestStatusEnum::CONCLUDED:
					$isActive = $this->contract->isServiceActive($this->partnerService->id);
					if ($isActive) {
						return '';
					}
					return 'К <a target="_blank" href="' . Url::toCardFinished() . '">результатам</a> анализов';
			}
		}
	}

	/**
	 * @return ServiceLogDao|null
	 */
	private function getAnalysesServiceLog(): ?ServiceLogDao
	{
		return ServiceLogDao::find()
			->byContractId($this->contract->id)
			->byPartnerServiceType(ServiceTypeEnum::ANALYSES)
			->sortUpdatedFirst()
			->one();
	}
}