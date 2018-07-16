<?php

namespace ApiClient\Action;

class ActionPull
{
    /** @var ActionAbstract[] $actions */
    private $actions = [];

    public function addAction(ActionAbstract $action): ActionPull
    {
        array_push($this->actions, $action);

        return $this;
    }
}