<?php
declare(strict_types=1);
namespace In2code\Lux\Controller;

use In2code\Lux\Domain\Action\Helper\ActionService;
use In2code\Lux\Domain\Factory\WorkflowFactory;
use In2code\Lux\Domain\Model\Action;
use In2code\Lux\Domain\Model\Trigger;
use In2code\Lux\Domain\Model\Workflow;
use In2code\Lux\Domain\Repository\WorkflowRepository;
use In2code\Lux\Domain\Trigger\Helper\TriggerService;
use In2code\Lux\Utility\LocalizationUtility;
use In2code\Lux\Utility\ObjectUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;

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
        $this->workflowRepository->add($workflowFactory->getNewWorkflowFromArguments($workflow, $trigger, $actions));
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
     */
    public function editAction(Workflow $workflow)
    {
        $this->view->assignMultiple([
            'workflow' => $workflow,
            'action' => $this->actionMethodName,
            'triggers' => $this->triggerService->getAllTriggersAsOptions(),
            'actions' => $this->actionService->getAllActionsAsOptions()
        ]);
    }

    /**
     * @param Workflow $workflow
     * @param array $trigger
     * @param array $actions
     * @return void
     * @throws IllegalObjectTypeException
     * @throws StopActionException
     * @throws UnsupportedRequestTypeException
     */
    public function updateAction(Workflow $workflow, array $trigger = [], array $actions = [])
    {
        /** @var WorkflowFactory $workflowFactory */
        $workflowFactory = ObjectUtility::getObjectManager()->get(WorkflowFactory::class);
        $this->workflowRepository->add(
            $workflowFactory->getUpdatedWorkflowFromArguments($workflow, $trigger, $actions)
        );
        $this->addFlashMessage(
            LocalizationUtility::translate(
                'LLL:EXT:lux/Resources/Private/Language/locallang_db.xlf:module.workflow.update'
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
        /** @var Trigger $trigger */
        $trigger = ObjectUtility::getObjectManager()->get(Trigger::class);
        $trigger->setClassName($request->getQueryParams()['trigger']);
        $response->getBody()->write(json_encode(
            ['html' => $trigger->renderTrigger((int)$request->getQueryParams()['index'])]
        ));
        return $response;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function addActionAjax(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        /** @var Action $action */
        $action = ObjectUtility::getObjectManager()->get(Action::class);
        $action->setClassName($request->getQueryParams()['action']);
        $response->getBody()->write(json_encode(
            ['html' => $action->renderAction((int)$request->getQueryParams()['index'])]
        ));
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
