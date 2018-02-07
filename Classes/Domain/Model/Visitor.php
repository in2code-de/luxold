<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Model;

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
     * Visitor constructor.
     */
    public function __construct()
    {
        $this->pagevisits = new ObjectStorage();
        $this->attributes = new ObjectStorage();
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
     * @return ObjectStorage
     */
    public function getPagevisits(): ObjectStorage
    {
        return $this->pagevisits;
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
    public function getReadableName(): string
    {
        if ($this->isIdentified() === false) {
            $name = 'Unknown';
        } else {
            $name = '';
            $firstname = $this->getPropertyFromAttributes('firstname');
            $lastname = $this->getPropertyFromAttributes('lastname');
            $company = $this->getPropertyFromAttributes('company');
            if ($lastname !== '') {
                $name .= $lastname . ', ';
            }
            if ($firstname !== '') {
                $name .= $firstname . ' ';
            }
            if ($company !== '') {
                $name .= '(' . $company . ')';
            }
        }
        return $name;
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
}
