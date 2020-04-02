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
$daoNameCases = $daoModel->getNameCases();
echo "<?php\n";
?>

namespace Simbirsoft\Marketplace\<?= $entityName ?>\AdminInterface;

use Simbirsoft\Marketplace\Common\MartketplaceAdminInterface;

/**
 * Описание интерфейса (табов и полей).
 *
 * Внимание! Класс автогенерируемый
 * Весь измененный код будет удалён
 *
 * {@inheritdoc}
 */
class <?= $entityName ?>AdminInterface extends MartketplaceAdminInterface
{
	/**
	* @return string
	*/
	public static function getDaoClass(): string
	{
		return '<?= $daoClass ?>';
	}

	/**
	* @inheritdoc
	*/
	public function helpers(): array
	{
		$daoModel = $this->buildDaoModel();
		$daoNameCases = $daoModel->getNameCases();

		return [
			<?= $entityName ?>ListHelper::class => [
				'BUTTONS' => [
					'LIST_CREATE_NEW' => [
						'TEXT' => "Добавить {$daoNameCases->getAccusative()}",
					],
				]
			],
			<?= $entityName ?>EditHelper::class => [
				'BUTTONS' => [
					'RETURN_TO_LIST' => [
						'TEXT' => "Список {$daoNameCases->getGenitivePlural()}",
					],
					'ADD_ELEMENT'    => [
						'TEXT' => "Создать {$daoNameCases->getAccusative()}",
					],
					'DELETE_ELEMENT' => [
						'TEXT' => "Удалить {$daoNameCases->getAccusative()}",
					],
				]
			],
		];
	}
}