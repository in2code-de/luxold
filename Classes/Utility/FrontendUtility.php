<?php
declare(strict_types=1);
namespace In2code\Lux\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FrontendUtility
 */
class FrontendUtility
{

    /**
     * @return string
     */
    public static function getActionName(): string
    {
        $action = '';
        $plugin = self::getPluginName();
        $arguments = GeneralUtility::_GPmerged($plugin);
        if (!empty($arguments['action'])) {
            $action = $arguments['action'];
        }
        return $action;
    }

    /**
     * @return string
     */
    public static function getPluginName(): string
    {
        $pluginName = 'tx_lux_lux_luxanalysis';
        if (!empty(GeneralUtility::_GPmerged('tx_lux_lux_luxworkflow'))) {
            $pluginName = 'tx_lux_lux_luxworkflow';
        }
        return $pluginName;
    }
}
