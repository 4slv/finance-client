<?php

namespace ApiClient\Task;

use ApiClient\IO\HttpAbstractRequest;
use ApiClient\IO\HttpResponse;
use ApiClient\IO\AbstractRequest;
use ApiClient\IO\RequestResolver;
use ApiClient\IO\ResponseInterface;
use ApiClient\Process\Process;
use ApiClient\Process\ProcessPoolPart;
use http\Env\Request;

class TaskManager
{
    /** @var Task $task */
    private $task;

    /** @var AbstractRequest $request */
    private $request;

    /** @var ResponseInterface $response */
    private $response;

    public function setTask(Task $task): TaskManager
    {
        $this->task = $task;

        return $this;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    /**
     * @return AbstractRequest
     */
    public function getRequest(): AbstractRequest
    {
        return $this->request;
    }

    /**
     * @param AbstractRequest $request
     * @return TaskManager
     */
    public function setRequest(AbstractRequest $request): TaskManager
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @param ResponseInterface $response
     * @return TaskManager
     */
    public function setResponse(ResponseInterface $response): TaskManager
    {
        $this->response = $response;
        return $this;
    }

    /** создание запроса
     * @throws \Exception
     */
    public function createRequest(): TaskManager
    {
        //
    }

    /** отправка запроса, получение ответа */
    public function sendRequest(): TaskManager
    {
        //

    }

    /** действие после получения ответа */
    public function afterRequest(): TaskManager
    {
        //
    }

    public function processToJson()
    {

    }
}