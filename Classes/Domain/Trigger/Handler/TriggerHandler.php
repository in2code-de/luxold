<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Trigger\Handler;

use In2code\Lux\Domain\Model\Visitor;

/**
 * Class TriggerHandler to handle all triggers and decide if there should be an action or not
 */
class TriggerHandler
{

    /**
     * Check all triggers of all workflows and decide if there should be an action or not
     *
     * @param Visitor $visitor
     * @return bool
     */
    public function getDecision(Visitor $visitor): bool
    {
        return true;
    }
}
