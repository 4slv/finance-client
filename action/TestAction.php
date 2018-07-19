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

        //

        //в цикле создаем задачу для каждого действия и добавляем ее в менеджер задач
        for($i = 0; $i < 25; $i++){
            $task = new Task();
            $task->setCreditId(($i+100)*3);
            $task->setParameters(['param1' => 'value1', 'param2' => $i]);
            $task->setAction($this->getActionModel());
            $this->getTaskManager()->addTask($task);
        }
    }
}