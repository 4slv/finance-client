<?php

namespace ApiClient\App;

use ApiClient\Action\ActionResolver;
use ApiClient\Config\LocalEntityManager;
use ApiClient\IO\Request;
use ApiClient\Model\Action;
use ApiClient\Model\Task;
use ApiClient\Model\Transfer;

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

        $em = LocalEntityManager::getEntityManager();
        $taskRepository = $em->getRepository(Task::class);
        $actionRepository = $em->getRepository(Action::class);

        $taskManager = new TaskManager();
        $taskManager
            ->setTaskRepository($taskRepository);

        $actionResolver = new ActionResolver();
        $action = $actionResolver->resolve($actionName);
        $action
            ->setActionRepository($actionRepository)
            ->setActionModel($actionModel)
            ->setTaskManager($taskManager)
            ->saveAction()
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
        $em = LocalEntityManager::getEntityManager();
        $taskRepository = $em->getRepository(Task::class);
        $transferRepository = $em->getRepository(Transfer::class);

        $openTaskManager = new OpenTaskManager();
        $openTaskManager
            ->setTaskRepository($taskRepository);

        if(is_null($openTaskManager->getTasks()))
            return null;

        $transferManager = new TransferManager();
        $transfer = new Transfer();
        $request = new Request();
        
        $transferManager
            ->setTransferRepository($transferRepository)
            ->setOpenTaskManager($openTaskManager)
            ->setTransfer($transfer)
            ->setRequest($request)
            ->buildBody()
            ->transfer()
            ->afterRequest();

        return true;
    }
}