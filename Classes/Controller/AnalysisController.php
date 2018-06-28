<?php
declare(strict_types=1);
namespace In2code\Lux\Controller;

use Doctrine\DBAL\DBALException;
use In2code\Lux\Domain\Model\Page;
use In2code\Lux\Domain\Model\Transfer\FilterDto;
use In2code\Lux\Domain\Repository\DownloadRepository;
use In2code\Lux\Domain\Repository\IpinformationRepository;
use In2code\Lux\Domain\Repository\LogRepository;
use In2code\Lux\Domain\Repository\PagevisitRepository;
use In2code\Lux\Domain\Repository\VisitorRepository;
use In2code\Lux\Utility\ObjectUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Generic\Exception\UnsupportedMethodException;

/**
 * Class AnalysisController
 */
class AnalysisController extends ActionController
{

    /**
     * @var VisitorRepository
     */
    protected $visitorRepository = null;

    /**
     * @var IpinformationRepository
     */
    protected $ipinformationRepository = null;

    /**
     * @var LogRepository
     */
    protected $logRepository = null;

    /**
     * @var PagevisitRepository
     */
    protected $pagevisitsRepository = null;

    /**
     * @var DownloadRepository
     */
    protected $downloadRepository = null;

    /**
     * @return void
     * @throws InvalidArgumentNameException
     */
    public function initializeDashboardAction()
    {
        $this->setFilterDto();
    }

    /**
     * @param FilterDto $filter
     * @return void
     * @throws InvalidQueryException
     * @throws DBALException
     */
    public function dashboardAction(FilterDto $filter)
    {
        $this->view->assignMultiple([
            'filter' => $filter,
            'hottestVisitors' => $this->visitorRepository->findByHottestScorings($filter),
            'numberOfUniqueSiteVisitors' => $this->visitorRepository->findByUniqueSiteVisits($filter)->count(),
            'numberOfRecurringSiteVisitors' => $this->visitorRepository->findByRecurringSiteVisits($filter)->count(),
            'numberOfIdentifiedVisitors' => $this->visitorRepository->findIdentified($filter)->count(),
            'numberOfUnknownVisitors' => $this->visitorRepository->findUnknown($filter)->count(),
            'interestingLogs' => $this->logRepository->findInterestingLogs($filter),
            'countries' => $this->ipinformationRepository->findAllCountryCodesGrouped($filter),
            'latestPagevisits' => $this->pagevisitsRepository->findLatestPagevisits($filter),
            'identifiedByMostVisits' => $this->visitorRepository->findIdentifiedByMostVisits($filter)
        ]);
    }

    /**
     * @return void
     * @throws InvalidArgumentNameException
     */
    public function initializeContentAction()
    {
        $this->setFilterDto();
    }

    /**
     * @param FilterDto $filter
     * @return void
     * @throws InvalidQueryException
     */
    public function contentAction(FilterDto $filter)
    {
        $this->view->assignMultiple([
            'filter' => $filter,
            'pages' => $this->pagevisitsRepository->findCombinedByPageIdentifier($filter),
            'downloads' => $this->downloadRepository->findCombinedByHref($filter),
            'numberOfVisitorsByDay' => $this->pagevisitsRepository->getNumberOfVisitorsByDay(),
            'numberOfDownloadsByDay' => $this->downloadRepository->getNumberOfDownloadsByDay(),
        ]);
    }

    /**
     * @param Page $page
     * @return void
     */
    public function detailPageAction(Page $page)
    {
        $this->view->assignMultiple([
            'pagevisits' => $this->pagevisitsRepository->findByPage($page)
        ]);
    }

    /**
     * @param string $href
     * @return void
     * @throws UnsupportedMethodException
     */
    public function detailDownloadAction(string $href)
    {
        $this->view->assignMultiple([
            'downloads' => $this->downloadRepository->findByHref($href)
        ]);
    }

    /**
     * Always set a default FilterDto even if there are no filter params
     *
     * @return void
     * @throws InvalidArgumentNameException
     */
    protected function setFilterDto()
    {
        try {
            $this->request->getArgument('filter');
        } catch (\Exception $exception) {
            unset($exception);
            $this->request->setArgument('filter', ObjectUtility::getFilterDto(FilterDto::PERIOD_THISYEAR));
        }
    }

    /**
     * @param VisitorRepository $visitorRepository
     * @return void
     */
    public function injectVisitorRepository(VisitorRepository $visitorRepository)
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

    /**
     * @param DownloadRepository $downloadRepository
     * @return void
     */
    public function injectDownloadRepository(DownloadRepository $downloadRepository)
    {
        $this->downloadRepository = $downloadRepository;
    }
}
