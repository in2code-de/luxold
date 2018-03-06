<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Action;

/**
 * Class RedirectAction
 */
class RedirectAction extends AbstractAction implements ActionInterface
{

    /**
     * @return void
     */
    public function doAction()
    {
        $this->setTransmission(
            'redirect',
            [
                'uri' => $this->getConfigurationByKey('uri')
            ]
        );
    }
}
