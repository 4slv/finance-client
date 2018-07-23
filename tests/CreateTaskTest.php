<?php

use ApiClient\Action\ActionResolver;
use ApiClient\App\ApiClientException;
use ApiClient\App\OpenTaskManager;
use ApiClient\App\TaskManager;
use ApiClient\App\TransferManager;
use ApiClient\IO\Request;
use ApiClient\IO\Response;
use ApiClient\Model\Task;
use ApiClient\App\Status;
use ApiClient\Model\Action;
use ApiClient\Model\Transfer;
use ApiClient\Repository\TaskRepository;
use ApiClient\Repository\TransferRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class CreateTasksTest extends PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider providerCreate()
     * @param $actionName
     * @param $actionParameters
     * @param $expectedActionDb
     * @param $expectedTasksDb
     * @throws ApiClientException
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

        $actionResolver = new ActionResolver();
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
                $this->assertEquals($taskExp->getProductId(), $taskDb->getProductId());
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
     * @param $tasksDb
     * @param $expectedTasksDb
     * @param $dataFromServer
     * @return bool|null
     * @throws ApiClientException
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testTransfer($action, $tasksDb, $expectedTasksDb, $dataFromServer)
    {
        $limit = 6;
        $attemptLimit = 5;

        $taskRepository = $this->getMockBuilder(TaskRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getOpenTasks', 'getFirstOpenAction', 'setInWorkForTasks', 'getLinkTasks', 'find', 'updateTasks'))
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
                    if($task instanceof Task){
                        $task->setInWork(true);
                    }
                }
            });

        $taskRepository->expects($this->any())->method('getLinkTasks')
            ->willReturnCallback(function ($task) use ($tasksDb){
                $openLinkTasks = [];
                foreach($tasksDb as $dbTask){
                    if($task instanceof Task and
                        $dbTask instanceof Task and
                        $task->getId() != $dbTask->getId() and
                        $task->getProductId() == $dbTask->getProductId() and
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

        $request = $this->getMockBuilder(Request::class)
            ->setMethods(array('send'))
            ->getMock();

        $request->expects($this->any())->method('send')
            ->willReturnCallback(function () use ($dataFromServer){
                return (new Response())
                    ->setCode(200)
                    ->setJsonData(json_encode($dataFromServer));
            });

        $transferRepository = $this->getMockBuilder(TransferRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(array('save'))
            ->getMock();

        $openTaskManager = new OpenTaskManager();
        $openTaskManager
            ->setTaskRepository($taskRepository);

        $transferManager = new TransferManager();
        $transfer = new Transfer();

        $transferManager
            ->setTransferRepository($transferRepository)
            ->setOpenTaskManager($openTaskManager)
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
                $this->assertEquals($taskExp->getProductId(), $taskDb->getProductId());
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
                    (new Task())->setId(1)->setAction($action)->setProductId(100)->setStatus(Status::OPEN)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(2)->setAction($action)->setProductId(101)->setStatus(Status::CLOSE)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(3)->setAction($action)->setProductId(102)->setStatus(Status::ERROR)->setAttempt(2)->setDescription('error message'),
                    (new Task())->setId(4)->setAction($action)->setProductId(103)->setStatus(Status::OPEN)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(5)->setAction($action)->setProductId(104)->setStatus(Status::REJECT)->setAttempt(1)->setDescription(''),
                    (new Task())->setId(6)->setAction($action)->setProductId(105)->setStatus(Status::ERROR)->setAttempt(4)->setDescription('error message'),
                    (new Task())->setId(7)->setAction($action)->setProductId(106)->setStatus(Status::BLOCK)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(8)->setAction($action)->setProductId(107)->setStatus(Status::OPEN)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(9)->setAction($action)->setProductId(108)->setStatus(Status::ERROR)->setAttempt(0)->setDescription('error message'),
                    (new Task())->setId(10)->setAction(new Action())->setProductId(102)->setStatus(Status::BLOCK)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(11)->setAction(new Action())->setProductId(103)->setStatus(Status::OPEN)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(12)->setAction(new Action())->setProductId(107)->setStatus(Status::OPEN)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(13)->setAction(new Action())->setProductId(108)->setStatus(Status::BLOCK)->setAttempt(0)->setDescription(''),
                ],
                [
                    (new Task())->setId(1)->setAction($action)->setProductId(100)->setStatus(Status::CLOSE)->setAttempt(1)->setDescription(''),
                    (new Task())->setId(2)->setAction($action)->setProductId(101)->setStatus(Status::CLOSE)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(3)->setAction($action)->setProductId(102)->setStatus(Status::CLOSE)->setAttempt(3)->setDescription(''),
                    (new Task())->setId(4)->setAction($action)->setProductId(103)->setStatus(Status::ERROR)->setAttempt(1)->setDescription('error message'),
                    (new Task())->setId(5)->setAction($action)->setProductId(104)->setStatus(Status::REJECT)->setAttempt(1)->setDescription(''),
                    (new Task())->setId(6)->setAction($action)->setProductId(105)->setStatus(Status::CANCEL)->setAttempt(5)->setDescription('error message'),
                    (new Task())->setId(7)->setAction($action)->setProductId(106)->setStatus(Status::BLOCK)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(8)->setAction($action)->setProductId(107)->setStatus(Status::REJECT)->setAttempt(1)->setDescription(''),
                    (new Task())->setId(9)->setAction($action)->setProductId(108)->setStatus(Status::REJECT)->setAttempt(1)->setDescription(''),
                    (new Task())->setId(10)->setAction(new Action())->setProductId(102)->setStatus(Status::OPEN)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(11)->setAction(new Action())->setProductId(103)->setStatus(Status::BLOCK)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(12)->setAction(new Action())->setProductId(107)->setStatus(Status::BLOCK)->setAttempt(0)->setDescription(''),
                    (new Task())->setId(13)->setAction(new Action())->setProductId(108)->setStatus(Status::BLOCK)->setAttempt(0)->setDescription(''),
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
                    (new Task())->setAction($action)->setParameters(['param1' => 'value1'])->setProductId(100)->setStatus(Status::OPEN)->setAttempt(0)->setInWork(0)->setDescription(''),
                    (new Task())->setAction($action)->setParameters(['param1' => 'value1'])->setProductId(101)->setStatus(Status::OPEN)->setAttempt(0)->setInWork(0)->setDescription(''),
                    (new Task())->setAction($action)->setParameters(['param1' => 'value1'])->setProductId(102)->setStatus(Status::OPEN)->setAttempt(0)->setInWork(0)->setDescription(''),
                    (new Task())->setAction($action)->setParameters(['param1' => 'value1'])->setProductId(103)->setStatus(Status::OPEN)->setAttempt(0)->setInWork(0)->setDescription(''),
                    (new Task())->setAction($action)->setParameters(['param1' => 'value1'])->setProductId(104)->setStatus(Status::OPEN)->setAttempt(0)->setInWork(0)->setDescription(''),
                ]
            ],
        ];
    }
}