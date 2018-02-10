<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Factory;

use In2code\Lux\Domain\Model\Page;
use In2code\Lux\Domain\Model\Pagevisit;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Repository\PageRepository;
use In2code\Lux\Domain\Repository\VisitorRepository;
use In2code\Lux\Signal\SignalTrait;
use In2code\Lux\Utility\ConfigurationUtility;
use In2code\Lux\Utility\IpUtility;
use In2code\Lux\Utility\ObjectUtility;
use In2code\Lux\Domain\Service\ConfigurationService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class VisitorFactory to add a new visitor to database (if not yet stored).
 * In addition: track a new pagerequest of this visitor if pageUid>0 in constructor.
 */
class VisitorFactory
{
    use SignalTrait;

    /**
     * @var string
     */
    protected $idCookie = '';

    /**
     * @var int
     */
    protected $pageUid = 0;

    /**
     * @var string
     */
    protected $referrer = '';

    /**
     * @var VisitorRepository|null
     */
    protected $visitorRepository = null;

    /**
     * VisitorFactory constructor.
     *
     * @param string $idCookie
     * @param int $pageUid
     * @param string $referrer
     */
    public function __construct(string $idCookie, int $pageUid = 0, string $referrer = '')
    {
        $this->idCookie = $idCookie;
        $this->pageUid = $pageUid;
        $this->referrer = $referrer;
        $this->visitorRepository = ObjectUtility::getObjectManager()->get(VisitorRepository::class);
    }

    /**
     * @return Visitor
     */
    public function getVisitor(): Visitor
    {
        $visitor = $this->getVisitorFromDatabase();
        if ($visitor === null) {
            $visitor = $this->createNewVisitor();
            $this->trackPagevisit($visitor);
            $this->visitorRepository->add($visitor);
        } else {
            $this->trackPagevisit($visitor);
            $this->visitorRepository->update($visitor);
        }
        $this->setVisits($visitor);
        $this->visitorRepository->persistAll();
        return $visitor;
    }

    /**
     * @return Visitor|null
     */
    protected function getVisitorFromDatabase()
    {
        return $this->visitorRepository->findOneByIdCookie($this->idCookie);
    }

    /**
     * @return Visitor
     */
    protected function createNewVisitor(): Visitor
    {
        $visitor = GeneralUtility::makeInstance(Visitor::class);
        $visitor->setIdCookie($this->idCookie);
        $visitor->setUserAgent(GeneralUtility::getIndpEnv('HTTP_USER_AGENT'));
        $visitor->setReferrer($this->referrer);
        $this->enrichNewVisitorWithIpInformation($visitor);
        $this->signalDispatch(__CLASS__, 'newVisitor', [$visitor]);
        return $visitor;
    }

    /**
     * @return Pagevisit
     */
    protected function getPageVisit(): Pagevisit
    {
        $pageVisit = ObjectUtility::getObjectManager()->get(Pagevisit::class);
        $pageRepository = ObjectUtility::getObjectManager()->get(PageRepository::class);
        /** @var Page $page */
        $page = $pageRepository->findByUid($this->pageUid);
        $pageVisit->setPage($page);
        return $pageVisit;
    }

    /**
     * @param Visitor $visitor
     * @return void
     */
    protected function trackPagevisit(Visitor $visitor)
    {
        if ($this->pageUid > 0 && $this->shouldTrackPagevisits()) {
            $visitor->addPagevisit($this->getPageVisit());
            $this->signalDispatch(__CLASS__, 'trackPagevisit', [$visitor]);
        }
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

    /**
     * @param Visitor $visitor
     * @return void
     */
    protected function enrichNewVisitorWithIpInformation(Visitor $visitor)
    {
        if (ConfigurationUtility::isIpLoggingDisabled() === false) {
            $visitor->setIpAddress(IpUtility::getIpAddress());
            if (ConfigurationUtility::isIpInformationDisabled() === false) {
                $ipInformationFactory = ObjectUtility::getObjectManager()->get(IpinformationFactory::class);
                $objectStorage = $ipInformationFactory->getObjectStorageWithIpinformation();
                $visitor->setIpinformations($objectStorage);
            }
        }
    }

    /**
     * @param Visitor $visitor
     * @return void
     */
    protected function setVisits(Visitor $visitor)
    {
        $visitor->setVisits($visitor->getNumberOfUniquePagevisits());
        $this->signalDispatch(__CLASS__, 'setVisits', [$visitor]);
    }
}
