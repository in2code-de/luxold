<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Trigger\Helper;

use In2code\Lux\Utility\LocalizationUtility;
use In2code\Lux\Utility\ObjectUtility;

/**
 * Class TriggerService
 */
class TriggerService
{

    /**
     * @return array
     */
    public function getAllTriggersAsOptions(): array
    {
        $triggers = $this->getAllTriggers();
        $options = [];
        foreach ($triggers as $trigger) {
            $options[$trigger['className']] = LocalizationUtility::translate($trigger['title']);
        }
        return $options;
    }

    /**
     * Get complete settings from given className
     *
     *  example:
     *  [
     *      'title' => 'LLL:EXT:lux/Resources/Private/Language/locallang_db.xlf:trigger.timeframe'
     *      'description' => 'LLL:EXT:lux/Resources/Private/Language/locallang_db.xlf:trigger.timeframe.description'
     *      'className' => 'In2code\Lux\Domain\Trigger\TimeFrameTrigger'
     *      'templateFile' => 'EXT:lux/Resources/Private/Templates/Workflow/Trigger/TimeFrame.html'
     *      'configuration' => []
     *  ]
     *
     * @param string $className
     * @return array
     */
    public function getTriggerSettingsFromClassName(string $className): array
    {
        $triggers = $this->getAllTriggers();
        $triggerSettings = [];
        foreach ($triggers as $trigger) {
            if ($trigger['className'] === $className) {
                $triggerSettings = $trigger;
            }
        }
        return $triggerSettings;
    }

    /**
     * @return array
     */
    protected function getAllTriggers(): array
    {
        return ObjectUtility::getConfigurationService()->getTypoScriptSettingsByPath('workflow.triggers');
    }
}
