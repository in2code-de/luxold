<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Action\Handler;

use In2code\Lux\Domain\Action\AbstractAction;
use In2code\Lux\Domain\Action\ActionInterface;
use In2code\Lux\Domain\Model\Action;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Model\Workflow;
use In2code\Lux\Domain\Repository\WorkflowRepository;
use In2code\Lux\Domain\Service\LogService;
use In2code\Lux\Domain\Trigger\Handler\TriggerHandler;
use In2code\Lux\Utility\ObjectUtility;

/**
 * Class ActionHandler to call action classes and return action array as json for the visitors JavaScript
 */
class ActionHandler
{

    /**
     * @var array
     */
    protected $actions = [];

    /**
     * @param Visitor $visitor
     * @param string $controllerAction
     * @param array $actionArray
     * @return array
     */
    public function startActions(Visitor $visitor, string $controllerAction, array $actionArray): array
    {
        unset($actionArray);
        /** @var TriggerHandler $triggerHandler */
        $triggerHandler = ObjectUtility::getObjectManager()->get(TriggerHandler::class);
        /** @var WorkflowRepository $workflowRepository */
        $workflowRepository = ObjectUtility::getObjectManager()->get(WorkflowRepository::class);
        $workflows = $workflowRepository->findAll();
        foreach ($workflows as $workflow) {
            if ($triggerHandler->isRelevantTrigger($visitor, $workflow)) {
                $this->setActionsForWorkflow($workflow, $visitor, $controllerAction);
            }
        }
        return [$visitor, $controllerAction, $this->actions];
    }

    /**
     * @param Workflow $workflow
     * @param Visitor $visitor
     * @param string $controllerAction
     * @return void
     */
    protected function setActionsForWorkflow(Workflow $workflow, Visitor $visitor, string $controllerAction)
    {
        /** @var Action $action */
        foreach ($workflow->getActions() as $action) {
            $this->checkAction($action);
            /** @var AbstractAction $actionObject */
            $actionObject = ObjectUtility::getObjectManager()->get(
                $action->getClassName(),
                $workflow,
                $action,
                $visitor,
                $controllerAction
            );
            $actionPerformed = $actionObject->startAction();
            if ($actionPerformed === true) {
                $this->log($workflow, $visitor);
            }
            if ($actionObject->isTransmission()) {
                $this->addAction(
                    $actionObject->getTransmissionActionName(),
                    $actionObject->getTransmissionConfiguration(),
                    $workflow
                );
            }
        }
    }

    /**
     * @param Workflow $workflow
     * @param Visitor $visitor
     * @return void
     */
    protected function log(Workflow $workflow, Visitor $visitor)
    {
        /** @var LogService $logService */
        $logService = ObjectUtility::getObjectManager()->get(LogService::class);
        $logService->logAction($visitor, $workflow);
    }

    /**
     * @param string $name
     * @param array $configuration
     * @param Workflow $workflow
     * @return void
     */
    protected function addAction(string $name, array $configuration, Workflow $workflow)
    {
        $this->actions[] = [
            'action' => $name,
            'configuration' => $configuration,
            'workflow' => $workflow->getUid()
        ];
    }

    /**
     * @param Action $action
     * @return void
     */
    protected function checkAction(Action $action)
    {
        if ($action->getClassName() === '') {
            throw new \UnexpectedValueException('No action classname given', 1520020767);
        }
        if ($action->getConfiguration() === '') {
            throw new \UnexpectedValueException('No action configuration given', 1520020772);
        }
        if (!class_exists($action->getClassName())) {
            throw new \UnexpectedValueException(
                'Class ' . $action->getClassName() . ' does not exist or is not loaded',
                1520020783
            );
        }
        if (!is_subclass_of($action->getClassName(), ActionInterface::class)) {
            throw new \UnexpectedValueException(
                'Given actionclass does not implement interface ' . ActionInterface::class,
                1520020827
            );
        }
    }
}
