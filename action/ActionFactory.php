<?php

namespace ApiClient\Action;

final class ActionFactory
{
    /**
     * @return ActionAbstract|null
     */
    public function getFirstAction(): ?ActionAbstract
    {
        return new FirstAction();
    }

    /**
     * @return ActionAbstract|null
     */
    public function getSecondAction(): ?ActionAbstract
    {
        return new SecondAction();
    }
}