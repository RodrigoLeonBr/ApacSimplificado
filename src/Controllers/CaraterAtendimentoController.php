<?php

namespace App\Controllers;

use App\Models\CaraterAtendimento;
use App\Middleware\AuthMiddleware;

class CaraterAtendimentoController extends BaseController
{
    private $caraterModel;
    
    public function __construct($db = null)
    {
        parent::__construct($db);
        $this->caraterModel = new CaraterAtendimento();
    }
    
    public function index()
    {
        AuthMiddleware::handle();
        
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 10;
        $total = $this->caraterModel->countTotal();
        $totalPages = max(1, ceil($total / $limit));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $limit;
        
        $carateres = $this->caraterModel->findPaginated($limit, $offset);
        
        $this->render('carater/index', [
            'carateres' => $carateres,
            'totalCarateres' => $total,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function show($id)
    {
        AuthMiddleware::handle();
        
        $carater = $this->caraterModel->findById($id);
        
        if (!$carater) {
            $this->flash('Caráter de Atendimento não encontrado', 'error');
            $this->redirect('/carater');
        }
        
        $this->render('carater/show', [
            'carater' => $carater,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function create()
    {
        AuthMiddleware::handle();
        
        $this->render('carater/form', [
            'carater' => null,
            'action' => '/carater',
            'method' => 'POST',
            'title' => 'Novo Caráter de Atendimento',
            'old' => [],
            'flash' => $this->getFlash()
        ]);
    }
    
    public function store()
    {
        AuthMiddleware::handle();
        
        $data = $this->getInput();
        $errors = $this->validateCarater($data);
        
        if (!empty($errors)) {
            \App\Utils\Session::flash('errors', $errors);
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/carater/create');
        }
        
        if ($this->caraterModel->findByCodigo($data['codigo'])) {
            $this->flash('Código já cadastrado no sistema', 'error');
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/carater/create');
        }
        
        try {
            $id = $this->caraterModel->create($data);
            
            if ($id) {
                $this->flash('Caráter de Atendimento cadastrado com sucesso', 'success');
                $this->redirect('/carater/' . $id);
            } else {
                $this->flash('Erro ao cadastrar caráter de atendimento', 'error');
                \App\Utils\Session::flash('old', $data);
                $this->redirect('/carater/create');
            }
        } catch (\Exception $e) {
            error_log('Erro ao cadastrar caráter de atendimento: ' . $e->getMessage());
            $this->flash('Erro ao cadastrar caráter de atendimento: ' . $e->getMessage(), 'error');
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/carater/create');
        }
    }
    
    public function edit($id)
    {
        AuthMiddleware::handle();
        
        $carater = $this->caraterModel->findById($id);
        
        if (!$carater) {
            $this->flash('Caráter de Atendimento não encontrado', 'error');
            $this->redirect('/carater');
        }
        
        $this->render('carater/form', [
            'carater' => $carater,
            'action' => '/carater/' . $id . '/update',
            'method' => 'POST',
            'title' => 'Editar Caráter de Atendimento',
            'old' => $carater,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function update($id)
    {
        AuthMiddleware::handle();
        
        $carater = $this->caraterModel->findById($id);
        
        if (!$carater) {
            $this->flash('Caráter de Atendimento não encontrado', 'error');
            $this->redirect('/carater');
        }
        
        $data = $this->getInput();
        $errors = $this->validateCarater($data);
        
        if (!empty($errors)) {
            \App\Utils\Session::flash('errors', $errors);
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/carater/' . $id . '/edit');
        }
        
        $existing = $this->caraterModel->findByCodigo($data['codigo']);
        if ($existing && $existing['id'] != $id) {
            $this->flash('Código já cadastrado para outro caráter de atendimento', 'error');
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/carater/' . $id . '/edit');
        }
        
        try {
            $updated = $this->caraterModel->update($id, $data);
            
            if ($updated) {
                $this->flash('Caráter de Atendimento atualizado com sucesso', 'success');
                $this->redirect('/carater/' . $id);
            } else {
                $this->flash('Erro ao atualizar caráter de atendimento', 'error');
                \App\Utils\Session::flash('old', $data);
                $this->redirect('/carater/' . $id . '/edit');
            }
        } catch (\Exception $e) {
            error_log('Erro ao atualizar caráter de atendimento: ' . $e->getMessage());
            $this->flash('Erro ao atualizar caráter de atendimento: ' . $e->getMessage(), 'error');
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/carater/' . $id . '/edit');
        }
    }
    
    public function delete($id)
    {
        AuthMiddleware::handle();
        
        $carater = $this->caraterModel->findById($id);
        
        if (!$carater) {
            $this->jsonResponse(['success' => false, 'message' => 'Caráter de Atendimento não encontrado'], 404);
            return;
        }
        
        try {
            $deleted = $this->caraterModel->delete($id);
            
            if ($deleted) {
                $this->jsonResponse(['success' => true, 'message' => 'Caráter de Atendimento excluído com sucesso']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Erro ao excluir caráter de atendimento'], 500);
            }
        } catch (\Exception $e) {
            error_log('Erro ao excluir caráter de atendimento: ' . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Erro ao excluir caráter de atendimento'], 500);
        }
    }
    
    public function ajax_search()
    {
        AuthMiddleware::handle();
        
        $q = $_GET['q'] ?? '';
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        if (strlen($q) === 0) {
            $carateres = $this->caraterModel->findPaginated($limit, $offset);
            $total = $this->caraterModel->countTotal();
        } else {
            $carateres = $this->caraterModel->searchPaginated($q, $limit, $offset);
            $total = $this->caraterModel->searchCount($q);
        }
        
        $totalPages = max(1, ceil($total / $limit));
        $page = min($page, $totalPages);
        
        $this->jsonResponse([
            'success' => true,
            'carateres' => $carateres,
            'total' => $total,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ]);
    }
    
    private function validateCarater($data)
    {
        $errors = [];
        
        if (empty($data['codigo'])) {
            $errors['codigo'] = 'Código é obrigatório';
        }
        
        if (empty($data['descricao'])) {
            $errors['descricao'] = 'Descrição é obrigatória';
        }
        
        return $errors;
    }
}
