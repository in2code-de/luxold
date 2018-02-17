<?php
declare(strict_types=1);
namespace In2code\Lux\Command;

use In2code\Lux\Domain\Model\Attribute;
use In2code\Lux\Domain\Model\Download;
use In2code\Lux\Domain\Model\Ipinformation;
use In2code\Lux\Domain\Model\Log;
use In2code\Lux\Domain\Model\Pagevisit;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Repository\VisitorRepository;
use In2code\Lux\Utility\DatabaseUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/**
 * Class TaskCommandController
 */
class LuxCleanupCommandController extends CommandController
{

    /**
     * Remove all unknown visitors where the last update is older than a given timestamp
     *
     *      Remove all unknown visitors where the last update (tstamp) is older than a given timestamp
     *      !!! Really removes visitors and all rows from related tables from the database
     *
     * @param int $timestamp
     * @return void
     */
    public function removeUnknownVisitorsByAgeCommand(int $timestamp)
    {
        $visitorRepository = $this->objectManager->get(VisitorRepository::class);
        $visitors = $visitorRepository->findByLastChangeUnknown($timestamp);
        foreach ($visitors as $visitor) {
            $this->removeRelatedTableRowsByVisitorUid($visitor->getUid());
            $this->removeVisitorByVisitorUid($visitor->getUid());
        }
    }

    /**
     * Remove all visitors where the last update is older than a given timestamp
     *
     *      Remove all visitors where the last update (tstamp) is older than a given timestamp
     *      !!! Really removes visitors and all rows from related tables from the database
     *
     * @param int $timestamp
     * @return void
     */
    public function removeVisitorsByAgeCommand(int $timestamp)
    {
        $visitorRepository = $this->objectManager->get(VisitorRepository::class);
        $visitors = $visitorRepository->findByLastChange($timestamp);
        foreach ($visitors as $visitor) {
            $this->removeRelatedTableRowsByVisitorUid($visitor->getUid());
            $this->removeVisitorByVisitorUid($visitor->getUid());
        }
    }

    /**
     * Remove a visitor by a given UID
     *
     *      Remove a single visitor by a given UID
     *      !!! Really removes visitors and all rows from related tables from the database
     *
     * @param int $visitorUid
     * @return void
     */
    public function removeVisitorByUidCommand(int $visitorUid)
    {
        $this->removeRelatedTableRowsByVisitorUid($visitorUid);
        $this->removeVisitorByVisitorUid($visitorUid);
    }

    /**
     * @param int $visitorUid
     * @return void
     */
    protected function removeVisitorByVisitorUid(int $visitorUid)
    {
        $connection = DatabaseUtility::getConnectionForTable(Visitor::TABLE_NAME);
        $connection->query('delete from ' . Visitor::TABLE_NAME . ' where uid=' . (int)$visitorUid);
    }

    /**
     * @param int $visitorUid
     * @return void
     */
    protected function removeRelatedTableRowsByVisitorUid(int $visitorUid)
    {
        $tables = [
            Attribute::TABLE_NAME,
            Pagevisit::TABLE_NAME,
            Ipinformation::TABLE_NAME,
            Download::TABLE_NAME,
            Log::TABLE_NAME
        ];
        foreach ($tables as $table) {
            $connection = DatabaseUtility::getConnectionForTable($table);
            $connection->query('delete from ' . $table . ' where visitor=' . (int)$visitorUid);
        }
    }
}
