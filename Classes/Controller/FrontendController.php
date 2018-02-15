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
     * @param string $idCookie
     * @param int $languageUid
     * @param int $pageUid
     * @param string $referrer
     * @return string
     */
    public function pageRequestAction(string $idCookie, int $languageUid, int $pageUid, string $referrer = ''): string
    {
        $visitorFactory = $this->objectManager->get(VisitorFactory::class, $idCookie, $pageUid, $referrer);
        $visitorFactory->getVisitor();
        return json_encode([]);
    }

    /**
     * @param string $idCookie
     * @param string $key
     * @param string $value
     * @return string
     */
    public function fieldListeningRequestAction(string $idCookie, string $key, string $value): string
    {
        $attributeFactory = $this->objectManager->get(
            AttributeFactory::class,
            $idCookie,
            AttributeFactory::CONTEXT_FIELDLISTENING
        );
        $attributeFactory->getVisitorAndAddAttribute($key, $value);
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
     * @param string $email
     * @param bool $sendEmail
     * @param string $href
     * @return string
     */
    public function email4LinkRequestAction(string $idCookie, string $email, bool $sendEmail, string $href): string
    {
        $attributeFactory = $this->objectManager->get(
            AttributeFactory::class,
            $idCookie,
            AttributeFactory::CONTEXT_EMAIL4LINK
        );
        $visitor = $attributeFactory->getVisitorAndAddAttribute('email', $email);
        if ($sendEmail === true) {
            $this->objectManager->get(SendAssetEmail4LinkService::class, $visitor)->sendMail($href);
        }
        return json_encode([]);
    }

    /**
     * @param string $idCookie
     * @param string $href
     * @return string
     */
    public function downloadRequestAction(string $idCookie, string $href): string
    {
        $downloadFactory = $this->objectManager->get(DownloadFactory::class, $idCookie);
        $downloadFactory->getVisitorAndAddDownload($href);
        return json_encode([]);
    }
}
