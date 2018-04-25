<?php
namespace In2code\Lux\Utility;

use In2code\Lux\Domain\Model\Transfer\FilterDto;
use In2code\Lux\Domain\Service\ConfigurationService;
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
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        return $objectManager;
    }

    /**
     * @return ConfigurationService
     */
    public static function getConfigurationService(): ConfigurationService
    {
        /** @var ConfigurationService $configurationService */
        $configurationService = self::getObjectManager()->get(ConfigurationService::class);
        return $configurationService;
    }

    /**
     * @param int $period
     * @return FilterDto
     */
    public static function getFilterDto(int $period = FilterDto::PERIOD_ALL): FilterDto
    {
        /** @var FilterDto $filterDto */
        $filterDto = self::getObjectManager()->get(FilterDto::class, $period);
        return $filterDto;
    }
}
