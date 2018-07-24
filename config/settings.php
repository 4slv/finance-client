<?php

return [
    //максимальное количество задач для одной отправки (0 - без ограничений)
    'taskLimit' => 1000,

    //максимальное количество попыток отправки задачи, после превышения ставится cancel
    'attemptLimit' => 10,

    //url адрес отправления запросов
    'url' => 'http://192.168.74.3/apitmp',

    //максимальное время ожидания ответа сервера
    'timeout' => 5,

    //полный путь до класса EntityManager
    'CEntityManagerPath' => 'Framework\Database\CEntityManager',

    //неймспейс до классов-действий
    'actionClassNamespace' => 'ApiClient\Action',

    //ключи для доступа к api
    'apiKey' => 'd4f2192cf3577c02c9a7de61a4d8d9e5',
];