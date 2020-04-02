<?php

namespace ddmp\console\generators\bxadmin;

use ddmp\common\base\data\models\AdminDaoInterface;
use ddmp\common\utils\PhpUtils;
use ddmp\console\generators\bxentity\BitrixAdminEntityGenerator;
use yii\gii\CodeFile;
use yii\gii\Generator;

/**
 * Class BitrixAdminGenerator
 *
 * @package ddmp\console\generators\bxadmin
 */
class BitrixAdminGenerator extends Generator
{
	private const PATH_TO_MODULE = '@shop/public/local/modules/simbirsoft.marketplace/admin';

	/**
	 * @var array
	 */
	protected $menuData = [];

	/**
	 * @return array
	 */
	public function getMenuData(): array
	{
		return $this->menuData;
	}

	/**
	 * @return string name of the code generator
	 */
	public function getName()
	{
		return 'bxadmin';
	}

	/**
	 * Generates the code based on the current user input and the specified code template files.
	 * This is the main method that child classes should implement.
	 * Please refer to [[\yii\gii\generators\controller\Generator::generate()]] as an example
	 * on how to implement this method.
	 *
	 * @return CodeFile[] a list of code files to be created.
	 */
	public function generate()
	{
		$daoDir = \Yii::getAlias('@common/data/models');
		$phpUtils = PhpUtils::build();

		foreach (scandir($daoDir, 0) as $daoDirName) {
			if ($daoDirName === '.' || $daoDirName === '..') {
				continue;
			}

			$daoDirPath = $daoDir . '/' . $daoDirName;

			if (!is_dir($daoDirPath)) {
				continue;
			}

			$entityName = $phpUtils->uc_first($daoDirName);

			foreach (scandir($daoDirPath, 0) as $daoFileName) {
				$daoFilePath = $daoDirPath . '/' . $daoFileName;
				if (is_dir($daoFilePath)) {
					continue;
				}

				$daoClassName = mb_substr($daoFileName, 0, -4);
				if (mb_substr($daoClassName, -3, 3) !== 'Dao') {
					continue;
				}

				$daoClass = "ddmp\\common\\data\\models\\{$daoDirName}\\{$daoClassName}";
				if (!class_exists($daoClass)) {
					echo "Класс не найден {$daoClass}" . PHP_EOL . PHP_EOL;
					continue;
				}

				$adminDaoInterfaceClass = AdminDaoInterface::class;
				if (!class_implements($daoClass, $adminDaoInterfaceClass)) {
					echo "Класс {$daoClass} не реализует интерфейс {$adminDaoInterfaceClass}, который необходим для генерации админки" . PHP_EOL . PHP_EOL;
					continue;
				}

				/** @var AdminDaoInterface $daoModel */
				$daoModel = new $daoClass();
				$isShouldAdministrate = $daoModel->isAdministrating();

				if (!$isShouldAdministrate) {
					echo "Класс {$daoClass} не помечен, как администрируемый. см AdminDaoInterface::isAdministrating" . PHP_EOL . PHP_EOL;
					continue;
				}

				$entityName = mb_substr($daoClassName, 0, -3);

				$generator = new BitrixAdminEntityGenerator();
				$generator->entity = $entityName;
				$files = $generator->generate();
				$answers = [];
				foreach ($files as $generatedFile) {
					$answers[$generatedFile->id] = true;
				}

				$result = '';
				$saved = $generator->save($files, $answers, $result);

				if ($saved) {
					$this->addToMenu($daoModel, $entityName);
				}
			}
		}

		$menuPath = \Yii::getAlias(self::PATH_TO_MODULE . '/' . 'menu.php');
		$codeFile = new CodeFile($menuPath, $this->render('menu.php'));

		return [$codeFile];
	}

	/**
	 * @param AdminDaoInterface $daoModel
	 * @param string            $entityName
	 *
	 * @internal param $param
	 */
	private function addToMenu(AdminDaoInterface $daoModel, string $entityName): void
	{
		$this->menuData[] = [
			'text'   => $daoModel->getNameCases()->getNominativePlural(),
			'entity' => $entityName,
		];
	}
}