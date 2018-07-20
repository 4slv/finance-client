<?php

use ApiClient\Model\Task;
use ApiClient\App\Status;
use ApiClient\Model\Action;

class CreateTasksTest extends PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        \Framework\Database\CEntityManager::connect();
    }

//    /**
//     * @dataProvider providerCreate()
//     * @param $actionName
//     * @param $actionParameters
//     * @param $expectedActionDb
//     * @param $expectedTasksDb
//     * @throws \ApiClient\App\ApiClientException
//     * @throws \Doctrine\ORM\ORMException
//     * @throws \Doctrine\ORM\OptimisticLockException
//     */
//    public function testCreateTasks($actionName, $actionParameters, $expectedActionDb, $expectedTasksDb)
//    {
//        $actionModel = new Action();
//        $actionModel->setName($actionName);
//        $actionModel->setParameters($actionParameters);
//
//        $realTaskDb = [];
//        $realActionDb = [];
//
//        $taskManager = $this->getMockBuilder(\ApiClient\App\TaskManager::class)
//            ->setMethods(array('save'))
//            ->getMock();
//
//        $taskManager->expects($this->once())->method('save')
//            ->willReturnCallback(function () use ($taskManager, &$realTaskDb, &$realActionDb){
//                array_push($realActionDb, $taskManager->getTasks()[0]->getAction());
//                foreach($taskManager->getTasks() as $task){
//                    array_push($realTaskDb, $task);
//                }
//            });
//
//        $actionResolver = new \ApiClient\Action\ActionResolver();
//        $action = $actionResolver->resolve($actionName);
//        $action
//            ->setActionModel($actionModel)
//            ->setTaskManager($taskManager)
//            ->generateTasks();
//
//        $taskManager->save();
//
//        $this->assertEquals($expectedActionDb, $realActionDb);
//
//        $i = 0;
//        foreach($realTaskDb as $taskDb){
//            $taskExp = $expectedTasksDb[$i];
//            if($taskDb instanceof Task and $taskExp instanceof Task){
//                $this->assertEquals($taskExp->getAction(), $taskDb->getAction());
//                $this->assertEquals($taskExp->getParameters(), $taskDb->getParameters());
//                $this->assertEquals($taskExp->getCreditId(), $taskDb->getCreditId());
//                $this->assertEquals($taskExp->getStatus(), $taskDb->getStatus());
//                $this->assertEquals($taskExp->getAttempt(), $taskDb->getAttempt());
//                $this->assertEquals($taskExp->getInWork(), $taskDb->getInWork());
//                $this->assertEquals($taskExp->getDescription(), $taskDb->getDescription());
//            }
//            $i++;
//        }
//
//    }
//
//    public function providerCreate()
//    {
//        return [
//            [
//                'TestAction',
//                [100, 101, 102, 103, 104],
//                [
//                    $action = (new Action())
//                        ->setName('TestAction')
//                        ->setParameters([100, 101, 102, 103, 104])
//                ],
//                [
//                    (new Task())->setAction($action)->setParameters(['param1' => 'value1'])
//                        ->setCreditId(100)
//                        ->setStatus(Status::OPEN)
//                        ->setAttempt(0)
//                        ->setInWork(0)
//                        ->setDescription(''),
//                    (new Task())->setAction($action)->setParameters(['param1' => 'value1'])
//                        ->setCreditId(101)
//                        ->setStatus(Status::OPEN)
//                        ->setAttempt(0)
//                        ->setInWork(0)
//                        ->setDescription(''),
//                    (new Task())->setAction($action)->setParameters(['param1' => 'value1'])
//                        ->setCreditId(102)
//                        ->setStatus(Status::OPEN)
//                        ->setAttempt(0)
//                        ->setInWork(0)
//                        ->setDescription(''),
//                    (new Task())->setAction($action)->setParameters(['param1' => 'value1'])
//                        ->setCreditId(103)
//                        ->setStatus(Status::OPEN)
//                        ->setAttempt(0)
//                        ->setInWork(0)
//                        ->setDescription(''),
//                    (new Task())->setAction($action)->setParameters(['param1' => 'value1'])
//                        ->setCreditId(104)
//                        ->setStatus(Status::OPEN)
//                        ->setAttempt(0)
//                        ->setInWork(0)
//                        ->setDescription(''),
//                ]
//            ],
//        ];
//    }

    /**
     * @dataProvider providerTransfer
     * @param $action
     * @param $dataFromServer
     * @param $dbTasks
     * @param $expectedDbTasks
     * @return bool|null
     * @throws \ApiClient\App\ApiClientException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testTransfer($action, $dbTasks, $expectedDbTasks, $dataFromServer)
    {
        $limit = 6;

        $entityManager = \ApiClient\Config\LocalEntityManager::getEntityManager();

        $taskRepository = $this->getMockBuilder(\ApiClient\Repository\TaskRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getOpenTasks', 'getFirstOpenAction', 'setInWorkForTasks', 'getOpenLinkTasks'))
            ->getMock();

        $taskRepository->expects($this->once())->method('getOpenTasks')
            ->willReturnCallback(function () use ($dbTasks, $limit){
                return array_slice($dbTasks, 0, $limit);
            });

        $taskRepository->expects($this->once())->method('getFirstOpenAction')
            ->willReturn($action);

        $taskRepository->expects($this->once())->method('setInWorkForTasks')
            ->willReturnCallback(function () use (&$dbTasks){
                foreach($dbTasks as &$task){
                    $task->setInWork(true);
                }
            });

        $taskRepository->expects($this->once())->method('getOpenLinkTasks')
            ->willReturnCallback(function ($task) use ($dbTasks){
                $openLinkTasks = [];
                foreach($dbTasks as $dbTask){
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

        $request = $this->getMockBuilder(\ApiClient\IO\Request::class)
            ->setMethods(array('send'))
            ->getMock();

        $taskRepository->expects($this->once())->method('setInWorkForTasks')
            ->willReturnCallback(function () use ($dataFromServer){
                return (new \ApiClient\IO\Response())
                    ->setCode(200)
                    ->setJsonData(json_encode($dataFromServer));
            });

        $taskManager = new \ApiClient\App\TaskManager();
        $taskManager
            ->setEm($entityManager)
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
}