<?php
namespace In2code\Lux\Hooks;

use In2code\Lux\Domain\Repository\VisitorRepository;
use In2code\Lux\Utility\ObjectUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class PageLayoutHeader
 */
class PageLayoutHeader
{

    /**
     * @var string
     */
    protected $templatePathAndFile = 'EXT:lux/Resources/Private/Templates/Backend/PageOverview.html';

    /**
     * @param array $parameters
     * @param object $parentObject
     * @return string
     */
    public function render(array $parameters, $parentObject): string
    {
        unset($parameters);
        $pageIdentifier = $parentObject->id;
        $visitorRepository = ObjectUtility::getObjectManager()->get(VisitorRepository::class);
        $visitors = $visitorRepository->findByVisitedPageIdentifier($pageIdentifier);
        $standaloneView = ObjectUtility::getObjectManager()->get(StandaloneView::class);
        $standaloneView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($this->templatePathAndFile));
        $standaloneView->assignMultiple([
            'visitors' => $visitors,
            'pageIdentifier' => $pageIdentifier
        ]);
        return $standaloneView->render();
    }
}
