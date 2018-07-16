<?php

namespace ApiClient\Command;

use ApiClient\Action\ActionResolver;
use ApiClient\Task\Task;
use ApiClient\Task\TaskManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    protected function configure()
    {
        $this->setName('app:new-task')
            ->addArgument('name', InputArgument::REQUIRED, 'Required name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $actionResolver = new ActionResolver();
        $actionName = $input->getArgument('name');
        $action = $actionResolver->resolve($actionName);
        
        if(is_null($action)){
            $output->writeln(sprintf("Action '%s' not registered", $actionName));
        }
        
        $actionPull = $action->prepare()->createPull();

        $task = new Task();
        $task->setActionPull($actionPull);

        $taskManager = new TaskManager();
        $taskManager->addTask($task);
    }
}