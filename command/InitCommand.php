<?php

namespace ApiClient\Command;

use ApiClient\Action\ActionResolver;
use ApiClient\Task\Task;
use ApiClient\Task\TaskManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    protected function configure()
    {
        $this->setName('app:new-task')
            ->addArgument('name', InputArgument::REQUIRED, 'Required name')
            ->addArgument('parameters', InputArgument::IS_ARRAY, 'Action parameters')
            ->addOption(
                'parameters',
                null,
                InputOption::VALUE_OPTIONAL,
                'Action parameters',
                false
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $actionName = $input->getArgument('name');
        $actionParameters = $input->getArgument('parameters');

        $actionResolver = new ActionResolver();
        $action = $actionResolver->resolve($actionName, $actionParameters);
        
        if(is_null($action)){
            $output->writeln(sprintf("Action '%s' not registered", $actionName));
            die();
        }
        
        $actionPool = $action->prepare()->createPool();

        $task = new Task();
        $task->setActionPool($actionPool);

        $taskManager = new TaskManager();
        $taskManager->addTask($task);
    }
}