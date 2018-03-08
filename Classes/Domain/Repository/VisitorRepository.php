<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Repository;

use In2code\Lux\Domain\Model\Transfer\FilterDto;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Class VisitorRepository
 */
class VisitorRepository extends AbstractRepository
{

    /**
     * @param FilterDto $filter
     * @return QueryResultInterface
     */
    public function findAllWithIdentifiedFirst(FilterDto $filter): QueryResultInterface
    {
        $query = $this->createQuery();
        $logicalAnd = $this->extendLogicalAndWithFilterConstraints($filter, $query, []);
        $query->matching($query->logicalAnd($logicalAnd));
        $query->setOrderings([
            'identified' => QueryInterface::ORDER_DESCENDING,
            'scoring' => QueryInterface::ORDER_DESCENDING,
            'tstamp' => QueryInterface::ORDER_DESCENDING
        ]);
        return $query->execute();
    }

    /**
     * Find 5 hottest visitors
     *
     * @param FilterDto $filter
     * @return QueryResultInterface
     */
    public function findByHottestScorings(FilterDto $filter): QueryResultInterface
    {
        $query = $this->createQuery();
        $logicalAnd = $this->extendLogicalAndWithFilterConstraints($filter, $query, []);
        $query->matching($query->logicalAnd($logicalAnd));
        $query->setLimit(6);
        $query->setOrderings([
            'scoring' => QueryInterface::ORDER_DESCENDING,
            'tstamp' => QueryInterface::ORDER_DESCENDING
        ]);
        return $query->execute();
    }

    /**
     * @param string $email
     * @return QueryResultInterface
     */
    public function findDuplicatesByEmail(string $email): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->matching($query->equals('email', $email));
        $query->setOrderings(['crdate' => QueryInterface::ORDER_ASCENDING]);
        return $query->execute();
    }

    /**
     * Show the last three visitors of a visited page
     *
     * @param int $pageIdentifier
     * @return QueryResultInterface
     */
    public function findByVisitedPageIdentifier(int $pageIdentifier): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->matching($query->equals('pagevisits.page', $pageIdentifier));
        $query->setLimit(3);
        $query->setOrderings(['tstamp' => QueryInterface::ORDER_DESCENDING]);
        return $query->execute();
    }

    /**
     * @param FilterDto $filter
     * @return QueryResultInterface
     */
    public function findByUniqueSiteVisits(FilterDto $filter): QueryResultInterface
    {
        $query = $this->createQuery();
        $logicalAnd = [$query->equals('visits', 1)];
        $logicalAnd = $this->extendLogicalAndWithFilterConstraints($filter, $query, $logicalAnd);
        $query->matching($query->logicalAnd($logicalAnd));
        return $query->execute();
    }

    /**
     * @param FilterDto $filter
     * @return QueryResultInterface
     */
    public function findByRecurringSiteVisits(FilterDto $filter): QueryResultInterface
    {
        $query = $this->createQuery();
        $logicalAnd = [$query->greaterThan('visits', 1)];
        $logicalAnd = $this->extendLogicalAndWithFilterConstraints($filter, $query, $logicalAnd);
        $query->matching($query->logicalAnd($logicalAnd));
        return $query->execute();
    }

    /**
     * @param FilterDto $filter
     * @return QueryResultInterface
     */
    public function findIdentified(FilterDto $filter): QueryResultInterface
    {
        $query = $this->createQuery();
        $logicalAnd = [$query->equals('identified', true)];
        $logicalAnd = $this->extendLogicalAndWithFilterConstraints($filter, $query, $logicalAnd);
        $query->matching($query->logicalAnd($logicalAnd));
        return $query->execute();
    }

    /**
     * @param FilterDto $filter
     * @return QueryResultInterface
     */
    public function findUnknown(FilterDto $filter): QueryResultInterface
    {
        $query = $this->createQuery();
        $logicalAnd = [$query->equals('identified', false)];
        $logicalAnd = $this->extendLogicalAndWithFilterConstraints($filter, $query, $logicalAnd);
        $query->matching($query->logicalAnd($logicalAnd));
        return $query->execute();
    }

    /**
     * @param FilterDto $filter
     * @return QueryResultInterface
     */
    public function findIdentifiedByMostVisits(FilterDto $filter): QueryResultInterface
    {
        $query = $this->createQuery();
        $logicalAnd = [$query->equals('identified', true)];
        $logicalAnd = $this->extendLogicalAndWithFilterConstraints($filter, $query, $logicalAnd);
        $query->matching($query->logicalAnd($logicalAnd));
        $query->setLimit(4);
        $query->setOrderings(['visits' => QueryInterface::ORDER_DESCENDING]);
        return $query->execute();
    }

    /**
     * Find visitors where tstamp is older then given timestamp
     *
     * @param int $timestamp
     * @return QueryResultInterface
     */
    public function findByLastChange(int $timestamp): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->matching($query->lessThan('tstamp', $timestamp));
        return $query->execute();
    }

    /**
     * Find unknown visitors where tstamp is older then given timestamp
     *
     * @param int $timestamp
     * @return QueryResultInterface
     */
    public function findByLastChangeUnknown(int $timestamp): QueryResultInterface
    {
        $query = $this->createQuery();
        $logicalAnd = [
            $query->equals('identified', false),
            $query->lessThan('tstamp', $timestamp)
        ];
        $query->matching($query->logicalAnd($logicalAnd));
        return $query->execute();
    }

    /**
     * @param FilterDto $filter
     * @param QueryInterface $query
     * @param array $logicalAnd
     * @return array
     */
    protected function extendLogicalAndWithFilterConstraints(
        FilterDto $filter,
        QueryInterface $query,
        array $logicalAnd
    ): array {
        $logicalAnd[] = $query->greaterThan('pagevisits.crdate', $filter->getStartTimeForFilter());
        $logicalAnd[] = $query->lessThan('pagevisits.crdate', $filter->getEndTimeForFilter());
        if ($filter->getSearchterms() !== []) {
            $logicalOr = [];
            foreach ($filter->getSearchterms() as $searchterm) {
                $logicalOr[] = $query->like('email', '%' . $searchterm . '%');
                $logicalOr[] = $query->like('ipAddress', '%' . $searchterm . '%');
                $logicalOr[] = $query->like('referrer', '%' . $searchterm . '%');
                $logicalOr[] = $query->like('description', '%' . $searchterm . '%');
                $logicalOr[] = $query->like('attributes.value', '%' . $searchterm . '%');
            }
            $logicalAnd[] = $query->logicalOr($logicalOr);
        }
        if ($filter->getPid() !== '') {
            $logicalAnd[] = $query->equals('pagevisits.page.uid', (int)$filter->getPid());
        }
        if ($filter->getScoring() > 0) {
            $logicalAnd[] = $query->greaterThan('scoring', $filter->getScoring());
        }
        if ($filter->getCategoryScoring() !== null) {
            $logicalAnd[] = $query->equals('categoryscorings.category', $filter->getCategoryScoring());
        }
        return $logicalAnd;
    }
}
