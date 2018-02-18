<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Service;

use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Repository\VisitorRepository;
use In2code\Lux\Utility\ObjectUtility;
use jlawrence\eos\Parser;

/**
 * Class ScoringService
 */
class ScoringService
{

    /**
     * @var string
     */
    protected $calculation = '';

    /**
     * ScoringService constructor.
     */
    public function __construct()
    {
        if (!class_exists(Parser::class)) {
            throw new \BadFunctionCallException('Parser class not found. Did you do a "composer update"?', 1518975126);
        }
        $this->setCalculation();
    }

    /**
     * @param Visitor $visitor
     * @return void
     */
    public function calculateScoring(Visitor $visitor)
    {
        $scoring = $this->getScoring($visitor);
        $visitor->setScoring($scoring);
        $visitorRepository = ObjectUtility::getObjectManager()->get(VisitorRepository::class);
        $visitorRepository->update($visitor);
        $visitorRepository->persistAll();
    }

    /**
     * @param Visitor $visitor
     * @return int
     */
    protected function getScoring(Visitor $visitor): int
    {
        $variables = [
            'numberOfSiteVisits' => $visitor->getVisits(),
            'numberOfPageVisits' => count($visitor->getPagevisits()),
            'lastVisitDaysAgo' => $this->getNumberOfDaysSinceLastVisit($visitor),
            'downloads' => count($visitor->getDownloads())
        ];
        return (int)Parser::solve($this->getCalculation(), $variables);
    }

    /**
     * @param Visitor $visitor
     * @return int
     */
    protected function getNumberOfDaysSinceLastVisit(Visitor $visitor): int
    {
        $days = 50;
        if ($visitor->getLastPagevisit() !== null) {
            $now = new \DateTime();
            $delta = $now->diff($visitor->getLastPagevisit()->getCrdate());
            $days = $delta->d;
        }
        return $days;
    }

    /**
     * @return string
     */
    public function getCalculation(): string
    {
        return $this->calculation;
    }

    /**
     * Calculation can be overruled with a string - otherwise try to get from TypoScript
     *
     * @param string $calculation
     * @return void
     */
    public function setCalculation(string $calculation = '')
    {
        if (!empty($calculation)) {
            $this->calculation = $calculation;
        } else {
            $configurationService = ObjectUtility::getObjectManager()->get(ConfigurationService::class);
            $this->calculation = $configurationService->getTypoScriptSettingsByPath('scoring.calculation');
        }
    }
}
