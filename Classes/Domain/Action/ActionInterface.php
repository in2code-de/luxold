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
     */
    public function __construct(Workflow $workflow, Action $action, Visitor $visitor);

    /**
     * @return void
     */
    public function initialize();

    /**
     * @return void
     */
    public function doAction();

    /**
     * @return void
     */
    public function afterAction();
}
