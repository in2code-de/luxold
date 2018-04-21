<?php
declare(strict_types=1);
namespace In2code\Lux\Controller;

use In2code\Lux\Domain\Model\Transfer\FilterDto;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Repository\CategoryRepository;
use In2code\Lux\Domain\Repository\PagevisitRepository;
use In2code\Lux\Domain\Repository\VisitorRepository;
use In2code\Lux\Utility\ObjectUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class LeadsController
 */
class LeadController extends ActionController
{

    /**
     * @var VisitorRepository
     */
    protected $visitorRepository = null;

    /**
     * @var PagevisitRepository
     */
    protected $pagevisitsRepository = null;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository = null;

    /**
     * @return void
     */
    public function initializeListAction()
    {
        $this->setFilterDto();
    }

    /**
     * @param FilterDto $filter
     * @param string $export
     * @return void
     * @throws StopActionException
     */
    public function listAction(FilterDto $filter, string $export = '')
    {
        if ($export === 'csv') {
            $this->forward('downloadCsv', null, null, ['filter' => $filter]);
        }
        $this->view->assignMultiple([
            'hottestVisitors' => $this->visitorRepository->findByHottestScorings($filter),
            'filter' => $filter,
            'allVisitors' => $this->visitorRepository->findAllWithIdentifiedFirst($filter),
            'identifiedByMostVisits' => $this->visitorRepository->findIdentifiedByMostVisits($filter),
            'numberOfVisitorsByDay' => $this->pagevisitsRepository->getNumberOfVisitorsByDay(),
            'luxCategories' => $this->categoryRepository->findAllLuxCategories()
        ]);
    }

    /**
     * @param FilterDto $filter
     * @return void
     */
    public function downloadCsvAction(FilterDto $filter)
    {
        $this->view->assignMultiple([
            'allVisitors' => $this->visitorRepository->findAllWithIdentifiedFirst($filter),
        ]);

        $this->response->setHeader('Content-Type', 'text/x-csv');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="Leads.csv"');
        $this->response->setHeader('Pragma', 'no-cache');
        $this->response->sendHeaders();
        echo $this->view->render();
        exit;
    }

    /**
     * @param Visitor $visitor
     * @return void
     */
    public function detailAction(Visitor $visitor)
    {
        $this->view->assign('visitor', $visitor);
    }

    /**
     * Really remove visitor completely from db (not only deleted=1)
     *
     * @param Visitor $visitor
     * @return void
     * @throws StopActionException
     * @throws UnsupportedRequestTypeException
     */
    public function removeAction(Visitor $visitor)
    {
        $this->visitorRepository->removeVisitorByVisitorUid($visitor->getUid());
        $this->visitorRepository->removeRelatedTableRowsByVisitorUid($visitor->getUid());
        $this->addFlashMessage('Visitor completely removed from database');
        $this->redirect('list');
    }

    /**
     * @param Visitor $visitor
     * @return void
     * @throws IllegalObjectTypeException
     * @throws StopActionException
     * @throws UnknownObjectException
     * @throws UnsupportedRequestTypeException
     */
    public function deactivateAction(Visitor $visitor)
    {
        $visitor->setHidden(true);
        $this->visitorRepository->update($visitor);
        $this->addFlashMessage('Visitor will be ignored from listing');
        $this->redirect('list');
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
            'EXT:lux/Resources/Private/Templates/Lead/DetailAjax.html'
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
     * Always set a default FilterDto even if there are no filter params. In addition remove categoryScoring with 0 to
     * avoid propertymapping exceptions
     *
     * @return void
     */
    protected function setFilterDto()
    {
        $filter = [];
        try {
            $filter = $this->request->getArgument('filter');
        } catch (\Exception $exception) {
            unset($exception);
            $this->request->setArgument('filter', ObjectUtility::getFilterDto());
        }
        if (array_key_exists('categoryScoring', $filter)
            && (is_array($filter['categoryScoring']) || $filter['categoryScoring'] === '')) {
            $filter['categoryScoring'] = 0;
            $this->request->setArgument('filter', $filter);
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
     * @param PagevisitRepository $pagevisitRepository
     * @return void
     */
    public function injectPagevisitRepository(PagevisitRepository $pagevisitRepository)
    {
        $this->pagevisitsRepository = $pagevisitRepository;
    }

    /**
     * @param CategoryRepository $categoryRepository
     * @return void
     */
    public function injectCategoryRepository(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }
}
