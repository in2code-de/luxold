<?php
declare(strict_types=1);
namespace In2code\Lux\ViewHelpers\Workflow;

use In2code\Lux\Utility\ObjectUtility;
use In2code\Lux\Domain\Repository\CategoryRepository;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetLuxCategoriesViewHelper
 */
class GetLuxCategoriesViewHelper extends AbstractViewHelper
{

    /**
     * @return QueryResultInterface
     */
    public function render(): QueryResultInterface
    {
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = ObjectUtility::getObjectManager()->get(CategoryRepository::class);
        return $categoryRepository->findAllLuxCategories();
    }
}
