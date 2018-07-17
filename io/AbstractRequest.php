<?php

namespace ApiClient\IO;

abstract class AbstractRequest
{
    /** @var array $body */
    private $body = [];

    private $url;

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     * @return AbstractRequest
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * @param array $body
     * @return AbstractRequest
     */
    public function setBody(array $body): AbstractRequest
    {
        $this->body = $body;
        return $this;
    }

    abstract public function send(): AbstractResponse;
}