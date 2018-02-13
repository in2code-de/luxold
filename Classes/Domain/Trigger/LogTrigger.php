<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Trigger;

use In2code\Lux\Domain\Model\Attribute;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Service\LogService;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Class LogTrigger
 */
class LogTrigger implements SingletonInterface
{

    /**
     * @var LogService|null
     */
    protected $logService = null;

    /**
     * @param LogService $logService
     * @return void
     */
    public function injectFormRepository(LogService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * @param Visitor $visitor
     * @return void
     */
    public function logNewVisitor(Visitor $visitor)
    {
        $this->logService->logNewVisitor($visitor);
    }

    /**
     * @param Attribute $attribute
     * @param Visitor $visitor
     * @return void
     */
    public function logIdentifiedVisitor(Attribute $attribute, Visitor $visitor)
    {
        $this->logService->logIdentifiedVisitor($visitor);
    }

    /**
     * @param Attribute $attribute
     * @param Visitor $visitor
     * @return void
     */
    public function logIdentifiedVisitorByEmail4Link(Attribute $attribute, Visitor $visitor)
    {
        $this->logService->logIdentifiedVisitorByEmail4Link($visitor);
    }
}
