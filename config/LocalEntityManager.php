<?php

namespace ApiClient\Config;

use ApiClient\App\ApiClientException;
use Doctrine\ORM\EntityManager;

class LocalEntityManager
{
    /**
     * @return EntityManager
     * @throws ApiClientException
     */
    public static function getEntityManager(): EntityManager
    {
        $error = false;

        $entityManagerPath = Config::get('CEntityManagerPath');
        if(!class_exists($entityManagerPath)){
            $error = true;
        }

        $entityManager = $entityManagerPath::getInstance();
        if(!$entityManager instanceof EntityManager){
            $error = true;
        }

        if($error){
            throw new ApiClientException("Укажите корректное имя класса 'CEntityManager'");
        }

        return $entityManager;
    }
}