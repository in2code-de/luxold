<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Repository;

use In2code\Lux\Domain\Model\Log;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Class LogRepository
 */
class LogRepository extends AbstractRepository
{

    /**
     * @return QueryResultInterface
     */
    public function findInterestingLogs()
    {
        $query = $this->createQuery();
        $query->matching($query->in('status', [Log::STATUS_NEW, Log::STATUS_IDENTIFIED]));
        $query->setLimit(10);
        return $query->execute();
    }
}
