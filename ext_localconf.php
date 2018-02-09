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
         * Hooks
         */
        if (\In2code\Lux\Utility\ConfigurationUtility::isLastLeadsBoxInPageDisabled() === false) {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/db_layout.php']['drawHeaderHook'][]
                = \In2code\Lux\Hooks\PageLayoutHeader::class . '->render';
        }
    }
);
