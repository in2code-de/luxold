<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Repository;

use In2code\Lux\Domain\Model\Transfer\FilterDto;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Class PagevisitRepository
 */
class PagevisitRepository extends AbstractRepository
{

    /**
     * @param FilterDto $filter
     * @return QueryResultInterface
     */
    public function findLatestPagevisits(FilterDto $filter)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd([
                $query->greaterThan('crdate', $filter->getStartTimeForFilter()),
                $query->lessThan('crdate', $filter->getEndTimeForFilter())
            ])
        );
        $query->setLimit(4);
        return $query->execute();
    }

    /**
     * Get the number of visitors of the last 8 days
     *      Example return
     *          [10,52,8,54,536,15,55,44] or
     *          [numberVisitorsToday,numberVisitorsYesterday,...]
     *
     * @return array
     */
    public function getNumberOfVisitorsByDay(): array
    {
        $frames = [
            [
                new \DateTime('today midnight'),
                new \DateTime()
            ],
            [
                new \DateTime('yesterday midnight'),
                new \DateTime('today midnight')
            ],
            [
                new \DateTime('2 days ago midnight'),
                new \DateTime('yesterday midnight')
            ],
            [
                new \DateTime('3 days ago midnight'),
                new \DateTime('2 days ago midnight')
            ],
            [
                new \DateTime('4 days ago midnight'),
                new \DateTime('3 days ago midnight')
            ],
            [
                new \DateTime('5 days ago midnight'),
                new \DateTime('4 days ago midnight')
            ],
            [
                new \DateTime('6 days ago midnight'),
                new \DateTime('5 days ago midnight')
            ],
            [
                new \DateTime('7 days ago midnight'),
                new \DateTime('6 days ago midnight')
            ]
        ];
        $frames = array_reverse($frames);
        $visits = [];
        foreach ($frames as $frame) {
            $query = $this->createQuery();
            $query->matching(
                $query->logicalAnd([
                    $query->greaterThan('crdate', $frame[0]),
                    $query->lessThan('crdate', $frame[1])
                ])
            );
            $visits[] = $query->execute()->count();
        }
        return $visits;
    }
}
