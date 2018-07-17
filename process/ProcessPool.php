<?php

namespace ApiClient\Process;

class ProcessPool
{
    /** @var Process[] $processes */
    private $processes = [];

    public function addProcess(Process $process): ProcessPool
    {
        array_push($this->processes, $process);

        return $this;
    }

    public function getProcesses(): array
    {
        return $this->processes;
    }
}