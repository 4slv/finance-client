<?php

use ApiClient\App\TaskManager;
use ApiClient\Model\Task;
use ApiClient\App\Status;
use ApiClient\Model\Action;

class CreateTasksTest extends PHPUnit\Framework\TestCase
{

    /**
     * @dataProvider providerCreate()
     * @param $actionName
     * @param $actionParameters
     * @param $expectedActionDb
     * @param $expectedTasksDb
     * @throws \ApiClient\App\ApiClientException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testCreateTasks($actionName, $actionParameters, $expectedActionDb, $expectedTasksDb)
    {
        $actionModel = new Action();
        $actionModel->setName($actionName);
        $actionModel->setParameters($actionParameters);

        $realTaskDb = [];
        $realActionDb = [];

        $taskManager = $this->getMockBuilder(TaskManager::class)
            ->setMethods(array('save'))
            ->getMock();

        $taskManager->expects($this->once())->method('save')
            ->willReturnCallback(function () use ($taskManager, &$realTaskDb, &$realActionDb){
                array_push($realActionDb, $taskManager->getTasks()[0]->getAction());
                foreach($taskManager->getTasks() as $task){
                    array_push($realTaskDb, $task);
                }
            });

        $actionResolver = new \ApiClient\Action\ActionResolver();
        $action = $actionResolver->resolve($actionName);
        $action
            ->setActionModel($actionModel)
            ->setTaskManager($taskManager)
            ->generateTasks();

        $taskManager->save();

        $this->assertEquals($expectedActionDb, $realActionDb);

        $i = 0;
        foreach($realTaskDb as $taskDb){
            $taskExp = $expectedTasksDb[$i];
            if($taskDb instanceof Task and $taskExp instanceof Task){
                $this->assertEquals($taskExp->getAction(), $taskDb->getAction());
                $this->assertEquals($taskExp->getParameters(), $taskDb->getParameters());
                $this->assertEquals($taskExp->getCreditId(), $taskDb->getCreditId());
                $this->assertEquals($taskExp->getStatus(), $taskDb->getStatus());
                $this->assertEquals($taskExp->getAttempt(), $taskDb->getAttempt());
                $this->assertEquals($taskExp->getInWork(), $taskDb->getInWork());
                $this->assertEquals($taskExp->getDescription(), $taskDb->getDescription());
            }
            $i++;
        }

    }

    /**
     * @dataProvider providerTransfer
     * @param $action
     * @param $dataFromServer
     * @param $tasksDb
     * @param $expectedDbTasks
     * @return bool|null
     * @throws \ApiClient\App\ApiClientException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testTransfer($action, $tasksDb, $expectedTasksDb, $dataFromServer)
    {
        $limit = 6;

        $taskRepository = $this->getMockBuilder(\ApiClient\Repository\TaskRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getOpenTasks', 'getFirstOpenAction', 'setInWorkForTasks', 'getOpenLinkTasks', 'find', 'updateTasks'))
            ->getMock();

        $taskRepository->expects($this->any())->method('getOpenTasks')
            ->willReturnCallback(function () use ($tasksDb, $limit){
                $tasks = [];
                foreach ($tasksDb as $task){
                    if(count($tasks) === $limit) break;
                    if($task instanceof Task and
                        $task->getInWork() == false and
                        ($task->getStatus() == Status::OPEN or
                            $task->getStatus() == Status::ERROR))
                    {
                        array_push($tasks, $task);
                    }
                }
                return $tasks;
            });

        $taskRepository->expects($this->any())->method('getFirstOpenAction')
            ->willReturn($action);

        $taskRepository->expects($this->any())->method('setInWorkForTasks')
            ->willReturnCallback(function () use (&$tasksDb){
                foreach($tasksDb as &$task){
                    $task->setInWork(true);
                }
            });

        $taskRepository->expects($this->any())->method('getOpenLinkTasks')
            ->willReturnCallback(function ($task) use ($tasksDb){
                $openLinkTasks = [];
                foreach($tasksDb as $dbTask){
                    if($task->getId() != $dbTask->getId() and
                        $task->getCreditId() == $dbTask->getCreditId() and
                        ($dbTask->getStatus() == Status::OPEN or
                            $dbTask->getStatus() == Status::BLOCK)
                    ){
                        array_push($openLinkTasks, $dbTask);
                    }
                }
                return $openLinkTasks;
            });

        $taskRepository->expects($this->any())->method('find')
            ->willReturnCallback(function ($taskId) use ($tasksDb){
                foreach ($tasksDb as $dbTask){
                    if($dbTask->getId() == $taskId)
                        return $dbTask;
                }
            });

        $taskRepository->expects($this->any())->method('updateTasks')
            ->willReturnCallback(function ($tasks) use (&$tasksDb){
                foreach ($tasksDb as &$dbTask){
                    foreach ($tasks as $task){
                        if($task instanceof Task and
                            $dbTask instanceof Task and
                            $task->getId() == $dbTask->getId())
                        {
                            $dbTask = $task;
                        }
                    }
                }
            });

        $request = $this->getMockBuilder(\ApiClient\IO\Request::class)
            ->setMethods(array('send'))
            ->getMock();

        $request->expects($this->any())->method('send')
            ->willReturnCallback(function () use ($dataFromServer){
                return (new \ApiClient\IO\Response())
                    ->setCode(200)
                    ->setJsonData(json_encode($dataFromServer));
            });

        $taskManager = new TaskManager();
        $taskManager
            ->setTaskRepository($taskRepository);

        $transferManager = new \ApiClient\App\TransferManager();
        $transfer = new \ApiClient\Model\Transfer();

        $transferManager
            ->setTaskManager($taskManager)
            ->setTransfer($transfer)
            ->setRequest($request)
            ->buildBody()
            ->transfer()
            ->afterRequest();

        $i = 0;
        foreach($tasksDb as $taskDb){
            $taskExp = $expectedTasksDb[$i];
            if($taskDb instanceof Task and $taskExp instanceof Task){
                $this->assertEquals($taskExp->getAction(), $taskDb->getAction());
                $this->assertEquals($taskExp->getParameters(), $taskDb->getParameters());
                $this->assertEquals($taskExp->getCreditId(), $taskDb->getCreditId());
                $this->assertEquals($taskExp->getStatus(), $taskDb->getStatus());
                $this->assertEquals($taskExp->getAttempt(), $taskDb->getAttempt());
                $this->assertEquals($taskExp->getDescription(), $taskDb->getDescription());
            }
            $i++;
        }

        return true;
    }

    public function providerTransfer()
    {
        return [
            [
                $action = (new Action())
                    ->setName('TestAction')
                    ->setParameters([]),
                [
                    (new Task())->setId(1)->setAction($action)->setCreditId(100)->setStatus(Status::OPEN)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(2)->setAction($action)->setCreditId(101)->setStatus(Status::CLOSE)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(3)->setAction($action)->setCreditId(102)->setStatus(Status::ERROR)->setAttempt(2)->setDescription('error message'),
                    (new Task())->setId(4)->setAction($action)->setCreditId(103)->setStatus(Status::OPEN)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(5)->setAction($action)->setCreditId(104)->setStatus(Status::REJECT)->setAttempt(1)->setDescription(''),
                    (new Task())->setId(6)->setAction($action)->setCreditId(105)->setStatus(Status::ERROR)->setAttempt(1)->setDescription('error message'),
                    (new Task())->setId(7)->setAction($action)->setCreditId(106)->setStatus(Status::BLOCK)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(8)->setAction($action)->setCreditId(107)->setStatus(Status::OPEN)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(9)->setAction($action)->setCreditId(108)->setStatus(Status::ERROR)->setAttempt(0)->setDescription('error message'),
                    (new Task())->setId(10)->setAction(new Action())->setCreditId(102)->setStatus(Status::BLOCK)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(11)->setAction(new Action())->setCreditId(103)->setStatus(Status::OPEN)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(12)->setAction(new Action())->setCreditId(107)->setStatus(Status::OPEN)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(13)->setAction(new Action())->setCreditId(108)->setStatus(Status::BLOCK)->setAttempt(0)->setDescription(''),
                ],
                [
                    (new Task())->setId(1)->setAction($action)->setCreditId(100)->setStatus(Status::CLOSE)->setAttempt(1)->setDescription(''),
                    (new Task())->setId(2)->setAction($action)->setCreditId(101)->setStatus(Status::CLOSE)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(3)->setAction($action)->setCreditId(102)->setStatus(Status::CLOSE)->setAttempt(3)->setDescription(''),
                    (new Task())->setId(4)->setAction($action)->setCreditId(103)->setStatus(Status::ERROR)->setAttempt(1)->setDescription('error message'),
                    (new Task())->setId(5)->setAction($action)->setCreditId(104)->setStatus(Status::REJECT)->setAttempt(1)->setDescription(''),
                    (new Task())->setId(6)->setAction($action)->setCreditId(105)->setStatus(Status::ERROR)->setAttempt(2)->setDescription('error message'),
                    (new Task())->setId(7)->setAction($action)->setCreditId(106)->setStatus(Status::BLOCK)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(8)->setAction($action)->setCreditId(107)->setStatus(Status::REJECT)->setAttempt(1)->setDescription(''),
                    (new Task())->setId(9)->setAction($action)->setCreditId(108)->setStatus(Status::REJECT)->setAttempt(1)->setDescription(''),
                    (new Task())->setId(10)->setAction(new Action())->setCreditId(102)->setStatus(Status::OPEN)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(11)->setAction(new Action())->setCreditId(103)->setStatus(Status::BLOCK)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(12)->setAction(new Action())->setCreditId(107)->setStatus(Status::REJECT)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(13)->setAction(new Action())->setCreditId(108)->setStatus(Status::REJECT)->setAttempt(0)->setDescription(''),
                ],
                [
                    ['taskId' => 1, 'status' => 'success', 'description' => ''],
                    ['taskId' => 3, 'status' => 'success', 'description' => ''],
                    ['taskId' => 4, 'status' => 'error', 'description' => 'error message'],
                    ['taskId' => 6, 'status' => 'error', 'description' => 'error message'],
                    ['taskId' => 8, 'status' => 'reject', 'description' => ''],
                    ['taskId' => 9, 'status' => 'reject', 'description' => '']
                ],
            ]

        ];
    }

    public function providerCreate()
    {
        return [
            [
                'TestAction',
                [100, 101, 102, 103, 104],
                [
                    $action = (new Action())
                        ->setName('TestAction')
                        ->setParameters([100, 101, 102, 103, 104])
                ],
                [
                    (new Task())->setAction($action)->setParameters(['param1' => 'value1'])->setCreditId(100)->setStatus(Status::OPEN)->setAttempt(0)->setInWork(0)->setDescription(''),
                    (new Task())->setAction($action)->setParameters(['param1' => 'value1'])->setCreditId(101)->setStatus(Status::OPEN)->setAttempt(0)->setInWork(0)->setDescription(''),
                    (new Task())->setAction($action)->setParameters(['param1' => 'value1'])->setCreditId(102)->setStatus(Status::OPEN)->setAttempt(0)->setInWork(0)->setDescription(''),
                    (new Task())->setAction($action)->setParameters(['param1' => 'value1'])->setCreditId(103)->setStatus(Status::OPEN)->setAttempt(0)->setInWork(0)->setDescription(''),
                    (new Task())->setAction($action)->setParameters(['param1' => 'value1'])->setCreditId(104)->setStatus(Status::OPEN)->setAttempt(0)->setInWork(0)->setDescription(''),
                ]
            ],
        ];
    }
}