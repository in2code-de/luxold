<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Action;

use In2code\Lux\Domain\Model\Workflow;

/**
 * Class AbstractAction
 */
abstract class AbstractAction
{

    /**
     * @var Workflow
     */
    protected $workflow = null;

    /**
     * TypoScript configuration
     *
     * @var array
     */
    protected $settings = [];

    /**
     * Configuration of workflow and trigger
     *
     * @var array
     */
    protected $configuration = [];

    /**
     * AbstractTrigger constructor.
     *
     * @param Workflow $workflow
     * @param array $settings
     * @param array $configuration
     */
    public function __construct(Workflow $workflow, array $settings, array $configuration)
    {
        $this->workflow = $workflow;
        $this->settings = $settings;
        $this->configuration = $configuration;
    }

    /**
     * @return bool
     */
    final public function checkIfIsTriggered(): bool
    {
        $this->initialize();
        $triggered = $this->isTriggered();
        $this->afterTrigger();
        return $triggered;
    }

    /**
     * @return void
     */
    public function initialize()
    {
    }

    /**
     * @return bool
     */
    public function isTriggered(): bool
    {
        return false;
    }

    /**
     * @return void
     */
    public function afterTrigger()
    {
    }
}
