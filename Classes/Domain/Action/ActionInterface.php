<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Action;

use In2code\Lux\Domain\Model\Action;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Model\Workflow;

/**
 * Interface ActionInterface
 */
interface ActionInterface
{

    /**
     * ActionInterface constructor.
     *
     * @param Workflow $workflow
     * @param Action $action
     * @param Visitor $visitor
     * @param string $controllerAction
     */
    public function __construct(Workflow $workflow, Action $action, Visitor $visitor, string $controllerAction);

    /**
     * Return true if action was performed
     *
     * @return void
     */
    public function initialize();

    /**
     * @return bool
     */
    public function doAction(): bool;

    /**
     * @return void
     */
    public function afterAction();
}
