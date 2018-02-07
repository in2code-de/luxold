<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Service;

use In2code\Lux\Utility\ObjectUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Service\TypoScriptService;

/**
 * Class ConfigurationService to get the typoscript configuration from extension and cache it for multiple calls
 */
class ConfigurationService implements SingletonInterface
{
    const EXTENSION_NAME = 'Lux';

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @param string $pluginName
     * @return array
     */
    public function getTypoScriptSettings($pluginName = 'Fe'): array
    {
        if (empty($this->settings[$pluginName])) {
            $this->settings[$pluginName] = $this->getTypoScriptSettingsFromOverallConfiguration($pluginName);
        }
        return $this->settings[$pluginName];
    }

    /**
     * @param string $pluginName
     * @return array
     */
    protected function getTypoScriptSettingsFromOverallConfiguration($pluginName): array
    {
        $configurationManager = ObjectUtility::getObjectManager()->get(ConfigurationManagerInterface::class);
        return (array)$configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            self::EXTENSION_NAME,
            $pluginName
        );
    }
}
