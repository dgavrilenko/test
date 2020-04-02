<?php

namespace ddmp\console\generators\bxentity;

use ddmp\common\base\data\models\AdminDaoInterface;
use ddmp\common\data\models\BaseDao;
use ddmp\common\exceptions\InternalException;
use ddmp\common\utils\PhpUtils;
use Yii;
use yii\gii\CodeFile;
use yii\gii\Generator;

/**
 * Class BitrixAdminGenerator
 *
 * @package ddmp\console\generators\bxentity
 */
class BitrixAdminEntityGenerator extends Generator
{
	private const PATH_TO_MODULE = '@shop/public/local/modules/simbirsoft.marketplace/lib';

	/**
	 * @var string
	 */
	public $entity;

	/**
	 * @var BaseDao
	 */
	private $daoModel;

	/**
	 * @return string name of the code generator
	 */
	public function getName()
	{
		return 'bxentity';
	}

	public function getEntityTableNamespace(): string
	{
		return "Simbirsoft\\Marketplace\\{$this->getEntityName()}";
	}

	public function getEntityTableClassName(): string
	{
		return $this->getEntityName() . 'Table';
	}

	/**
	 * @return string
	 */
	public function getEntityName(): string
	{
		return $this->entity;
	}

	/**
	 * @return string
	 */
	public function getDaoClass(): string
	{
		$phpUtils = PhpUtils::build();
		$entityName = $this->getEntityName();
		$lcFirstName = $phpUtils->lc_first($entityName);
		return "ddmp\\common\\data\\models\\{$lcFirstName}\\{$entityName}Dao";
	}

	/**
	 * @return AdminDaoInterface
	 */
	public function getDaoModel(): AdminDaoInterface
	{
		if ($this->daoModel === null) {
			$daoClass = $this->getDaoClass();
			$this->daoModel = new $daoClass();
		}

		return $this->daoModel;
	}

	/**
	 * Generates the code based on the current user input and the specified code template files.
	 * This is the main method that child classes should implement.
	 * Please refer to [[\yii\gii\generators\controller\Generator::generate()]] as an example
	 * on how to implement this method.
	 *
	 * @return CodeFile[] a list of code files to be created.
	 * @throws InternalException
	 */
	public function generate()
	{
		$entityName = mb_strtolower($this->getEntityName());

		$entityDir = Yii::getAlias(self::PATH_TO_MODULE . '/' . $entityName);

		if (!is_dir($entityDir) && !mkdir($entityDir) && !is_dir($entityDir)) {
			throw new InternalException(InternalException::ERROR_DENIED, "Не удалось создать папку: {$entityDir}");
		}

		$entityTableFilePath = "{$entityDir}/{$entityName}.php";

		$files = [
			new CodeFile($entityTableFilePath, $this->render('table.php')),
		];

		$helpersDir = __DIR__ . '/default/admininterface';

		if (!is_dir($helpersDir) && !mkdir($helpersDir) && !is_dir($helpersDir)) {
			throw new InternalException(InternalException::ERROR_DENIED, "Не удалось создать папку: {$helpersDir}");
		}

		foreach ($dirs = scandir($helpersDir, null) as $file) {
			if ($file === '.' || $file === '..') {
				continue;
			}

			$templateFilePath = $helpersDir . '/' . $file;
			$codeFilePath = $entityDir . '/admininterface/' . $entityName . $file;
			if (is_file($templateFilePath) && pathinfo($templateFilePath, PATHINFO_EXTENSION) === 'php') {
				$files[] = new CodeFile($codeFilePath, $this->render("admininterface/$file"));
			}
		}

		return $files;
	}
}