<?php

namespace ApiClient\Repository;

use ApiClient\App\Status;
use ApiClient\Config\Config;
use ApiClient\Model\Action;
use ApiClient\Model\Task;

class TaskRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Получает действие первой открытой задачи
     * @return Action|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getFirstOpenAction(): ?Action
    {
        $task = $this->createQueryBuilder('task')
            ->select('task', 'action')
            ->join('task.action', 'action')
            ->where('task.inWork = :inWork')
            ->andWhere('task.status in (:status)')
            ->setParameters([
                'inWork' => false,
                'status' => [Status::OPEN, Status::ERROR]
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if(!$task){
            return null;
        }

        return $task->getAction();
    }

    /**
     * Получает первые N открытых задач
     * @param Action $action
     * @return array
     */
    public function getOpenTasks(Action $action): array
    {
        $qb = $this->createQueryBuilder('task')
            ->select('task', 'action')
            ->join('task.action', 'action')
            ->where('task.inWork = :inWork')
            ->andWhere('task.status in (:status)')
            ->andWhere('task.action = :action')
            ->setParameters([
                'inWork' => false,
                'status' => [Status::OPEN, Status::ERROR],
                'action' => $action
            ]);

        if(Config::get('taskLimit') > 0){
            $qb->setMaxResults(Config::get('taskLimit'));
        }

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * Получает открытые связанные задачи
     * @param Task $task
     * @return array
     */
    public function getOpenLinkTasks(Task $task): array
    {
        return $this->createQueryBuilder('task')
            ->where('task.inWork = :inWork')
            ->andWhere('task.status in (:status)')
            ->andWhere('task.creditId = :creditId')
            ->setParameters([
                'inWork' => false,
                'status' => [Status::OPEN, Status::BLOCK],
                'creditId' => $task->getCreditId()
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * Устанавливает inWork для задач
     * @param array $tasks
     */
    public function setInWorkForTasks(array $tasks, bool $inWork)
    {
        foreach($this->getOpenTasks() as $openTask){
            if($openTask instanceof Task){
                $openTask->setInWork($inWork);
                $this->em->persist($openTask);
            }
        }

        $this->getEntityManager()->flush();
    }

    public function updateTasks(array $tasks)
    {
        foreach($this->getOpenTasks() as $openTask){
            if($openTask instanceof Task){
                $this->em->persist($openTask);
            }
        }

        $this->getEntityManager()->flush();
    }
}
