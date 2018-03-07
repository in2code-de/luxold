<?php
declare(strict_types=1);

namespace In2code\Lux\Domain\Action;

use In2code\Lux\Utility\ObjectUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class SlackAction
 */
class SlackAction extends AbstractAction implements ActionInterface
{

    /**
     * Define and overwrite conrollter action on which your action should listen. Per default actions are only called
     * from pageRequestAction. In some cases (e.g. let's send an email on identification) it could be helpful to
     * also add another controller action as entry point.
     * Possible actions are:
     *  "pageRequestAction", "fieldListeningRequestAction", "email4LinkRequestAction", "downloadRequestAction"
     *
     * @var array
     */
    protected $controllerActions = [
        'pageRequestAction',
        'fieldListeningRequestAction',
        'email4LinkRequestAction'
    ];

    /**
     * @return void
     */
    public function doAction()
    {
        $this->sendToSlack();
    }

    /**
     * @return void
     */
    protected function sendToSlack()
    {
        $resource = curl_init($this->getSettingsByPath('configuration.webhookUrl'));
        curl_setopt_array(
            $resource,
            [
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_POSTFIELDS => json_encode($this->getMessage()),
            ]
        );
        $result = curl_exec($resource);
        if ($result === false) {
            throw new \DomainException('Could not connect to Slack with given URL.', 1520368562);
        }
    }

    /**
     * @return array
     */
    protected function getMessage(): array
    {
        return [
            'text' => $this->getText(),
            'username' => $this->getSettingsByPath('configuration.username'),
            'icon_emoji' => $this->getSettingsByPath('configuration.emoji'),
        ];
    }

    protected function getText(): string
    {
        /** @var StandaloneView $standaloneView */
        $standaloneView = ObjectUtility::getObjectManager()->get(StandaloneView::class);
        $standaloneView->setTemplateSource($this->getConfigurationByKey('text'));
        $standaloneView->assignMultiple([
            'visitor' => $this->getVisitor(),
            'workflow' => $this->getWorkflow()
        ]);
        return $standaloneView->render();
    }
}
