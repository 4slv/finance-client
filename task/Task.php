<?php

namespace ApiClient\Task;

use ApiClient\Action\ActionPull;

class Task
{
    /** @var ActionPull $actionPull */
    private $actionPull;

    /**
     * @return ActionPull
     */
    public function getActionPull(): ActionPull
    {
        return $this->actionPull;
    }

    /**
     * @param ActionPull $actionPull
     * @return Task
     */
    public function setActionPull(ActionPull $actionPull): Task
    {
        $this->actionPull = $actionPull;
        return $this;
    }


}