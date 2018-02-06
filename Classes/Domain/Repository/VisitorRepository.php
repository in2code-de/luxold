<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Repository;

use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Utility\ObjectUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Class VisitorRepository
 */
class VisitorRepository extends Repository
{

    /**
     * @return void
     */
    public function initializeObject()
    {
        $defaultQuerySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $defaultQuerySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($defaultQuerySettings);
    }

    /**
     * @param string $idCookie
     * @return Visitor|null
     */
    public function findByIdCookie(string $idCookie)
    {
        $query = $this->createQuery();
        $query->matching($query->equals('idCookie', $idCookie));
        /** @var Visitor $visitor */
        $visitor = $query->execute()->getFirst();
        return $visitor;
    }

    /**
     * @return void
     */
    public function persistAll()
    {
        $persistanceManager = ObjectUtility::getObjectManager()->get(PersistenceManager::class);
        $persistanceManager->persistAll();
    }
}
