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
     * @return void
     */
    public function doAction()
    {
        /** @var MailMessage $message */
        $message = ObjectUtility::getObjectManager()->get(MailMessage::class);
        $message
            ->setTo([$this->getConfigurationByKey('receiverEmail') => 'Receiver'])
            ->setFrom([$this->getConfigurationByKey('senderEmail') => $this->getConfigurationByKey('senderName')])
            ->setReplyTo([$this->getConfigurationByKey('senderEmail') => $this->getConfigurationByKey('senderName')])
            ->setSubject($this->getConfigurationByKey('subject'))
            ->setBody($this->getBodytext(), 'text/plain')
            ->send();
    }

    /**
     * Parse visitor and workflow in bodytext, so anyone can use e.g. {visitor.property} in mail bodytext
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
}
