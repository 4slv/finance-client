<?php

namespace ApiClient\Action;

final class ActionFactory
{
    /**
     * @return ActionAbstract|null
     */
    public function getTestAction(): ?ActionAbstract
    {
        return new TestAction();
    }
}