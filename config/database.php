<?php

return [
    'host' => getenv('MYSQL_HOST') ?: 'localhost',
    'port' => '3306',
    'database' => getenv('MYSQL_DATABASE') ?: 'apac_system',
    'username' => getenv('MYSQL_USER') ?: 'root',
    'password' => getenv('MYSQL_PASSWORD') ?: '',
    'charset' => 'utf8mb4',
    'driver' => 'mysql'
];
