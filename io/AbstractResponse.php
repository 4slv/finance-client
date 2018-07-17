<?php

namespace ApiClient\IO;

abstract class AbstractResponse
{
    private $error;

    private $message;

    abstract public function getError();
    abstract public function setError($error);
    abstract public function getMessage();
    abstract public function setMessage($message);
}