<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Repository;

use In2code\Lux\Domain\Model\Download;
use In2code\Lux\Domain\Model\Transfer\FilterDto;
use In2code\Lux\Domain\Model\Visitor;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Class DownloadRepository
 */
class DownloadRepository extends AbstractRepository
{

    /**
     * Find all combined by href ordered by number of downloads with a limit of 100
     *
     * @param FilterDto $filter
     * @return array
     */
    public function findCombinedByHref(FilterDto $filter): array
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd([
                $query->greaterThan('crdate', $filter->getStartTimeForFilter()),
                $query->lessThan('crdate', $filter->getEndTimeForFilter())
            ])
        );
        $assets = $query->execute();
        $result = [];
        /** @var Download $asset */
        foreach ($assets as $asset) {
            $result[$asset->getHref()][] = $asset;
        }
        array_multisort(array_map('count', $result), SORT_DESC, $result);
        $result = array_slice($result, 0, 100);
        return $result;
    }

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
