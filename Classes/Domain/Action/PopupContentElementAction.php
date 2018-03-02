<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Action;

/**
 * Class PopupContentElementAction
 */
class PopupContentElementAction extends AbstractAction implements ActionInterface
{

    /**
     * @return void
     */
    public function doAction()
    {
        $this->setTransmission('lightboxContent', ['contentElement' => (int)$this->getConfigurationByKey('page')]);
    }
}
