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

        $task = $action->setParameters($parameters)
            ->prepare()
            ->createTask();
    }
}