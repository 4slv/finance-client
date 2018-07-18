<?php

namespace ApiClient\App;

use ApiClient\Action\ActionResolver;
use ApiClient\IO\HttpRequest;
use ApiClient\Model\Action;

class ApiClient
{
    public function __construct()
    {
        //require __DIR__.'\..\vendor\autoload.php';
    }

    /**
     * @param string $actionName
     * @param array $inputParameters
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createTasks(string $actionName, array $inputParameters = [])
    {
        $actionModel = new Action();
        $actionModel->setName($actionName);
        $actionModel->setParameters($inputParameters);

        $taskManager = new TaskManager();

        $actionResolver = new ActionResolver();
        $action = $actionResolver->resolve($actionName);
        $action
            ->setActionModel($actionModel)
            ->setTaskManager($taskManager)
            ->generateTasks();

        $taskManager->save();
    }

    public function transfer()
    {
        $taskManager = new TaskManager();
        $request = new HttpRequest();
        $dispatch = new Dispatch();

        $dispatch
            ->setTaskManager($taskManager)
            ->setRequest($request);

        $openTasks = $dispatch->getTaskManager()->getTasksForTransport();
        $dispatch->createBody()->transfer();
    }
}