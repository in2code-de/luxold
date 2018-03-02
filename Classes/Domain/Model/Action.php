<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Model;

use In2code\Lux\Domain\Action\Helper\ActionService;
use In2code\Lux\Utility\ObjectUtility;
use In2code\Lux\Utility\StringUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class Action
 */
class Action extends AbstractEntity
{
    const TABLE_NAME = 'tx_lux_domain_model_action';

    /**
     * @var string
     */
    protected $className = '';

    /**
     * @var string
     */
    protected $configuration = '';

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param string $className
     * @return $this
     */
    public function setClassName(string $className)
    {
        $this->className = $className;
        return $this;
    }

    /**
     * @return string
     */
    public function getConfiguration(): string
    {
        return $this->configuration;
    }

    /**
     * @return array
     */
    public function getConfigurationAsArray(): array
    {
        $configuration = $this->getConfiguration();
        if (StringUtility::isJsonArray($configuration)) {
            return json_decode($configuration, true);
        }
        return [];
    }

    /**
     * @param string $configuration
     * @return $this
     */
    public function setConfiguration(string $configuration)
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * @param array $configuration
     * @return $this
     */
    public function setConfigurationFromArray(array $configuration)
    {
        $this->configuration = json_encode($configuration);
        return $this;
    }

    /**
     * @return array
     */
    public function getActionSettings(): array
    {
        /** @var ActionService $actionService */
        $actionService = ObjectUtility::getObjectManager()->get(ActionService::class);
        return $actionService->getActionSettingsFromClassName($this->getClassName());
    }

    /**
     * @param int $index
     * @return string
     */
    public function renderAction(int $index): string
    {
        $actionSettings = $this->getActionSettings();
        /** @var StandaloneView $view */
        $view = ObjectUtility::getObjectManager()->get(StandaloneView::class);
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($actionSettings['templateFile']));
        $view->assignMultiple([
            'index' => $index,
            'actionSettings' => $actionSettings,
            'configuration' => $this->getConfigurationAsArray(),
            'trigger' => $this
        ]);
        return $view->render();
    }
}
