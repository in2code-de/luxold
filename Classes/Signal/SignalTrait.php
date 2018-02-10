<?php
declare(strict_types=1);
namespace In2code\Lux\Signal;

use In2code\Lux\Utility\ObjectUtility;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * Trait SignalTrait
 */
trait SignalTrait
{
    /**
     * @var bool
     */
    protected $signalEnabled = true;

    /**
     * Instance a new signalSlotDispatcher and offer a signal
     *
     * @param string $signalClassName
     * @param string $signalName
     * @param array $arguments
     */
    protected function signalDispatch(string $signalClassName, string $signalName, array $arguments)
    {
        if ($this->isSignalEnabled()) {
            $signalSlotDispatcher = ObjectUtility::getObjectManager()->get(Dispatcher::class);
            $signalSlotDispatcher->dispatch($signalClassName, $signalName, $arguments);
        }
    }

    /**
     * @return boolean
     */
    protected function isSignalEnabled()
    {
        return $this->signalEnabled;
    }

    /**
     * Signal can be disabled for testing
     *
     * @return void
     */
    protected function disableSignals()
    {
        $this->signalEnabled = false;
    }
}
