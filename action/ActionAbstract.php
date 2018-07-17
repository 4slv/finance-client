<?php

namespace ApiClient\Action;

use ApiClient\Process\ProcessPool;

abstract class ActionAbstract
{
    private $inputParameters = [];

    /**
     * @return array
     */
    public function getInputParameters(): array
    {
        return $this->inputParameters;
    }

    /**
     * @param array $inputParameters
     * @return ActionAbstract
     */
    public function setInputParameters(array $inputParameters = []): ActionAbstract
    {
        $this->inputParameters = $inputParameters;
        return $this;
    }

    /**
     * Подготовка данных
     * @return ActionAbstract
     */
    abstract public function prepare(): ActionAbstract;

    /**
     * Создание процессов, обеъдинение в пул
     * @return ProcessPool
     */
    abstract public function createProcessPool(): ProcessPool;
}