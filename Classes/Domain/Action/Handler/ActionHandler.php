<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Action\Handler;

use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Trigger\Handler\TriggerHandler;
use In2code\Lux\Utility\ObjectUtility;

/**
 * Class ActionHandler to call action classes and return action array as json for the visitors JavaScript
 */
class ActionHandler
{

    /**
     * @param Visitor $visitor
     * @param string $actionName
     * @param array $actionArray
     * @return array
     */
    public function startActions(Visitor $visitor, string $actionName, array $actionArray): array
    {
        if ($this->isAllowedStartAction($actionName)) {
            /** @var TriggerHandler $triggerHandler */
            $triggerHandler = ObjectUtility::getObjectManager()->get(TriggerHandler::class);
            if ($triggerHandler->getDecision($visitor)) {
                $actionArray = ['action' => 'openSomething'];
            }
        }
        return [$visitor, $actionName, $actionArray];
    }

    /**
     * @param string $actionName
     * @return bool
     */
    protected function isAllowedStartAction(string $actionName): bool
    {
        return $actionName === 'pageRequestAction';
    }
}
