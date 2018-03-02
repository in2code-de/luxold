<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Action\Handler;

use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Model\Workflow;
use In2code\Lux\Domain\Repository\WorkflowRepository;
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
                    $this->setActionsForWorkflow($workflow);
                }
            }
        }
        return [$visitor, $actionName, $this->actions];
    }

    /**
     * @param Workflow $workflow
     * @return void
     */
    protected function setActionsForWorkflow(Workflow $workflow)
    {
        $this->addAction('testAction', ['foo' => 'bar']);
    }

    /**
     * @param string $name
     * @param array $configuration
     * @return void
     */
    protected function addAction(string $name, array $configuration)
    {
        $this->actions[] = [
            'action' => $name,
            'configuration' => $configuration
        ];
    }

    /**
     * @param string $actionName
     * @return bool
     */
    protected function isAllowedStartAction(string $actionName): bool
    {
        return $actionName === 'pageRequestAction';
    }
}
