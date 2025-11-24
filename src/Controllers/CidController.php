<?php

namespace App\Controllers;

use App\Models\Cid;
use App\Middleware\AuthMiddleware;

class CidController extends BaseController
{
    private $cidModel;
    
    public function __construct($db = null)
    {
        parent::__construct($db);
        $this->cidModel = new Cid();
    }
    
    public function index()
    {
        AuthMiddleware::handle();
        
        $cids = $this->cidModel->findAll();
        
        $this->render('cids.index', [
            'cids' => $cids,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function show($id)
    {
        AuthMiddleware::handle();
        
        $cid = $this->cidModel->findById($id);
        
        if (!$cid) {
            $this->flash('CID não encontrado', 'error');
            $this->redirect('/cids');
        }
        
        $this->render('cids.show', [
            'cid' => $cid,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function create()
    {
        AuthMiddleware::handle();
        
        $this->render('cids.create', [
            'flash' => $this->getFlash()
        ]);
    }
    
    public function store()
    {
        AuthMiddleware::handle();
        
        $data = $this->getInput();
        
        $errors = $this->validateCid($data);
        if (!empty($errors)) {
            $this->flash(implode(', ', $errors), 'error');
            $this->redirect('/cids/create');
        }
        
        if ($this->cidModel->findByCodigo($data['codigo'])) {
            $this->flash('Código CID já cadastrado no sistema', 'error');
            $this->redirect('/cids/create');
        }
        
        $id = $this->cidModel->create($data);
        
        if ($id) {
            $this->flash('CID cadastrado com sucesso', 'success');
            $this->redirect('/cids/' . $id);
        } else {
            $this->flash('Erro ao cadastrar CID', 'error');
            $this->redirect('/cids/create');
        }
    }
    
    public function edit($id)
    {
        AuthMiddleware::handle();
        
        $cid = $this->cidModel->findById($id);
        
        if (!$cid) {
            $this->flash('CID não encontrado', 'error');
            $this->redirect('/cids');
        }
        
        $this->render('cids.edit', [
            'cid' => $cid,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function update($id)
    {
        AuthMiddleware::handle();
        
        $cid = $this->cidModel->findById($id);
        
        if (!$cid) {
            $this->flash('CID não encontrado', 'error');
            $this->redirect('/cids');
        }
        
        $data = $this->getInput();
        
        $errors = $this->validateCid($data);
        if (!empty($errors)) {
            $this->flash(implode(', ', $errors), 'error');
            $this->redirect('/cids/' . $id . '/edit');
        }
        
        $existing = $this->cidModel->findByCodigo($data['codigo']);
        if ($existing && $existing['id'] != $id) {
            $this->flash('Código CID já cadastrado', 'error');
            $this->redirect('/cids/' . $id . '/edit');
        }
        
        $updated = $this->cidModel->update($id, $data);
        
        if ($updated) {
            $this->flash('CID atualizado com sucesso', 'success');
            $this->redirect('/cids/' . $id);
        } else {
            $this->flash('Erro ao atualizar CID', 'error');
            $this->redirect('/cids/' . $id . '/edit');
        }
    }
    
    public function delete($id)
    {
        AuthMiddleware::handle();
        
        $cid = $this->cidModel->findById($id);
        
        if (!$cid) {
            $this->jsonResponse(['success' => false, 'message' => 'CID não encontrado'], 404);
        }
        
        $deleted = $this->cidModel->delete($id);
        
        if ($deleted) {
            $this->flash('CID excluído com sucesso', 'success');
            $this->jsonResponse(['success' => true, 'message' => 'CID excluído com sucesso']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Erro ao excluir CID'], 500);
        }
    }
    
    public function ajax_search()
    {
        AuthMiddleware::handle();
        
        $termo = $this->getInput('termo', '');
        
        if (strlen($termo) < 2) {
            $this->jsonResponse(['success' => false, 'message' => 'Digite ao menos 2 caracteres']);
        }
        
        $cids = $this->cidModel->search($termo);
        
        $this->jsonResponse([
            'success' => true,
            'data' => $cids
        ]);
    }
    
    public function ajax_list()
    {
        AuthMiddleware::handle();
        
        $page = (int) $this->getInput('page', 1);
        $limit = (int) $this->getInput('limit', 10);
        $offset = ($page - 1) * $limit;
        
        $cids = $this->cidModel->findAll($limit, $offset);
        $total = $this->cidModel->count();
        
        $this->jsonResponse([
            'success' => true,
            'data' => $cids,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }
    
    private function validateCid($data)
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
