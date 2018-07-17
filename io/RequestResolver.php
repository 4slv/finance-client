<?php

namespace ApiClient\IO;

class RequestResolver
{
    public function resolve(string $typeRequest): AbstractRequest
    {
        $requestFactory = new RequestFactory();

        switch($typeRequest){
            case 'http': $request = $requestFactory->getHttpRequest(); break;
            default: return null;
        }

        return $request;
    }
}