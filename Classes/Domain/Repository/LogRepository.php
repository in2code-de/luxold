<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Repository;

use In2code\Lux\Domain\Model\Log;
use In2code\Lux\Domain\Model\Transfer\FilterDto;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Model\Workflow;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Class LogRepository
 */
class LogRepository extends AbstractRepository
{

    /**
     * @param FilterDto $filter
     * @return array|QueryResultInterface
     */
    public function findInterestingLogs(FilterDto $filter)
    {
        $query = $this->createQuery();
        $logicalAnd = [$query->greaterThan('status', Log::STATUS_DEFAULT)];
        $logicalAnd = $this->extendLogicalAndWithFilterConstraints($filter, $query, $logicalAnd);
        $query->matching($query->logicalAnd($logicalAnd));
        $query->setLimit(10);
        return $query->execute();
    }

    /**
     * @param Visitor $visitor
     * @param Workflow $workflow
     * @return Log|null
     */
    public function findOneByVisitorAndWorkflow(Visitor $visitor, Workflow $workflow)
    {
        $query = $this->createQuery();
        $logicalAnd = [
            $query->equals('visitor', $visitor),
            $query->like('properties', '%"workflowUid":' . $workflow->getUid() . '%')
        ];
        $query->matching($query->logicalAnd($logicalAnd));
        $query->setLimit(1);
        /** @var Log $log */
        $log = $query->execute()->getFirst();
        return $log;
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
        $logicalAnd[] = $query->greaterThan('crdate', $filter->getStartTimeForFilter());
        $logicalAnd[] = $query->lessThan('crdate', $filter->getEndTimeForFilter());
        return $logicalAnd;
    }
}
