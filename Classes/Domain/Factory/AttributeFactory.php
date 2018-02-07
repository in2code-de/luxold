<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Factory;

use In2code\Lux\Domain\Model\Attribute;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Repository\AttributeRepository;
use In2code\Lux\Domain\Repository\VisitorRepository;
use In2code\Lux\Utility\ObjectUtility;

/**
 * Class AttributeFactory
 */
class AttributeFactory
{

    /**
     * @var string
     */
    protected $idCookie = '';

    /**
     * @var VisitorRepository|null
     */
    protected $visitorRepository = null;

    /**
     * @var AttributeRepository|null
     */
    protected $attributeRepository = null;

    /**
     * AttributeFactory constructor.
     *
     * @param string $idCookie
     */
    public function __construct(string $idCookie)
    {
        $this->idCookie = $idCookie;
        $this->visitorRepository = ObjectUtility::getObjectManager()->get(VisitorRepository::class);
        $this->attributeRepository = ObjectUtility::getObjectManager()->get(AttributeRepository::class);
    }

    /**
     * Add or update an attribute of a visitor and return the visitor
     *
     * @param string $key
     * @param string $value
     * @return Visitor
     */
    public function getVisitorAndAddAttribute(string $key, string $value): Visitor
    {
        $visitor = $this->getVisitorFromDatabase();
        if (!empty($value)) {
            $attribute = $this->getAndUpdateAttributeFromDatabase($key, $value);
            if ($attribute === null) {
                $attribute = $this->createNewAttribute($key, $value);
                $visitor->addAttribute($attribute);
            }
            if ($attribute->isEmail()) {
                $visitor->setIdentified(true);
                $visitor->setEmail($value);
            }
            $this->visitorRepository->update($visitor);
            $this->visitorRepository->persistAll();
        }
        return $visitor;
    }

    /**
     * @param string $key
     * @param string $value
     * @return Attribute|null
     */
    protected function getAndUpdateAttributeFromDatabase(string $key, string $value)
    {
        $attribute = $this->attributeRepository->findByIdCookieAndKey($this->idCookie, $key);
        if ($attribute !== null) {
            $attribute->setValue($value);
            $this->attributeRepository->update($attribute);
        }
        return $attribute;
    }

    /**
     * @return Visitor|null
     */
    protected function getVisitorFromDatabase()
    {
        return $this->visitorRepository->findOneByIdCookie($this->idCookie);
    }

    /**
     * @param string $key
     * @param string $value
     * @return Attribute
     */
    protected function createNewAttribute(string $key, string $value): Attribute
    {
        $attribute = ObjectUtility::getObjectManager()->get(Attribute::class);
        $attribute->setName($key);
        $attribute->setValue($value);
        return $attribute;
    }
}
