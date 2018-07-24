<?php

namespace ApiClient\IO;

use ApiClient\App\ApiClientException;
use ApiClient\Config\Config;

class Request
{
    /** @var string $url */
    private $url;

    /** @var array $body */
    private $body = [];

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return Request
     */
    public function setUrl(string $url): Request
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
     * @return Request
     */
    public function setBody(array $body): Request
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return Response
     * @throws ApiClientException
     */
    public function send(): Response
    {
        if(!filter_var($this->getUrl(), FILTER_VALIDATE_URL)){
            throw new ApiClientException(sprintf("Invalid URL address '%s'", $this->getUrl()));
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, Config::get('timeout'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->getBody()));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $this->getUrl());
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($this->getBody())))
        );

        $response = new Response();
        $response->setJsonData(curl_exec($ch));
        $response->setCode(curl_getinfo($ch, CURLINFO_HTTP_CODE));

        curl_close($ch);

        return $response;
    }

}