<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Repository;

use In2code\Lux\Domain\Model\Ipinformation;
use In2code\Lux\Domain\Model\Transfer\FilterDto;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Class IpinformationRepository
 */
class IpinformationRepository extends AbstractRepository
{

    /**
     * Get an array with number of visitors depending to the country where they came from
     *  Example return value:
     *  [
     *      'de' => 234,
     *      'at' => 45,
     *      'ch' => 11
     *  ]
     * @param FilterDto $filter
     * @return array
     */
    public function findAllCountryCodesGrouped(FilterDto $filter): array
    {
        $query = $this->createQuery();
        $logicalAnd = [$query->equals('name', 'countryCode')];
        $logicalAnd = $this->extendLogicalAndWithFilterConstraints($filter, $query, $logicalAnd);
        $query->matching($query->logicalAnd($logicalAnd));
        $ipinformations = $query->execute();

        $countryCodes = [];
        /** @var Ipinformation $ipinformation */
        foreach ($ipinformations as $ipinformation) {
            $countryCode = $ipinformation->getValue();
            if (!array_key_exists($countryCode, $countryCodes)) {
                $countryCodes[$countryCode] = 1;
            } else {
                $countryCodes[$countryCode]++;
            }
        }
        arsort($countryCodes);
        return $countryCodes;
    }

    /**
     * @param FilterDto $filter
     * @param QueryInterface $query
     * @param array $logicalAnd
     * @return array
     */
    protected function extendLogicalAndWithFilterConstraints(
        FilterDto $filter,
        QueryInterface $query,
        array $logicalAnd
    ): array {
        $logicalAnd[] = $query->greaterThan('crdate', $filter->getStartTimeForFilter());
        $logicalAnd[] = $query->lessThan('crdate', $filter->getEndTimeForFilter());
        return $logicalAnd;
    }
}
