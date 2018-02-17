<?php
declare(strict_types=1);
namespace In2code\Lux\Controller;

use In2code\Lux\Domain\Factory\DownloadFactory;
use In2code\Lux\Domain\Factory\VisitorFactory;
use In2code\Lux\Domain\Service\SendAssetEmail4LinkService;
use In2code\Lux\Domain\Tracker\AttributeTracker;
use In2code\Lux\Domain\Tracker\PageTracker;
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
        $visitorFactory = $this->objectManager->get(VisitorFactory::class, $idCookie, $arguments['referrer']);
        $pageTracker = $this->objectManager->get(PageTracker::class);
        $pageTracker->trackPage($visitorFactory->getVisitor(), (int)$arguments['pageUid']);
        return json_encode([]);
    }

    /**
     * @param string $idCookie
     * @param array $arguments
     * @return string
     */
    public function fieldListeningRequestAction(string $idCookie, array $arguments): string
    {
        $visitorFactory = $this->objectManager->get(VisitorFactory::class, $idCookie);
        $attributeTracker = $this->objectManager->get(
            AttributeTracker::class,
            $visitorFactory->getVisitor(),
            AttributeTracker::CONTEXT_FIELDLISTENING
        );
        $attributeTracker->addAttribute($arguments['key'], $arguments['value']);
        return json_encode([]);
    }

    /**
     * @param string $idCookie
     * @param array $arguments
     * @return string
     */
    public function email4LinkRequestAction(string $idCookie, array $arguments): string
    {
        $visitorFactory = $this->objectManager->get(VisitorFactory::class, $idCookie);
        $visitor = $visitorFactory->getVisitor();
        $attributeTracker = $this->objectManager->get(
            AttributeTracker::class,
            $visitor,
            AttributeTracker::CONTEXT_EMAIL4LINK
        );
        $attributeTracker->addAttribute('email', $arguments['email']);
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
