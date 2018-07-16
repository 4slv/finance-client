<?php

return [
    'driver'   => 'pdo_mysql',
    'host'     => getenv('MYSQL_HOST'),
    'dbname'   => getenv('MYSQL_DATABASE'),
    'user'     => getenv('MYSQL_USER'),
    'password' => getenv('MYSQL_PASSWORD'),
    'port'     => getenv('MYSQL_PORT'),
    'charset'  => 'utf8'
];