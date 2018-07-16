<?php

namespace ApiClient\Action;

class CalcPenalty extends ActionAbstract
{
    /** подготовка данных */
    public function prepare(): ActionAbstract
    {
        //

        return $this;
    }

    /** действия объединяются в пулл */
    public function createPull(): ActionPull
    {
        $actionPull = new ActionPull();

        //

        return $actionPull;
    }
}