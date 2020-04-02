<?php

use \Bitrix\Main\Localization\Loc;

$moduleId = "simbrirsoft.marketplace";

Loc::loadMessages(__FILE__);

\Bitrix\Main\Loader::includeModule($moduleId);

$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();

/*
 * Типы опций types: [checkbox, text, number]
 **/
$aTabs = [
	[
		'DIV' => 'sms-button',
		'TAB' => 'Sms-Кнопка',
		'TITLE' => 'Sms-Кнопка',
		'OPTIONS' => [
			[],
		],
		'MESSAGES' => [
			['FILE' => 'includes/sms-list.php']
		],
	],
];

/* Визуальный вывод */
$tabConrol = new CAdminTabControl('tabControl', $aTabs);

$tabConrol->Begin();
?>
	<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($request['mid'])?>&amp;lang=<?=$request['lang']?>" >
		<?
		foreach ($aTabs as $aTab) {
			if ($aTab['OPTIONS']) {
				$tabConrol->BeginNextTab();
				__AdmSettingsDrawList($moduleId, $aTab['OPTIONS']);
			}

			if ($aTab['MESSAGES']) {
				foreach ($aTab['MESSAGES'] as $item) {
					if ($item) {
						include(__DIR__ . '/' . $item['FILE']);
					}
				}
			}
		}

		$tabConrol->BeginNextTab();

		$tabConrol->Buttons();
		?>

		<?=bitrix_sessid_post();?>
		<input type="submit" name="Update" value="<?=Loc::getMessage("MAIN_SAVE")?>">
		<input type="reset" name="reset" value="Сбросить">
	</form>

<?
$tabConrol->End();
?>