<?php

use App\Utils\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\FaixaController;
use App\Controllers\ApacController;

$router = new Router();

$router->get('/', function() {
    header('Location: /dashboard');
    exit;
});

$router->get('/login', function() {
    $controller = new AuthController();
    $controller->showLogin();
});

$router->post('/login', function() {
    $controller = new AuthController();
    $controller->login();
});

$router->get('/logout', function() {
    $controller = new AuthController();
    $controller->logout();
});

$router->get('/dashboard', function() {
    $controller = new DashboardController();
    $controller->index();
});

$router->get('/faixas', function() {
    $controller = new FaixaController();
    $controller->index();
});

$router->get('/faixas/create', function() {
    $controller = new FaixaController();
    $controller->create();
});

$router->post('/faixas', function() {
    $controller = new FaixaController();
    $controller->store();
});

$router->get('/faixas/{id}', function($id) {
    $controller = new FaixaController();
    $controller->show($id);
});

$router->post('/faixas/{id}/delete', function($id) {
    $controller = new FaixaController();
    $controller->delete($id);
});

$router->get('/apacs', function() {
    $controller = new ApacController();
    $controller->index();
});

$router->get('/apacs/create', function() {
    $controller = new ApacController();
    $controller->create();
});

$router->post('/apacs', function() {
    $controller = new ApacController();
    $controller->store();
});

$router->post('/apacs/{id}/imprimir', function($id) {
    $controller = new ApacController();
    $controller->marcarImpresso($id);
});

return $router;
