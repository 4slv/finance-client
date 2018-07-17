<?php

namespace ApiClient\Task;

use ApiClient\IO\Request;
use ApiClient\IO\Response;

class TaskManager
{
    /** @var Task[] $tasks */
    private $tasks = [];

    /** @var Request $request */
    private $request;

    /** @var Response $response */
    private $response;

    public function addTask(Task $task): TaskManager
    {
        array_push($this->tasks, $task);

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
     * @return TaskManager
     */
    public function setRequest(Request $request): TaskManager
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
     * @return TaskManager
     */
    public function setResponse(Response $response): TaskManager
    {
        $this->response = $response;
        return $this;
    }

    /** создание запроса */
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
}