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
        
        $caracteres = $this->caraterModel->findAll();
        
        $this->render('carater_atendimento.index', [
            'caracteres' => $caracteres,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function show($id)
    {
        AuthMiddleware::handle();
        
        $carater = $this->caraterModel->findById($id);
        
        if (!$carater) {
            $this->flash('Caráter de atendimento não encontrado', 'error');
            $this->redirect('/carater-atendimento');
        }
        
        $this->render('carater_atendimento.show', [
            'carater' => $carater,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function create()
    {
        AuthMiddleware::handle();
        
        $this->render('carater_atendimento.create', [
            'flash' => $this->getFlash()
        ]);
    }
    
    public function store()
    {
        AuthMiddleware::handle();
        
        $data = $this->getInput();
        
        $errors = $this->validateCarater($data);
        if (!empty($errors)) {
            $this->flash(implode(', ', $errors), 'error');
            $this->redirect('/carater-atendimento/create');
        }
        
        if ($this->caraterModel->findByCodigo($data['codigo'])) {
            $this->flash('Código já cadastrado no sistema', 'error');
            $this->redirect('/carater-atendimento/create');
        }
        
        $id = $this->caraterModel->create($data);
        
        if ($id) {
            $this->flash('Caráter de atendimento cadastrado com sucesso', 'success');
            $this->redirect('/carater-atendimento/' . $id);
        } else {
            $this->flash('Erro ao cadastrar caráter de atendimento', 'error');
            $this->redirect('/carater-atendimento/create');
        }
    }
    
    public function edit($id)
    {
        AuthMiddleware::handle();
        
        $carater = $this->caraterModel->findById($id);
        
        if (!$carater) {
            $this->flash('Caráter de atendimento não encontrado', 'error');
            $this->redirect('/carater-atendimento');
        }
        
        $this->render('carater_atendimento.edit', [
            'carater' => $carater,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function update($id)
    {
        AuthMiddleware::handle();
        
        $carater = $this->caraterModel->findById($id);
        
        if (!$carater) {
            $this->flash('Caráter de atendimento não encontrado', 'error');
            $this->redirect('/carater-atendimento');
        }
        
        $data = $this->getInput();
        
        $errors = $this->validateCarater($data);
        if (!empty($errors)) {
            $this->flash(implode(', ', $errors), 'error');
            $this->redirect('/carater-atendimento/' . $id . '/edit');
        }
        
        $existing = $this->caraterModel->findByCodigo($data['codigo']);
        if ($existing && $existing['id'] != $id) {
            $this->flash('Código já cadastrado', 'error');
            $this->redirect('/carater-atendimento/' . $id . '/edit');
        }
        
        $updated = $this->caraterModel->update($id, $data);
        
        if ($updated) {
            $this->flash('Caráter de atendimento atualizado com sucesso', 'success');
            $this->redirect('/carater-atendimento/' . $id);
        } else {
            $this->flash('Erro ao atualizar caráter de atendimento', 'error');
            $this->redirect('/carater-atendimento/' . $id . '/edit');
        }
    }
    
    public function delete($id)
    {
        AuthMiddleware::handle();
        
        $carater = $this->caraterModel->findById($id);
        
        if (!$carater) {
            $this->jsonResponse(['success' => false, 'message' => 'Caráter de atendimento não encontrado'], 404);
        }
        
        $deleted = $this->caraterModel->delete($id);
        
        if ($deleted) {
            $this->flash('Caráter de atendimento excluído com sucesso', 'success');
            $this->jsonResponse(['success' => true, 'message' => 'Caráter de atendimento excluído com sucesso']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Erro ao excluir caráter de atendimento'], 500);
        }
    }
    
    public function ajax_search()
    {
        AuthMiddleware::handle();
        
        $termo = $this->getInput('termo', '');
        
        if (strlen($termo) < 2) {
            $this->jsonResponse(['success' => false, 'message' => 'Digite ao menos 2 caracteres']);
        }
        
        $caracteres = $this->caraterModel->search($termo);
        
        $this->jsonResponse([
            'success' => true,
            'data' => $caracteres
        ]);
    }
    
    public function ajax_list()
    {
        AuthMiddleware::handle();
        
        $page = (int) $this->getInput('page', 1);
        $limit = (int) $this->getInput('limit', 10);
        $offset = ($page - 1) * $limit;
        
        $caracteres = $this->caraterModel->findAll($limit, $offset);
        $total = $this->caraterModel->count();
        
        $this->jsonResponse([
            'success' => true,
            'data' => $caracteres,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }
    
    private function validateCarater($data)
    {
        $errors = [];
        
        if (empty($data['codigo'])) {
            $errors[] = 'Código é obrigatório';
        }
        
        if (empty($data['descricao'])) {
            $errors[] = 'Descrição é obrigatória';
        }
        
        return $errors;
    }
}
