<?php

namespace App\Controllers;

use App\Models\Profissional;
use App\Middleware\AuthMiddleware;

class ProfissionalController extends BaseController
{
    private $profissionalModel;
    
    public function __construct($db = null)
    {
        parent::__construct($db);
        $this->profissionalModel = new Profissional();
    }
    
    public function index()
    {
        AuthMiddleware::handle();
        
        $profissionais = $this->profissionalModel->findAll();
        
        $this->render('profissionais.index', [
            'profissionais' => $profissionais,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function show($id)
    {
        AuthMiddleware::handle();
        
        $profissional = $this->profissionalModel->findById($id);
        
        if (!$profissional) {
            $this->flash('Profissional não encontrado', 'error');
            $this->redirect('/profissionais');
        }
        
        $this->render('profissionais.show', [
            'profissional' => $profissional,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function create()
    {
        AuthMiddleware::handle();
        
        $this->render('profissionais.create', [
            'flash' => $this->getFlash()
        ]);
    }
    
    public function store()
    {
        AuthMiddleware::handle();
        
        $data = $this->getInput();
        
        $errors = $this->validateProfissional($data);
        if (!empty($errors)) {
            $this->flash(implode(', ', $errors), 'error');
            $this->redirect('/profissionais/create');
        }
        
        if ($this->profissionalModel->findByCns($data['cns'])) {
            $this->flash('CNS já cadastrado no sistema', 'error');
            $this->redirect('/profissionais/create');
        }
        
        $id = $this->profissionalModel->create($data);
        
        if ($id) {
            $this->flash('Profissional cadastrado com sucesso', 'success');
            $this->redirect('/profissionais/' . $id);
        } else {
            $this->flash('Erro ao cadastrar profissional', 'error');
            $this->redirect('/profissionais/create');
        }
    }
    
    public function edit($id)
    {
        AuthMiddleware::handle();
        
        $profissional = $this->profissionalModel->findById($id);
        
        if (!$profissional) {
            $this->flash('Profissional não encontrado', 'error');
            $this->redirect('/profissionais');
        }
        
        $this->render('profissionais.edit', [
            'profissional' => $profissional,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function update($id)
    {
        AuthMiddleware::handle();
        
        $profissional = $this->profissionalModel->findById($id);
        
        if (!$profissional) {
            $this->flash('Profissional não encontrado', 'error');
            $this->redirect('/profissionais');
        }
        
        $data = $this->getInput();
        
        $errors = $this->validateProfissional($data);
        if (!empty($errors)) {
            $this->flash(implode(', ', $errors), 'error');
            $this->redirect('/profissionais/' . $id . '/edit');
        }
        
        $existing = $this->profissionalModel->findByCns($data['cns']);
        if ($existing && $existing['id'] != $id) {
            $this->flash('CNS já cadastrado para outro profissional', 'error');
            $this->redirect('/profissionais/' . $id . '/edit');
        }
        
        $updated = $this->profissionalModel->update($id, $data);
        
        if ($updated) {
            $this->flash('Profissional atualizado com sucesso', 'success');
            $this->redirect('/profissionais/' . $id);
        } else {
            $this->flash('Erro ao atualizar profissional', 'error');
            $this->redirect('/profissionais/' . $id . '/edit');
        }
    }
    
    public function delete($id)
    {
        AuthMiddleware::handle();
        
        $profissional = $this->profissionalModel->findById($id);
        
        if (!$profissional) {
            $this->jsonResponse(['success' => false, 'message' => 'Profissional não encontrado'], 404);
        }
        
        $deleted = $this->profissionalModel->delete($id);
        
        if ($deleted) {
            $this->flash('Profissional excluído com sucesso', 'success');
            $this->jsonResponse(['success' => true, 'message' => 'Profissional excluído com sucesso']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Erro ao excluir profissional'], 500);
        }
    }
    
    public function ajax_search()
    {
        AuthMiddleware::handle();
        
        $termo = $this->getInput('termo', '');
        
        if (strlen($termo) < 3) {
            $this->jsonResponse(['success' => false, 'message' => 'Digite ao menos 3 caracteres']);
        }
        
        $profissionais = $this->profissionalModel->search($termo);
        
        $this->jsonResponse([
            'success' => true,
            'data' => $profissionais
        ]);
    }
    
    public function ajax_list()
    {
        AuthMiddleware::handle();
        
        $page = (int) $this->getInput('page', 1);
        $limit = (int) $this->getInput('limit', 10);
        $offset = ($page - 1) * $limit;
        
        $profissionais = $this->profissionalModel->findAll($limit, $offset);
        $total = $this->profissionalModel->count();
        
        $this->jsonResponse([
            'success' => true,
            'data' => $profissionais,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }
    
    private function validateProfissional($data)
    {
        $errors = [];
        
        if (empty($data['nome'])) {
            $errors[] = 'Nome é obrigatório';
        }
        
        if (empty($data['cns']) || strlen($data['cns']) != 15) {
            $errors[] = 'CNS é obrigatório e deve ter 15 dígitos';
        }
        
        return $errors;
    }
}
