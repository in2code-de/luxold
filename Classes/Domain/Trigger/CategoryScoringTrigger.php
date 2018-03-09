<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Trigger;

use In2code\Lux\Domain\Repository\CategoryRepository;
use In2code\Lux\Utility\ObjectUtility;

/**
 * Class CategoryScoringTrigger
 */
class CategoryScoringTrigger extends AbstractTrigger implements TriggerInterface
{

    /**
     * @return bool
     */
    public function isTriggered(): bool
    {
        $category = ObjectUtility::getObjectManager()->get(CategoryRepository::class)->findByUid(
            (int)$this->getConfigurationByKey('category')
        );
        return $this->getVisitor()->getCategoryscoringByCategory($category)->getScoring()
            >= $this->getConfigurationByKey('scoring');
    }
}
