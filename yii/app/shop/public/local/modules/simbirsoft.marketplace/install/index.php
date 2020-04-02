<?php

use Bitrix\Main\ModuleManager;
use Bitrix\Main\Loader;

class simbirsoft_marketplace extends CModule
{
	var $MODULE_ID = 'simbirsoft.marketplace';

	function __construct()
	{
		$arModuleVersion = array();

		include __DIR__ . '/version.php';

		if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}

		$this->MODULE_NAME = 'Администратор Marketplace';
		$this->MODULE_DESCRIPTION = 'Модуль для управления данными Marketplace';
		$this->PARTNER_NAME = 'Simbirsoft';
		$this->PARTNER_URI = '#';
	}

	public function DoInstall()
	{
		ModuleManager::registerModule($this->MODULE_ID);
		Loader::includeModule($this->MODULE_ID);
	}

	public function DoUninstall()
	{
		Loader::includeModule($this->MODULE_ID);
		ModuleManager::unRegisterModule($this->MODULE_ID);
	}
}