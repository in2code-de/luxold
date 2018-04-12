<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Trigger;

/**
 * Class CompanyTrigger
 */
class CompanyTrigger extends AbstractTrigger implements TriggerInterface
{

    /**
     * @return bool
     */
    public function isTriggered(): bool
    {
        return $this->getVisitor()->getCompany() !== '';
    }
}
