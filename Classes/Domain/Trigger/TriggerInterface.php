<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Trigger;

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
     * @param array $settings
     * @param array $configuration
     */
    public function __construct(Workflow $workflow, array $settings, array $configuration);

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
