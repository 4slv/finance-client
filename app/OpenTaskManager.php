<?php

namespace ApiClient\App;

use ApiClient\Model\Task;
use ApiClient\Model\Action;
use ApiClient\Model\Transfer;
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
     * @throws \Doctrine\ORM\OptimisticLockException
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
     * @throws \Doctrine\ORM\OptimisticLockException
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
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateStatus(string $status): OpenTaskManager
    {
        foreach($this->getTasks() as &$task){
            $this->setStatus($task, $status);
        }

        return $this;
    }

    /**
     * Обновляет открытые задачи
     * @param array $tasksData
     * @param string|null $status если указан, то установится для всех открытых задач в очереди
     * @return OpenTaskManager
     * @throws ApiClientException
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateTasks(array $tasksData, string $status = null): OpenTaskManager
    {
        foreach($tasksData as $taskData){

            $task = $this->getTaskFromData($taskData->taskId);

            if(!is_null($status)){
                $taskData->status = (new Status($status))->getValue();
            }

            $task = $this->setStatus($task, $taskData->status);
            $task->setDescription($taskData->description ?? null);
            $task->setAttempt($task->getAttempt() + 1);
            $task->setInWork(false);
        }

        return $this;
    }

    /**
     * Поиск открытой задачи по id
     * @param int $taskId
     * @return Task
     * @throws ApiClientException
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function getTaskFromData(int $taskId): Task
    {
        foreach ($this->getTasks() as $task){
            if($task->getId() == $taskId){
                return $task;
            }
        }

        throw new ApiClientException("Открытая задача #%d не найдена", $taskId);
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
                if ($task->getStatus() == Status::OPEN) {
                    $task->setStatus(Status::ERROR);
                    $this->updateStatusForLinkTasks($task, Status::BLOCK);
                }
                break;
            case Status::REJECT:
                $task->setStatus(Status::REJECT);
                $this->updateStatusForLinkTasks($task, Status::REJECT);
                break;
        }

        return $task;
    }

    /**
     * Устанавливает статус для всех связанных задач
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
     * @param Transfer $transfer
     * @return OpenTaskManager
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
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