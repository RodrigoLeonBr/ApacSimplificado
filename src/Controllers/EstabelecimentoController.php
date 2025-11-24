<?php

namespace App\Controllers;

use App\Models\Estabelecimento;
use App\Middleware\AuthMiddleware;
use App\Services\ValidacaoService;

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
        
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 10;
        $total = $this->estabelecimentoModel->countTotal();
        $totalPages = max(1, ceil($total / $limit));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $limit;
        
        $estabelecimentos = $this->estabelecimentoModel->findPaginated($limit, $offset);
        
        $this->render('estabelecimentos/index', [
            'estabelecimentos' => $estabelecimentos,
            'totalEstabelecimentos' => $total,
            'currentPage' => $page,
            'totalPages' => $totalPages,
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
        
        $laudos = $this->estabelecimentoModel->getLaudos($id, 10);
        
        $this->render('estabelecimentos/show', [
            'estabelecimento' => $estabelecimento,
            'laudos' => $laudos,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function create()
    {
        AuthMiddleware::handle();
        
        $this->render('estabelecimentos/form', [
            'estabelecimento' => null,
            'action' => '/estabelecimentos',
            'method' => 'POST',
            'title' => 'Novo Estabelecimento',
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
        
        if ($this->estabelecimentoModel->findByCnpj($data['cnpj'])) {
            $this->flash('CNPJ já cadastrado no sistema', 'error');
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
        
        $this->render('estabelecimentos/form', [
            'estabelecimento' => $estabelecimento,
            'action' => '/estabelecimentos/' . $id . '/update',
            'method' => 'POST',
            'title' => 'Editar Estabelecimento',
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
        
        $existing = $this->estabelecimentoModel->findByCnpj($data['cnpj']);
        if ($existing && $existing['id'] != $id) {
            $this->flash('CNPJ já cadastrado para outro estabelecimento', 'error');
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
            return;
        }
        
        try {
            $deleted = $this->estabelecimentoModel->delete($id);
            
            if ($deleted) {
                $this->jsonResponse(['success' => true, 'message' => 'Estabelecimento excluído com sucesso']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Erro ao excluir estabelecimento'], 500);
            }
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Erro ao excluir estabelecimento'], 500);
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
            $estabelecimentos = $this->estabelecimentoModel->findPaginated($limit, $offset);
            $total = $this->estabelecimentoModel->countTotal();
        } else {
            $estabelecimentos = $this->estabelecimentoModel->searchPaginated($q, $limit, $offset);
            $total = $this->estabelecimentoModel->searchCount($q);
        }
        
        $totalPages = max(1, ceil($total / $limit));
        $page = min($page, $totalPages);
        
        $this->jsonResponse([
            'success' => true,
            'estabelecimentos' => $estabelecimentos,
            'total' => $total,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ]);
    }
    
    public function ajax_list()
    {
        AuthMiddleware::handle();
        
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $estabelecimentos = $this->estabelecimentoModel->findPaginated($limit, $offset);
        $total = $this->estabelecimentoModel->countTotal();
        
        $this->jsonResponse([
            'success' => true,
            'estabelecimentos' => $estabelecimentos,
            'total' => $total,
            'totalPages' => ceil($total / $limit),
            'currentPage' => $page
        ]);
    }
    
    private function validateEstabelecimento($data)
    {
        $errors = [];
        
        if (empty($data['cnes']) || strlen($data['cnes']) != 7) {
            $errors[] = 'CNES é obrigatório e deve ter 7 dígitos';
        }
        
        if (empty($data['cnpj'])) {
            $errors[] = 'CNPJ é obrigatório';
        } elseif (!ValidacaoService::validarCnpj($data['cnpj'])) {
            $errors[] = 'CNPJ inválido';
        }
        
        if (empty($data['razao_social'])) {
            $errors[] = 'Razão Social é obrigatória';
        }
        
        if (empty($data['logradouro'])) {
            $errors[] = 'Logradouro é obrigatório';
        }
        
        if (empty($data['numero'])) {
            $errors[] = 'Número é obrigatório';
        }
        
        if (empty($data['bairro'])) {
            $errors[] = 'Bairro é obrigatório';
        }
        
        if (empty($data['cep']) || !ValidacaoService::validarCep($data['cep'])) {
            $errors[] = 'CEP inválido';
        }
        
        if (empty($data['municipio'])) {
            $errors[] = 'Município é obrigatório';
        }
        
        if (!empty($data['email']) && !ValidacaoService::validarEmail($data['email'])) {
            $errors[] = 'E-mail inválido';
        }
        
        return $errors;
    }
}
