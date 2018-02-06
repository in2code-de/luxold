<?php
namespace In2code\Lux\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class ObjectUtility
 */
class ObjectUtility
{

    /**
     * @return ObjectManager
     */
    public static function getObjectManager(): ObjectManager
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }
}
