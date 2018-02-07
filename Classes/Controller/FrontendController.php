<?php
declare(strict_types=1);
namespace In2code\Lux\Controller;

use In2code\Lux\Domain\Factory\AttributeFactory;
use In2code\Lux\Domain\Factory\VisitorFactory;
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
     * @return string
     */
    public function pageRequestAction(string $idCookie, int $languageUid, int $pageUid): string
    {
        $visitorFactory = $this->objectManager->get(VisitorFactory::class, $idCookie, $pageUid);
        $visitor = $visitorFactory->getVisitor();
        return json_encode([]);
    }

    /**
     * @param string $idCookie
     * @param string $key
     * @param string $value
     * @return string
     */
    public function fieldListeningRequestAction(string $idCookie, string $key, string $value)
    {
        $attributeFactory = $this->objectManager->get(AttributeFactory::class, $idCookie);
        $visitor = $attributeFactory->getVisitorAndAddAttribute($key, $value);
        return json_encode([]);
    }
}
