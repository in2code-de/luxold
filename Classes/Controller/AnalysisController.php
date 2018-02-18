<?php
declare(strict_types=1);
namespace In2code\Lux\Controller;

use In2code\Lux\Domain\Model\Transfer\FilterDto;
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
    public function initializeDashboardAction()
    {
        $this->setFilterDto();
    }

    /**
     * @param FilterDto|null $filter
     * @return void
     */
    public function dashboardAction(FilterDto $filter)
    {
        $numberOfUniqueSiteVisitors = $this->visitorRepository->findByUniqueSiteVisits($filter)->count();
        $numberOfRecurringSiteVisitors = $this->visitorRepository->findByRecurringSiteVisits($filter)->count();
        $numberOfIdentifiedVisitors = $this->visitorRepository->findIdentified($filter)->count();
        $numberOfUnknownVisitors = $this->visitorRepository->findUnknown($filter)->count();
        $interestingLogs = $this->logRepository->findInterestingLogs($filter);
        $countries = $this->ipinformationRepository->findAllCountryCodesGrouped($filter);
        $latestPagevisits = $this->pagevisitsRepository->findLatestPagevisits($filter);
        $identifiedByMostVisits = $this->visitorRepository->findIdentifiedByMostVisits($filter);
        $this->view->assignMultiple([
            'filter' => $filter,
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
     * @return void
     */
    public function initializeListAction()
    {
        $this->setFilterDto();
    }

    /**
     * @param FilterDto $filter
     * @return void
     */
    public function listAction(FilterDto $filter)
    {
        $allVisitors = $this->visitorRepository->findAllWithIdentifiedFirst();
        $identifiedByMostVisits = $this->visitorRepository->findIdentifiedByMostVisits($filter);
        $numberOfVisitorsByDay = $this->pagevisitsRepository->getNumberOfVisitorsByDay();
        $this->view->assignMultiple([
            'allVisitors' => $allVisitors,
            'identifiedByMostVisits' => $identifiedByMostVisits,
            'numberOfVisitorsByDay' => $numberOfVisitorsByDay,
        ]);
    }

    /**
     * Always set a FilterDto even if there are no filter params
     *
     * @return void
     */
    protected function setFilterDto()
    {
        try {
            $this->request->getArgument('filter');
        } catch (\Exception $exception) {
            unset($exception);
            $this->request->setArgument('filter', $this->objectManager->get(FilterDto::class));
        }
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
