<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Action;

/**
 * Class PopupContentElementAction
 */
class PopupContentElementAction extends AbstractAction implements ActionInterface
{

    /**
     * @return bool
     */
    public function isTriggered(): bool
    {
        return true;
    }
}
