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
     * @param string $actionName
     * @param array $actionArray
     * @return array
     */
    public function startActions(Visitor $visitor, string $actionName, array $actionArray): array
    {
        unset($actionArray);
        if ($this->isAllowedStartAction($actionName)) {
            /** @var TriggerHandler $triggerHandler */
            $triggerHandler = ObjectUtility::getObjectManager()->get(TriggerHandler::class);
            /** @var WorkflowRepository $workflowRepository */
            $workflowRepository = ObjectUtility::getObjectManager()->get(WorkflowRepository::class);
            $workflows = $workflowRepository->findAll();
            foreach ($workflows as $workflow) {
                if ($triggerHandler->isRelevantTrigger($visitor, $workflow)) {
                    $this->setActionsForWorkflow($workflow, $visitor);
                    $this->log($workflow, $visitor);
                }
            }
        }
        return [$visitor, $actionName, $this->actions];
    }

    /**
     * @param Workflow $workflow
     * @param Visitor $visitor
     * @return void
     */
    protected function setActionsForWorkflow(Workflow $workflow, Visitor $visitor)
    {
        /** @var Action $action */
        foreach ($workflow->getActions() as $action) {
            $this->checkAction($action);
            /** @var AbstractAction $actionObject */
            $actionObject = ObjectUtility::getObjectManager()->get(
                $action->getClassName(),
                $workflow,
                $action,
                $visitor
            );
            $actionObject->startAction();
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
     * Todo: Implement further startingpoints
     *
     * @param string $actionName
     * @return bool
     */
    protected function isAllowedStartAction(string $actionName): bool
    {
        return $actionName === 'pageRequestAction';
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
