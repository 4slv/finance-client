<?php

namespace ApiClient\Action;

use ApiClient\Model\Task;

class TestAction extends ActionAbstract
{
    public function generateTasks()
    {
        foreach($this->getActionModel()->getParameters() as $parameter){
            $task = new Task();
            $task->setProductId($parameter);
            $task->setParameters(['param1' => 'value1']);
            $task->setAction($this->getActionModel());
            $this->getTaskManager()->addTask($task);
        }
    }
}