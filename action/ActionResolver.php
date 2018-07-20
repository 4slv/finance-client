<?php

namespace ApiClient\Action;

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
            default: return null;
        }

        return $action;
    }
}