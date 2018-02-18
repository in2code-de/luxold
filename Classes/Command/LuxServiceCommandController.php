<?php
declare(strict_types=1);
namespace In2code\Lux\Command;

use In2code\Lux\Domain\Repository\VisitorRepository;
use In2code\Lux\Domain\Service\ScoringService;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/**
 * Class LuxServiceCommandController
 */
class LuxServiceCommandController extends CommandController
{

    /**
     * Recalculate scoring of all visitors
     *
     *      Recalculate scoring of all visitors
     *
     * @param string $calculation
     * @return void
     */
    public function reCalculateScoringCommand(
        string $calculation = '(10 * numberOfSiteVisits) + (1 * numberOfPageVisits) + (20 * downloads) - (1 * lastVisitDaysAgo)'
    ) {
        $scoringService = $this->objectManager->get(ScoringService::class);
        $visitorRepository = $this->objectManager->get(VisitorRepository::class);
        $visitors = $visitorRepository->findAll();
        foreach ($visitors as $visitor) {
            $scoringService->setCalculation($calculation);
            $scoringService->calculateScoring($visitor);
        }
    }
}
