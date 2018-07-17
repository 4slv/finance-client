<?php

namespace ApiClient\Action;

final class ActionFactory
{
    /**
     * @return ActionAbstract|null
     */
    public function getCalcPenalty(): ?ActionAbstract
    {
        return new CalcPenalty();
    }
}