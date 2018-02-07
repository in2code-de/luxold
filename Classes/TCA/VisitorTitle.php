<?php
namespace In2code\Lux\TCA;

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
        $email = 'Unknown';
        if (!empty($properties['email'])) {
            $email = $properties['email'];
        }
        return $email;
    }
}
