<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Trigger;

use In2code\Lux\Domain\Repository\CategoryRepository;
use In2code\Lux\Utility\FrontendUtility;
use In2code\Lux\Utility\ObjectUtility;

/**
 * Class CategoryTrigger
 */
class CategoryTrigger extends AbstractTrigger implements TriggerInterface
{

    /**
     * @return bool
     */
    public function isTriggered(): bool
    {
        // todo respect mode $this->getConfigurationByKey('mode')
        return $this->isPageOfSelectedCategory() || $this->isDownloadOfSelectedCategory();
    }

    /**
     * @return bool
     */
    protected function isPageOfSelectedCategory(): bool
    {
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = ObjectUtility::getObjectManager()->get(CategoryRepository::class);
        return $categoryRepository->isPageIdentifierRelatedToCategoryIdentifier(
            FrontendUtility::getCurrentPageIdentifier(),
            (int)$this->getConfigurationByKey('category')
        );
    }

    /**
     * Check if there was a download in the past of the given category
     *
     * @return bool
     */
    protected function isDownloadOfSelectedCategory(): bool
    {
        // todo implement download trigger
        //foreach ($this->getVisitor()->getDownloads() as $download) {
        //}
        return true;
    }
}
