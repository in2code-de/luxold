<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Trigger;

use In2code\Lux\Utility\FrontendUtility;

/**
 * Class PageVisitTrigger
 */
class PageVisitTrigger extends AbstractTrigger implements TriggerInterface
{

    /**
     * @return bool
     */
    public function isTriggered(): bool
    {
        return $this->isCurrentPage() && $this->hasNumberOfMinimumVisitsReached();
    }

    /**
     * @return bool
     */
    protected function isCurrentPage(): bool
    {
        return FrontendUtility::getCurrentPageIdentifier() === (int)$this->getConfigurationByKey('page');
    }

    /**
     * @return bool
     */
    protected function hasNumberOfMinimumVisitsReached(): bool
    {
        $pagevisits = $this->getVisitor()->getPagevisitsOfGivenPageIdentifier(
            FrontendUtility::getCurrentPageIdentifier()
        );
        return count($pagevisits) >= (int)$this->getConfigurationByKey('visit');
    }
}
