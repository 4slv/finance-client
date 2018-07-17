<?php

namespace ApiClient\Task;

use ApiClient\IO\HttpAbstractRequest;
use ApiClient\IO\HttpResponse;
use ApiClient\IO\AbstractRequest;
use ApiClient\IO\RequestResolver;
use ApiClient\IO\ResponseInterface;
use ApiClient\Process\Process;
use ApiClient\Process\ProcessPoolPart;
use Doctrine\ORM\EntityManager;
use Framework\Database\CEntityManager;
use http\Env\Request;

class TaskManager
{
    /** @var Task[] $tasks */
    private $tasks;

    /** @var \PDO $pdo */
    private $pdo;

    public function __construct()
    {
        $this->pdo = CEntityManager::getPdoConnect();
    }

    public function addTask(Task $task): TaskManager
    {
        array_push($this->tasks, $task);

        return $this;
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }

    public function createTasks()
    {
        $sql = "INSERT INTO Task ( createDatetime, parameters, status ) VALUES";

        foreach($this->getTasks() as $task){
            if($task instanceof Task){
                $insertRow = sprintf("('%s', '%s', '%s')",
                    date('Y-m-d H:i:s'),
                    json_encode($task->getParameters()),
                    Task::NEW
                );
                $insertRow .= end($this->getTasks()) ? ';' : ',';
                $sql .= $insertRow;
            }
        }

        $this->pdo->query($sql)->execute();
    }
}