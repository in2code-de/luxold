<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Trigger;

use In2code\Lux\Domain\Model\Trigger;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Model\Workflow;
use TYPO3\CMS\Core\Utility\ArrayUtility;

/**
 * Class AbstractTrigger
 */
abstract class AbstractTrigger implements TriggerInterface
{

    /**
     * @var Workflow
     */
    protected $workflow = null;

    /**
     * @var Trigger
     */
    protected $trigger = null;

    /**
     * @var Visitor
     */
    protected $visitor = null;

    /**
     * AbstractTrigger constructor.
     *
     * @param Workflow $workflow
     * @param Trigger $trigger
     * @param Visitor $visitor
     */
    public function __construct(Workflow $workflow, Trigger $trigger, Visitor $visitor)
    {
        $this->workflow = $workflow;
        $this->trigger = $trigger;
        $this->visitor = $visitor;
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

    /**
     * @return Workflow
     */
    final protected function getWorkflow(): Workflow
    {
        return $this->workflow;
    }

    /**
     * @return Trigger
     */
    final protected function getTrigger(): Trigger
    {
        return $this->trigger;
    }

    /**
     * @return Visitor
     */
    final protected function getVisitor(): Visitor
    {
        return $this->visitor;
    }

    /**
     * Get complete configuration (stored information in database)
     *
     * @return array
     */
    final protected function getConfiguration(): array
    {
        return $this->getTrigger()->getConfigurationAsArray();
    }

    /**
     * Get any stored information by given key
     *
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
     * Get any TypoScript settings from trigger configuration
     *
     * @param string $path "configuration.foo"
     * @return string
     */
    final protected function getSettingsByPath(string $path)
    {
        try {
            $value = ArrayUtility::getValueByPath($this->getTrigger()->getTriggerSettings(), $path, '.');
        } catch (\Exception $exception) {
            unset($exception);
            $value = '';
        }
        return $value;
    }
}
