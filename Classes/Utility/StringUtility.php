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

    /**
     * @param string $string
     * @param bool $toLower
     * @return string
     */
    public static function cleanString(string $string, bool $toLower = false): string
    {
        $string = preg_replace('/[^a-zA-Z0-9_-]/', '', $string);
        if ($toLower === true) {
            $string = strtolower($string);
        }
        return $string;
    }

    /**
     * create a random string
     *
     * @param int $length
     * @param bool $lowerAndUpperCase
     * @return string
     */
    public static function getRandomString(int $length = 32, bool $lowerAndUpperCase = true)
    {
        $characters = implode('', range(0, 9)) . implode('', range('a', 'z'));
        if ($lowerAndUpperCase) {
            $characters .= implode('', range('A', 'Z'));
        }
        $fileName = '';
        for ($i = 0; $i < $length; $i++) {
            $key = mt_rand(0, strlen($characters) - 1);
            $fileName .= $characters[$key];
        }
        return $fileName;
    }
}
