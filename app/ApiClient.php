<?php

namespace ApiClient\App;

use ApiClient\Action\ActionResolver;
use ApiClient\IO\Request;
use ApiClient\Model\Action;
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

        $taskManager = new TaskManager();

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
        $taskManager = new TaskManager();
        if(is_null($taskManager->getOpenTasks()))
            return null;

        $transferManager = new TransferManager();
        $transfer = new Transfer();
        $request = new Request();

        $transferManager
            ->setTaskManager($taskManager)
            ->setTransfer($transfer)
            ->setRequest($request)
            ->buildBody()
            ->transfer()
            ->afterRequest();

        return true;
    }
}