<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Tracker;

use In2code\Lux\Domain\Model\Page;
use In2code\Lux\Domain\Model\Pagevisit;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Repository\PageRepository;
use In2code\Lux\Domain\Repository\VisitorRepository;
use In2code\Lux\Domain\Service\ConfigurationService;
use In2code\Lux\Signal\SignalTrait;
use In2code\Lux\Utility\ObjectUtility;

/**
 * Class PageTracker
 */
class PageTracker
{
    use SignalTrait;

    /**
     * @var VisitorRepository|null
     */
    protected $visitorRepository = null;

    /**
     * PageTracker constructor.
     */
    public function __construct()
    {
        $this->visitorRepository = ObjectUtility::getObjectManager()->get(VisitorRepository::class);
    }

    /**
     * @param Visitor $visitor
     * @param int $pageUid
     * @return void
     */
    public function trackPage(Visitor $visitor, int $pageUid)
    {
        if ($pageUid > 0 && $this->shouldTrackPagevisits()) {
            $visitor->addPagevisit($this->getPageVisit($pageUid));
            $visitor->setVisits($visitor->getNumberOfUniquePagevisits());
            $this->visitorRepository->update($visitor);
            $this->visitorRepository->persistAll();
            $this->signalDispatch(__CLASS__, 'trackPagevisit', [$visitor]);
        }
    }

    /**
     * @param int $pageUid
     * @return Pagevisit
     */
    protected function getPageVisit(int $pageUid): Pagevisit
    {
        $pageVisit = ObjectUtility::getObjectManager()->get(Pagevisit::class);
        $pageRepository = ObjectUtility::getObjectManager()->get(PageRepository::class);
        /** @var Page $page */
        $page = $pageRepository->findByUid($pageUid);
        $pageVisit->setPage($page);
        return $pageVisit;
    }

    /**
     * Check if tracking of pagevisits is turned on via TypoScript
     *
     * @return bool
     */
    protected function shouldTrackPagevisits(): bool
    {
        $configurationService = ObjectUtility::getObjectManager()->get(ConfigurationService::class);
        $settings = $configurationService->getTypoScriptSettings();
        return !empty($settings['tracking']['pagevisits']['_enable'])
            && $settings['tracking']['pagevisits']['_enable'] === '1';
    }
}
