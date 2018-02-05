<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}
call_user_func(
    function () {
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
