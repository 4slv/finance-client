<?php

namespace ApiClient\Action;

final class ActionResolver
{
    /**
     * @param string $actionName
     * @param array $parameters
     * @return ActionAbstract|null
     */
    public function resolve(string $actionName, array $parameters = []): ?ActionAbstract
    {
        $factoryAction = new ActionFactory();

        switch($actionName){
            case 'CalcPenalty': $action = $factoryAction->getCalcPenalty(); break;
            default: return null;
        }

        $action->setParameters($parameters);

        return $action;
    }
}