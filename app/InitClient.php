<?php

namespace ApiClient\Command;

use ApiClient\Action\CalcPenalty;
use ApiClient\Model\Action;
use ApiClient\Task\TaskManager;

class ApiClient
{
    public function __construct()
    {
        require __DIR__.'\..\vendor\autoload.php';
    }

    /**
     * @param array $inputParameters
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createTasks(array $inputParameters)
    {
        /** @var array $inputParameters внешние параметры */

        $actionModel = new Action();
        $actionModel->setParameters($inputParameters);
        $taskManager = new TaskManager();

        $action = new CalcPenalty(); //todo заменить на фабрику действий
        $action
            ->setActionModel($actionModel)
            ->setTaskManager($taskManager)
            ->generateTasks();

        $taskManager->save();
    }

    public function startTransfer()
    {

    }
}