<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Action\Helper;

use In2code\Lux\Domain\Service\ConfigurationService;
use In2code\Lux\Utility\LocalizationUtility;
use In2code\Lux\Utility\ObjectUtility;

/**
 * Class ActionService
 */
class ActionService
{

    /**
     * @return array
     */
    public function getAllActionsAsOptions(): array
    {
        $actions = $this->getAllActions();
        $options = [];
        foreach ($actions as $action) {
            $options[$action['className']] = LocalizationUtility::translate($action['title']);
        }
        return $options;
    }

    /**
     * Get complete settings from given className
     *
     *  example:
     *  [
     *      'title' => 'LLL:EXT:lux/Resources/Private/Language/locallang_db.xlf:action.email'
     *      'description' => 'LLL:EXT:lux/Resources/Private/Language/locallang_db.xlf:action.email.description'
     *      'className' => 'In2code\Lux\Domain\Action\EmailAction'
     *      'templateFile' => 'EXT:lux/Resources/Private/Templates/Workflow/Action/Email.html'
     *      'configuration' => []
     *  ]
     *
     * @param string $className
     * @return array
     */
    public function getActionSettingsFromClassName(string $className): array
    {
        $actions = $this->getAllActions();
        $actionSettings = [];
        foreach ($actions as $action) {
            if ($action['className'] === $className) {
                $actionSettings = $action;
            }
        }
        return $actionSettings;
    }

    /**
     * @return array
     */
    protected function getAllActions(): array
    {
        return ObjectUtility::getConfigurationService()->getTypoScriptSettingsByPath('workflow.actions');
    }
}
