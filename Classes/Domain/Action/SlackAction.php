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
     * @return bool
     */
    public function doAction(): bool
    {
        return $this->sendToSlack();
    }

    /**
     * @return bool
     */
    protected function sendToSlack(): bool
    {
        $resource = curl_init($this->getActionSettingsByKey('webhookUrl'));
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
        return $result !== false;
    }

    /**
     * @return array
     */
    protected function getMessage(): array
    {
        $message = [
            'text' => $this->getText(),
            'username' => $this->getActionSettingsByKey('username')
        ];
        if ($this->getActionSettingsByKey('emoji') !== '') {
            $message['icon_emoji'] = $this->getActionSettingsByKey('emoji');
        }
        return $message;
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
        $text = $standaloneView->render();
        if (empty($text)) {
            throw new \DomainException('No text stored that can be published to slack', 1523559116);
        }
        return $text;
    }

    /**
     * @param string $key
     * @return string
     */
    protected function getActionSettingsByKey(string $key): string
    {
        $setting = '';
        $configurationKey = $this->getConfigurationByKey('configuration');
        if (is_numeric($configurationKey)) {
            $settings = $this->getSettingsByPath('configuration.' . $configurationKey);
            if (is_array($settings) && array_key_exists($key, $settings)) {
                $setting = $settings[$key];
            }
        } else {
            throw new \DomainException('Key is no number. Possible TS misconfiguration', 1523558913);
        }
        return $setting;
    }
}
