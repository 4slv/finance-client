<?php

return [
    //максимальное количество задач для одной отправки (0 - без ограничений)
    'taskLimit' => 5,

    //url адрес отправления запросов
    'url' => 'http://192.168.74.37/apitmp',

    //максимальное время ожидания ответа сервера
    'timeout' => 10,

    //полный путь до класса EntityManager
    'CEntityManagerPath' => 'Framework\Database\CEntityManager',

    //ключи для доступа к api
    'apiKey' => [
        'ChSlovoRu' => 'd4f2192cf3577c02c9a7de61a4d8d9e5',
        'ChSlovoKz' => '1b1670cb15ce66067c7db7c414c9dc61',
        'ChSlovoGe' => '61aa39ea0d82be10b0a776880f30d267'
    ],
];