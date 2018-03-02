<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Trigger;

use In2code\Lux\Domain\Model\Trigger;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Model\Workflow;

/**
 * Interface TriggerInterface
 */
interface TriggerInterface
{

    /**
     * TriggerInterface constructor.
     *
     * @param Workflow $workflow
     * @param Trigger $trigger
     * @param Visitor $visitor
     */
    public function __construct(Workflow $workflow, Trigger $trigger, Visitor $visitor);

    /**
     * @return void
     */
    public function initialize();

    /**
     * @return bool
     */
    public function isTriggered(): bool;

    /**
     * @return void
     */
    public function afterTrigger();
}
