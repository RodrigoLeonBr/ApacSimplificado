<?php

session_start();

date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__ . '/../config/constants.php';

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

error_reporting(E_ALL);
ini_set('display_errors', 1);

$router = require __DIR__ . '/../router.php';
$router->dispatch();
