<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Class Log
 */
class Log extends AbstractEntity
{
    const TABLE_NAME = 'tx_lux_domain_model_log';
    const STATUS_NEW = 1;
    const STATUS_IDENTIFIED = 2;
    const STATUS_ATTRIBUTE = 3;
    const STATUS_PAGEVISIT2 = 40;
    const STATUS_PAGEVISIT3 = 41;
    const STATUS_PAGEVISIT4 = 42;
    const STATUS_PAGEVISIT5 = 43;
}
