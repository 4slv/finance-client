<?php

namespace ApiClient\IO;

use Exception;

class HttpRequest extends AbstractRequest
{
    /**
     * @return mixed
     * @throws Exception
     */
    public function send(): AbstractResponse
    {
        if(!filter_var($this->getUrl(), FILTER_VALIDATE_URL)){
            throw new Exception(sprintf("Invalid URL address '%s'", $this->getUrl()));
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getBody());
        curl_setopt($ch, CURLOPT_URL, $this->getUrl());
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($this->getBody())))
        );

        $result = curl_exec($ch);
        curl_close($ch);

        $response = new HttpResponse();
    }

}