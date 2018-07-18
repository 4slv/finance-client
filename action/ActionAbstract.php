<?php

namespace ApiClient\Action;

use ApiClient\Model\Action;
use ApiClient\App\TaskManager;

abstract class ActionAbstract
{
    /** @var TaskManager $taskManager */
    private $taskManager;

    /** @var Action $action */
    private $action;

    /**
     * @return Action
     */
    public function getActionModel(): Action
    {
        return $this->action;
    }

    /**
     * @param Action $action
     * @return ActionAbstract
     */
    public function setActionModel(Action $action): ActionAbstract
    {
        $this->action = $action;
        return $this;
    }

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
     * Генерация задач
     */
    abstract public function generateTasks(): void ;
}