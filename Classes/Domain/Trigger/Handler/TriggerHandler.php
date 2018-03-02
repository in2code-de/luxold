<?php
declare(strict_types=1);

namespace In2code\Lux\Domain\Trigger\Handler;

use In2code\Lux\Domain\Model\Trigger;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Model\Workflow;
use In2code\Lux\Domain\Trigger\AbstractTrigger;
use In2code\Lux\Domain\Trigger\TriggerInterface;
use In2code\Lux\Utility\ObjectUtility;

/**
 * Class TriggerHandler to handle all triggers and decide if there should be an action or not
 */
class TriggerHandler
{

    /**
     * Check all triggers of all workflows and decide if there should be an action or not
     *
     * @param Visitor $visitor
     * @param Workflow $workflow
     * @return bool
     */
    public function isRelevantTrigger(Visitor $visitor, Workflow $workflow): bool
    {
        $moreTriggers = $result = false;
        /** @var Trigger $trigger */
        foreach ($workflow->getTriggers() as $trigger) {
            $this->checkTrigger($trigger);
            /** @var AbstractTrigger $triggerObject */
            $triggerObject = ObjectUtility::getObjectManager()->get(
                $trigger->getClassName(),
                $workflow,
                $trigger,
                $visitor
            );
            if ($moreTriggers === false) {
                $result = $triggerObject->checkIfIsTriggered();
                $moreTriggers = true;
            } else {
                if ($trigger->getConjunction() === 'AND') {
                    $result = $result && $triggerObject->checkIfIsTriggered();
                } else {
                    $result = $result || $triggerObject->checkIfIsTriggered();
                }
            }
        }
        return $result;
    }

    /**
     * @param Trigger $trigger
     * @return void
     */
    protected function checkTrigger(Trigger $trigger)
    {
        if ($trigger->getClassName() === '') {
            throw new \UnexpectedValueException('No trigger classname given', 1520005596);
        }
        if ($trigger->getConfiguration() === '') {
            throw new \UnexpectedValueException('No trigger configuration given', 1520005692);
        }
        if (!class_exists($trigger->getClassName())) {
            throw new \UnexpectedValueException(
                'Class ' . $trigger->getClassName() . ' does not exist or is not loaded',
                1520005733
            );
        }
        if (!is_subclass_of($trigger->getClassName(), TriggerInterface::class)) {
            throw new \UnexpectedValueException(
                'Given triggerclass does not implement interface ' . TriggerInterface::class,
                1520006065
            );
        }
    }
}
