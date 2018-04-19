<?php
declare(strict_types=1);
namespace In2code\Lux\Command;

use In2code\Lux\Domain\Repository\VisitorRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/**
 * Class LuxLeadCommandController
 */
class LuxLeadCommandController extends CommandController
{

    /**
     * @var VisitorRepository
     */
    protected $visitorRepository = null;

    /**
     * Send a summary of leads
     *
     *      Send a summary of leads to one or more email addresses as a table. Define if leads should be identified or
     *      not and if they should have a minimum scoring to be sent.
     *
     * @param string $emails Commaseparated value of email addresses for receiving mails
     * @param bool $identifiedOnly Send only identified leads
     * @param int $minimumScoring Send only leads with a minimum scoring of this value.
     * @return void
     */
    public function sendSummaryCommand(
        string $emails = '',
        bool $identifiedOnly = true,
        int $minimumScoring = 0
    ) {
    }

    /**
     * Send a summary of leads of a lux category
     *
     *      Send a summary of leads to one or more email addresses as a table. Define if leads should be identified or
     *      not and if you want only leads from a given category. Also a minimum scoring is possible.
     *
     * @param string $emails Commaseparated value of email addresses for receiving mails.
     * @param bool $identifiedOnly Send only identified leads.
     * @param int $luxCategory Send only leads that have a scoring in this category.
     * @param int $minimumCategoryScoring Send only leads with a minimum category scoring of this value.
     * @return void
     */
    public function sendSummaryOfLuxCategoryCommand(
        string $emails = '',
        bool $identifiedOnly = true,
        int $luxCategory = 0,
        int $minimumCategoryScoring = 1
    ) {
    }

    /**
     * Send a summary of leads with known companies
     *
     *      Send a summary of leads with known companies to one or more email addresses as a table. Define if leads
     *      should have a minimum scoring (0 disables this function). Also define if only leads should be send with
     *      a scoring in a category.
     *
     * @param string $emails Commaseparated value of email addresses for receiving mails.
     * @param int $minimumScoring Send only leads with a minimum scoring of this value.
     * @param int $luxCategory Send only leads that have a scoring in this category (0 disables this feature).
     * @param int $minimumCategoryScoring Send only leads with a minimum category scoring of this value.
     * @return void
     */
    public function sendSummaryOfKnownCompaniesCommand(
        string $emails = '',
        int $minimumScoring = 0,
        int $luxCategory = 0,
        int $minimumCategoryScoring = 1
    ) {
    }

    /**
     * @param VisitorRepository $visitorRepository
     * @return void
     */
    public function injectWorkflowRepository(VisitorRepository $visitorRepository)
    {
        $this->visitorRepository = $visitorRepository;
    }
}
