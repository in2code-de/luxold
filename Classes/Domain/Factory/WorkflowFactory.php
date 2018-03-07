<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Factory;

use In2code\Lux\Domain\Model\Action;
use In2code\Lux\Domain\Model\Trigger;
use In2code\Lux\Domain\Model\Workflow;
use In2code\Lux\Domain\Repository\UserRepository;
use In2code\Lux\Utility\BackendUtility;
use In2code\Lux\Utility\ObjectUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class WorkflowFactory
 */
class WorkflowFactory
{

    /**
     * @param Workflow $workflow
     * @param array $trigger
     * @param array $action
     * @return Workflow
     */
    public function getUpdatedWorkflowFromArguments(Workflow $workflow, array $trigger, array $action): Workflow
    {
        $this->enrichWorkflow($workflow);
        $this->addTriggers($workflow, $trigger);
        $this->addActions($workflow, $action);
        return $workflow;
    }

    /**
     * @param array $workflow
     * @param array $trigger
     * @param array $action
     * @return Workflow
     */
    public function getNewWorkflowFromArguments(array $workflow, array $trigger, array $action): Workflow
    {
        /** @var Workflow $newWorkflow */
        $newWorkflow = ObjectUtility::getObjectManager()->get(Workflow::class);
        $newWorkflow->setTitle($workflow['title']);
        $newWorkflow->setDescription($workflow['description']);
        $this->enrichWorkflow($newWorkflow);
        $this->addTriggers($newWorkflow, $trigger);
        $this->addActions($newWorkflow, $action);
        return $newWorkflow;
    }

    /**
     * @param Workflow $newWorkflow
     * @return void
     */
    protected function enrichWorkflow(Workflow $newWorkflow)
    {
        $userRepository = ObjectUtility::getObjectManager()->get(UserRepository::class);
        $newWorkflow->setCruserId($userRepository->findByUid(BackendUtility::getPropertyFromBackendUser()));
    }

    /**
     * @param Workflow $workflow
     * @param array $trigger
     * @return void
     */
    protected function addTriggers(Workflow $workflow, array $trigger)
    {
        $workflow->setTriggers(new ObjectStorage());
        foreach ($trigger as $triggerItem) {
            $this->checkTrigger($triggerItem);
            /** @var Trigger $trigger */
            $trigger = ObjectUtility::getObjectManager()->get(Trigger::class);
            $trigger->setClassName($triggerItem['className']);
            $trigger->setConfigurationFromArray((array)$triggerItem['configuration']);
            $trigger->setConjunction($triggerItem['conjunction']);
            $workflow->addTrigger($trigger);
        }
    }

    /**
     * @param Workflow $workflow
     * @param array $action
     * @return void
     */
    protected function addActions(Workflow $workflow, array $action)
    {
        $workflow->setActions(new ObjectStorage());
        foreach ($action as $actionItem) {
            $this->checkAction($actionItem);
            /** @var Action $action */
            $action = ObjectUtility::getObjectManager()->get(Action::class);
            $action->setClassName($actionItem['className']);
            $action->setConfigurationFromArray($actionItem['configuration']);
            $workflow->addAction($action);
        }
    }

    /**
     * @param array $triggerItem
     * @return void
     */
    protected function checkTrigger(array $triggerItem)
    {
        if (empty($triggerItem['className'])) {
            throw new \UnexpectedValueException('No classname given', 1520367239);
        }
        if (!class_exists($triggerItem['className'])) {
            throw new \UnexpectedValueException(
                'Class ' . $triggerItem['className'] . ' does not exist',
                1519928321
            );
        }
    }

    /**
     * @param array $actionItem
     * @return void
     */
    protected function checkAction(array $actionItem)
    {
        if (empty($actionItem['className'])) {
            throw new \UnexpectedValueException('No classname given', 1520367241);
        }
        if (empty($actionItem['configuration']) || !is_array($actionItem['configuration'])) {
            throw new \UnexpectedValueException('No configuration given', 1520367245);
        }
        if (!class_exists($actionItem['className'])) {
            throw new \UnexpectedValueException(
                'Class ' . $actionItem['className'] . ' does not exist',
                1520367248
            );
        }
    }
}
