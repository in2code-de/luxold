<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Action;

use In2code\Lux\Domain\Model\Action;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Model\Workflow;

/**
 * Class AbstractAction
 */
abstract class AbstractAction implements ActionInterface
{

    /**
     * @var Workflow
     */
    protected $workflow = null;

    /**
     * @var Action
     */
    protected $action = null;

    /**
     * @var Visitor
     */
    protected $visitor = null;

    /**
     * Transmit any value to clientside
     *
     * @var bool
     */
    protected $transmission = false;

    /**
     * @var string
     */
    protected $transmissionActionName = '';

    /**
     * @var array
     */
    protected $transmissionConfiguration = [];

    /**
     * AbstractAction constructor.
     *
     * @param Workflow $workflow
     * @param Action $action
     * @param Visitor $visitor
     */
    public function __construct(Workflow $workflow, Action $action, Visitor $visitor)
    {
        $this->workflow = $workflow;
        $this->action = $action;
        $this->visitor = $visitor;
    }

    /**
     * @return void
     */
    final public function startAction()
    {
        $this->initialize();
        $this->doAction();
        $this->afterAction();
    }

    /**
     * @return void
     */
    public function initialize()
    {
    }

    /**
     * @return void
     */
    public function doAction()
    {
    }

    /**
     * @return void
     */
    public function afterAction()
    {
    }

    /**
     * @return Workflow
     */
    final protected function getWorkflow(): Workflow
    {
        return $this->workflow;
    }

    /**
     * @return Action
     */
    final protected function getAction(): Action
    {
        return $this->action;
    }

    /**
     * @return Visitor
     */
    final protected function getVisitor(): Visitor
    {
        return $this->visitor;
    }

    /**
     * Get complete configuration
     *
     * @return array
     */
    final protected function getConfiguration(): array
    {
        return $this->getAction()->getConfigurationAsArray();
    }

    /**
     * @param string $key
     * @return string
     */
    final protected function getConfigurationByKey(string $key): string
    {
        $value = '';
        if (array_key_exists($key, $this->getConfiguration())) {
            $value = $this->getConfiguration()[$key];
        }
        return $value;
    }

    /**
     * @param string $transmissionActionName
     * @param array $transmissionConfiguration
     * @return void
     */
    final protected function setTransmission(string $transmissionActionName, array $transmissionConfiguration)
    {
        $this->transmission = true;
        $this->transmissionActionName = $transmissionActionName;
        $this->transmissionConfiguration = $transmissionConfiguration;
    }

    /**
     * @return string
     */
    public function getTransmissionActionName(): string
    {
        return $this->transmissionActionName;
    }

    /**
     * @return array
     */
    public function getTransmissionConfiguration(): array
    {
        return $this->transmissionConfiguration;
    }

    /**
     * @return bool
     */
    public function isTransmission(): bool
    {
        return $this->transmission;
    }
}
