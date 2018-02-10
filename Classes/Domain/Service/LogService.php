<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Service;

use In2code\Lux\Domain\Model\Log;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Utility\ObjectUtility;

/**
 * Class LogService
 */
class LogService
{

    /**
     * @param Visitor $visitor
     * @return void
     */
    public function logNewVisitor(Visitor $visitor)
    {
        $this->log(Log::STATUS_NEW, $visitor);
    }

    /**
     * @param Visitor $visitor
     * @return void
     */
    public function logIdentifiedVisitor(Visitor $visitor)
    {
        $this->log(Log::STATUS_IDENTIFIED, $visitor);
    }

    /**
     * @param int $status
     * @param Visitor $visitor
     * @return void
     */
    protected function log(int $status, Visitor $visitor)
    {
        $log = ObjectUtility::getObjectManager()->get(Log::class)->setStatus($status);
        $visitor->addLog($log);
    }
}
