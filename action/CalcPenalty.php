<?php

namespace ApiClient\Action;

use ApiClient\Exception\ApiClientException;
use ApiClient\Model\Task;

class CalcPenalty extends ActionAbstract
{
    /**
     * @return void
     * @throws ApiClientException
     */
    public function generateTasks(): void
    {
        if(is_null($this->getTaskManager()))
            throw new ApiClientException("TaskManager is not initialized");

        if(is_null($this->getActionModel()))
            throw new ApiClientException("Model Action is not initialized");

        //$outCredits = $calcPenalty->getCreditsQueue($requestIds);

        //в цикле создаем задачу для каждого действия и добавляем ее в менеджер задач
        /** @var array $parameters параметры задачи */
        //foreach(){
            $task = new Task();
            $this->getActionModel()->setParameters($parameters);
            $task->setAction($this->getActionModel());
            $this->getTaskManager()->addTask($task);
        //}
    }
}