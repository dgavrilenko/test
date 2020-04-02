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

use ddmp\common\base\data\models\AdminDaoInterface;
use ddmp\common\data\models\BaseDao;
use DigitalWand\AdminHelper\Helper\AdminEditHelper;
use Simbirsoft\Marketplace\<?= $entityName ?>\<?= $entityName ?>Table;

/**
 * Хелпер описывает интерфейс, выводящий форму редактирования.
 *
 * Внимание! Класс автогенерируемый
 * Весь измененный код будет удалён
 *
 * {@inheritdoc}
 */
class <?= $entityName ?>EditHelper extends AdminEditHelper
{
	protected static $model = <?= $entityName ?>Table::class;

	/**
	 * @inheritdoc
	 */
	public function setTitle($title)
	{
		$daoModel = $this->buildDaoModel();
		$nameCases = $daoModel->getNameCases();

		if (!empty($this->data)) {
			$title = "{$nameCases->getNominative()}: {$this->data['ID']}";
		} else {
			$title = $nameCases->getNominative();
		}

		parent::setTitle($title);
	}

	/**
	* @return BaseDao
	*/
	public function buildDaoModel(): BaseDao
	{
		$daoClass = static::getDaoClass();
		/** @var BaseDao $daoModel */
		$daoModel = new $daoClass();
		$daoModel->setScenario(AdminDaoInterface::SCENARIO_ADMIN_EDIT);

		return $daoModel;
	}

	/**
	* @return string
	*/
	public static function getDaoClass(): string
	{
		return '<?= $daoClass ?>';
	}
}