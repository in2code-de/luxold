<?php
namespace In2code\Lux\Utility;

/**
 * Class FileUtility
 */
class FileUtility
{

    /**
     * @param string $pathAndFilename
     * @return string
     */
    public static function getFilenameFromPathAndFilename(string $pathAndFilename): string
    {
        $pathInfo = pathinfo($pathAndFilename);
        return $pathInfo['basename'];
    }
}
