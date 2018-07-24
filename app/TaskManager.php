<?php

namespace ApiClient\App;

use ApiClient\Model\Task;
use ApiClient\Repository\TaskRepository;
use Doctrine\ORM\ORMException;

/** Управление задачами */
class TaskManager
{
    /** @var Task[] $tasks */
    protected $tasks = [];

    /** @var TaskRepository|
     * \Doctrine\Common\Persistence\ObjectRepository|
     * \Doctrine\ORM\EntityRepository $taskRepository
     */
    private $taskRepository;

    public function addTask(Task $task): TaskManager
    {
        array_push($this->tasks, $task);

        return $this;
    }

    public function getTasks(): ?array
    {
        return $this->tasks;
    }

    /**
     * @param array $tasks
     * @return TaskManager
     */
    public function setTasks(array $tasks): TaskManager
    {
        $this->tasks = $tasks;
        return $this;
    }

    /**
     * @return TaskRepository
     */
    public function getTaskRepository(): TaskRepository
    {
        return $this->taskRepository;
    }

    /**
     * @param TaskRepository $taskRepository
     * @return TaskManager
     */
    public function setTaskRepository(TaskRepository $taskRepository): TaskManager
    {
        $this->taskRepository = $taskRepository;
        return $this;
    }

    /**
     * Сохраняет задачи в БД
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save()
    {
        $this->getTaskRepository()->updateTasks($this->getTasks());
    }
}