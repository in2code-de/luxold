<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Service;

use In2code\Lux\Domain\Model\Attribute;
use In2code\Lux\Domain\Model\Log;
use In2code\Lux\Domain\Model\Pagevisit;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Repository\AttributeRepository;
use In2code\Lux\Domain\Repository\VisitorRepository;
use In2code\Lux\Signal\SignalTrait;
use In2code\Lux\Utility\DatabaseUtility;
use In2code\Lux\Utility\ObjectUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * If visitor has a new idCookie but tells the system a known email address, we have to more all attributes and
 * pagevisits to the existing visitor and set the new idCookie
 *
 * Class VisitorMergeService
 */
class VisitorMergeService
{
    use SignalTrait;

    /**
     * @var string
     */
    protected $email = '';

    /**
     * @var Visitor|null
     */
    protected $firstVisitor = null;

    /**
     * @var VisitorRepository|null
     */
    protected $visitorRepository = null;

    /**
     * @var AttributeRepository|null
     */
    protected $attributeRepository = null;

    /**
     * VisitorMergeService constructor.
     *
     * @param string $email
     */
    public function __construct(string $email)
    {
        $this->email = $email;
        $this->visitorRepository = ObjectUtility::getObjectManager()->get(VisitorRepository::class);
        $this->attributeRepository = ObjectUtility::getObjectManager()->get(AttributeRepository::class);
    }

    /**
     * @return void
     */
    public function merge()
    {
        $visitors = $this->visitorRepository->findDuplicatesByEmail($this->email);
        /** @var QueryResultInterface $visitors */
        if ($visitors->count() > 1) {
            foreach ($visitors as $visitor) {
                $this->setFirstVisitor($visitor);
                if ($visitor !== $this->firstVisitor) {
                    $this->mergePagevisits($visitor);
                    $this->mergeLogs($visitor);
                    $this->mergeAttributes($visitor);
                    $this->updateIdCookie($visitor);
                    $this->deleteVisitor($visitor);
                }
            }
            $this->signalDispatch(__CLASS__, 'mergeVisitors', [$visitors]);
        }
    }

    /**
     * Update existing pagevisits with another parent visitor uid
     *
     * @param Visitor $newVisitor
     * @return void
     */
    protected function mergePagevisits(Visitor $newVisitor)
    {
        $connection = DatabaseUtility::getConnectionForTable(Pagevisit::TABLE_NAME);
        $connection->query(
            'update ' . Pagevisit::TABLE_NAME . ' set visitor = ' . (int)$this->firstVisitor->getUid() . ' ' .
            'where uid = ' . (int)$newVisitor->getUid()
        )->execute();
    }

    /**
     * Update existing logs with another parent visitor uid
     *
     * @param Visitor $newVisitor
     * @return void
     */
    protected function mergeLogs(Visitor $newVisitor)
    {
        $connection = DatabaseUtility::getConnectionForTable(Log::TABLE_NAME);
        $connection->query(
            'update ' . Log::TABLE_NAME . ' set visitor = ' . (int)$this->firstVisitor->getUid() . ' ' .
            'where visitor = ' . (int)$newVisitor->getUid()
        )->execute();
    }

    /**
     * Update existing attributes with another parent visitor uid
     *
     * @param Visitor $newVisitor
     * @return void
     */
    protected function mergeAttributes(Visitor $newVisitor)
    {
        foreach ($newVisitor->getAttributes() as $newAttribute) {
            $attribute = $this->attributeRepository->findByVisitorAndKey($this->firstVisitor, $newAttribute->getName());
            if ($attribute !== null) {
                $attribute->setValue($newAttribute->getValue());
                $this->attributeRepository->update($attribute);
                $this->attributeRepository->remove($newAttribute);
                $this->attributeRepository->persistAll();
            } else {
                $connection = DatabaseUtility::getConnectionForTable(Attribute::TABLE_NAME);
                $connection->query(
                    'update ' . Attribute::TABLE_NAME . ' set visitor = ' . $this->firstVisitor->getUid() . ' ' .
                    'where uid = ' . (int)$newAttribute->getUid()
                )->execute();
            }
        }
    }

    /**
     * @param Visitor $newVisitor
     * @return void
     */
    protected function updateIdCookie(Visitor $newVisitor)
    {
        $this->firstVisitor->setIdCookie($newVisitor->getIdCookie());
        $this->visitorRepository->update($this->firstVisitor);
        $this->visitorRepository->persistAll();
    }

    /**
     * @param Visitor $newVisitor
     * @return void
     */
    protected function deleteVisitor(Visitor $newVisitor)
    {
        $connection = DatabaseUtility::getConnectionForTable(Visitor::TABLE_NAME);
        $connection
            ->query('update ' . Visitor::TABLE_NAME . ' set deleted=1 where uid=' . (int)$newVisitor->getUid())
            ->execute();
    }

    /**
     * @param Visitor $visitor
     * @return void
     */
    protected function setFirstVisitor(Visitor $visitor)
    {
        if ($this->firstVisitor === null) {
            $this->firstVisitor = $visitor;
        }
    }
}
