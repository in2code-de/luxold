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
    const STATUS_DEFAULT = 0;
    const STATUS_NEW = 1;
    const STATUS_IDENTIFIED = 2;
    const STATUS_IDENTIFIED_EMAIL4LINK = 21;
    const STATUS_IDENTIFIED_EMAIL4LINK_SENDEMAIL = 22;
    const STATUS_IDENTIFIED_EMAIL4LINK_SENDEMAILFAILED = 23;
    const STATUS_ATTRIBUTE = 3;
    const STATUS_PAGEVISIT2 = 40;
    const STATUS_PAGEVISIT3 = 41;
    const STATUS_PAGEVISIT4 = 42;
    const STATUS_PAGEVISIT5 = 43;

    /**
     * @var \In2code\Lux\Domain\Model\Visitor
     */
    protected $visitor = null;

    /**
     * @var int
     */
    protected $status = 0;

    /**
     * @var \DateTime|null
     */
    protected $crdate = null;

    /**
     * @var string
     */
    protected $properties = '';

    /**
     * @return Visitor
     */
    public function getVisitor(): Visitor
    {
        return $this->visitor;
    }

    /**
     * @param Visitor $visitor
     * @return Log
     */
    public function setVisitor(Visitor $visitor): Log
    {
        $this->visitor = $visitor;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return Log
     */
    public function setStatus(int $status): Log
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCrdate(): \DateTime
    {
        return $this->crdate;
    }

    /**
     * @param \DateTime $crdate
     * @return Log
     */
    public function setCrdate(\DateTime $crdate): Log
    {
        $this->crdate = $crdate;
        return $this;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return json_decode($this->properties, true);
    }

    /**
     * @param array $properties
     * @return Log
     */
    public function setProperties(array $properties): Log
    {
        $this->properties = json_encode($properties);
        return $this;
    }

    /**
     * @return string
     */
    public function getHref(): string
    {
        return $this->getPropertyByKey('href');
    }

    /**
     * @param string $key
     * @return string
     */
    protected function getPropertyByKey(string $key): string
    {
        $property = '';
        $properties = $this->getProperties();
        if (array_key_exists($key, $properties)) {
            $property = $properties[$key];
        }
        return $property;
    }
}
