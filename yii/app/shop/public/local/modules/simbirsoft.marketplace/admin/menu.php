<?php

use Bitrix\Main\Loader;

/*
 *
 * Внимание! Код автогенерируемый
 * Все изменения будут удалены
 *
 */

if (!Loader::includeModule('digitalwand.admin_helper') || !Loader::includeModule('simbirsoft.marketplace')) {
	return;
}

return array(
	array(
		'parent_menu' => 'global_menu_content',
		'sort'        => 300,
		'icon'        => 'sale_menu_icon_store',
		'page_icon'   => 'fileman_sticker_icon',
		'text'        => 'Marketplace',
		'items_id'    => 'menu_marketplace',
		'items'       => [
			[
				'parent_menu' => 'menu_marketplace',
				'sort'        => 300,
				'icon'        => 'sale_menu_icon_buyers',
				'page_icon'   => 'fileman_sticker_icon',
				'text'        => 'Sms шаблоны',
				'url'         => \Simbirsoft\Marketplace\SmsTemplate\AdminInterface\SmsTemplateListHelper::getUrl(),
				'more_url'    => array(
					\Simbirsoft\Marketplace\SmsTemplate\AdminInterface\SmsTemplateEditHelper::getUrl(),
				),
			],
			[
				'parent_menu' => 'menu_marketplace',
				'sort'        => 300,
				'icon'        => 'sale_menu_icon_buyers',
				'page_icon'   => 'fileman_sticker_icon',
				'text'        => 'Контракты',
				'url'         => \Simbirsoft\Marketplace\Contract\AdminInterface\ContractListHelper::getUrl(),
				'more_url'    => array(
					\Simbirsoft\Marketplace\Contract\AdminInterface\ContractEditHelper::getUrl(),
				),
			],
			[
				'parent_menu' => 'menu_marketplace',
				'sort'        => 300,
				'icon'        => 'sale_menu_icon_buyers',
				'page_icon'   => 'fileman_sticker_icon',
				'text'        => 'Партнёры',
				'url'         => \Simbirsoft\Marketplace\Partner\AdminInterface\PartnerListHelper::getUrl(),
				'more_url'    => array(
					\Simbirsoft\Marketplace\Partner\AdminInterface\PartnerEditHelper::getUrl(),
				),
			],
			[
				'parent_menu' => 'menu_marketplace',
				'sort'        => 300,
				'icon'        => 'sale_menu_icon_buyers',
				'page_icon'   => 'fileman_sticker_icon',
				'text'        => 'Пациенты',
				'url'         => \Simbirsoft\Marketplace\Patient\AdminInterface\PatientListHelper::getUrl(),
				'more_url'    => array(
					\Simbirsoft\Marketplace\Patient\AdminInterface\PatientEditHelper::getUrl(),
				),
			],
			[
				'parent_menu' => 'menu_marketplace',
				'sort'        => 300,
				'icon'        => 'sale_menu_icon_buyers',
				'page_icon'   => 'fileman_sticker_icon',
				'text'        => 'Продукты',
				'url'         => \Simbirsoft\Marketplace\Product\AdminInterface\ProductListHelper::getUrl(),
				'more_url'    => array(
					\Simbirsoft\Marketplace\Product\AdminInterface\ProductEditHelper::getUrl(),
				),
			],
			[
				'parent_menu' => 'menu_marketplace',
				'sort'        => 300,
				'icon'        => 'sale_menu_icon_buyers',
				'page_icon'   => 'fileman_sticker_icon',
				'text'        => 'Промо коды',
				'url'         => \Simbirsoft\Marketplace\PromoCode\AdminInterface\PromoCodeListHelper::getUrl(),
				'more_url'    => array(
					\Simbirsoft\Marketplace\PromoCode\AdminInterface\PromoCodeEditHelper::getUrl(),
				),
			],
			[
				'parent_menu' => 'menu_marketplace',
				'sort'        => 300,
				'icon'        => 'sale_menu_icon_buyers',
				'page_icon'   => 'fileman_sticker_icon',
				'text'        => 'События sms-шаблонов',
				'url'         => \Simbirsoft\Marketplace\SmsTemplateEvent\AdminInterface\SmsTemplateEventListHelper::getUrl(),
				'more_url'    => array(
					\Simbirsoft\Marketplace\SmsTemplateEvent\AdminInterface\SmsTemplateEventEditHelper::getUrl(),
				),
			],
			[
				'parent_menu' => 'menu_marketplace',
				'sort'        => 300,
				'icon'        => 'sale_menu_icon_buyers',
				'page_icon'   => 'fileman_sticker_icon',
				'text'        => 'Токены доступа',
				'url'         => \Simbirsoft\Marketplace\AuthToken\AdminInterface\AuthTokenListHelper::getUrl(),
				'more_url'    => array(
					\Simbirsoft\Marketplace\AuthToken\AdminInterface\AuthTokenEditHelper::getUrl(),
				),
			],
			[
				'parent_menu' => 'menu_marketplace',
				'sort'        => 300,
				'icon'        => 'sale_menu_icon_buyers',
				'page_icon'   => 'fileman_sticker_icon',
				'text'        => 'Услуги партнёров',
				'url'         => \Simbirsoft\Marketplace\PartnerService\AdminInterface\PartnerServiceListHelper::getUrl(),
				'more_url'    => array(
					\Simbirsoft\Marketplace\PartnerService\AdminInterface\PartnerServiceEditHelper::getUrl(),
				),
			],
			[
				'parent_menu' => 'menu_marketplace',
				'sort'        => 300,
				'icon'        => 'sale_menu_icon_buyers',
				'page_icon'   => 'fileman_sticker_icon',
				'text'        => 'Услуги продуктов',
				'url'         => \Simbirsoft\Marketplace\ProductService\AdminInterface\ProductServiceListHelper::getUrl(),
				'more_url'    => array(
					\Simbirsoft\Marketplace\ProductService\AdminInterface\ProductServiceEditHelper::getUrl(),
				),
			],
		]
	)
);