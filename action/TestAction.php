<?php

namespace ApiClient\Action;

use ApiClient\App\ApiClientException;
use ApiClient\Model\Task;

class TestAction extends ActionAbstract
{
    /**
     * @return void
     * @throws ApiClientException
     */
    public function generateTasks(): void
    {
        parent::generateTasks();

        foreach($this->getActionModel()->getParameters() as $parameter){
            $task = new Task();
            $task->setCreditId($parameter);
            $task->setParameters(['param1' => 'value1']);
            $task->setAction($this->getActionModel());
            $this->getTaskManager()->addTask($task);
        }
    }
}