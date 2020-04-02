<?php

namespace ddmp\common\data\models\partner;

/**
 * Class PartnerManager
 *
 * @package ddmp\common\data\models\partner
 */
class PartnerManager
{
	const ID_TO_CODE_RELATION = [
		PartnerDao::PARTNER_ID_DOC_PLUS => PartnerDao::PARTNER_CODE_DOC_PLUS,
		PartnerDao::PARTNER_ID_DOCTOR_RYADOM => PartnerDao::PARTNER_CODE_DOCTOR_RYADOM,
		PartnerDao::PARTNER_ID_MMT => PartnerDao::PARTNER_CODE_MMT,
		PartnerDao::PARTNER_ID_MAT_I_DITYA => PartnerDao::PARTNER_CODE_MAT_I_DITYA,
		PartnerDao::PARTNER_ID_MPC => PartnerDao::PARTNER_CODE_MPC,
		PartnerDao::PARTNER_ID_IBOLIT => PartnerDao::PARTNER_CODE_IBOLIT,
	];

	/**
	 * @param int $partnerId
	 *
	 * @return null|string
	 */
	public function getCodeById(int $partnerId) : ?string
	{
		return $this->getIdToCodeRelations()[$partnerId] ?? null;
	}

	/**
	 * @return array
	 */
	protected function getIdToCodeRelations() : array
	{
		return self::ID_TO_CODE_RELATION;
	}
}