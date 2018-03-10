<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Action;

/**
 * Class RedirectAction
 */
class RedirectAction extends AbstractAction implements ActionInterface
{

    /**
     * @return bool
     */
    public function doAction(): bool
    {
        $this->setTransmission(
            'redirect',
            [
                'uri' => $this->getConfigurationByKey('uri')
            ]
        );
        return true;
    }
}
