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
        
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 10;
        $total = $this->procedimentoModel->countTotal();
        $totalPages = max(1, ceil($total / $limit));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $limit;
        
        $procedimentos = $this->procedimentoModel->findPaginated($limit, $offset);
        
        $this->render('procedimento/index', [
            'procedimentos' => $procedimentos,
            'totalProcedimentos' => $total,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function show($id)
    {
        AuthMiddleware::handle();
        
        $procedimento = $this->procedimentoModel->findById($id);
        
        if (!$procedimento) {
            $this->flash('Procedimento não encontrado', 'error');
            $this->redirect('/procedimento');
        }
        
        $this->render('procedimento/show', [
            'procedimento' => $procedimento,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function create()
    {
        AuthMiddleware::handle();
        
        $this->render('procedimento/form', [
            'procedimento' => null,
            'action' => '/procedimento',
            'method' => 'POST',
            'title' => 'Novo Procedimento',
            'old' => [],
            'flash' => $this->getFlash()
        ]);
    }
    
    public function store()
    {
        AuthMiddleware::handle();
        
        $data = $this->getInput();
        $errors = $this->validateProcedimento($data);
        
        if (!empty($errors)) {
            \App\Utils\Session::flash('errors', $errors);
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/procedimento/create');
        }
        
        if ($this->procedimentoModel->findByCodigo($data['codigo_procedimento'])) {
            $this->flash('Código do procedimento já cadastrado no sistema', 'error');
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/procedimento/create');
        }
        
        try {
            $id = $this->procedimentoModel->create($data);
            
            if ($id) {
                $this->flash('Procedimento cadastrado com sucesso', 'success');
                $this->redirect('/procedimento/' . $id);
            } else {
                $this->flash('Erro ao cadastrar procedimento', 'error');
                \App\Utils\Session::flash('old', $data);
                $this->redirect('/procedimento/create');
            }
        } catch (\Exception $e) {
            error_log('Erro ao cadastrar procedimento: ' . $e->getMessage());
            $this->flash('Erro ao cadastrar procedimento: ' . $e->getMessage(), 'error');
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/procedimento/create');
        }
    }
    
    public function edit($id)
    {
        AuthMiddleware::handle();
        
        $procedimento = $this->procedimentoModel->findById($id);
        
        if (!$procedimento) {
            $this->flash('Procedimento não encontrado', 'error');
            $this->redirect('/procedimento');
        }
        
        $this->render('procedimento/form', [
            'procedimento' => $procedimento,
            'action' => '/procedimento/' . $id . '/update',
            'method' => 'POST',
            'title' => 'Editar Procedimento',
            'old' => $procedimento,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function update($id)
    {
        AuthMiddleware::handle();
        
        $procedimento = $this->procedimentoModel->findById($id);
        
        if (!$procedimento) {
            $this->flash('Procedimento não encontrado', 'error');
            $this->redirect('/procedimento');
        }
        
        $data = $this->getInput();
        $errors = $this->validateProcedimento($data);
        
        if (!empty($errors)) {
            \App\Utils\Session::flash('errors', $errors);
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/procedimento/' . $id . '/edit');
        }
        
        $existing = $this->procedimentoModel->findByCodigo($data['codigo_procedimento']);
        if ($existing && $existing['id'] != $id) {
            $this->flash('Código do procedimento já cadastrado para outro procedimento', 'error');
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/procedimento/' . $id . '/edit');
        }
        
        try {
            $updated = $this->procedimentoModel->update($id, $data);
            
            if ($updated) {
                $this->flash('Procedimento atualizado com sucesso', 'success');
                $this->redirect('/procedimento/' . $id);
            } else {
                $this->flash('Erro ao atualizar procedimento', 'error');
                \App\Utils\Session::flash('old', $data);
                $this->redirect('/procedimento/' . $id . '/edit');
            }
        } catch (\Exception $e) {
            error_log('Erro ao atualizar procedimento: ' . $e->getMessage());
            $this->flash('Erro ao atualizar procedimento: ' . $e->getMessage(), 'error');
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/procedimento/' . $id . '/edit');
        }
    }
    
    public function delete($id)
    {
        AuthMiddleware::handle();
        
        $procedimento = $this->procedimentoModel->findById($id);
        
        if (!$procedimento) {
            $this->jsonResponse(['success' => false, 'message' => 'Procedimento não encontrado'], 404);
            return;
        }
        
        try {
            $deleted = $this->procedimentoModel->delete($id);
            
            if ($deleted) {
                $this->jsonResponse(['success' => true, 'message' => 'Procedimento excluído com sucesso']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Erro ao excluir procedimento'], 500);
            }
        } catch (\Exception $e) {
            error_log('Erro ao excluir procedimento: ' . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Erro ao excluir procedimento'], 500);
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
            $procedimentos = $this->procedimentoModel->findPaginated($limit, $offset);
            $total = $this->procedimentoModel->countTotal();
        } else {
            $procedimentos = $this->procedimentoModel->searchPaginated($q, $limit, $offset);
            $total = $this->procedimentoModel->searchCount($q);
        }
        
        $totalPages = max(1, ceil($total / $limit));
        $page = min($page, $totalPages);
        
        $this->jsonResponse([
            'success' => true,
            'procedimentos' => $procedimentos,
            'total' => $total,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ]);
    }
    
    /**
     * Lista relacionamentos (CIDs) de um procedimento
     */
    public function relacionamentos($id)
    {
        AuthMiddleware::handle();
        
        $procedimento = $this->procedimentoModel->findById($id);
        
        if (!$procedimento) {
            $this->flash('Procedimento não encontrado', 'error');
            $this->redirect('/procedimento');
        }
        
        $relacionamentos = $this->procedimentoModel->findRelacionamentosCid($id);
        
        $this->render('procedimento/relacionamentos', [
            'procedimento' => $procedimento,
            'relacionamentos' => $relacionamentos,
            'flash' => $this->getFlash()
        ]);
    }
    
    private function validateProcedimento($data)
    {
        $errors = [];
        
        if (empty($data['codigo_procedimento'])) {
            $errors['codigo_procedimento'] = 'Código do procedimento é obrigatório';
        } elseif (strlen($data['codigo_procedimento']) > 10) {
            $errors['codigo_procedimento'] = 'Código do procedimento deve ter no máximo 10 caracteres';
        }
        
        if (empty($data['descricao'])) {
            $errors['descricao'] = 'Descrição é obrigatória';
        }
        
        // Validações SIGTAP
        if (isset($data['tp_complexidade']) && $data['tp_complexidade'] !== '') {
            $complexidadesValidas = ['0', '1', '2', '3'];
            if (!in_array($data['tp_complexidade'], $complexidadesValidas)) {
                $errors['tp_complexidade'] = 'Tipo de complexidade inválido. Use: 0, 1, 2 ou 3';
            }
        }
        
        if (isset($data['tp_sexo']) && $data['tp_sexo'] !== '') {
            $sexosValidos = ['M', 'F', 'I', 'N'];
            if (!in_array($data['tp_sexo'], $sexosValidos)) {
                $errors['tp_sexo'] = 'Tipo de sexo inválido. Use: M, F, I ou N';
            }
        }
        
        if (isset($data['qt_maxima_execucao']) && $data['qt_maxima_execucao'] !== '') {
            $qtMaxima = (int)$data['qt_maxima_execucao'];
            if ($qtMaxima < 0) {
                $errors['qt_maxima_execucao'] = 'Quantidade máxima de execução deve ser um número positivo';
            }
        }
        
        // Validações de valores monetários
        $camposMonetarios = ['vl_sh', 'vl_sa', 'vl_sp'];
        foreach ($camposMonetarios as $campo) {
            if (isset($data[$campo]) && $data[$campo] !== '') {
                $valor = (float)$data[$campo];
                if ($valor < 0) {
                    $errors[$campo] = 'Valor não pode ser negativo';
                }
            }
        }
        
        // Validação de dt_competencia (formato YYYYMM)
        if (isset($data['dt_competencia']) && $data['dt_competencia'] !== '') {
            if (!preg_match('/^\d{6}$/', $data['dt_competencia'])) {
                $errors['dt_competencia'] = 'Data de competência deve estar no formato YYYYMM (ex: 202511)';
            } else {
                $ano = substr($data['dt_competencia'], 0, 4);
                $mes = substr($data['dt_competencia'], 4, 2);
                if ($ano < 2000 || $ano > 2100 || $mes < 1 || $mes > 12) {
                    $errors['dt_competencia'] = 'Data de competência inválida';
                }
            }
        }
        
        return $errors;
    }
}
