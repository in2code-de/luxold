<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Class Trigger
 */
class Trigger extends AbstractEntity
{
    const TABLE_NAME = 'tx_lux_domain_model_trigger';
    const CONJUNCTION_AND = 'AND';
    const CONJUNCTION_OR = 'OR';

    /**
     * @var string
     */
    protected $className = '';

    /**
     * @var string
     */
    protected $configuration = '';

    /**
     * @var string
     */
    protected $conjunction = self::CONJUNCTION_AND;

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param string $className
     * @return Trigger
     */
    public function setClassName(string $className)
    {
        $this->className = $className;
        return $this;
    }

    /**
     * @return string
     */
    public function getConfiguration(): string
    {
        return $this->configuration;
    }

    /**
     * @return array
     */
    public function getConfigurationAsArray(): array
    {
        return json_decode($this->configuration);
    }

    /**
     * @param string $configuration
     * @return Trigger
     */
    public function setConfiguration(string $configuration)
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * @param array $configuration
     * @return $this
     */
    public function setConfigurationFromArray(array $configuration)
    {
        $this->configuration = json_encode($configuration);
        return $this;
    }

    /**
     * @return string
     */
    public function getConjunction(): string
    {
        return $this->conjunction;
    }

    /**
     * @param string $conjunction
     * @return Trigger
     */
    public function setConjunction(string $conjunction)
    {
        $this->conjunction = $conjunction;
        return $this;
    }
}
