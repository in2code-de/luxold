<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Model;

use In2code\Lux\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class Visitor
 */
class Visitor extends AbstractEntity
{
    const TABLE_NAME = 'tx_lux_domain_model_visitor';

    /**
     * @var string
     */
    protected $idCookie = '';

    /**
     * @var string
     */
    protected $email = '';

    /**
     * @var bool
     */
    protected $identified = false;

    /**
     * @var int
     */
    protected $visits = 0;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\Lux\Domain\Model\Pagevisit>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @extensionScannerIgnoreLine Still needed for TYPO3 8.7
     * @lazy
     */
    protected $pagevisits = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\Lux\Domain\Model\Attribute>
     */
    protected $attributes = null;

    /**
     * @var string
     */
    protected $referrer = '';

    /**
     * @var string
     */
    protected $userAgent = '';

    /**
     * @var string
     */
    protected $ipAddress = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\Lux\Domain\Model\Ipinformation>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @extensionScannerIgnoreLine Still needed for TYPO3 8.7
     * @lazy
     */
    protected $ipinformations = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\Lux\Domain\Model\Log>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @extensionScannerIgnoreLine Still needed for TYPO3 8.7
     * @lazy
     */
    protected $logs = null;

    /**
     * Visitor constructor.
     */
    public function __construct()
    {
        $this->pagevisits = new ObjectStorage();
        $this->attributes = new ObjectStorage();
        $this->ipinformations = new ObjectStorage();
        $this->logs = new ObjectStorage();
    }

    /**
     * @return string
     */
    public function getIdCookie(): string
    {
        return $this->idCookie;
    }

    /**
     * @param string $idCookie
     * @return Visitor
     */
    public function setIdCookie(string $idCookie)
    {
        $this->idCookie = $idCookie;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Visitor
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return bool
     */
    public function isIdentified(): bool
    {
        return $this->identified;
    }

    /**
     * @param bool $identified
     * @return Visitor
     */
    public function setIdentified(bool $identified)
    {
        $this->identified = $identified;
        return $this;
    }

    /**
     * @return int
     */
    public function getVisits(): int
    {
        return $this->visits;
    }

    /**
     * @param int $visits
     * @return Visitor
     */
    public function setVisits(int $visits)
    {
        $this->visits = $visits;
        return $this;
    }

    /**
     * @return array
     */
    public function getPagevisits(): array
    {
        $pagevisits = $this->pagevisits;
        $pagevisitsArray = [];
        /** @var Pagevisit $pagevisit */
        foreach ($pagevisits as $pagevisit) {
            $pagevisitsArray[$pagevisit->getCrdate()->getTimestamp()] = $pagevisit;
        }
        krsort($pagevisitsArray);
        return $pagevisitsArray;
    }

    /**
     * @var ObjectStorage $pagevisits
     * @return Visitor
     */
    public function setPagevisits(ObjectStorage $pagevisits)
    {
        $this->pagevisits = $pagevisits;
        return $this;
    }

    /**
     * @param Pagevisit $pagevisit
     * @return $this
     */
    public function addPagevisit(Pagevisit $pagevisit)
    {
        $this->pagevisits->attach($pagevisit);
        return $this;
    }

    /**
     * @param Pagevisit $pagevisit
     * @return $this
     */
    public function removePagevisit(Pagevisit $pagevisit)
    {
        $this->pagevisits->detach($pagevisit);
        return $this;
    }

    /**
     * Calculate number of unique page visits. If user show a reaction after min. 1h we define it as new pagevisit.
     * @return int
     */
    public function getNumberOfUniquePagevisits(): int
    {
        $pagevisits = $this->pagevisits;
        $number = 1;
        if (count($pagevisits) > 1) {
            $lastVisit = null;
            foreach ($pagevisits as $pagevisit) {
                if ($lastVisit !== null) {
                    /** @var Pagevisit $pagevisit */
                    $interval = $lastVisit->diff($pagevisit->getCrdate());
                    // if difference is greater then one hour
                    if ($interval->h > 0) {
                        $number++;
                    }
                }
                $lastVisit = $pagevisit->getCrdate();
            }
        }
        return $number;
    }

    /**
     * @return ObjectStorage
     */
    public function getAttributes(): ObjectStorage
    {
        return $this->attributes;
    }

    /**
     * @var ObjectStorage $attributes
     * @return Visitor
     */
    public function setAttributes(ObjectStorage $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @param Attribute $attribute
     * @return $this
     */
    public function addAttribute(Attribute $attribute)
    {
        $this->attributes->attach($attribute);
        return $this;
    }

    /**
     * @param Attribute $attribute
     * @return $this
     */
    public function removeAttribute(Attribute $attribute)
    {
        $this->attributes->detach($attribute);
        return $this;
    }

    /**
     * @return string
     */
    public function getReferrer(): string
    {
        return $this->referrer;
    }

    /**
     * @param string $referrer
     * @return Visitor
     */
    public function setReferrer(string $referrer)
    {
        $this->referrer = $referrer;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     * @return Visitor
     */
    public function setUserAgent(string $userAgent)
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * @return string
     */
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    /**
     * @param string $ipAddress
     * @return Visitor
     */
    public function setIpAddress(string $ipAddress)
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getIpinformations(): ObjectStorage
    {
        return $this->ipinformations;
    }

    /**
     * @var ObjectStorage $ipinformations
     * @return Visitor
     */
    public function setIpinformations(ObjectStorage $ipinformations)
    {
        $this->ipinformations = $ipinformations;
        return $this;
    }

    /**
     * @param Ipinformation $ipinformation
     * @return $this
     */
    public function addIpinformation(Ipinformation $ipinformation)
    {
        $this->ipinformations->attach($ipinformation);
        return $this;
    }

    /**
     * @param Ipinformation $ipinformation
     * @return $this
     */
    public function removeIpinformation(Ipinformation $ipinformation)
    {
        $this->ipinformations->detach($ipinformation);
        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getLogs(): ObjectStorage
    {
        return $this->logs;
    }

    /**
     * @var ObjectStorage $logs
     * @return Visitor
     */
    public function setLogs(ObjectStorage $logs)
    {
        $this->logs = $logs;
        return $this;
    }

    /**
     * @param Log $log
     * @return $this
     */
    public function addLog(Log $log)
    {
        $this->logs->attach($log);
        return $this;
    }

    /**
     * @param Log $log
     * @return $this
     */
    public function removeLog(Log $log)
    {
        $this->logs->detach($log);
        return $this;
    }

    /**
     * Calculated properties
     */

    /**
     * "Lastname, Firstname"
     *
     * @return string
     */
    public function getFullName(): string
    {
        if ($this->isIdentified()) {
            $name = '';
            $firstname = $this->getPropertyFromAttributes('firstname');
            $lastname = $this->getPropertyFromAttributes('lastname');
            if (!empty($lastname)) {
                $name .= $lastname;
                if (!empty($firstname)) {
                    $name .= ', ';
                }
            }
            if (!empty($firstname)) {
                $name .= $firstname;
            }
        } else {
            $name = LocalizationUtility::translate('LLL:EXT:lux/Resources/Private/Language/locallang_db.xlf:anonym');
        }
        return $name;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        $country = $this->getCountry();
        $city = $this->getCity();
        $location = '';
        if (!empty($city)) {
            $location .= $city;
        }
        if (!empty($country)) {
            if (!empty($city)) {
                $location .= ' / ';
            }
            $location .= $country;
        }
        return $location;
    }

    /**
     * @return string
     */
    public function getCompany(): string
    {
        return $this->getPropertyFromAttributes('company');
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->getPropertyFromIpinformations('country');
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->getPropertyFromIpinformations('city');
    }

    /**
     * @param string $key
     * @return string
     */
    protected function getPropertyFromAttributes(string $key): string
    {
        $attributes = $this->getAttributes();
        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            if ($attribute->getName() === $key) {
                return $attribute->getValue();
            }
        }
        return '';
    }

    /**
     * @param string $key
     * @return string
     */
    protected function getPropertyFromIpinformations(string $key): string
    {
        $ipinformations = $this->getIpinformations();
        if ($ipinformations->count() > 0) {
            /** @var Ipinformation $ipinformation */
            foreach ($ipinformations as $ipinformation) {
                if ($ipinformation->getName() === $key) {
                    return $ipinformation->getValue();
                }
            }
        }
        return '';
    }
}
