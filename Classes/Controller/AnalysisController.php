<?php
declare(strict_types=1);
namespace In2code\Lux\Controller;

use In2code\Lux\Domain\Model\Transfer\FilterDto;
use In2code\Lux\Domain\Repository\IpinformationRepository;
use In2code\Lux\Domain\Repository\LogRepository;
use In2code\Lux\Domain\Repository\PagevisitRepository;
use In2code\Lux\Domain\Repository\VisitorRepository;
use In2code\Lux\Utility\ObjectUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Fluid\View\StandaloneView;

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
        $visitors = $this->visitorRepository->findByHottestScorings($filter);
        $numberOfUniqueSiteVisitors = $this->visitorRepository->findByUniqueSiteVisits($filter)->count();
        $numberOfRecurringSiteVisitors = $this->visitorRepository->findByRecurringSiteVisits($filter)->count();
        $numberOfIdentifiedVisitors = $this->visitorRepository->findIdentified($filter)->count();
        $numberOfUnknownVisitors = $this->visitorRepository->findUnknown($filter)->count();
        $interestingLogs = $this->logRepository->findInterestingLogs($filter);
        $countries = $this->ipinformationRepository->findAllCountryCodesGrouped($filter);
        $latestPagevisits = $this->pagevisitsRepository->findLatestPagevisits($filter);
        $identifiedByMostVisits = $this->visitorRepository->findIdentifiedByMostVisits($filter);
        $this->view->assignMultiple([
            'visitors' => $visitors,
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
        $visitors = $this->visitorRepository->findByHottestScorings($filter);
        $allVisitors = $this->visitorRepository->findAllWithIdentifiedFirst($filter);
        $identifiedByMostVisits = $this->visitorRepository->findIdentifiedByMostVisits($filter);
        $numberOfVisitorsByDay = $this->pagevisitsRepository->getNumberOfVisitorsByDay();
        $this->view->assignMultiple([
            'visitors' => $visitors,
            'filter' => $filter,
            'allVisitors' => $allVisitors,
            'identifiedByMostVisits' => $identifiedByMostVisits,
            'numberOfVisitorsByDay' => $numberOfVisitorsByDay,
        ]);
    }

    /**
     * AJAX action to show a detail view
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function detailAjax(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $visitorRepository = ObjectUtility::getObjectManager()->get(VisitorRepository::class);
        $standaloneView = ObjectUtility::getObjectManager()->get(StandaloneView::class);
        $standaloneView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName(
            'EXT:lux/Resources/Private/Templates/Analysis/Detail.html'
        ));
        $standaloneView->assignMultiple([
            'visitor' => $visitorRepository->findByUid((int)$request->getQueryParams()['visitor'])
        ]);
        $response->getBody()->write(json_encode(['html' => $standaloneView->render()]));
        return $response;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     */
    public function detailDescriptionAjax(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        /** @var VisitorRepository $visitorRepository */
        $visitorRepository = ObjectUtility::getObjectManager()->get(VisitorRepository::class);
        $visitor = $visitorRepository->findByUid((int)$request->getQueryParams()['visitor']);
        $visitor->setDescription($request->getQueryParams()['value']);
        $visitorRepository->update($visitor);
        $visitorRepository->persistAll();
        return $response;
    }

    /**
     * Always set a default FilterDto even if there are no filter params
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
