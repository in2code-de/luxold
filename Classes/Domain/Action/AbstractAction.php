<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Action;

use In2code\Lux\Domain\Model\Action;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Model\Workflow;
use In2code\Lux\Domain\Repository\LogRepository;
use In2code\Lux\Utility\ObjectUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;

/**
 * Class AbstractAction
 */
abstract class AbstractAction implements ActionInterface
{

    /**
     * Define and overwrite controller action on which your action should listen. Per default actions are only called
     * from pageRequestAction. In some cases (e.g. let's send an email on identification) it could be helpful to
     * also add another controller action as entry point.
     * Possible actions are:
     *  "pageRequestAction", "fieldListeningRequestAction", "email4LinkRequestAction", "downloadRequestAction"
     *
     * @var array
     */
    protected $controllerActions = [
        'pageRequestAction'
    ];

    /**
     * Current controllerAction
     *
     * @var string
     */
    protected $controllerAction = '';

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
     * @param string $controllerAction
     */
    public function __construct(Workflow $workflow, Action $action, Visitor $visitor, string $controllerAction)
    {
        $this->workflow = $workflow;
        $this->action = $action;
        $this->visitor = $visitor;
        $this->controllerAction = $controllerAction;
    }

    /**
     * @return bool
     */
    final public function startAction(): bool
    {
        $actionPerformed = false;
        if ($this->shouldPerform()) {
            $this->initialize();
            $actionPerformed = $this->doAction();
            $this->afterAction();
        }
        return $actionPerformed;
    }

    /**
     * @return void
     */
    public function initialize()
    {
    }

    /**
     * Return true if action was performed
     *
     * @return bool
     */
    public function doAction(): bool
    {
        return false;
    }

    /**
     * @return void
     */
    public function afterAction()
    {
    }

    /**
     * Perform action only if given controllerAction is a allowed starting point. In addition don't perform action
     * if workflow was already performed and configuration for recurring is turned to "single".
     *
     * @return bool
     * @throws InvalidQueryException
     */
    public function shouldPerform(): bool
    {
        $perform = false;
        if (in_array($this->controllerAction, $this->controllerActions)) {
            $perform = true;
            $configuration = $this->getAction()->getConfigurationAsArray();
            if (!empty($configuration['recurring']) && $configuration['recurring'] === 'single') {
                /** @var LogRepository $logRepository */
                $logRepository = ObjectUtility::getObjectManager()->get(LogRepository::class);
                $log = $logRepository->findOneByVisitorAndWorkflow($this->getVisitor(), $this->getWorkflow());
                $perform = $log === null;
            }
        }
        return $perform;
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
     * Get complete configuration (stored information in database)
     *
     * @return array
     */
    final protected function getConfiguration(): array
    {
        return $this->getAction()->getConfigurationAsArray();
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
     * Get any TypoScript settings from action configuration
     *
     * @param string $path "configuration.emailOverrides.senderEmail"
     * @return string
     */
    final protected function getSettingsByPath(string $path)
    {
        try {
            $value = ArrayUtility::getValueByPath($this->getAction()->getActionSettings(), $path, '.');
        } catch (\Exception $exception) {
            unset($exception);
            $value = '';
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
