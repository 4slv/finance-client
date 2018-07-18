<?php

namespace ApiClient\App;

use ApiClient\Model\Task;
use Doctrine\ORM\EntityManager;

class TaskManager
{
    /** @var Task[] $tasks */
    private $tasks;

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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save()
    {
        foreach($this->getTasks() as $task){
            if($task instanceof Task){
                $this->em->persist($task);
            }
        }

        $this->em->flush();
    }
}