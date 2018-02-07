<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Repository;

use In2code\Lux\Domain\Model\Attribute;
use In2code\Lux\Domain\Model\Visitor;

/**
 * Class AttributeRepository
 */
class AttributeRepository extends AbstractRepository
{

    /**
     * @param string $idCookie
     * @param string $key
     * @return Attribute|null
     */
    public function findByIdCookieAndKey(string $idCookie, string $key)
    {
        $visitorRepository = $this->objectManager->get(VisitorRepository::class);
        $visitor = $visitorRepository->findOneByIdCookie($idCookie);
        return $this->findByVisitorAndKey($visitor, $key);
    }

    /**
     * @param Visitor $visitor
     * @param string $key
     * @return Attribute|null|object
     */
    public function findByVisitorAndKey(Visitor $visitor, string $key)
    {
        $query = $this->createQuery();
        $logicalAnd = [
            $query->equals('visitor', $visitor),
            $query->equals('name', $key)
        ];
        $query->matching($query->logicalAnd($logicalAnd));
        return $query->execute()->getFirst();
    }
}
