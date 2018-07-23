<?php

namespace ApiClient\Action;

use ApiClient\App\ApiClientException;

final class ActionResolver
{
    /**
     * @param string $actionName
     * @return ActionAbstract|null
     */
    public function resolve(string $actionName): ?ActionAbstract
    {
        $factoryAction = new ActionFactory();

        switch($actionName){
            case 'TestAction': $action = $factoryAction->getTestAction(); break;
            default: throw new ApiClientException(sprintf("Действие '%s' не сконфигурировано", $actionName));
        }

        return $action;
    }
}