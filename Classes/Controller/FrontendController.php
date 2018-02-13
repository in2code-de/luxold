<?php
declare(strict_types=1);
namespace In2code\Lux\Controller;

use In2code\Lux\Domain\Factory\AttributeFactory;
use In2code\Lux\Domain\Factory\VisitorFactory;
use In2code\Lux\Signal\SignalTrait;
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
     * @param string $idCookie
     * @param string $email
     * @return string
     */
    public function email4LinkRequestAction(string $idCookie, string $email): string
    {
        $attributeFactory = $this->objectManager->get(
            AttributeFactory::class,
            $idCookie,
            AttributeFactory::CONTEXT_EMAIL4LINK
        );
        $attributeFactory->getVisitorAndAddAttribute('email', $email);
        return json_encode([]);
    }
}
