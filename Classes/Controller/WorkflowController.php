<?php
declare(strict_types=1);
namespace In2code\Lux\Controller;

use In2code\Lux\Domain\Action\Helper\ActionService;
use In2code\Lux\Domain\Factory\WorkflowFactory;
use In2code\Lux\Domain\Model\Workflow;
use In2code\Lux\Domain\Repository\WorkflowRepository;
use In2code\Lux\Domain\Trigger\Helper\TriggerService;
use In2code\Lux\Utility\LocalizationUtility;
use In2code\Lux\Utility\ObjectUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class WorkflowController
 */
class WorkflowController extends ActionController
{

    /**
     * @var WorkflowRepository
     */
    protected $workflowRepository = null;

    /**
     * @var TriggerService
     */
    protected $triggerService = null;

    /**
     * @var ActionService
     */
    protected $actionService = null;

    /**
     * WorkflowController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->triggerService = ObjectUtility::getObjectManager()->get(TriggerService::class);
        $this->actionService = ObjectUtility::getObjectManager()->get(ActionService::class);
    }

    /**
     * @return void
     */
    public function listAction()
    {
        $this->view->assign('workflows', $this->workflowRepository->findAll());
    }

    /**
     * @return void
     */
    public function newAction()
    {
        $this->view->assignMultiple([
            'triggers' => $this->triggerService->getAllTriggersAsOptions(),
            'actions' => $this->actionService->getAllActionsAsOptions()
        ]);
    }

    /**
     * @param array $workflow
     * @param array $trigger
     * @param array $actions
     * @return void
     * @throws IllegalObjectTypeException
     * @throws StopActionException
     * @throws UnsupportedRequestTypeException
     */
    public function createAction(array $workflow, array $trigger = [], array $actions = [])
    {
        /** @var WorkflowFactory $workflowFactory */
        $workflowFactory = ObjectUtility::getObjectManager()->get(WorkflowFactory::class);
        $this->workflowRepository->add($workflowFactory->getWorkflowFromArguments($workflow, $trigger, $actions));
        $this->addFlashMessage(
            LocalizationUtility::translate(
                'LLL:EXT:lux/Resources/Private/Language/locallang_db.xlf:module.workflow.new'
            )
        );
        $this->redirect('list');
    }

    /**
     * @param Workflow $workflow
     * @return void
     * @throws StopActionException
     * @throws UnsupportedRequestTypeException
     * @throws IllegalObjectTypeException
     */
    public function deleteAction(Workflow $workflow)
    {
        $this->workflowRepository->remove($workflow);
        $this->redirect('list');
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function addTriggerAjax(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $triggerSettings = $this->triggerService->getTriggerSettingsFromClassName(
            $request->getQueryParams()['trigger']
        );
        /** @var StandaloneView $view */
        $view = ObjectUtility::getObjectManager()->get(StandaloneView::class);
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($triggerSettings['templateFile']));
        $view->assignMultiple(['index' => $request->getQueryParams()['index'], 'triggerSettings' => $triggerSettings]);
        $response->getBody()->write(json_encode(['html' => $view->render()]));
        return $response;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function addActionAjax(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $actionSettings = $this->actionService->getActionSettingsFromClassName($request->getQueryParams()['action']);
        /** @var StandaloneView $view */
        $view = ObjectUtility::getObjectManager()->get(StandaloneView::class);
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($actionSettings['templateFile']));
        $view->assignMultiple(['index' => $request->getQueryParams()['index'], 'actionSettings' => $actionSettings]);
        $response->getBody()->write(json_encode(['html' => $view->render()]));
        return $response;
    }

    /**
     * @param WorkflowRepository $workflowRepository
     * @return void
     */
    public function injectWorkflowRepository(WorkflowRepository $workflowRepository)
    {
        $this->workflowRepository = $workflowRepository;
    }
}
