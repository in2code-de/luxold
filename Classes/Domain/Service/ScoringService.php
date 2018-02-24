<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Service;

use In2code\Lux\Domain\Model\Pagevisit;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Repository\DownloadRepository;
use In2code\Lux\Domain\Repository\PagevisitRepository;
use In2code\Lux\Domain\Repository\VisitorRepository;
use In2code\Lux\Utility\ConfigurationUtility;
use In2code\Lux\Utility\ObjectUtility;
use jlawrence\eos\Parser;

/**
 * Class ScoringService to calculate a scoring to a visitor
 */
class ScoringService
{

    /**
     * Calculation string like "(10 * numberOfSiteVisits)"
     *
     * @var string
     */
    protected $calculation = '';

    /**
     * @var \DateTime|null
     */
    protected $time = null;

    /**
     * ScoringService constructor.
     *
     * @param \DateTime|null $time Set a time if you want to calculate a scoring from the past
     */
    public function __construct(\DateTime $time = null)
    {
        if (!class_exists(Parser::class)) {
            throw new \BadFunctionCallException('Parser class not found. Did you do a "composer update"?', 1518975126);
        }
        if ($time !== null) {
            $this->time = $time;
        } else {
            $this->time = new \DateTime();
        }
        $this->setCalculation();
    }

    /**
     * @param Visitor $visitor
     * @return void
     */
    public function calculateAndSetScoring(Visitor $visitor)
    {
        $scoring = $this->calculateScoring($visitor);
        $visitor->setScoring($scoring);
        $visitorRepository = ObjectUtility::getObjectManager()->get(VisitorRepository::class);
        $visitorRepository->update($visitor);
        $visitorRepository->persistAll();
    }

    /**
     * @param Visitor $visitor
     * @return int Integer value 0 or higher
     */
    public function calculateScoring(Visitor $visitor): int
    {
        $variables = [
            'numberOfSiteVisits' => $this->getNumberOfSiteVisits($visitor),
            'numberOfPageVisits' => $this->getNumberOfVisits($visitor),
            'lastVisitDaysAgo' => $this->getNumberOfDaysSinceLastVisit($visitor),
            'downloads' => $this->getNumberOfDownloads($visitor)
        ];
        $scoring = (int)Parser::solve($this->getCalculation(), $variables);
        if ($scoring < 0) {
            $scoring = 0;
        }
        return $scoring;
    }

    /**
     * @param Visitor $visitor
     * @return int
     */
    protected function getNumberOfSiteVisits(Visitor $visitor): int
    {
        /** @var PagevisitRepository $pagevisitRepository */
        $pagevisitRepository = ObjectUtility::getObjectManager()->get(PagevisitRepository::class);
        $pagevisits = $pagevisitRepository->findByVisitorAndTime($visitor, $this->time);
        $sitevisits = 0;
        if ($pagevisits > 0) {
            $lastVisit = null;
            foreach ($pagevisits as $pagevisit) {
                if ($lastVisit !== null) {
                    /** @var Pagevisit $pagevisit */
                    $interval = $lastVisit->diff($pagevisit->getCrdate());
                    // if difference is greater then one hour
                    if ($interval->h > 0) {
                        $sitevisits++;
                    }
                }
                $lastVisit = $pagevisit->getCrdate();
            }
        }
        return $sitevisits;
    }

    /**
     * @param Visitor $visitor
     * @return int
     */
    protected function getNumberOfVisits(Visitor $visitor): int
    {
        /** @var PagevisitRepository $pagevisitRepository */
        $pagevisitRepository = ObjectUtility::getObjectManager()->get(PagevisitRepository::class);
        $pagevisits = $pagevisitRepository->findByVisitorAndTime($visitor, $this->time);
        return $pagevisits->count();
    }

    /**
     * @param Visitor $visitor
     * @return int
     */
    protected function getNumberOfDaysSinceLastVisit(Visitor $visitor): int
    {
        $days = 50;
        $pagevisitRepository = ObjectUtility::getObjectManager()->get(PagevisitRepository::class);
        /** @var Pagevisit $lastPagevisit */
        $lastPagevisit = $pagevisitRepository->findLastByVisitorAndTime($visitor, $this->time);
        if ($lastPagevisit !== null) {
            $delta = $this->time->diff($lastPagevisit->getCrdate());
            $days = $delta->d;
        }
        return $days;
    }

    /**
     * @param Visitor $visitor
     * @return int
     */
    protected function getNumberOfDownloads(Visitor $visitor): int
    {
        /** @var DownloadRepository $downloadRepository */
        $downloadRepository = ObjectUtility::getObjectManager()->get(DownloadRepository::class);
        $downloads = $downloadRepository->findByVisitorAndTime($visitor, $this->time)->count();
        return $downloads;
    }

    /**
     * @return string
     */
    public function getCalculation(): string
    {
        return $this->calculation;
    }

    /**
     * @return void
     */
    public function setCalculation()
    {
        $this->calculation = ConfigurationUtility::getScoringCalculation();
    }
}
