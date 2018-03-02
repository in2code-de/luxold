<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Trigger;

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
        return false;
    }

    /**
     * @return int
     */
    protected function getPage(): int
    {
        return (int)$this->getConfigurationByKey('page');
    }

    /**
     * @return int
     */
    protected function getVisit(): int
    {
        return (int)$this->getConfigurationByKey('visit');
    }
}
