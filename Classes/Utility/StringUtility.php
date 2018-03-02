<?php
namespace In2code\Lux\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class StringUtility
 */
class StringUtility
{

    /**
     * @param string $pathAndFilename
     * @return string
     */
    public static function getExtensionFromPathAndFilename(string $pathAndFilename): string
    {
        $path = parse_url($pathAndFilename, PHP_URL_PATH);
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Check if string starts with another string
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    public static function startsWith(string $haystack, string $needle): bool
    {
        return stristr($haystack, $needle) && strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    /**
     * Get current scheme, domain and path of the current installation
     *
     * @return string
     */
    public static function getCurrentUri(): string
    {
        $uri = '';
        $uri .= parse_url(GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'), PHP_URL_SCHEME);
        $uri .= '://' . GeneralUtility::getIndpEnv('HTTP_HOST') . '/';
        $uri .= rtrim(GeneralUtility::getIndpEnv('TYPO3_SITE_PATH'), '/');
        return $uri;
    }

    /**
     * @param string $string
     * @return bool
     */
    public static function isJsonArray(string $string): bool
    {
        if (!is_string($string)) {
            return false;
        }
        return is_array(json_decode($string, true));
    }
}
