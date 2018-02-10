<?php
declare(strict_types=1);
namespace In2code\Lux\Controller;

use In2code\Lux\Domain\Repository\LogRepository;
use In2code\Lux\Domain\Repository\VisitorRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class AnalysisController
 */
class AnalysisController extends ActionController
{

    /**
     * @var VisitorRepository|null
     */
    protected $visitorRepository = null;

    /**
     * @var LogRepository|null
     */
    protected $logRepository = null;

    /**
     * @return void
     */
    public function dashboardAction()
    {
        $uniqueVisits = $this->visitorRepository->findByUniqueSiteVisits()->count();
        $recurringVisits = $this->visitorRepository->findByRecurringSiteVisits()->count();
        $identifiedVisits = $this->visitorRepository->findIdentified()->count();
        $unknownVisits = $this->visitorRepository->findUnknown()->count();
        $interestingLogs = $this->logRepository->findInterestingLogs();
        $this->view->assignMultiple([
            'numberOfUniqueSiteVisitors' => $uniqueVisits,
            'numberOfRecurringSiteVisitors' => $recurringVisits,
            'numberOfIdentifiedVisitors' => $identifiedVisits,
            'numberOfUnknownVisitors' => $unknownVisits,
            'interestingLogs' => $interestingLogs
        ]);
    }

    /**
     * @param VisitorRepository $visitorRepository
     * @return void
     */
    public function injectFormRepository(VisitorRepository $visitorRepository)
    {
        $this->visitorRepository = $visitorRepository;
    }

    /**
     * @param LogRepository $logRepository
     * @return void
     */
    public function injectLogRepository(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
    }
}
