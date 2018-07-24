<?php

namespace ApiClient\App;

use ApiClient\Config\Config;
use ApiClient\Model\Task;
use ApiClient\Model\Action;
use ApiClient\Model\Transfer;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/** Управление открытыми задачами */
class OpenTaskManager extends TaskManager
{
    /** @var Action $action действие первой открытой задачи */
    private $action;

    /**
     * @return Action
     */
    public function getAction(): Action
    {
        return $this->action;
    }

    /**
     * @param Action $action
     * @return OpenTaskManager
     */
    public function setAction(Action $action): OpenTaskManager
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Получает все открытые задачи в рамках одного действия
     * @return array|null
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function getTasks(): ?array
    {
        if(empty($this->tasks)){
            $action = $this->getTaskRepository()->getFirstOpenAction();

            if(is_null($action))
                return null;

            $this->setAction($action);
            $this->setTasks($this->getTaskRepository()->getOpenTasks($action));
            $this->openTasks();
        }

        return $this->tasks;
    }

    /**
     * Для открытых задач в очереди: устанавливает inWork = true
     * @throws ORMException
     * @throws OptimisticLockException
     * @return OpenTaskManager
     */
    private function openTasks(): OpenTaskManager
    {
        $this->getTaskRepository()->setInWorkForTasks(
            $this->getTasks(), true
        );

        return $this;
    }

    /**
     * Устанавливает статус для открытых задач в очереди
     * @param string $status
     * @return OpenTaskManager
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function updateStatus(string $status): OpenTaskManager
    {
        foreach($this->getTasks() as &$task){

            if($task->getAttempt() + 1 == Config::get('attemptLimit')){
                $status = Status::CANCEL;
            }

            $task = $this->setStatus($task, $status);
            $task->setAttempt($task->getAttempt() + 1);
            $task->setInWork(false);
        }

        return $this;
    }

    /**
     * Обновляет открытые задачи
     * @param array $tasksData
     * @return OpenTaskManager
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function updateTasks(array $tasksData): OpenTaskManager
    {
        foreach($this->getTasks() as &$task){
            foreach($tasksData as $taskData){
                if($task instanceof Task and
                    $task->getId() == $taskData->taskId){

                    $status = $taskData->status;

                    if($task->getAttempt() + 1 == Config::get('attemptLimit')){
                        $status = Status::CANCEL;
                    }

                    $task = $this->setStatus($task, $status);
                    $task->setDescription(strlen($taskData->description) > 0 ? $taskData->description : null);
                    $task->setAttempt($task->getAttempt() + 1);
                    $task->setInWork(false);
                }
            }
        }

        return $this;
    }

    /**
     * Устанавливает статус для задачи и зависимым от нее задачам
     * @param Task $task
     * @param string $status
     * @return Task
     * @throws ORMException
     */
    private function setStatus(Task $task, string $status): Task
    {
        switch ($status) {
            case Status::SUCCESS:
            case Status::OPEN:
                if ($task->getStatus() == Status::ERROR) {
                    $this->updateStatusForLinkTasks($task, Status::OPEN);
                }
                $task->setStatus(Status::CLOSE);
                break;
            case Status::ERROR:
            case Status::REJECT:
            case Status::CANCEL:
                $task->setStatus($status);
                $this->updateStatusForLinkTasks($task, Status::BLOCK);
                break;
        }

        return $task;
    }

    /**
     * Устанавливает статус для связанных задач
     * @param Task $task
     * @param string $status
     * @throws ORMException
     */
    private function updateStatusForLinkTasks(Task $task, string $status)
    {
        $linkTasks = $this->getTaskRepository()->getLinkTasks($task);

        if(count($linkTasks)){
            foreach($linkTasks as &$linkTask){
                if($linkTask instanceof Task) {
                    $linkTask->setStatus($status);
                }
            }

            $this->getTaskRepository()->updateTasks($linkTasks);
        }
    }

    /**
     * Устанавливает передачу для открытых задач
     * @param Transfer $transfer
     * @return OpenTaskManager
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function setTransfer(Transfer $transfer): OpenTaskManager
    {
        foreach ($this->getTasks() as &$task){
            if($task instanceof Task){
                $task->addTransfer($transfer);
            }
        }

        return $this;
    }

}