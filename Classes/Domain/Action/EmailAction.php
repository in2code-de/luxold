<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Action;

use In2code\Lux\Utility\ObjectUtility;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class EmailAction
 */
class EmailAction extends AbstractAction implements ActionInterface
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
        /** @var MailMessage $message */
        $message = ObjectUtility::getObjectManager()->get(MailMessage::class);
        $message
            ->setTo([$this->getConfigurationByKey('receiverEmail') => 'Receiver'])
            ->setFrom($this->getFrom())
            ->setReplyTo([$this->getConfigurationByKey('senderEmail') => $this->getConfigurationByKey('senderName')])
            ->setSubject($this->getConfigurationByKey('subject'))
            ->setBody($this->getBodytext(), 'text/plain')
            ->send();
        return $message->isSent();
    }

    /**
     * Parse visitor and workflow in bodytext, so anyone can use e.g. {visitor.property} in mail bodytext
     *
     * @return string
     */
    protected function getBodytext(): string
    {
        /** @var StandaloneView $standaloneView */
        $standaloneView = ObjectUtility::getObjectManager()->get(StandaloneView::class);
        $standaloneView->setTemplateSource($this->getConfigurationByKey('bodytext'));
        $standaloneView->assignMultiple([
            'visitor' => $this->getVisitor(),
            'workflow' => $this->getWorkflow()
        ]);
        return $standaloneView->render();
    }

    /**
     * Get array with from values and override with values from TypoScript
     *
     * @return array
     */
    protected function getFrom(): array
    {
        $senderName = $this->getConfigurationByKey('senderName');
        $overrideName = $this->getSettingsByPath('configuration.emailOverrides.senderName');
        if (!empty($overrideName)) {
            $senderName = $overrideName;
        }
        $senderEmail = $this->getConfigurationByKey('senderEmail');
        $overrideEmail = $this->getSettingsByPath('configuration.emailOverrides.senderEmail');
        if (!empty($overrideEmail)) {
            $senderEmail = $overrideEmail;
        }
        return [$senderEmail => $senderName];
    }
}
