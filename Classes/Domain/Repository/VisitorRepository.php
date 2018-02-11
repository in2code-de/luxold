<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Class VisitorRepository
 */
class VisitorRepository extends AbstractRepository
{

    /**
     * @param string $email
     * @return QueryResultInterface
     */
    public function findDuplicatesByEmail(string $email)
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
    public function findByVisitedPageIdentifier(int $pageIdentifier)
    {
        $query = $this->createQuery();
        $query->matching($query->equals('pagevisits.page', $pageIdentifier));
        $query->setLimit(3);
        return $query->execute();
    }

    /**
     * @return QueryResultInterface
     */
    public function findByUniqueSiteVisits()
    {
        $query = $this->createQuery();
        $query->matching($query->equals('visits', 1));
        return $query->execute();
    }

    /**
     * @return QueryResultInterface
     */
    public function findByRecurringSiteVisits()
    {
        $query = $this->createQuery();
        $query->matching($query->greaterThan('visits', 1));
        return $query->execute();
    }

    /**
     * @return QueryResultInterface
     */
    public function findIdentified()
    {
        $query = $this->createQuery();
        $query->matching($query->equals('identified', true));
        return $query->execute();
    }

    /**
     * @return QueryResultInterface
     */
    public function findUnknown()
    {
        $query = $this->createQuery();
        $query->matching($query->equals('identified', false));
        return $query->execute();
    }

    /**
     * @return QueryResultInterface
     */
    public function findIdentifiedByMostVisits()
    {
        $query = $this->createQuery();
        $query->matching($query->equals('identified', true));
        $query->setLimit(4);
        $query->setOrderings(['visits' => QueryInterface::ORDER_DESCENDING]);
        return $query->execute();
    }
}
