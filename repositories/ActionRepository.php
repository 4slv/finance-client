<?php

namespace ApiClient\Repository;

use ApiClient\App\Status;
use ApiClient\Config\Config;
use ApiClient\Model\Action;
use ApiClient\Model\Task;

class ActionRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Сохраняет в БД действие
     * @param Action $action
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Action $action)
    {
        $em = $this->getEntityManager();
        $em->persist($action);
        $em->flush($action);
    }
}
