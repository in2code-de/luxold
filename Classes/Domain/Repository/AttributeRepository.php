<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Repository;

use In2code\Lux\Domain\Model\Attribute;

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

        $query = $this->createQuery();
        $logicalAnd = [
            $query->equals('visitor', $visitor),
            $query->equals('name', $key)
        ];
        $query->matching($query->logicalAnd($logicalAnd));
        return $query->execute()->getFirst();
    }
}
