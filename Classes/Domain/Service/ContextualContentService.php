<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Service;

use In2code\Lux\Domain\Model\Category;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Utility\DatabaseUtility;
use In2code\Lux\Utility\ObjectUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\FlexFormService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class ContextualContentService should decide which content fits most to a current visitor.
 */
class ContextualContentService
{

    /**
     * Content uid of the container
     *
     * @var int
     */
    protected $contentUid = 0;

    /**
     * @var null
     */
    protected $visitor = null;

    /**
     * @var array
     */
    protected $flexFormSettings = [];

    /**
     * ContextualContentService constructor.
     *
     * @param int $contentUid
     * @param Visitor|null $visitor Can be null - in this case the default CE should be rendered
     */
    public function __construct(int $contentUid, $visitor)
    {
        $this->contentUid = $contentUid;
        $this->visitor = $visitor;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        $contentUid = $this->getBestFittingContentUidFromFlexForm();
        if ($this->visitor !== null) {
            /** @var LogService $logService */
            $logService = ObjectUtility::getObjectManager()->get(LogService::class);
            $logService->logContextualContent($this->visitor, $contentUid, $this->contentUid);
        }
        return $this->renderContentElement($contentUid);
    }

    /**
     * @return int
     */
    protected function getBestFittingContentUidFromFlexForm(): int
    {
        $settings = $this->getFlexFormSettings();
        $contentUid = $settings['default'];
        if ($this->visitor !== null) {
            foreach ($this->visitor->getCategoryscoringsSortedByScoring() as $categoryscoring) {
                if ($this->isCategoryDefinedInFlexForm($categoryscoring->getCategory())) {
                    $categorySettings = $this->getCategorySettingsForCategory($categoryscoring->getCategory());
                    $contentUids = GeneralUtility::trimExplode(',', $categorySettings['contentElements'], true);
                    $contentUid = $contentUids[rand(0, count($contentUids) - 1)];
                    break;
                }
            }
        }
        return (int)$contentUid;
    }

    /**
     * @param Category $category
     * @return bool
     */
    protected function isCategoryDefinedInFlexForm(Category $category): bool
    {
        return $this->getCategorySettingsForCategory($category) !== [];
    }

    /**
     * @param Category $category
     * @return array
     */
    protected function getCategorySettingsForCategory(Category $category): array
    {
        $settings = $this->getFlexFormSettings();
        foreach ($settings['content'] as $item) {
            $categoryUid  = (int)$item['element']['category'];
            if ($category->getUid() === $categoryUid) {
                return $item['element'];
            }
        }
        return [];
    }

    /**
     * Get FlexForm configuration from content element
     *
     * @return array
     */
    protected function getFlexFormSettings(): array
    {
        if ($this->flexFormSettings === []) {
            /** @var FlexFormService $ffService */
            $ffService = ObjectUtility::getObjectManager()->get(FlexFormService::class);
            $queryBuilder = DatabaseUtility::getQueryBuilderForTable('tt_content');
            $ffString = $queryBuilder
                ->select('pi_flexform')
                ->from('tt_content')
                ->where('uid=' . (int)$this->contentUid)
                ->execute()
                ->fetchColumn(0);
            $ffArray = $ffService->convertFlexFormContentToArray($ffString);
            $this->flexFormSettings = $flexFormSettings = $ffArray['settings'];
        } else {
            $flexFormSettings = $this->flexFormSettings;
        }
        return $flexFormSettings;
    }

    /**
     * @param int $contentUid
     * @return string
     */
    protected function renderContentElement(int $contentUid): string
    {
        $contentObject = ObjectUtility::getObjectManager()->get(ContentObjectRenderer::class);
        $configuration = ['tables' => 'tt_content', 'source' => $contentUid, 'dontCheckPid' => 1];
        return $contentObject->cObjGetSingle('RECORDS', $configuration);
    }
}
