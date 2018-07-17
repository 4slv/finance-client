<?php

namespace ApiClient\IO;

class RequestFactory
{
    public function getHttpRequest(): HttpRequest
    {
        return new HttpRequest();
    }
}