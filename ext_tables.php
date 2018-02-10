<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(
    function () {

        /**
         * Register Icons
         */
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Imaging\IconRegistry::class
        );
        $iconRegistry->registerIcon(
            'extension-lux-module',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:lux/Resources/Public/Icons/lux.svg']
        );

        /**
         * Include Plugins
         */
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('lux', 'Fe', 'Lux');

        /**
         * Include Modules
         */
        // Add Main module "LUX".
        // Acces to a main module is implicit, as soon as a user has access to at least one of its submodules.
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule(
            'lux',
            '',
            '',
            null,
            [
                'name' => 'lux',
                'labels' => 'LLL:EXT:lux/Resources/Private/Language/locallang_mod.xlf',
                'iconIdentifier' => 'extension-lux-module'
            ]
        );
        if (\In2code\Lux\Utility\ConfigurationUtility::isAnalysisModuleDisabled() === false) {
            // Add module for analysis
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'In2code.lux',
                'lux',
                'analysis',
                '',
                [
                    'Analysis' => 'dashboard'
                ],
                [
                    'access' => 'user,group',
                    'icon' => 'EXT:lux/Resources/Public/Icons/lux.svg',
                    'labels' => 'LLL:EXT:lux/Resources/Private/Language/locallang_mod_analysis.xlf',
                ]
            );
        }

        /**
         * Add TypoScript Static Template
         */
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
            'lux',
            'Configuration/TypoScript/',
            'Main TypoScript'
        );
    }
);
