<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Repository;

use In2code\Lux\Utility\DatabaseUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Class CategoryRepository
 */
class CategoryRepository extends AbstractRepository
{

    /**
     * @return QueryResultInterface
     */
    public function findAllLuxCategories(): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->matching($query->equals('lux_category', true));
        $query->setOrderings(['title' => QueryInterface::ORDER_ASCENDING]);
        return $query->execute();
    }

    /**
     * @param int $pageIdentifier
     * @param int $categoryIdentifier
     * @return bool
     */
    public function isPageIdentifierRelatedToCategoryIdentifier(int $pageIdentifier, int $categoryIdentifier): bool
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable('sys_category_record_mm');
        $relations = $queryBuilder
            ->select('*')
            ->from('sys_category_record_mm')
            ->where('tablenames = "pages" and fieldname = "categories" and uid_local = '
                . (int)$categoryIdentifier . ' and uid_foreign = ' . (int)$pageIdentifier)
            ->execute()
            ->fetchAll();
        return !empty($relations[0]);
    }
}
