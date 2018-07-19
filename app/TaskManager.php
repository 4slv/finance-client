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

    /**
     * TaskManager constructor.
     * @throws ApiClientException
     */
    public function __construct()
    {
        $this->em = LocalEntityManager::getEntityManager();
        $this->taskRepository = $this->em->getRepository(Task::class);
    }

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
     * Для открытых задач в очереди: устанавливает inWork = true и attempt++
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function startOpenTasks()
    {
        foreach($this->getOpenTasks() as $openTask){
            if($openTask instanceof Task){
                $openTask->setAttempt($openTask->getAttempt() + 1);
                $openTask->setInWork(true);
                $this->em->persist($openTask);
            }
        }

        $this->em->flush();
    }

    /**
     * Устанавливает статус для открытых задач в очереди
     * @param string $status
     * @param Transfer $transfer
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setStatusForAllOpenTasks(string $status, Transfer $transfer)
    {
        foreach($this->getOpenTasks() as $task){
            $transfer->addTask($task);
            $this->changeStatus($task, (new Status($status))->getValue());
        }

        $this->em->persist($transfer);
        $this->em->flush();
    }

    /**
     * Устанавливает статус для открытых задач в очереди
     * @param array $tasksData
     * @param Transfer $transfer
     * @param string|null $status если указан, то установится для всех задач
     * @throws ApiClientException
     * @throws \Doctrine\ORM\ORMException
     */
    public function updateTasks(array $tasksData, Transfer $transfer, string $status = null)
    {
        foreach($tasksData as $taskData){
            $task = $this->taskRepository->find($taskData->taskId);

            if(!$task or !$task instanceof Task){
                throw new ApiClientException("Задача #%d не найдена", $taskData->taskId);
            }

            if(!in_array($taskData->status, [Status::ERROR, Status::REJECT, Status::SUCCESS])){
                throw new ApiClientException(
                    "Не корректный статус '%s' для задачи #%d", $taskData->status, $taskData->taskId
                );
            }

            if(!is_null($status)){
                $taskData->status = (new Status($status))->getValue();
            }

            $task->setDescription($taskData->description ?? null);
            $transfer->addTask($task);
            $this->changeStatus($task, $taskData->status);
        }

        $this->em->persist($transfer);
        $this->em->flush();
    }

    /**
     * Устанавливает статус для задачи и зависимым от нее задачам
     * @param Task $task
     * @param Status $status
     * @throws \Doctrine\ORM\ORMException
     */
    private function changeStatus(Task $task, $status)
    {
        //print $task->getId().' - '.$status.PHP_EOL;

        switch($status){
            case Status::SUCCESS or Status::OPEN:
                if($task->getStatus() == Status::ERROR){
                    foreach($this->taskRepository->getOpenLinkTasks($task) as $openLinkTask){
                        $openLinkTask->setStatus(Status::OPEN);
                        $this->em->persist($openLinkTask);
                    }
                }
                $task->setStatus(Status::CLOSE);
                break;
            case Status::ERROR:
                if($task->getStatus() == Status::OPEN){
                    $task->setStatus(Status::ERROR);
                    foreach($this->taskRepository->getOpenLinkTasks($task) as $openLinkTask){
                        $openLinkTask->setStatus(Status::BLOCK);
                        $this->em->persist($openLinkTask);
                    }
                }
                break;
            case Status::REJECT:
                $task->setStatus(Status::REJECT);
                foreach($this->taskRepository->getOpenLinkTasks($task) as $openLinkTask){
                    $openLinkTask->setStatus(Status::REJECT);
                    $this->em->persist($openLinkTask);
                }
                break;
        }

        $task->setInWork(false);

        $this->em->persist($task);
        $this->em->flush();
    }

}