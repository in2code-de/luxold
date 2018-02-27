<?php
declare(strict_types=1);
namespace In2code\Lux\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class WorkflowController
 */
class WorkflowController extends ActionController
{

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
    }

    public function createAction()
    {

    }
}
