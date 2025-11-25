<?php

namespace App\Controllers;

use App\Models\Profissional;
use App\Middleware\AuthMiddleware;
use App\Services\ValidacaoService;

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
        
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 10;
        $total = $this->profissionalModel->countTotal();
        $totalPages = max(1, ceil($total / $limit));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $limit;
        
        $profissionais = $this->profissionalModel->findPaginated($limit, $offset);
        
        $this->render('profissional/index', [
            'profissionais' => $profissionais,
            'totalProfissionais' => $total,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function show($id)
    {
        AuthMiddleware::handle();
        
        $profissional = $this->profissionalModel->findById($id);
        
        if (!$profissional) {
            $this->flash('Profissional não encontrado', 'error');
            $this->redirect('/profissional');
        }
        
        $laudos = $this->profissionalModel->getLaudos($id, 5);
        $totalLaudosMes = $this->profissionalModel->countLaudosPorMes($id, date('m'), date('Y'));
        $totalLaudosAno = $this->profissionalModel->countLaudosPorMes($id, null, date('Y'));
        
        $this->render('profissional/show', [
            'profissional' => $profissional,
            'laudos' => $laudos,
            'totalLaudosMes' => $totalLaudosMes,
            'totalLaudosAno' => $totalLaudosAno,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function create()
    {
        AuthMiddleware::handle();
        
        $this->render('profissional/form', [
            'profissional' => null,
            'action' => '/profissional',
            'method' => 'POST',
            'title' => 'Novo Profissional',
            'old' => [],
            'flash' => $this->getFlash()
        ]);
    }
    
    public function store()
    {
        AuthMiddleware::handle();
        
        $data = $this->getInput();
        $data = $this->prepareData($data);
        
        $errors = $this->validateProfissional($data);
        if (!empty($errors)) {
            \App\Utils\Session::flash('errors', $errors);
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/profissional/create');
        }
        
        if (!empty($data['cns']) && $this->profissionalModel->findByCns($data['cns'])) {
            $this->flash('CNS já cadastrado no sistema', 'error');
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/profissional/create');
        }
        
        if (!empty($data['cpf']) && $this->profissionalModel->findByCpf($data['cpf'])) {
            $this->flash('CPF já cadastrado no sistema', 'error');
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/profissional/create');
        }
        
        try {
            $id = $this->profissionalModel->create($data);
            
            if ($id) {
                $this->flash('Profissional cadastrado com sucesso', 'success');
                $this->redirect('/profissional/' . $id);
            } else {
                $this->flash('Erro ao cadastrar profissional', 'error');
                \App\Utils\Session::flash('old', $data);
                $this->redirect('/profissional/create');
            }
        } catch (\Exception $e) {
            error_log('Erro ao cadastrar profissional: ' . $e->getMessage());
            $this->flash('Erro ao cadastrar profissional: ' . $e->getMessage(), 'error');
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/profissional/create');
        }
    }
    
    public function edit($id)
    {
        AuthMiddleware::handle();
        
        $profissional = $this->profissionalModel->findById($id);
        
        if (!$profissional) {
            $this->flash('Profissional não encontrado', 'error');
            $this->redirect('/profissional');
        }
        
        $this->render('profissional/form', [
            'profissional' => $profissional,
            'action' => '/profissional/' . $id . '/update',
            'method' => 'POST',
            'title' => 'Editar Profissional',
            'old' => $profissional,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function update($id)
    {
        AuthMiddleware::handle();
        
        $profissional = $this->profissionalModel->findById($id);
        
        if (!$profissional) {
            $this->flash('Profissional não encontrado', 'error');
            $this->redirect('/profissional');
        }
        
        $data = $this->getInput();
        $data = $this->prepareData($data);
        
        $errors = $this->validateProfissional($data);
        if (!empty($errors)) {
            \App\Utils\Session::flash('errors', $errors);
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/profissional/' . $id . '/edit');
        }
        
        if (!empty($data['cns'])) {
            $existing = $this->profissionalModel->findByCns($data['cns']);
            if ($existing && $existing['id'] != $id) {
                $this->flash('CNS já cadastrado para outro profissional', 'error');
                \App\Utils\Session::flash('old', $data);
                $this->redirect('/profissional/' . $id . '/edit');
            }
        }
        
        if (!empty($data['cpf'])) {
            $existing = $this->profissionalModel->findByCpf($data['cpf']);
            if ($existing && $existing['id'] != $id) {
                $this->flash('CPF já cadastrado para outro profissional', 'error');
                \App\Utils\Session::flash('old', $data);
                $this->redirect('/profissional/' . $id . '/edit');
            }
        }
        
        try {
            $updated = $this->profissionalModel->update($id, $data);
            
            if ($updated) {
                $this->flash('Profissional atualizado com sucesso', 'success');
                $this->redirect('/profissional/' . $id);
            } else {
                $this->flash('Erro ao atualizar profissional', 'error');
                \App\Utils\Session::flash('old', $data);
                $this->redirect('/profissional/' . $id . '/edit');
            }
        } catch (\Exception $e) {
            error_log('Erro ao atualizar profissional: ' . $e->getMessage());
            $this->flash('Erro ao atualizar profissional: ' . $e->getMessage(), 'error');
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/profissional/' . $id . '/edit');
        }
    }
    
    public function delete($id)
    {
        AuthMiddleware::handle();
        
        $profissional = $this->profissionalModel->findById($id);
        
        if (!$profissional) {
            $this->jsonResponse(['success' => false, 'message' => 'Profissional não encontrado'], 404);
            return;
        }
        
        try {
            $deleted = $this->profissionalModel->delete($id);
            
            if ($deleted) {
                $this->jsonResponse(['success' => true, 'message' => 'Profissional excluído com sucesso']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Erro ao excluir profissional'], 500);
            }
        } catch (\Exception $e) {
            error_log('Erro ao excluir profissional: ' . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Erro ao excluir profissional'], 500);
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
            $profissionais = $this->profissionalModel->findPaginated($limit, $offset);
            $total = $this->profissionalModel->countTotal();
        } else {
            $profissionais = $this->profissionalModel->searchPaginated($q, $limit, $offset);
            $total = $this->profissionalModel->searchCount($q);
        }
        
        $totalPages = max(1, ceil($total / $limit));
        $page = min($page, $totalPages);
        
        $this->jsonResponse([
            'success' => true,
            'profissionais' => $profissionais,
            'total' => $total,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ]);
    }
    
    private function prepareData($data)
    {
        // Limpar CNS (remover caracteres não numéricos)
        if (!empty($data['cns'])) {
            $data['cns'] = preg_replace('/[^0-9]/', '', $data['cns']);
        } else {
            $data['cns'] = null;
        }
        
        // Limpar CPF (remover caracteres não numéricos)
        if (!empty($data['cpf'])) {
            $data['cpf'] = preg_replace('/[^0-9]/', '', $data['cpf']);
        } else {
            $data['cpf'] = null;
        }
        
        // Limpar telefone (remover caracteres não numéricos)
        if (!empty($data['telefone'])) {
            $data['telefone'] = preg_replace('/[^0-9]/', '', $data['telefone']);
        } else {
            $data['telefone'] = null;
        }
        
        // Tratar campos opcionais
        $optionalFields = ['matricula', 'email', 'especialidade', 'uf', 'municipio'];
        foreach ($optionalFields as $field) {
            if (isset($data[$field]) && empty($data[$field])) {
                $data[$field] = null;
            }
        }
        
        // Status padrão
        if (empty($data['status'])) {
            $data['status'] = 'ativo';
        }
        
        return $data;
    }
    
    private function validateProfissional($data)
    {
        $errors = [];
        
        if (empty($data['nome'])) {
            $errors['nome'] = 'Nome é obrigatório';
        }
        
        if (!empty($data['cns'])) {
            $cnsLimpo = preg_replace('/[^0-9]/', '', $data['cns']);
            if (strlen($cnsLimpo) != 15) {
                $errors['cns'] = 'CNS deve ter 15 dígitos';
            } elseif (!ValidacaoService::validarCns($cnsLimpo)) {
                $errors['cns'] = 'CNS inválido';
            }
        }
        
        if (!empty($data['cpf'])) {
            $cpfLimpo = preg_replace('/[^0-9]/', '', $data['cpf']);
            if (strlen($cpfLimpo) != 11) {
                $errors['cpf'] = 'CPF deve ter 11 dígitos';
            } elseif (!ValidacaoService::validarCpf($cpfLimpo)) {
                $errors['cpf'] = 'CPF inválido';
            }
        }
        
        if (!empty($data['email']) && !ValidacaoService::validarEmail($data['email'])) {
            $errors['email'] = 'E-mail inválido';
        }
        
        return $errors;
    }
}
