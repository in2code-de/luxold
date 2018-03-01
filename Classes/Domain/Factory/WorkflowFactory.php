<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Factory;

use In2code\Lux\Domain\Model\Trigger;
use In2code\Lux\Domain\Model\Workflow;
use In2code\Lux\Utility\ObjectUtility;

/**
 * Class WorkflowFactory
 */
class WorkflowFactory
{

    /**
     * @return Workflow
     */
    public function getWorkflowFromArguments(array $workflow, array $trigger, array $actions): Workflow
    {
        /** @var Workflow $newWorkflow */
        $newWorkflow = ObjectUtility::getObjectManager()->get(Workflow::class);
        $newWorkflow->setTitle($workflow['title']);
        $newWorkflow->setDescription($workflow['description']);
        $this->addTriggers($newWorkflow, $trigger);
        return $newWorkflow;
    }

    /**
     * @param Workflow $workflow
     * @param array $trigger
     * @return void
     */
    protected function addTriggers(Workflow $workflow, array $trigger)
    {
        foreach ($trigger as $triggerItem) {
            $this->checkTrigger($triggerItem);
            /** @var Trigger $trigger */
            $trigger = ObjectUtility::getObjectManager()->get(Trigger::class);
            $trigger->setClassName($triggerItem['className']);
            $trigger->setConfigurationFromArray($triggerItem['configuration']);
            $workflow->addTrigger($trigger);
        }
    }

    /**
     * @param array $triggerItem
     * @return void
     */
    protected function checkTrigger(array $triggerItem)
    {
        if (empty($triggerItem['className'])) {
            throw new \UnexpectedValueException('No classname given', 1519928150);
        }
        if (empty($triggerItem['configuration']) || !is_array($triggerItem['configuration'])) {
            throw new \UnexpectedValueException('No configuration given', 1519928166);
        }
        if (!class_exists($triggerItem['className'])) {
            throw new \UnexpectedValueException(
                'Class ' . $triggerItem['className'] . ' does not exist',
                1519928321
            );
        }
    }
}
