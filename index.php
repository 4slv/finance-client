<?php

require __DIR__.'\vendor\autoload.php';

use ApiClient\Command\InitCommand;
use Symfony\Component\Console\Application;

$application = new Application('action', '1.0.0');
$command = new InitCommand();
$application->add($command);

try{
    $application->run();
}catch(Exception $e){
    //
}