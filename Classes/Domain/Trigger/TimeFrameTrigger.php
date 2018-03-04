<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Trigger;

/**
 * Class TimeFrameTrigger
 */
class TimeFrameTrigger extends AbstractTrigger implements TriggerInterface
{

    /**
     * @return bool
     */
    public function isTriggered(): bool
    {
        $now = new \DateTime();
        return $this->getConfiguredTimeFrom() <= $now && $this->getConfiguredTimeTo() >= $now;
    }

    /**
     * @return \DateTime
     */
    protected function getConfiguredTimeFrom(): \DateTime
    {
        return new \DateTime($this->getConfigurationByKey('timefrom'));
    }

    /**
     * @return \DateTime
     */
    protected function getConfiguredTimeTo(): \DateTime
    {
        return new \DateTime($this->getConfigurationByKey('timeto'));
    }
}
