<?php

namespace ApiClient\Command;

use ApiClient\Action\ActionResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    protected function configure()
    {
        $this->setName('app:new-task')
            ->addArgument('name', InputArgument::REQUIRED, 'Required name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $actionResolver = new ActionResolver();
        $actionName = $input->getArgument('name');
        if(is_null($actionResolver->resolve($actionName))){
            $output->writeln(sprintf("Action '%s' not registered", $actionName));
        }
    }
}