<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Trigger;

/**
 * Class ScoringTrigger
 */
class ScoringTrigger extends AbstractTrigger implements TriggerInterface
{

    /**
     * @return bool
     */
    public function isTriggered(): bool
    {
        return true;
    }
}
