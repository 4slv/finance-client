<?php

namespace ApiClient\Process;

class ProcessPool
{
    /** @var Process[] $processes */
    private $processes = [];

    private $processPoolParts = [];

    public function addProcess(Process $process): ProcessPool
    {
        array_push($this->processes, $process);

        return $this;
    }

    public function addProcessPoolPart(ProcessPoolPart $processPoolPart): ProcessPool
    {
        array_push($this->processPoolParts, $processPoolPart);

        return $this;
    }
    
    public function getProcesses(): array
    {
        return $this->processes;
    }

    /**
     * При указании $size, разделит $this->process на части заданного размера
     * @param int $size
     * @return ProcessPool
     */
    public function separate(int $size = 0): ProcessPool
    {
        if($size > 0){
            $pullSize = 0;
            foreach($this->getProcesses() as $process){
                if($pullSize < $size){
                    $poolPart = new ProcessPoolPart();
                    $poolPart->addProcess($process);
                    $pullSize++;
                }

                if($pullSize == $size){
                    $this->addProcessPoolPart($poolPart);
                    $pullSize = 0;
                }
            }
            return $this;
        }

        $poolPart = new ProcessPoolPart();
        $poolPart->setProcesses($this->getProcesses());
        $this->addProcessPoolPart($poolPart);
        return $this;
    }

    public function getProcessPoolParts(): array
    {
        return $this->processPoolParts;
    }
}