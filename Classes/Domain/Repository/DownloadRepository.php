<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Repository;

use In2code\Lux\Domain\Model\Visitor;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Class DownloadRepository
 */
class DownloadRepository extends AbstractRepository
{

    /**
     * Find all downloads of a visitor but with a given time. If a visitor would download an asset every single day
     * since a week ago (so also today) and the given time is yesterday, we want to get all downloads but not from
     * today.
     *
     * @param Visitor $visitor
     * @param \DateTime $time
     * @return QueryResultInterface
     */
    public function findByVisitorAndTime(Visitor $visitor, \DateTime $time): QueryResultInterface
    {
        $query = $this->createQuery();
        $logicalAnd = [
            $query->equals('visitor', $visitor),
            $query->lessThanOrEqual('crdate', $time)
        ];
        $query->matching($query->logicalAnd($logicalAnd));
        $query->setOrderings(['crdate' => QueryInterface::ORDER_DESCENDING]);
        return $query->execute();
    }
}
