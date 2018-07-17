<?php

namespace ApiClient\Task;

use ApiClient\Process\ProcessPool;

class Task
{
    /** @var ProcessPool $processPool */
    private $processPool;

    /**
     * @return ProcessPool
     */
    public function getProcessPool(): ProcessPool
    {
        return $this->processPool;
    }

    /**
     * @param ProcessPool $processPool
     * @return Task
     */
    public function setProcessPool(ProcessPool $processPool): Task
    {
        $this->processPool = $processPool;
        return $this;
    }
}