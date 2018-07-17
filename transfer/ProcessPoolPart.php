<?php

namespace ApiClient\Process;

class ProcessPoolPart
{
    /** @var Process[] $processes */
    private $processes = [];

    /**
     * @return Process[]
     */
    public function getProcesses(): array
    {
        return $this->processes;
    }

    /**
     * @param Process[] $processes
     * @return ProcessPoolPart
     */
    public function setProcesses(array $processes): ProcessPoolPart
    {
        $this->processes = $processes;
        return $this;
    }

    public function addProcess(Process $process): ProcessPoolPart
    {
        array_push($this->processes, $process);

        return $this;
    }
}