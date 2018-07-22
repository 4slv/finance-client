<?php

namespace ApiClient\App;

use ApiClient\Action\ActionResolver;
use ApiClient\Config\LocalEntityManager;
use ApiClient\IO\Request;
use ApiClient\Model\Action;
use ApiClient\Model\Task;
use ApiClient\Model\Transfer;
use Doctrine\ORM\EntityManager;
use Framework\Database\CEntityManager;

/** Класс усправление модулем */
class ApiClient
{
    /**
     * Создает задачи на основе действия и праметров
     * @param string $actionName
     * @param array $inputParameters
     * @throws ApiClientException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createTasks(string $actionName, array $inputParameters = [])
    {
        $actionModel = new Action();
        $actionModel->setName($actionName);
        $actionModel->setParameters($inputParameters);

        $taskRepository = LocalEntityManager::getEntityManager()->getRepository(Task::class);

        $taskManager = new TaskManager();
        $taskManager
            ->setTaskRepository($taskRepository);

        $actionResolver = new ActionResolver();
        $action = $actionResolver->resolve($actionName);
        $action
            ->setActionModel($actionModel)
            ->setTaskManager($taskManager)
            ->generateTasks();

        $taskManager->save();
    }

    /**
     * Отправляет задачи
     * @return null возвращает в случае отсутствия задач в очереди
     * @return true возвращает в случае отправки
     * @throws ApiClientException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function transfer()
    {
        $entityManager = LocalEntityManager::getEntityManager();
        $taskRepository = $entityManager->getRepository(Task::class);

        $openTaskManager = new OpenTaskManager();
        $openTaskManager
            ->setTaskRepository($taskRepository);

        if(is_null($openTaskManager->getTasks()))
            return null;

        $transferManager = new TransferManager();
        $transfer = new Transfer();
        $request = new Request();

        $transferManager
            ->setOpenTaskManager($openTaskManager)
            ->setTransfer($transfer)
            ->setRequest($request)
            ->buildBody()
            ->transfer()
            ->afterRequest();

        return true;
    }
}