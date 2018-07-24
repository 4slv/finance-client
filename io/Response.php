<?php

namespace ApiClient\IO;

class Response
{
    /** @var string $jsonData */
    private $jsonData;

    /** @var integer $code */
    private $code;

    /**
     * @return string
     */
    public function getJsonData(): string
    {
        return $this->jsonData;
    }

    /**
     * @param string $jsonData
     * @return Response
     */
    public function setJsonData(string $jsonData): Response
    {
        $this->jsonData = $jsonData;
        return $this;
    }

    /**
     * Преобразует json ответ сервера в массив с данными
     * @return array|null
     */
    public function getData(): ?array
    {
        return json_decode($this->getJsonData());
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     * @return Response
     */
    public function setCode(int $code): Response
    {
        $this->code = $code;
        return $this;
    }


}