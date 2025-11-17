<?php

return [
    'host' => getenv('PGHOST') ?: 'localhost',
    'port' => getenv('PGPORT') ?: '5432',
    'database' => getenv('PGDATABASE') ?: 'apac_system',
    'username' => getenv('PGUSER') ?: 'root',
    'password' => getenv('PGPASSWORD') ?: '',
    'charset' => 'utf8mb4',
    'driver' => 'pgsql'
];
