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
        
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 10;
        $total = $this->cidModel->countTotal();
        $totalPages = max(1, ceil($total / $limit));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $limit;
        
        $cids = $this->cidModel->findPaginated($limit, $offset);
        
        $this->render('cid/index', [
            'cids' => $cids,
            'totalCids' => $total,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function show($id)
    {
        AuthMiddleware::handle();
        
        $cid = $this->cidModel->findById($id);
        
        if (!$cid) {
            $this->flash('CID não encontrado', 'error');
            $this->redirect('/cid');
        }
        
        $this->render('cid/show', [
            'cid' => $cid,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function create()
    {
        AuthMiddleware::handle();
        
        $this->render('cid/form', [
            'cid' => null,
            'action' => '/cid',
            'method' => 'POST',
            'title' => 'Novo CID',
            'old' => [],
            'flash' => $this->getFlash()
        ]);
    }
    
    public function store()
    {
        AuthMiddleware::handle();
        
        $data = $this->getInput();
        $errors = $this->validateCid($data);
        
        if (!empty($errors)) {
            \App\Utils\Session::flash('errors', $errors);
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/cid/create');
        }
        
        if ($this->cidModel->findByCodigo($data['codigo'])) {
            $this->flash('Código CID já cadastrado no sistema', 'error');
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/cid/create');
        }
        
        try {
            $id = $this->cidModel->create($data);
            
            if ($id) {
                $this->flash('CID cadastrado com sucesso', 'success');
                $this->redirect('/cid/' . $id);
            } else {
                $this->flash('Erro ao cadastrar CID', 'error');
                \App\Utils\Session::flash('old', $data);
                $this->redirect('/cid/create');
            }
        } catch (\Exception $e) {
            error_log('Erro ao cadastrar CID: ' . $e->getMessage());
            $this->flash('Erro ao cadastrar CID: ' . $e->getMessage(), 'error');
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/cid/create');
        }
    }
    
    public function edit($id)
    {
        AuthMiddleware::handle();
        
        $cid = $this->cidModel->findById($id);
        
        if (!$cid) {
            $this->flash('CID não encontrado', 'error');
            $this->redirect('/cid');
        }
        
        $this->render('cid/form', [
            'cid' => $cid,
            'action' => '/cid/' . $id . '/update',
            'method' => 'POST',
            'title' => 'Editar CID',
            'old' => $cid,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function update($id)
    {
        AuthMiddleware::handle();
        
        $cid = $this->cidModel->findById($id);
        
        if (!$cid) {
            $this->flash('CID não encontrado', 'error');
            $this->redirect('/cid');
        }
        
        $data = $this->getInput();
        $errors = $this->validateCid($data);
        
        if (!empty($errors)) {
            \App\Utils\Session::flash('errors', $errors);
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/cid/' . $id . '/edit');
        }
        
        $existing = $this->cidModel->findByCodigo($data['codigo']);
        if ($existing && $existing['id'] != $id) {
            $this->flash('Código CID já cadastrado para outro CID', 'error');
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/cid/' . $id . '/edit');
        }
        
        try {
            $updated = $this->cidModel->update($id, $data);
            
            if ($updated) {
                $this->flash('CID atualizado com sucesso', 'success');
                $this->redirect('/cid/' . $id);
            } else {
                $this->flash('Erro ao atualizar CID', 'error');
                \App\Utils\Session::flash('old', $data);
                $this->redirect('/cid/' . $id . '/edit');
            }
        } catch (\Exception $e) {
            error_log('Erro ao atualizar CID: ' . $e->getMessage());
            $this->flash('Erro ao atualizar CID: ' . $e->getMessage(), 'error');
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/cid/' . $id . '/edit');
        }
    }
    
    public function delete($id)
    {
        AuthMiddleware::handle();
        
        $cid = $this->cidModel->findById($id);
        
        if (!$cid) {
            $this->jsonResponse(['success' => false, 'message' => 'CID não encontrado'], 404);
            return;
        }
        
        try {
            $deleted = $this->cidModel->delete($id);
            
            if ($deleted) {
                $this->jsonResponse(['success' => true, 'message' => 'CID excluído com sucesso']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Erro ao excluir CID'], 500);
            }
        } catch (\Exception $e) {
            error_log('Erro ao excluir CID: ' . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Erro ao excluir CID'], 500);
        }
    }
    
    public function ajax_search()
    {
        AuthMiddleware::handle();
        
        $q = $_GET['q'] ?? '';
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = max(10, min(100, (int) ($_GET['limit'] ?? 10))); // Aceita 10, 20, 50 ou 100
        $offset = ($page - 1) * $limit;
        
        if (strlen($q) === 0) {
            $cids = $this->cidModel->findPaginated($limit, $offset);
            $total = $this->cidModel->countTotal();
        } else {
            $cids = $this->cidModel->searchPaginated($q, $limit, $offset);
            $total = $this->cidModel->searchCount($q);
        }
        
        $totalPages = max(1, ceil($total / $limit));
        $page = min($page, $totalPages);
        
        $this->jsonResponse([
            'success' => true,
            'cids' => $cids,
            'total' => $total,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ]);
    }
    
    /**
     * Lista relacionamentos (Procedimentos) de um CID
     */
    public function relacionamentos($id)
    {
        AuthMiddleware::handle();
        
        $cid = $this->cidModel->findById($id);
        
        if (!$cid) {
            $this->flash('CID não encontrado', 'error');
            $this->redirect('/cid');
        }
        
        $relacionamentos = $this->cidModel->findRelacionamentosProcedimento($id);
        
        $this->render('cid/relacionamentos', [
            'cid' => $cid,
            'relacionamentos' => $relacionamentos,
            'flash' => $this->getFlash()
        ]);
    }
    
    private function validateCid($data)
    {
        $errors = [];
        
        if (empty($data['codigo'])) {
            $errors['codigo'] = 'Código é obrigatório';
        } elseif (strlen($data['codigo']) > 10) {
            $errors['codigo'] = 'Código deve ter no máximo 10 caracteres';
        }
        
        if (empty($data['descricao'])) {
            $errors['descricao'] = 'Descrição é obrigatória';
        }
        
        // Validações SIGTAP
        if (isset($data['tp_agravo']) && $data['tp_agravo'] !== '') {
            $agravosValidos = ['0', '1', '2'];
            if (!in_array($data['tp_agravo'], $agravosValidos)) {
                $errors['tp_agravo'] = 'Tipo de agravo inválido. Use: 0, 1 ou 2';
            }
        }
        
        if (isset($data['tp_sexo']) && $data['tp_sexo'] !== '') {
            $sexosValidos = ['M', 'F', 'I'];
            if (!in_array($data['tp_sexo'], $sexosValidos)) {
                $errors['tp_sexo'] = 'Tipo de sexo inválido. Use: M, F ou I';
            }
        }
        
        if (isset($data['tp_estadio']) && $data['tp_estadio'] !== '') {
            $estadiosValidos = ['S', 'N'];
            if (!in_array($data['tp_estadio'], $estadiosValidos)) {
                $errors['tp_estadio'] = 'Tipo de estádio inválido. Use: S ou N';
            }
        }
        
        if (isset($data['vl_campos_irradiados']) && $data['vl_campos_irradiados'] !== '') {
            $vlCampos = (int)$data['vl_campos_irradiados'];
            if ($vlCampos < 0) {
                $errors['vl_campos_irradiados'] = 'Valor de campos irradiados deve ser um número positivo';
            }
        }
        
        return $errors;
    }
}
