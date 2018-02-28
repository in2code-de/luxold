<?php
declare(strict_types=1);
namespace In2code\Lux\Controller;

use In2code\Lux\Domain\Trigger\Helper\TriggerService;
use In2code\Lux\Utility\ObjectUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class WorkflowController
 */
class WorkflowController extends ActionController
{

    /**
     * @var TriggerService
     */
    protected $triggerService = null;

    /**
     * WorkflowController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->triggerService = ObjectUtility::getObjectManager()->get(TriggerService::class);
    }

    /**
     * @return void
     */
    public function listAction()
    {
        $this->view->assign('workflowss', 13);
    }

    /**
     * @return void
     */
    public function newAction()
    {
        $this->view->assign('triggers', $this->triggerService->getAllTriggersAsOptions());
    }

    public function createAction()
    {
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function addTriggerAjax(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $trigger = $request->getQueryParams()['trigger'];
        $triggerSettings = $this->triggerService->getTriggerSettingsFromClassName($trigger);
        /** @var StandaloneView $view */
        $view = ObjectUtility::getObjectManager()->get(StandaloneView::class);
        $view->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName($triggerSettings['templateFile'])
        );
        $view->assignMultiple($triggerSettings);
        $response->getBody()->write(json_encode(['html' => $view->render()]));
        return $response;
    }
}
