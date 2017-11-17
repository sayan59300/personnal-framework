<?php

return [
    'paths' => [
        'migrations' => __DIR__ . '/db/migrations/',
        'seeds' => __DIR__ . '/db/seeds/'
    ],
    'environments' =>[
        'default_migration_table' => 'phinxlog',
        'default_database' => 'development',
        'production' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'production_db',
            'user' => 'root',
            'pass' => '',
            'port' => 3306,
            'charset' => 'utf8'
        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => '127.0.0.1',
            'name' => 'sayan593_dev',
            'user' => 'root',
            'pass' => 'root',
            'port' => 8889,
            'charset' => 'utf8',
            'unix_sowket' => '/Applications/MAMP/tmp/mysql/mysql.sock'
        ]
    ]
];
