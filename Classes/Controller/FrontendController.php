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
     * @return void
     */
    public function pageRequestAction(string $idCookie, int $languageUid, int $pageUid)
    {
        $visitorFactory = $this->objectManager->get(VisitorFactory::class, $idCookie, $pageUid);
        $visitor = $visitorFactory->getVisitor();
        return json_encode([]);
    }

    /**
     * @param string $idCookie
     * @param string $key
     * @param string $value
     * @return void
     */
    public function fieldListeningRequestAction(string $idCookie, string $key, string $value)
    {
        $attributeFactory = $this->objectManager->get(AttributeFactory::class, $idCookie);
        $visitor = $attributeFactory->getVisitorAndAddAttribute($key, $value);
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($visitor, 'in2code: ' . __CLASS__ . ':' . __LINE__);die('hard');
        return json_encode([]);
    }
}
