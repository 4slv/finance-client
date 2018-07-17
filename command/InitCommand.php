<?php

namespace ApiClient\Command;

use ApiClient\Action\CalcPenalty;
use ApiClient\IO\HttpAbstractRequest;
use ApiClient\IO\RequestResolver;
use ApiClient\Task\Task;
use ApiClient\Task\TaskManager;

class InitCommand
{
    public function init()
    {
        $parameters = [];

        $action = new CalcPenalty(); //todo
        $processPull = $action->setParameters($parameters)
            ->prepare()
            ->createProcessPool();

        $task = new Task();
        $task->setProcessPool($processPull);

        $taskManager = new TaskManager();
        try{
            $taskManager->setTask($task)
                ->createRequest()
                ->sendRequest()
                ->afterRequest();
        }catch(\Exception $e){
            //
        }
    }
}