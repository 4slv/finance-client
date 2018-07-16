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
    public function setParameters(array $parameters): ActionAbstract
    {
        $this->parameters = $parameters;
        return $this;
    }

    abstract public function prepare(): ActionAbstract;
    abstract public function process();
}