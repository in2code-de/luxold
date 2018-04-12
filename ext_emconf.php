<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "lux".
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'lux',
    'description' => 'Living User Experience - LUX - the marketing automation tool for TYPO3.',
    'category' => 'plugin',
    'version' => '1.16.1',
    'author' => 'Alex Kellner',
    'author_email' => 'alexander.kellner@in2code.de',
    'author_company' => 'in2code.de',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-8.99.99',
            'php' => '7.0.0-7.99.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    '_md5_values_when_last_written' => '',
];
