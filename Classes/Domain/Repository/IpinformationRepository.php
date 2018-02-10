<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Repository;

use In2code\Lux\Domain\Model\Ipinformation;

/**
 * Class IpinformationRepository
 */
class IpinformationRepository extends AbstractRepository
{

    /**
     * @return array
     */
    public function findAllCountryCodesGrouped(): array
    {
        $query = $this->createQuery();
        $query->matching($query->equals('name', 'countryCode'));
        $ipinformations = $query->execute();

        $countryCodes = [];
        /** @var Ipinformation $ipinformation */
        foreach ($ipinformations as $ipinformation) {
            $countryCode = $ipinformation->getValue();
            if (!in_array($countryCode, $countryCodes)) {
                $countryCodes[] = $countryCode;
            }
        }
        return $countryCodes;
    }
}
