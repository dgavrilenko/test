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

namespace Simbirsoft\Marketplace\<?= $entityName ?>\AdminInterface;

use DigitalWand\AdminHelper\Helper\AdminListHelper;
use Simbirsoft\Marketplace\<?= $entityName ?>\<?= $entityName ?>Table;

/**
 * Хелпер описывает интерфейс, выводящий список.
 *
 * Внимание! Класс автогенерируемый
 * Весь измененный код будет удалён
 *
 * {@inheritdoc}
 */
class <?= $entityName ?>ListHelper extends AdminListHelper
{
	protected static $model = <?= $entityName ?>Table::class;
}