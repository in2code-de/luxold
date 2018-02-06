<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Factory;

use In2code\Lux\Domain\Model\Page;
use In2code\Lux\Domain\Model\Pagevisit;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Repository\PageRepository;
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
     * @var int
     */
    protected $pageUid = 0;

    /**
     * @var VisitorRepository|null
     */
    protected $visitorRepository = null;

    /**
     * VisitorFactory constructor.
     *
     * @param string $idCookie
     * @param int $pageUid
     */
    public function __construct(string $idCookie, int $pageUid = 0)
    {
        $this->idCookie = $idCookie;
        $this->pageUid = $pageUid;
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
        }
        if ($this->pageUid > 0) {
            $visitor->addPagevisit($this->getPageVisit());
        }
        $this->visitorRepository->add($visitor);
        $this->visitorRepository->persistAll();
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

    /**
     * @return Pagevisit
     */
    protected function getPageVisit(): Pagevisit
    {
        $pageVisit = ObjectUtility::getObjectManager()->get(Pagevisit::class);
        $pageRepository = ObjectUtility::getObjectManager()->get(PageRepository::class);
        /** @var Page $page */
        $page = $pageRepository->findByUid($this->pageUid);
        $pageVisit->setPage($page);
        return $pageVisit;
    }
}
