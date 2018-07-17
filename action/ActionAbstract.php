<?php

namespace ApiClient\Action;

use ApiClient\Task\TaskManager;

abstract class ActionAbstract
{
    private $parameters = [];

    /** @var TaskManager $taskManager */
    private $taskManager;

    /**
     * @return TaskManager
     */
    public function getTaskManager(): TaskManager
    {
        return $this->taskManager;
    }

    /**
     * @param TaskManager $taskManager
     * @return ActionAbstract
     */
    public function setTaskManager(TaskManager $taskManager): ActionAbstract
    {
        $this->taskManager = $taskManager;
        return $this;
    }

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
     * Генерация задач
     */
    abstract public function generateTasks(): ActionAbstract;
}