<?php

require_once __DIR__ . '/env.php';

return [
    'class' => 'yii\db\Connection',
    'dsn' => getenv('DB_DSN') ?: 'pgsql:host=db;port=5432;dbname=dbEducacional',
    'username' => getenv('DB_USERNAME') ?: 'educacional',
    'password' => getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '',
    'charset' => 'utf8',
    'schemaMap' => [
        'pgsql' => [
            'class' => 'yii\db\pgsql\Schema',
            'defaultSchema' => 'public'
        ]
    ],
];
