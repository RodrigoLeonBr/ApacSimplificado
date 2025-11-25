<?php

use App\Utils\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\FaixaController;
use App\Controllers\ApacController;
use App\Controllers\PacienteController;
use App\Controllers\CidController;
use App\Controllers\ProcedimentoController;
use App\Controllers\EstabelecimentoController;
use App\Controllers\ProfissionalController;
use App\Controllers\CaraterAtendimentoController;
use App\Controllers\LaudoController;
use App\Controllers\ApiController;

$router = new Router();

$router->get('/', function() use ($router) {
    Router::redirect('/dashboard');
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

// API Routes
$router->get('/api/validar-cns', function() {
    $controller = new ApiController();
    $controller->validarCns();
});

$router->get('/api/validar-cpf', function() {
    $controller = new ApiController();
    $controller->validarCpf();
});

$router->get('/api/validar-cep', function() {
    $controller = new ApiController();
    $controller->validarCep();
});

$router->get('/api/validar-email', function() {
    $controller = new ApiController();
    $controller->validarEmail();
});

$router->get('/api/validar-cnpj', function() {
    $controller = new ApiController();
    $controller->validarCnpj();
});

$router->get('/api/buscar-cep', function() {
    $controller = new ApiController();
    $controller->buscarCep();
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

$router->get('/pacientes/ajax/search', function() {
    $controller = new PacienteController();
    $controller->ajax_search();
});

$router->get('/pacientes/ajax/list', function() {
    $controller = new PacienteController();
    $controller->ajax_list();
});

$router->get('/pacientes', function() {
    $controller = new PacienteController();
    $controller->index();
});

$router->get('/pacientes/create', function() {
    $controller = new PacienteController();
    $controller->create();
});

$router->post('/pacientes', function() {
    $controller = new PacienteController();
    $controller->store();
});

$router->get('/pacientes/{id}', function($id) {
    $controller = new PacienteController();
    $controller->show($id);
});

$router->get('/pacientes/{id}/edit', function($id) {
    $controller = new PacienteController();
    $controller->edit($id);
});

$router->post('/pacientes/{id}/update', function($id) {
    $controller = new PacienteController();
    $controller->update($id);
});

$router->post('/pacientes/{id}/delete', function($id) {
    $controller = new PacienteController();
    $controller->delete($id);
});

$router->get('/estabelecimentos/ajax/search', function() {
    $controller = new EstabelecimentoController();
    $controller->ajax_search();
});

$router->get('/estabelecimentos/ajax/list', function() {
    $controller = new EstabelecimentoController();
    $controller->ajax_list();
});

$router->get('/estabelecimentos', function() {
    $controller = new EstabelecimentoController();
    $controller->index();
});

$router->get('/estabelecimentos/create', function() {
    $controller = new EstabelecimentoController();
    $controller->create();
});

$router->post('/estabelecimentos', function() {
    $controller = new EstabelecimentoController();
    $controller->store();
});

$router->get('/estabelecimentos/{id}', function($id) {
    $controller = new EstabelecimentoController();
    $controller->show($id);
});

$router->get('/estabelecimentos/{id}/edit', function($id) {
    $controller = new EstabelecimentoController();
    $controller->edit($id);
});

$router->post('/estabelecimentos/{id}/update', function($id) {
    $controller = new EstabelecimentoController();
    $controller->update($id);
});

$router->post('/estabelecimentos/{id}/delete', function($id) {
    $controller = new EstabelecimentoController();
    $controller->delete($id);
});

$router->get('/profissionais/ajax/search', function() {
    $controller = new ProfissionalController();
    $controller->ajax_search();
});

$router->get('/profissionais/ajax/list', function() {
    $controller = new ProfissionalController();
    $controller->ajax_list();
});

$router->get('/profissionais', function() {
    $controller = new ProfissionalController();
    $controller->index();
});

$router->get('/profissionais/create', function() {
    $controller = new ProfissionalController();
    $controller->create();
});

$router->post('/profissionais', function() {
    $controller = new ProfissionalController();
    $controller->store();
});

$router->get('/profissionais/{id}', function($id) {
    $controller = new ProfissionalController();
    $controller->show($id);
});

$router->get('/profissionais/{id}/edit', function($id) {
    $controller = new ProfissionalController();
    $controller->edit($id);
});

$router->post('/profissionais/{id}/update', function($id) {
    $controller = new ProfissionalController();
    $controller->update($id);
});

$router->post('/profissionais/{id}/delete', function($id) {
    $controller = new ProfissionalController();
    $controller->delete($id);
});

$router->get('/laudos/ajax/search', function() {
    $controller = new LaudoController();
    $controller->ajax_search();
});

$router->get('/laudos/ajax/list', function() {
    $controller = new LaudoController();
    $controller->ajax_list();
});

$router->get('/laudos', function() {
    $controller = new LaudoController();
    $controller->index();
});

$router->get('/laudos/create', function() {
    $controller = new LaudoController();
    $controller->create();
});

$router->post('/laudos', function() {
    $controller = new LaudoController();
    $controller->store();
});

$router->get('/laudos/{id}', function($id) {
    $controller = new LaudoController();
    $controller->show($id);
});

$router->get('/laudos/{id}/edit', function($id) {
    $controller = new LaudoController();
    $controller->edit($id);
});

$router->post('/laudos/{id}/update', function($id) {
    $controller = new LaudoController();
    $controller->update($id);
});

$router->post('/laudos/{id}/delete', function($id) {
    $controller = new LaudoController();
    $controller->delete($id);
});

// Rotas de Profissionais
$router->get('/profissional/ajax/search', function() {
    $controller = new ProfissionalController();
    $controller->ajax_search();
});

$router->get('/profissional', function() {
    $controller = new ProfissionalController();
    $controller->index();
});

$router->get('/profissional/create', function() {
    $controller = new ProfissionalController();
    $controller->create();
});

$router->post('/profissional', function() {
    $controller = new ProfissionalController();
    $controller->store();
});

$router->get('/profissional/{id}', function($id) {
    $controller = new ProfissionalController();
    $controller->show($id);
});

$router->get('/profissional/{id}/edit', function($id) {
    $controller = new ProfissionalController();
    $controller->edit($id);
});

$router->post('/profissional/{id}/update', function($id) {
    $controller = new ProfissionalController();
    $controller->update($id);
});

$router->post('/profissional/{id}/delete', function($id) {
    $controller = new ProfissionalController();
    $controller->delete($id);
});

// Rotas de CIDs
$router->get('/cid/ajax/search', function() {
    $controller = new CidController();
    $controller->ajax_search();
});

$router->get('/cid', function() {
    $controller = new CidController();
    $controller->index();
});

$router->get('/cid/create', function() {
    $controller = new CidController();
    $controller->create();
});

$router->post('/cid', function() {
    $controller = new CidController();
    $controller->store();
});

$router->get('/cid/{id}', function($id) {
    $controller = new CidController();
    $controller->show($id);
});

$router->get('/cid/{id}/edit', function($id) {
    $controller = new CidController();
    $controller->edit($id);
});

$router->post('/cid/{id}/update', function($id) {
    $controller = new CidController();
    $controller->update($id);
});

$router->post('/cid/{id}/delete', function($id) {
    $controller = new CidController();
    $controller->delete($id);
});

// Rotas de Procedimentos
$router->get('/procedimento/ajax/search', function() {
    $controller = new ProcedimentoController();
    $controller->ajax_search();
});

$router->get('/procedimento', function() {
    $controller = new ProcedimentoController();
    $controller->index();
});

$router->get('/procedimento/create', function() {
    $controller = new ProcedimentoController();
    $controller->create();
});

$router->post('/procedimento', function() {
    $controller = new ProcedimentoController();
    $controller->store();
});

$router->get('/procedimento/{id}', function($id) {
    $controller = new ProcedimentoController();
    $controller->show($id);
});

$router->get('/procedimento/{id}/edit', function($id) {
    $controller = new ProcedimentoController();
    $controller->edit($id);
});

$router->post('/procedimento/{id}/update', function($id) {
    $controller = new ProcedimentoController();
    $controller->update($id);
});

$router->post('/procedimento/{id}/delete', function($id) {
    $controller = new ProcedimentoController();
    $controller->delete($id);
});

// Rotas de Caráter de Atendimento
$router->get('/carater/ajax/search', function() {
    $controller = new CaraterAtendimentoController();
    $controller->ajax_search();
});

$router->get('/carater', function() {
    $controller = new CaraterAtendimentoController();
    $controller->index();
});

$router->get('/carater/create', function() {
    $controller = new CaraterAtendimentoController();
    $controller->create();
});

$router->post('/carater', function() {
    $controller = new CaraterAtendimentoController();
    $controller->store();
});

$router->get('/carater/{id}', function($id) {
    $controller = new CaraterAtendimentoController();
    $controller->show($id);
});

$router->get('/carater/{id}/edit', function($id) {
    $controller = new CaraterAtendimentoController();
    $controller->edit($id);
});

$router->post('/carater/{id}/update', function($id) {
    $controller = new CaraterAtendimentoController();
    $controller->update($id);
});

$router->post('/carater/{id}/delete', function($id) {
    $controller = new CaraterAtendimentoController();
    $controller->delete($id);
});

return $router;
