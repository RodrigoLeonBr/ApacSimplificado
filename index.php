<?php
/**
 * Arquivo de entrada na raiz do projeto
 * Redireciona para o diretório public/
 */

$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$basePath = '/ApacSimplificado';

// Se já está acessando via public/, não faz nada
if (strpos($requestUri, '/public/') !== false || $requestUri === $basePath . '/public') {
    // Já está no public/, não precisa redirecionar
    return;
}

// Remove o basePath do requestUri para obter o path relativo
$relativePath = $requestUri;
if (strpos($requestUri, $basePath) === 0) {
    $relativePath = substr($requestUri, strlen($basePath));
}

// Remove barra inicial se existir
$relativePath = ltrim($relativePath, '/');

// Se é a raiz ou vazio, redireciona para public/
if (empty($relativePath) || $relativePath === '') {
    $redirectUrl = $basePath . '/public/';
} else {
    // Redireciona para public/ mantendo o path
    $redirectUrl = $basePath . '/public/' . $relativePath;
}

// Preserva query string se existir
if (!empty($_SERVER['QUERY_STRING'])) {
    $redirectUrl .= '?' . $_SERVER['QUERY_STRING'];
}

header("Location: {$redirectUrl}", true, 301);
exit;

