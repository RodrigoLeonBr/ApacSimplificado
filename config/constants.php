<?php

define('APP_NAME', 'Sistema APAC');
define('APP_VERSION', '1.0.0');
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', BASE_PATH . '/public');
define('VIEWS_PATH', BASE_PATH . '/views');
define('LOGS_PATH', BASE_PATH . '/logs');
define('TEMP_PATH', BASE_PATH . '/temp');

// Detectar BASE_URL automaticamente
// O script está em public/index.php, então SCRIPT_NAME será algo como /ApacSimplificado/public/index.php
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
$scriptDir = dirname($scriptName);
$baseUrl = rtrim(str_replace('\\', '/', $scriptDir), '/');

// Se estiver em subdiretório (ex: /ApacSimplificado/public/index.php)
// O scriptDir será /ApacSimplificado/public, então removemos /public
if (strpos($baseUrl, '/public') !== false) {
    $baseUrl = str_replace('/public', '', $baseUrl);
}

// Se baseUrl estiver vazio ou for apenas '/', define como vazio (raiz)
if ($baseUrl === '' || $baseUrl === '/') {
    $baseUrl = '';
} else {
    // Garante que começa com / e não termina com /
    $baseUrl = '/' . trim($baseUrl, '/');
}

define('BASE_URL', $baseUrl);

define('STATUS_DISPONIVEL', 'disponivel');
define('STATUS_EM_USO', 'em_uso');
define('STATUS_ESGOTADA', 'esgotada');

define('ROLE_ADMIN', 'admin');
define('ROLE_USER', 'user');
