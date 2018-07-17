<?php

namespace ApiClient\Action;

use ApiClient\Task\Task;

abstract class ActionAbstract
{
    private $parameters = [];

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     * @return ActionAbstract
     */
    public function setParameters(array $parameters = []): ActionAbstract
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * Подготовка данных
     * @return ActionAbstract
     */
    abstract public function prepare(): ActionAbstract;

    /**
     * Создание задачи
     */
    public function createTask()
    {
        $task = new Task();
        $task->setAction($this);
    }
}