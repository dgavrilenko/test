<?php
use ddmp\console\generators\bxentity\BitrixAdminEntityGenerator;

/**
 * @var BitrixAdminEntityGenerator $generator
 */

$entityName = $generator->getEntityName();
$daoClass = $generator->getDaoClass();
$daoModel = $generator->getDaoModel();
$tableNamespace = $generator->getEntityTableNamespace();
$tableClassName = $generator->getEntityTableClassName();
echo "<?php\n";
?>
namespace <?= $tableNamespace ?>;

use Simbirsoft\Marketplace\Common\MarketplaceDataManager;

/**
 * Class <?= $entityName ?>Table
 *
 * Внимание! Класс автогенерируемый
 * Весь измененный код будет удалён
 *
 * @package <?= $tableNamespace ?>
 *
 */
class <?= $tableClassName ?> extends MarketplaceDataManager
{
	/**
	 * @inheritdoc
	 */
	public static function getTableName()
	{
		return '<?= $daoClass::tableName(); ?>';
	}

	public static function getFilePath()
	{
		return __FILE__;
	}

	/**
	* @return string
	*/
	public static function getDaoClass(): string
	{
		return '<?= $daoClass ?>';
	}
}