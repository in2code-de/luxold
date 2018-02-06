<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Factory;

use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Repository\VisitorRepository;
use In2code\Lux\Utility\ObjectUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Visitor
 */
class VisitorFactory
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
     * VisitorFactory constructor.
     *
     * @param string $idCookie
     */
    public function __construct(string $idCookie)
    {
        $this->idCookie = $idCookie;
        $this->visitorRepository = ObjectUtility::getObjectManager()->get(VisitorRepository::class);
    }

    /**
     * @return Visitor
     */
    public function getVisitor(): Visitor
    {
        $visitor = $this->getVisitoryFromDatabase();
        if ($visitor === null) {
            $visitor = $this->createNewVisitor();
            $this->visitorRepository->add($visitor);
            $this->visitorRepository->persistAll();
        }
        return $visitor;
    }

    /**
     * @return Visitor|null
     */
    protected function getVisitoryFromDatabase()
    {
        return $this->visitorRepository->findOneByIdCookie($this->idCookie);
    }

    /**
     * @return Visitor
     */
    protected function createNewVisitor(): Visitor
    {
        $visitor = GeneralUtility::makeInstance(Visitor::class);
        $visitor->setIdCookie($this->idCookie);
        return $visitor;
    }
}
