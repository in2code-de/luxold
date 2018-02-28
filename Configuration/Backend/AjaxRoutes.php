<?php
return [
    '/lux/detail' => [
        'path' => '/lux/detail',
        'target' => \In2code\Lux\Controller\AnalysisController::class . '::detailAjax',
    ],
    '/lux/visitordescription' => [
        'path' => '/lux/visitordescription',
        'target' => \In2code\Lux\Controller\AnalysisController::class . '::detailDescriptionAjax',
    ],
    '/lux/addtrigger' => [
        'path' => '/lux/addtrigger',
        'target' => \In2code\Lux\Controller\WorkflowController::class . '::addTriggerAjax',
    ]
];
