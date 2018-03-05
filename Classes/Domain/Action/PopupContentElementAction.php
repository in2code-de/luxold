<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Action;

/**
 * Class PopupContentElementAction
 */
class PopupContentElementAction extends AbstractAction implements ActionInterface
{

    /**
     * Just set some values for JSON transmit to clientside, because the whole magic is related in JS
     *
     * @return void
     */
    public function doAction()
    {
        $this->setTransmission(
            'lightboxContent',
            [
                'contentElement' => (int)$this->getConfigurationByKey('page'),
                'delay' => (int)$this->getConfigurationByKey('delay')
            ]
        );
    }
}
