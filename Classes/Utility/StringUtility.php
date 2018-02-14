<?php
namespace In2code\Lux\Utility;

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
}
