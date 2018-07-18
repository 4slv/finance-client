<?php

namespace ApiClient\Action;

final class ActionResolver
{
    /**
     * @param string $actionName
     * @param array $parameters
     * @return ActionAbstract|null
     */
    public function resolve(string $actionName): ?ActionAbstract
    {
        $factoryAction = new ActionFactory();

        switch($actionName){
            case 'FirstAction': $action = $factoryAction->getFirstAction(); break;
            case 'SecondAction': $action = $factoryAction->getSecondAction(); break;
            default: return null;
        }

        return $action;
    }
}