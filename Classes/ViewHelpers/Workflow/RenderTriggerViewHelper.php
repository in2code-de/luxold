<?php
declare(strict_types=1);
namespace In2code\Lux\ViewHelpers\Workflow;

use In2code\Lux\Domain\Model\Trigger;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class RenderTriggerViewHelper
 */
class RenderTriggerViewHelper extends AbstractViewHelper
{

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @var bool
     */
    protected $escapeChildren = false;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('trigger', Trigger::class, 'Given trigger to render', true);
        $this->registerArgument('index', 'int', 'Index number', true);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        /** @var Trigger $trigger */
        $trigger = $this->arguments['trigger'];
        return $trigger->renderTrigger((int)$this->arguments['index']);
    }
}
