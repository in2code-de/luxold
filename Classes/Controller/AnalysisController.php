<?php
declare(strict_types=1);
namespace In2code\Lux\Controller;

use In2code\Lux\Domain\Repository\IpinformationRepository;
use In2code\Lux\Domain\Repository\LogRepository;
use In2code\Lux\Domain\Repository\PagevisitRepository;
use In2code\Lux\Domain\Repository\VisitorRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class AnalysisController
 */
class AnalysisController extends ActionController
{

    /**
     * @var VisitorRepository|null
     */
    protected $visitorRepository = null;

    /**
     * @var IpinformationRepository|null
     */
    protected $ipinformationRepository = null;

    /**
     * @var LogRepository|null
     */
    protected $logRepository = null;

    /**
     * @var PagevisitRepository|null
     */
    protected $pagevisitsRepository = null;

    /**
     * @return void
     */
    public function dashboardAction()
    {
        $numberOfUniqueSiteVisitors = $this->visitorRepository->findByUniqueSiteVisits()->count();
        $numberOfRecurringSiteVisitors = $this->visitorRepository->findByRecurringSiteVisits()->count();
        $numberOfIdentifiedVisitors = $this->visitorRepository->findIdentified()->count();
        $numberOfUnknownVisitors = $this->visitorRepository->findUnknown()->count();
        $interestingLogs = $this->logRepository->findInterestingLogs();
        $countries = $this->ipinformationRepository->findAllCountryCodesGrouped();
        $latestPagevisits = $this->pagevisitsRepository->findLatestPagevisits();
        $identifiedByMostVisits = $this->visitorRepository->findIdentifiedByMostVisits();
        $this->view->assignMultiple([
            'numberOfUniqueSiteVisitors' => $numberOfUniqueSiteVisitors,
            'numberOfRecurringSiteVisitors' => $numberOfRecurringSiteVisitors,
            'numberOfIdentifiedVisitors' => $numberOfIdentifiedVisitors,
            'numberOfUnknownVisitors' => $numberOfUnknownVisitors,
            'interestingLogs' => $interestingLogs,
            'countries' => $countries,
            'latestPagevisits' => $latestPagevisits,
            'identifiedByMostVisits' => $identifiedByMostVisits,
        ]);
    }

    /**
     * @param VisitorRepository $visitorRepository
     * @return void
     */
    public function injectFormRepository(VisitorRepository $visitorRepository)
    {
        $this->visitorRepository = $visitorRepository;
    }

    /**
     * @param IpinformationRepository $ipinformationRepository
     * @return void
     */
    public function injectIpinformationRepository(IpinformationRepository $ipinformationRepository)
    {
        $this->ipinformationRepository = $ipinformationRepository;
    }

    /**
     * @param LogRepository $logRepository
     * @return void
     */
    public function injectLogRepository(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    /**
     * @param PagevisitRepository $pagevisitRepository
     * @return void
     */
    public function injectPagevisitRepository(PagevisitRepository $pagevisitRepository)
    {
        $this->pagevisitsRepository = $pagevisitRepository;
    }
}
