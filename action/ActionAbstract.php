<?php

namespace ApiClient\Action;

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
    abstract public function createTask();
}