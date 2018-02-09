<?php
namespace In2code\Lux\TCA;

use In2code\Lux\Utility\LocalizationUtility;

/**
 * Class VisitorTitle
 */
class VisitorTitle
{

    /**
     * @param array $parameters
     * @param object $parentObject
     * @return void
     */
    public function getContactTitle(array &$parameters, $parentObject)
    {
        unset($parentObject);
        $parameters['title'] = $this->getEmail($parameters['row']) . ' (' . $parameters['row']['id_cookie'] . ')';
    }

    /**
     * @param array $properties
     * @return string
     */
    protected function getEmail(array $properties): string
    {
        $email = LocalizationUtility::translate('LLL:EXT:lux/Resources/Private/Language/locallang_db.xlf:anonym');
        if (!empty($properties['email'])) {
            $email = $properties['email'];
        }
        return $email;
    }
}
