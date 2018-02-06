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
                'Frontend' => 'pageRequest'
            ],
            [
                'Frontend' => 'pageRequest'
            ]
        );
    }
);
