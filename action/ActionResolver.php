<?php

namespace ApiClient\Action;

use ApiClient\App\ApiClientException;
use ApiClient\Config\Config;

final class ActionResolver
{
    /**
     * @param string $actionName
     * @return ActionAbstract|null
     * @throws ApiClientException
     */
    public function resolve(string $actionName): ?ActionAbstract
    {
        $actionClassFullName = Config::get('actionClassNamespace') . '\\' . ucfirst($actionName);

        if(!class_exists($actionClassFullName)){
            throw new ApiClientException(sprintf("Неверное имя класса '%s'", $actionClassFullName));
        }

        $action = new $actionClassFullName;

        if(!$action instanceof ActionAbstract){
            throw new ApiClientException(sprintf("Действие '%s' не сконфигурировано", $actionName));
        }

        return $action;
    }
}