<?php

namespace ApiClient\Task;

class Task
{
    const NEW = 'new';
    const SUCCESS = 'success';
    const ERROR = 'error';
    const IN_WORK = 'in_work';

    private $transfer;

    private $action;

    private $parameters;

    /**
     * @return mixed
     */
    public function getTransfer()
    {
        return $this->transfer;
    }

    /**
     * @param mixed $transfer
     * @return Task
     */
    public function setTransfer($transfer)
    {
        $this->transfer = $transfer;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     * @return Task
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param mixed $parameters
     * @return Task
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }
}