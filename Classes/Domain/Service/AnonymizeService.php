<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Service;

use Doctrine\DBAL\Query\QueryBuilder;
use In2code\Lux\Domain\Model\Attribute;
use In2code\Lux\Domain\Model\Ipinformation;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Utility\DatabaseUtility;
use In2code\Lux\Utility\StringUtility;
use TYPO3\CMS\Core\Database\Connection;

/**
 * Class AnonymizeService to really anonymize and overwrite all privacy values (for local development or for a
 * presentation)
 */
class AnonymizeService
{

    /**
     * Dummy firstnames
     *
     * @var array
     */
    protected $firstNames = [
        'Alex',
        'Alexander',
        'Stefan',
        'Sebastian',
        'Martin',
        'Markus',
        'Sandra',
        'Sybille',
        'Andrea',
        'Michael',
        'Melanie'
    ];

    /**
     * Dummy lastnames
     *
     * @var array
     */
    protected $lastNames = [
        'Müller',
        'Kellner',
        'Pohl',
        'Busemann',
        'Herrmann',
        'Köhler',
        'Muster',
        'Schmidt',
        'Meier',
        'Horn',
        'Ochs'
    ];

    /**
     * Dummy company names
     *
     * @var array
     */
    protected $companies = [
        'MMarkt',
        'Agentur XYZ',
        'InstaSeviceAgency',
        'in2code GmbH',
        'Master Ltd.',
        'Meier Wurstwaren & Co. Kg',
        'Holzverarbeitung Müller',
        'Friseursalon Hairbert',
        'Marketing Superhero Agency',
        'First Agency'
    ];

    /**
     * @var array
     */
    protected $toplevelDomains = [
        'org',
        'de',
        'com',
        'at',
        'ch',
        'org'
    ];

    /**
     * @var array
     */
    protected $referrers = [
        'https://t.co/Mk2LeGhnMJ',
        'https://www.google.de',
        'https://www.google.com',
        'https://typo3.org',
        'https://www.in2code.de',
        ''
    ];

    /**
     * @var array
     */
    protected $descriptions = [
        'This lead is a good lead because of...',
        ''
    ];

    /**
     * @return void
     */
    public function anonymizeAll()
    {
        $this->anonymizeIdentifiedVisitors();
        $this->anonymizeAttributes();
        $this->anonymizeIpinformation();
    }

    /**
     * @return void
     */
    protected function anonymizeIdentifiedVisitors()
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Visitor::TABLE_NAME, true);
        $rows = $queryBuilder
            ->select('*')
            ->from(Visitor::TABLE_NAME)
            ->where('identified=1')
            ->execute()
            ->fetchAll();
        foreach ($rows as $row) {
            $properties = [
                'email' => $this->getRandomEmail(),
                'id_cookie' => StringUtility::getRandomString(32, false),
                'ip_address' => '127.0.0.***',
                'referrer' => $this->getRandomReferrer(),
                'description' => $this->getRandomDescription()
            ];
            /** @var Connection $connection */
            $connection = DatabaseUtility::getConnectionForTable(Visitor::TABLE_NAME);
            $connection->query(
                'update ' . Visitor::TABLE_NAME . ' set ' . $this->getUpdateQueryFromProperties($properties)
                . ' where uid=' . $row['uid']
            )->execute();
        }
    }

    /**
     * @return void
     */
    protected function anonymizeAttributes()
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Attribute::TABLE_NAME, true);
        $rows = $queryBuilder
            ->select('*')
            ->from(Attribute::TABLE_NAME)
            ->execute()
            ->fetchAll();
        foreach ($rows as $row) {
            $value = StringUtility::getRandomString(8);
            if ($row['name'] === 'firstname') {
                $value = $this->getRandomFirstname();
            }
            if ($row['name'] === 'lastname') {
                $value = $this->getRandomLastname();
            }
            if ($row['name'] === 'email') {
                $value = $this->getRandomEmail();
            }
            if ($row['name'] === 'company') {
                $value = $this->getRandomCompanyname();
            }
            if ($row['value'] === '') {
                $value = '';
            }
            /** @var Connection $connection */
            $connection = DatabaseUtility::getConnectionForTable(Attribute::TABLE_NAME);
            $connection->query(
                'update ' . Attribute::TABLE_NAME . ' set value="' . $value . '" where uid=' . $row['uid']
            )->execute();
        }
    }

    /**
     * @return void
     */
    protected function anonymizeIpinformation()
    {
        /** @var Connection $connection */
        $connection = DatabaseUtility::getConnectionForTable(Ipinformation::TABLE_NAME);
        $connection->query(
            'delete from ' . Ipinformation::TABLE_NAME . ' where name not in ("org","country","city")'
        )->execute();

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Ipinformation::TABLE_NAME, true);
        $rows = $queryBuilder
            ->select('*')
            ->from(Ipinformation::TABLE_NAME)
            ->execute()
            ->fetchAll();
        foreach ($rows as $row) {
            $value = StringUtility::getRandomString(8);
            if ($row['name'] === 'city') {
                $value = 'Rosenheim';
            }
            if ($row['name'] === 'country') {
                $value = 'Germany';
            }
            if ($row['name'] === 'org') {
                $value = 'Telekom Ltd.';
            }
            /** @var Connection $connection */
            $connection = DatabaseUtility::getConnectionForTable(Ipinformation::TABLE_NAME);
            $connection->query(
                'update ' . Ipinformation::TABLE_NAME . ' set value="' . $value . '" where uid=' . $row['uid']
            )->execute();
        }
    }

    /**
     * @param array $properties
     * @return string
     */
    protected function getUpdateQueryFromProperties(array $properties): string
    {
        $query = '';
        foreach ($properties as $field => $value) {
            $query .= $field . '="' . $value . '",';
        }
        return rtrim($query, ',');
    }

    /**
     * @return string
     */
    protected function getRandomEmail(): string
    {
        $firstname = StringUtility::cleanString($this->getRandomFirstname(), true);
        $lastname = StringUtility::cleanString($this->getRandomLastname(), true);
        $company = StringUtility::cleanString($this->getRandomLastname(), true);
        $toplevelDomain = StringUtility::cleanString($this->getRandomToplevelDomain(), true);
        return $firstname . '.' . $lastname . '@' . $company . '.' . $toplevelDomain;
    }

    /**
     * @return string
     */
    protected function getRandomFirstname(): string
    {
        $key = rand(0, count($this->firstNames) - 1);
        return $this->firstNames[$key];
    }

    /**
     * @return string
     */
    protected function getRandomLastname(): string
    {
        $key = rand(0, count($this->lastNames) - 1);
        return $this->lastNames[$key];
    }

    /**
     * @return string
     */
    protected function getRandomCompanyname(): string
    {
        $key = rand(0, count($this->companies) - 1);
        return $this->companies[$key];
    }

    /**
     * @return string
     */
    protected function getRandomToplevelDomain(): string
    {
        $key = rand(0, count($this->toplevelDomains) - 1);
        return $this->toplevelDomains[$key];
    }

    /**
     * @return string
     */
    protected function getRandomReferrer(): string
    {
        $key = rand(0, count($this->referrers) - 1);
        return $this->referrers[$key];
    }

    /**
     * @return string
     */
    protected function getRandomDescription(): string
    {
        $key = rand(0, count($this->descriptions) - 1);
        return $this->descriptions[$key];
    }
}
