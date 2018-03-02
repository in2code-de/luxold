<?php
declare(strict_types=1);
namespace In2code\Lux\ViewHelpers\Workflow;

use In2code\Lux\Domain\Model\Action;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class RenderActionViewHelper
 */
class RenderActionViewHelper extends AbstractViewHelper
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
        $this->registerArgument('action', Action::class, 'Given action to render', true);
        $this->registerArgument('index', 'int', 'Index number', true);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        /** @var Action $action */
        $action = $this->arguments['action'];
        return $action->renderAction((int)$this->arguments['index']);
    }
}
