<?php

namespace ApiClient\Action;

use ApiClient\App\ApiClientException;
use ApiClient\Model\Action;
use ApiClient\App\TaskManager;
use ApiClient\Repository\ActionRepository;

abstract class ActionAbstract
{
    /** @var TaskManager $taskManager */
    private $taskManager;

    /** @var Action $action */
    private $action;
    
    /** @var ActionRepository $actionRepository */
    private $actionRepository;

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
     * @return mixed
     */
    public function getActionRepository(): ActionRepository
    {
        return $this->actionRepository;
    }

    /**
     * @param mixed $actionRepository
     * @return ActionAbstract
     */
    public function setActionRepository($actionRepository): ActionAbstract
    {
        $this->actionRepository = $actionRepository;
        return $this;
    }

    /**
     * @return ActionAbstract
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveAction(): ActionAbstract
    {
        $this->getActionRepository()->save(
            $this->getActionModel()
        );

        return $this;
    }

    /**
     * Генерация задач
     */
    abstract public function generateTasks();
}