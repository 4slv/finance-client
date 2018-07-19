<?php

namespace ApiClient\App;

use ApiClient\Config\Config;
use ApiClient\IO\Request;
use ApiClient\IO\Response;
use ApiClient\Model\Task;
use ApiClient\Model\Transfer;

/** Менеджер отправки задач */
class TransferManager
{
    /** @var TaskManager $taskManager */
    private $taskManager;

    /** @var Transfer $transfer */
    private $transfer;

    /** @var Request $request */
    private $request;

    /** @var Response $response */
    private $response;

    /**
     * @return TaskManager
     */
    public function getTaskManager(): TaskManager
    {
        return $this->taskManager;
    }

    /**
     * @param TaskManager $taskManager
     * @return TransferManager
     */
    public function setTaskManager(TaskManager $taskManager): TransferManager
    {
        $this->taskManager = $taskManager;
        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     * @return TransferManager
     */
    public function setRequest(Request $request): TransferManager
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param Response $response
     * @return TransferManager
     */
    public function setResponse(Response $response): TransferManager
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @return Transfer
     */
    public function getTransfer(): Transfer
    {
        return $this->transfer;
    }

    /**
     * @param Transfer $transfer
     * @return TransferManager
     */
    public function setTransfer(Transfer $transfer): TransferManager
    {
        $this->transfer = $transfer;
        return $this;
    }

    /**
     * Преобразовывает параметры задач из очереди в массив для отправки
     * @return TransferManager
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function buildBody(): TransferManager
    {
        $tasksParameters = [];
        foreach($this->getTaskManager()->getOpenTasks() as $task){
            if($task instanceof Task){
                array_push($tasksParameters, [
                    'taskId' => $task->getId(),
                    'parameters' => $task->getParameters()
                ]);
            }
        }

        $body = [
            'apiKey' => '0000', //todo
            'action' => $this->getTaskManager()->getFirstOpenAction()->getName(),
            'tasks' => $tasksParameters
        ];

        $this->getRequest()->setBody($body);

        return $this;
    }

    /**
     * Отправялет задачи, получает ответ
     * @return TransferManager
     * @throws ApiClientException
     */
    public function transfer(): TransferManager
    {
        $response = $this->getRequest()
            ->setUrl(Config::get('url'))
            ->send();

        $this->setResponse($response);

        return $this;
    }

    /**
     * Действия после полечения ответа
     * @throws ApiClientException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function afterRequest()
    {
        $data = $this->getResponse()->getData();
        $this->getTransfer()->setCode($this->getResponse()->getCode());

        if(is_null($data) or $this->getResponse()->getCode() !== 200){
            $this->getTaskManager()->setStatusForAllOpenTasks(Status::ERROR, $this->getTransfer());
        } else{
            $this->getTaskManager()->updateTasks($data, $this->getTransfer());
        }
    }
}