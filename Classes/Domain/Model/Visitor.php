<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

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
}
