<?php
namespace In2code\Lux\Utility;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/**
 * Class ConfigurationUtility
 */
class ConfigurationUtility
{

    /**
     * Check if disableLastLeadsBoxInPage is active
     *
     * @return bool
     */
    public static function isLastLeadsBoxInPageDisabled(): bool
    {
        $extensionConfig = self::getExtensionConfiguration();
        return $extensionConfig['disableLastLeadsBoxInPage'] === '1';
    }

    /**
     * Decide if TYPO3 8.7 is used or newer
     *
     * @return bool
     */
    public static function isTypo3OlderThen9(): bool
    {
        return VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) < 9000000;
    }

    /**
     * Get extension configuration from LocalConfiguration.php
     *
     * @return array
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected static function getTypo3ConfigurationVariables(): array
    {
        return (array)$GLOBALS['TYPO3_CONF_VARS'];
    }

    /**
     * Get extension configuration from LocalConfiguration.php
     *
     * @return array
     */
    protected static function getExtensionConfiguration(): array
    {
        $configuration = [];
        if (ConfigurationUtility::isTypo3OlderThen9()) {
            $configVariables = self::getTypo3ConfigurationVariables();
            // @extensionScannerIgnoreLine We still need to access extConf for TYPO3 8.7
            $possibleConfig = unserialize((string)$configVariables['EXT']['extConf']['lux']);
            if (!empty($possibleConfig) && is_array($possibleConfig)) {
                $configuration = $possibleConfig;
            }
        } else {
            $configuration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('lux');
        }
        return $configuration;
    }
}
