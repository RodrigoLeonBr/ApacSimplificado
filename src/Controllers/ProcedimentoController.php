<?php

namespace App\Controllers;

use App\Models\Procedimento;
use App\Middleware\AuthMiddleware;

class ProcedimentoController extends BaseController
{
    private $procedimentoModel;
    
    public function __construct($db = null)
    {
        parent::__construct($db);
        $this->procedimentoModel = new Procedimento();
    }
    
    public function index()
    {
        AuthMiddleware::handle();
        
        $procedimentos = $this->procedimentoModel->findAll();
        
        $this->render('procedimentos.index', [
            'procedimentos' => $procedimentos,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function show($id)
    {
        AuthMiddleware::handle();
        
        $procedimento = $this->procedimentoModel->findById($id);
        
        if (!$procedimento) {
            $this->flash('Procedimento não encontrado', 'error');
            $this->redirect('/procedimentos');
        }
        
        $this->render('procedimentos.show', [
            'procedimento' => $procedimento,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function create()
    {
        AuthMiddleware::handle();
        
        $this->render('procedimentos.create', [
            'flash' => $this->getFlash()
        ]);
    }
    
    public function store()
    {
        AuthMiddleware::handle();
        
        $data = $this->getInput();
        
        $errors = $this->validateProcedimento($data);
        if (!empty($errors)) {
            $this->flash(implode(', ', $errors), 'error');
            $this->redirect('/procedimentos/create');
        }
        
        if ($this->procedimentoModel->findByCodigo($data['codigo_procedimento'])) {
            $this->flash('Código de procedimento já cadastrado', 'error');
            $this->redirect('/procedimentos/create');
        }
        
        $id = $this->procedimentoModel->create($data);
        
        if ($id) {
            $this->flash('Procedimento cadastrado com sucesso', 'success');
            $this->redirect('/procedimentos/' . $id);
        } else {
            $this->flash('Erro ao cadastrar procedimento', 'error');
            $this->redirect('/procedimentos/create');
        }
    }
    
    public function edit($id)
    {
        AuthMiddleware::handle();
        
        $procedimento = $this->procedimentoModel->findById($id);
        
        if (!$procedimento) {
            $this->flash('Procedimento não encontrado', 'error');
            $this->redirect('/procedimentos');
        }
        
        $this->render('procedimentos.edit', [
            'procedimento' => $procedimento,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function update($id)
    {
        AuthMiddleware::handle();
        
        $procedimento = $this->procedimentoModel->findById($id);
        
        if (!$procedimento) {
            $this->flash('Procedimento não encontrado', 'error');
            $this->redirect('/procedimentos');
        }
        
        $data = $this->getInput();
        
        $errors = $this->validateProcedimento($data);
        if (!empty($errors)) {
            $this->flash(implode(', ', $errors), 'error');
            $this->redirect('/procedimentos/' . $id . '/edit');
        }
        
        $existing = $this->procedimentoModel->findByCodigo($data['codigo_procedimento']);
        if ($existing && $existing['id'] != $id) {
            $this->flash('Código de procedimento já cadastrado', 'error');
            $this->redirect('/procedimentos/' . $id . '/edit');
        }
        
        $updated = $this->procedimentoModel->update($id, $data);
        
        if ($updated) {
            $this->flash('Procedimento atualizado com sucesso', 'success');
            $this->redirect('/procedimentos/' . $id);
        } else {
            $this->flash('Erro ao atualizar procedimento', 'error');
            $this->redirect('/procedimentos/' . $id . '/edit');
        }
    }
    
    public function delete($id)
    {
        AuthMiddleware::handle();
        
        $procedimento = $this->procedimentoModel->findById($id);
        
        if (!$procedimento) {
            $this->jsonResponse(['success' => false, 'message' => 'Procedimento não encontrado'], 404);
        }
        
        $deleted = $this->procedimentoModel->delete($id);
        
        if ($deleted) {
            $this->flash('Procedimento excluído com sucesso', 'success');
            $this->jsonResponse(['success' => true, 'message' => 'Procedimento excluído com sucesso']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Erro ao excluir procedimento'], 500);
        }
    }
    
    public function ajax_search()
    {
        AuthMiddleware::handle();
        
        $termo = $this->getInput('termo', '');
        
        if (strlen($termo) < 2) {
            $this->jsonResponse(['success' => false, 'message' => 'Digite ao menos 2 caracteres']);
        }
        
        $procedimentos = $this->procedimentoModel->search($termo);
        
        $this->jsonResponse([
            'success' => true,
            'data' => $procedimentos
        ]);
    }
    
    public function ajax_list()
    {
        AuthMiddleware::handle();
        
        $page = (int) $this->getInput('page', 1);
        $limit = (int) $this->getInput('limit', 10);
        $offset = ($page - 1) * $limit;
        
        $procedimentos = $this->procedimentoModel->findAll($limit, $offset);
        $total = $this->procedimentoModel->count();
        
        $this->jsonResponse([
            'success' => true,
            'data' => $procedimentos,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }
    
    private function validateProcedimento($data)
    {
        $errors = [];
        
        if (empty($data['codigo_procedimento'])) {
            $errors[] = 'Código do procedimento é obrigatório';
        }
        
        if (empty($data['nome'])) {
            $errors[] = 'Nome é obrigatório';
        }
        
        return $errors;
    }
}
