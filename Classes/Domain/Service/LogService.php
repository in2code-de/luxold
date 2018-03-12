<?php
declare(strict_types=1);

namespace In2code\Lux\Domain\Service;

use In2code\Lux\Domain\Model\Action;
use In2code\Lux\Domain\Model\Download;
use In2code\Lux\Domain\Model\Log;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Model\Workflow;
use In2code\Lux\Domain\Repository\LogRepository;
use In2code\Lux\Domain\Repository\VisitorRepository;
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
     * @param Visitor $visitor
     * @return void
     */
    public function logIdentifiedVisitorByEmail4Link(Visitor $visitor)
    {
        $this->log(Log::STATUS_IDENTIFIED_EMAIL4LINK, $visitor);
    }

    /**
     * @param Visitor $visitor
     * @param string $href
     * @return void
     */
    public function logEmail4LinkEmail(Visitor $visitor, string $href)
    {
        $this->log(Log::STATUS_IDENTIFIED_EMAIL4LINK_SENDEMAIL, $visitor, ['href' => $href]);
    }

    /**
     * @param Visitor $visitor
     * @param string $href
     * @return void
     */
    public function logEmail4LinkEmailFailed(Visitor $visitor, string $href)
    {
        $this->log(Log::STATUS_IDENTIFIED_EMAIL4LINK_SENDEMAILFAILED, $visitor, ['href' => $href]);
    }

    /**
     * @param Download $download
     * @return void
     */
    public function logDownload(Download $download)
    {
        $this->log(Log::STATUS_DOWNLOAD, $download->getVisitor(), ['href' => $download->getHref()]);
    }

    /**
     * @param Visitor $visitor
     * @param int $shownContentUid
     * @param int $containerContentUid
     * @return void
     */
    public function logContextualContent(Visitor $visitor, int $shownContentUid, int $containerContentUid)
    {
        $this->log(
            Log::STATUS_CONTEXTUAL_CONTENT,
            $visitor,
            [
                'shownContentUid' => $shownContentUid,
                'containerContentUid' => $containerContentUid
            ]
        );
    }

    /**
     * @param Visitor $visitor
     * @param Workflow $workflow
     * @return void
     */
    public function logAction(Visitor $visitor, Workflow $workflow)
    {
        $this->log(
            Log::STATUS_ACTION,
            $visitor,
            ['workflowUid' => $workflow->getUid(), 'workflowTitle' => $workflow->getTitle()]
        );
    }

    /**
     * @param int $status
     * @param Visitor $visitor
     * @param array $properties
     * @return void
     */
    protected function log(int $status, Visitor $visitor, array $properties = [])
    {
        $logRepository = ObjectUtility::getObjectManager()->get(LogRepository::class);
        $visitorRepository = ObjectUtility::getObjectManager()->get(VisitorRepository::class);

        $log = ObjectUtility::getObjectManager()->get(Log::class)->setStatus($status)->setProperties($properties);
        $logRepository->add($log);
        $visitor->addLog($log);
        if ($visitor->getUid() > 0) {
            $visitorRepository->update($visitor);
        } else {
            $visitorRepository->add($visitor);
        }
        $logRepository->persistAll();
    }
}
