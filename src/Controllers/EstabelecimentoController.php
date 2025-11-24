<?php

namespace App\Controllers;

use App\Models\Estabelecimento;
use App\Middleware\AuthMiddleware;

class EstabelecimentoController extends BaseController
{
    private $estabelecimentoModel;
    
    public function __construct($db = null)
    {
        parent::__construct($db);
        $this->estabelecimentoModel = new Estabelecimento();
    }
    
    public function index()
    {
        AuthMiddleware::handle();
        
        $estabelecimentos = $this->estabelecimentoModel->findAll();
        
        $this->render('estabelecimentos.index', [
            'estabelecimentos' => $estabelecimentos,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function show($id)
    {
        AuthMiddleware::handle();
        
        $estabelecimento = $this->estabelecimentoModel->findById($id);
        
        if (!$estabelecimento) {
            $this->flash('Estabelecimento não encontrado', 'error');
            $this->redirect('/estabelecimentos');
        }
        
        $this->render('estabelecimentos.show', [
            'estabelecimento' => $estabelecimento,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function create()
    {
        AuthMiddleware::handle();
        
        $this->render('estabelecimentos.create', [
            'flash' => $this->getFlash()
        ]);
    }
    
    public function store()
    {
        AuthMiddleware::handle();
        
        $data = $this->getInput();
        
        $errors = $this->validateEstabelecimento($data);
        if (!empty($errors)) {
            $this->flash(implode(', ', $errors), 'error');
            $this->redirect('/estabelecimentos/create');
        }
        
        if ($this->estabelecimentoModel->findByCnes($data['cnes'])) {
            $this->flash('CNES já cadastrado no sistema', 'error');
            $this->redirect('/estabelecimentos/create');
        }
        
        $id = $this->estabelecimentoModel->create($data);
        
        if ($id) {
            $this->flash('Estabelecimento cadastrado com sucesso', 'success');
            $this->redirect('/estabelecimentos/' . $id);
        } else {
            $this->flash('Erro ao cadastrar estabelecimento', 'error');
            $this->redirect('/estabelecimentos/create');
        }
    }
    
    public function edit($id)
    {
        AuthMiddleware::handle();
        
        $estabelecimento = $this->estabelecimentoModel->findById($id);
        
        if (!$estabelecimento) {
            $this->flash('Estabelecimento não encontrado', 'error');
            $this->redirect('/estabelecimentos');
        }
        
        $this->render('estabelecimentos.edit', [
            'estabelecimento' => $estabelecimento,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function update($id)
    {
        AuthMiddleware::handle();
        
        $estabelecimento = $this->estabelecimentoModel->findById($id);
        
        if (!$estabelecimento) {
            $this->flash('Estabelecimento não encontrado', 'error');
            $this->redirect('/estabelecimentos');
        }
        
        $data = $this->getInput();
        
        $errors = $this->validateEstabelecimento($data);
        if (!empty($errors)) {
            $this->flash(implode(', ', $errors), 'error');
            $this->redirect('/estabelecimentos/' . $id . '/edit');
        }
        
        $existing = $this->estabelecimentoModel->findByCnes($data['cnes']);
        if ($existing && $existing['id'] != $id) {
            $this->flash('CNES já cadastrado para outro estabelecimento', 'error');
            $this->redirect('/estabelecimentos/' . $id . '/edit');
        }
        
        $updated = $this->estabelecimentoModel->update($id, $data);
        
        if ($updated) {
            $this->flash('Estabelecimento atualizado com sucesso', 'success');
            $this->redirect('/estabelecimentos/' . $id);
        } else {
            $this->flash('Erro ao atualizar estabelecimento', 'error');
            $this->redirect('/estabelecimentos/' . $id . '/edit');
        }
    }
    
    public function delete($id)
    {
        AuthMiddleware::handle();
        
        $estabelecimento = $this->estabelecimentoModel->findById($id);
        
        if (!$estabelecimento) {
            $this->jsonResponse(['success' => false, 'message' => 'Estabelecimento não encontrado'], 404);
        }
        
        $deleted = $this->estabelecimentoModel->delete($id);
        
        if ($deleted) {
            $this->flash('Estabelecimento excluído com sucesso', 'success');
            $this->jsonResponse(['success' => true, 'message' => 'Estabelecimento excluído com sucesso']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Erro ao excluir estabelecimento'], 500);
        }
    }
    
    public function ajax_search()
    {
        AuthMiddleware::handle();
        
        $termo = $this->getInput('termo', '');
        
        if (strlen($termo) < 2) {
            $this->jsonResponse(['success' => false, 'message' => 'Digite ao menos 2 caracteres']);
        }
        
        $estabelecimentos = $this->estabelecimentoModel->search($termo);
        
        $this->jsonResponse([
            'success' => true,
            'data' => $estabelecimentos
        ]);
    }
    
    public function ajax_list()
    {
        AuthMiddleware::handle();
        
        $page = (int) $this->getInput('page', 1);
        $limit = (int) $this->getInput('limit', 10);
        $offset = ($page - 1) * $limit;
        
        $estabelecimentos = $this->estabelecimentoModel->findAll($limit, $offset);
        $total = $this->estabelecimentoModel->count();
        
        $this->jsonResponse([
            'success' => true,
            'data' => $estabelecimentos,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }
    
    private function validateEstabelecimento($data)
    {
        $errors = [];
        
        if (empty($data['cnes']) || strlen($data['cnes']) != 7) {
            $errors[] = 'CNES é obrigatório e deve ter 7 dígitos';
        }
        
        if (empty($data['nome'])) {
            $errors[] = 'Nome é obrigatório';
        }
        
        return $errors;
    }
}
