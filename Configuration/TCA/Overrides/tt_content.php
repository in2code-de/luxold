<?php
defined('TYPO3_MODE') || die();

/**
 * Register Plugins
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('lux', 'Pi1', 'Lux: TrackingOptOut');
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('lux', 'Pi2', 'Lux: Contextual Content');

/**
 * Disable not needed fields in tt_content
 */
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['lux_pi1'] = 'select_key,pages,recursive';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['lux_pi2'] = 'select_key,pages,recursive';

/**
 * Include Flexform
 */
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['lux_pi1'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'lux_pi1',
    'FILE:EXT:lux/Configuration/FlexForms/FlexFormPi1.xml'
);
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['lux_pi2'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'lux_pi2',
    'FILE:EXT:lux/Configuration/FlexForms/FlexFormPi2.xml'
);
