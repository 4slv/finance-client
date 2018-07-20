<?php

namespace ApiClient\App;

use ApiClient\Config\LocalEntityManager;
use ApiClient\Model\Task;
use ApiClient\Model\Transfer;
use Doctrine\ORM\EntityManager;
use ApiClient\Model\Action;

/** Управление задачами */
class TaskManager
{
    /** @var Task[] $tasks */
    private $tasks = [];

    /** @var Task[] $openTasks открытые задачи, взятые в работу */
    private $openTasks = [];

    /** @var Action $firstOpenAction действие первой открытой задачи */
    private $firstOpenAction;

    /** @var EntityManager $em */
    private $em;

    /** @var \ApiClient\Repository\TaskRepository|
     * \Doctrine\Common\Persistence\ObjectRepository|
     * \Doctrine\ORM\EntityRepository $taskRepository
     */
    private $taskRepository;

    public function addTask(Task $task): TaskManager
    {
        array_push($this->tasks, $task);

        return $this;
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }

    /**
     * @return EntityManager
     */
    public function getEm(): EntityManager
    {
        return $this->em;
    }

    /**
     * @param EntityManager $em
     * @return TaskManager
     */
    public function setEm(EntityManager $em): TaskManager
    {
        $this->em = $em;
        return $this;
    }

    /**
     * @return \ApiClient\Repository\TaskRepository
     */
    public function getTaskRepository(): \ApiClient\Repository\TaskRepository
    {
        return $this->taskRepository;
    }

    /**
     * @param \ApiClient\Repository\TaskRepository $taskRepository
     * @return TaskManager
     */
    public function setTaskRepository(\ApiClient\Repository\TaskRepository $taskRepository): TaskManager
    {
        $this->taskRepository = $taskRepository;
        return $this;
    }

    /**
     * @return Action
     */
    public function getFirstOpenAction(): Action
    {
        return $this->firstOpenAction;
    }

    /**
     * @param Action $firstOpenAction
     * @return TaskManager
     */
    public function setFirstOpenAction(Action $firstOpenAction): TaskManager
    {
        $this->firstOpenAction = $firstOpenAction;
        return $this;
    }

    /**
     * @param Task[] $openTasks
     * @return TaskManager
     */
    public function setOpenTasks(array $openTasks): TaskManager
    {
        $this->openTasks = $openTasks;
        return $this;
    }

    /**
     * Сохраняет задачи в БД
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save()
    {
        foreach($this->getTasks() as $task){
            if($task instanceof Task){
                $this->em->persist($task->getAction());
                $this->em->persist($task);
            }
        }

        $this->em->flush();
    }

    /**
     * Получает все открытые задачи в рамках одного действия
     * @return array|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getOpenTasks(): ?array
    {
        if(empty($this->openTasks)){
            $action = $this->taskRepository->getFirstOpenAction();

            if(is_null($action))
                return null;

            $this->setFirstOpenAction($action);
            $this->setOpenTasks($this->taskRepository->getOpenTasks($action));
            $this->startOpenTasks();
        }

        return $this->openTasks;
    }

    /**
     * Для открытых задач в очереди: устанавливает inWork = true
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function startOpenTasks()
    {
        $this->taskRepository->setInWorkForTasks(
            $this->getOpenTasks(), true
        );
    }

    /**
     * Устанавливает статус для открытых задач в очереди
     * @param string $status
     * @param Transfer $transfer
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateStatusForAllOpenTasks(string $status, Transfer $transfer)
    {
        foreach($this->getOpenTasks() as $task){
            $task = $this->updateStatus($task, (new Status($status))->getValue());
            $task->addTransfer($transfer);
            $this->em->persist($task);
        }

        $this->em->flush();
    }

    /**
     * Устанавливает статус для открытых задач в очереди
     * @param array $tasksData
     * @param Transfer $transfer
     * @param string|null $status если указан, то установится для всех открытых задач в очереди
     * @throws ApiClientException
     * @throws \Doctrine\ORM\ORMException
     */
    public function updateTasks(array $tasksData, Transfer $transfer, string $status = null)
    {
        $updatedTasks = [];

        foreach($tasksData as $taskData){

            $task = $this->getTaskFromData($taskData);

            if(!is_null($status)){
                $taskData->status = (new Status($status))->getValue();
            }

            $task = $this->updateStatus($task, $taskData->status);
            $task->setDescription($taskData->description ?? null);
            $task->setAttempt($task->getAttempt() + 1);
            $task->setInWork(false);
            $task->addTransfer($transfer);

            array_push($updatedTasks, $task);
        }

        $this->taskRepository->updateTasks($updatedTasks);
    }

    /**
     * Получает объект задачи из массива ответа сервера
     * @param $taskData
     * @return Task
     * @throws ApiClientException
     */
    private function getTaskFromData($taskData): Task
    {
        $task = $this->taskRepository->find($taskData->taskId);

        if(!$task or !$task instanceof Task){
            throw new ApiClientException("Задача #%d не найдена", $taskData->taskId);
        }

        if(!in_array($taskData->status, [Status::ERROR, Status::REJECT, Status::SUCCESS])){
            throw new ApiClientException(
                "Не корректный статус '%s' для задачи #%d", $taskData->status, $taskData->taskId
            );
        }

        return $task;
    }

    /**
     * Устанавливает статус для задачи и зависимым от нее задачам
     * @param Task $task
     * @param Status $status
     * @throws \Doctrine\ORM\ORMException
     */
    private function updateStatus(Task $task, $status): Task
    {
        switch($status){
            case Status::SUCCESS or Status::OPEN:
                if($task->getStatus() == Status::ERROR){
                    $this->updateStatusForLinkTasks($task, Status::OPEN);
                }
                $task->setStatus(Status::CLOSE);
                break;
            case Status::ERROR:
                if($task->getStatus() == Status::OPEN){
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
     * @throws \Doctrine\ORM\ORMException
     */
    public function updateStatusForLinkTasks(Task $task, string $status)
    {
        $openLinkTasks = $this->getOpenLinkTasks($task);
        if(count($openLinkTasks)){
            foreach($openLinkTasks as &$openLinkTask){
                $openLinkTask->setStatus($status);
            }

            $this->taskRepository->updateTasks($openLinkTasks);
        }
    }

    /**
     * Получает все связанные задачи
     * @param Task $task
     * @return array
     */
    private function getOpenLinkTasks(Task $task)
    {
        return $this->taskRepository->getOpenLinkTasks($task);
    }

}