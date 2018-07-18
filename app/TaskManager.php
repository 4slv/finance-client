<?php

namespace ApiClient\App;

use ApiClient\Model\Task;
use Doctrine\ORM\EntityManager;
use Framework\Database\CEntityManager;
use ApiClient\Model\Action;

class TaskManager
{
    /** @var Task[] $tasks */
    private $tasks = [];

    /** @var Action $firstOpenAction действие первой открытой задачи */
    private $firstOpenAction;

    /** @var EntityManager $em */
    private $em;

    public function __construct()
    {
        $this->em = CEntityManager::getInstance();
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

    /** Получает все открытые задачи в рамках одного действия */
    public function getTasksForTransport(): array
    {
        $taskRepository = $this->em->getRepository(Task::class);
        $this->setFirstOpenAction($taskRepository->getFirstOpenAction());
        return $taskRepository->getOpenTasks($this->getFirstOpenAction());
    }
}