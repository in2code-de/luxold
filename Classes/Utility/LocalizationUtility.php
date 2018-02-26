<?php
declare(strict_types=1);
namespace In2code\Lux\Utility;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility as LocalizationUtilityExtbase;

/**
 * Class LocalizationUtility
 */
class LocalizationUtility
{

    /**
     * @param string $key
     * @param string $extensionName
     * @param array|null $arguments
     * @return string|null
     */
    public static function translate(string $key, string $extensionName = 'Lux', array $arguments = null)
    {
        return LocalizationUtilityExtbase::translate($key, $extensionName, $arguments);
    }
}
