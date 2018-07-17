<?php

namespace ApiClient\Process;

class Process
{
    /** @var array $parameters */
    private $parameters = [];

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     * @return Process
     */
    public function setParameters(array $parameters): Process
    {
        $this->parameters = $parameters;
        return $this;
    }
}