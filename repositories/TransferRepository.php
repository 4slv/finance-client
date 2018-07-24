<?php

namespace ApiClient\Repository;

use ApiClient\App\Status;
use ApiClient\Config\Config;
use ApiClient\Model\Action;
use ApiClient\Model\Task;
use ApiClient\Model\Transfer;

class TransferRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Сохраняет в бд передачу
     * @param Transfer $transfer
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Transfer $transfer)
    {
        $em = $this->getEntityManager();
        $em->persist($transfer);
        $em->flush($transfer);
    }
}
