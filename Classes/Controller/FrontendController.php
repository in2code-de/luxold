<?php
declare(strict_types=1);
namespace In2code\Lux\Controller;

use In2code\Lux\Domain\Factory\AttributeFactory;
use In2code\Lux\Domain\Factory\DownloadFactory;
use In2code\Lux\Domain\Factory\VisitorFactory;
use In2code\Lux\Domain\Service\SendAssetEmail4LinkService;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class FrontendController
 */
class FrontendController extends ActionController
{

    /**
     * Check for allowed actions
     *
     * @return void
     */
    public function initializeDispatchRequestAction()
    {
        $allowedActions = [
            'pageRequest',
            'fieldListeningRequest',
            'email4LinkRequest',
            'downloadRequest'
        ];
        $action = $this->request->getArgument('dispatchAction');
        if (!in_array($action, $allowedActions)) {
            throw new \UnexpectedValueException('Action not allowed', 1518815149);
        }
    }

    /**
     * @param string $dispatchAction
     * @param string $idCookie
     * @param array $arguments
     * @return void
     */
    public function dispatchRequestAction(string $dispatchAction, string $idCookie, array $arguments)
    {
        $this->forward($dispatchAction, null, null, ['idCookie' => $idCookie, 'arguments' => $arguments]);
    }

    /**
     * @param string $idCookie
     * @param array $arguments
     * @return string
     */
    public function pageRequestAction(string $idCookie, array $arguments): string
    {
        $visitorFactory = $this->objectManager->get(
            VisitorFactory::class,
            $idCookie,
            (int)$arguments['pageUid'],
            $arguments['referrer']
        );
        $visitorFactory->getVisitor();
        return json_encode([]);
    }

    /**
     * @param string $idCookie
     * @param array $arguments
     * @return string
     */
    public function fieldListeningRequestAction(string $idCookie, array $arguments): string
    {
        $attributeFactory = $this->objectManager->get(
            AttributeFactory::class,
            $idCookie,
            AttributeFactory::CONTEXT_FIELDLISTENING
        );
        $attributeFactory->getVisitorAndAddAttribute($arguments['key'], $arguments['value']);
        return json_encode([]);
    }

    /**
     * @return void
     */
    public function initializeEmail4LinkRequestAction()
    {
        try {
            $sendEmail = $this->request->getArgument('sendEmail');
            $this->request->setArgument('sendEmail', $sendEmail === 'true');
        } catch (\Exception $exception) {
            unset($exception);
        }
    }

    /**
     * @param string $idCookie
     * @param array $arguments
     * @return string
     */
    public function email4LinkRequestAction(string $idCookie, array $arguments): string
    {
        $attributeFactory = $this->objectManager->get(
            AttributeFactory::class,
            $idCookie,
            AttributeFactory::CONTEXT_EMAIL4LINK
        );
        $visitor = $attributeFactory->getVisitorAndAddAttribute('email', $arguments['email']);
        if ($arguments['sendEmail'] === 'true') {
            $this->objectManager->get(SendAssetEmail4LinkService::class, $visitor)->sendMail($arguments['href']);
        }
        return json_encode([]);
    }

    /**
     * @param string $idCookie
     * @param array $arguments
     * @return string
     */
    public function downloadRequestAction(string $idCookie, array $arguments): string
    {
        $downloadFactory = $this->objectManager->get(DownloadFactory::class, $idCookie);
        $downloadFactory->getVisitorAndAddDownload($arguments['href']);
        return json_encode([]);
    }
}
