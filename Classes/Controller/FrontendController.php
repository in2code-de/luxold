<?php
declare(strict_types=1);
namespace In2code\Lux\Controller;

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
}
