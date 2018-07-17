<?php

namespace ApiClient\Action;

use ApiClient\Task\Task;
use ApiClient\Task\TaskManager;
use Model\Action;

class CalcPenalty extends ActionAbstract
{
    public function prepare(): ActionAbstract
    {
        // TODO: Implement prepare() method.

        return $this;
    }

    public function createTask()
    {
        foreach($a as $b){
            $task = new Task();
            $task->setParameters($this->getParameters());
            $this->getTaskManager()->addTask($task);
        }
    }
}