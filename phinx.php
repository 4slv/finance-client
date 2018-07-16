<?php

$config = include 'config.php';

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],

    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'main',
        'main' => [
            'adapter' => 'mysql',
            'host' => $config['host'],
            'name' => $config['dbname'],
            'user' => $config['user'],
            'pass' => $config['password'],
            'port' => $config['port'],
            'charset' => $config['charset']
        ]
    ],

    'version_order' => 'creation'
];
