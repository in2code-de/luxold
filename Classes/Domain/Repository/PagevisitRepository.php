<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Class PagevisitRepository
 */
class PagevisitRepository extends AbstractRepository
{

    /**
     * @return QueryResultInterface
     */
    public function findLatestPagevisits()
    {
        $query = $this->createQuery();
        $query->setLimit(4);
        return $query->execute();
    }
}
