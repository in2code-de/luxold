<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(
    function () {

        /**
         * Include Frontend Plugins
         */
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'In2code.lux',
            'Fe',
            [
                'Frontend' => 'pageRequest,fieldListeningRequest'
            ],
            [
                'Frontend' => 'pageRequest,fieldListeningRequest'
            ]
        );

        /**
         * Add page TSConfig
         */
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:lux/Configuration/TSConfig/Lux.typoscript">'
        );

        /**
         * Hooks
         */
        if (\In2code\Lux\Utility\ConfigurationUtility::isLastLeadsBoxInPageDisabled() === false) {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/db_layout.php']['drawHeaderHook'][]
                = \In2code\Lux\Hooks\PageLayoutHeader::class . '->render';
        }

        /**
         * Register Slots
         */
        $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
        );
        // Log: New visitor
        $signalSlotDispatcher->connect(
            \In2code\Lux\Domain\Factory\VisitorFactory::class,
            'newVisitor',
            \In2code\Lux\Domain\Trigger\LogTrigger::class,
            'logNewVisitor',
            false
        );
        // Log: Identified visitor
        $signalSlotDispatcher->connect(
            \In2code\Lux\Domain\Factory\AttributeFactory::class,
            'isIdentified',
            \In2code\Lux\Domain\Trigger\LogTrigger::class,
            'logIdentifiedVisitor',
            false
        );

        /**
         * CK editor configuration
         */
        $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['lux'] = 'EXT:lux/Configuration/Yaml/CkEditor.yaml';

        /**
         * Fluid Namespace
         */
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['lux'][] = 'In2code\Lux\ViewHelpers';
    }
);
