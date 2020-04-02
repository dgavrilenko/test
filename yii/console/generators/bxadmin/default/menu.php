<?php

use ddmp\console\generators\bxadmin\BitrixAdminGenerator;
use yii\helpers\ArrayHelper;

/**
 * @var BitrixAdminGenerator $generator
 */

$menuData = $generator->getMenuData();
$menuData = ArrayHelper::index($menuData, 'text');
ksort($menuData);
echo "<?php\n";
?>

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
<?php foreach ($menuData as $value): ?>
			[
				'parent_menu' => 'menu_marketplace',
				'sort'        => 300,
				'icon'        => 'sale_menu_icon_buyers',
				'page_icon'   => 'fileman_sticker_icon',
				'text'        => '<?= $value['text'] ?>',
				'url'         => \Simbirsoft\Marketplace\<?= $value['entity'] ?>\AdminInterface\<?= $value['entity'] ?>ListHelper::getUrl(),
				'more_url'    => array(
					\Simbirsoft\Marketplace\<?= $value['entity'] ?>\AdminInterface\<?= $value['entity'] ?>EditHelper::getUrl(),
				),
			],
<?php endforeach; ?>
		]
	)
);