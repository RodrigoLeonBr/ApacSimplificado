<?php

namespace App\Controllers;

use App\Models\RelacionamentoProcedimentoCid;
use App\Models\Procedimento;
use App\Models\Cid;
use App\Middleware\AuthMiddleware;

class RelacionamentoProcedimentoCidController extends BaseController
{
    private $relacionamentoModel;
    private $procedimentoModel;
    private $cidModel;
    
    public function __construct($db = null)
    {
        parent::__construct($db);
        $this->relacionamentoModel = new RelacionamentoProcedimentoCid();
        $this->procedimentoModel = new Procedimento();
        $this->cidModel = new Cid();
    }
    
    /**
     * Lista paginada de relacionamentos
     */
    public function index()
    {
        AuthMiddleware::handle();
        
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 10;
        $search = $_GET['search'] ?? '';
        $filtroProcedimento = $_GET['procedimento_id'] ?? '';
        $filtroCid = $_GET['cid_id'] ?? '';
        $filtroPrincipal = $_GET['st_principal'] ?? '';
        
        $filtros = [];
        if (!empty($filtroProcedimento)) {
            $filtros['procedimento_id'] = $filtroProcedimento;
        }
        if (!empty($filtroCid)) {
            $filtros['cid_id'] = $filtroCid;
        }
        if ($filtroPrincipal !== '') {
            $filtros['st_principal'] = $filtroPrincipal;
        }
        
        if (!empty($search)) {
            $relacionamentos = $this->relacionamentoModel->searchPaginated($search, $limit, ($page - 1) * $limit);
            $total = $this->relacionamentoModel->searchCount($search);
        } else {
            $relacionamentos = $this->relacionamentoModel->findPaginated($limit, ($page - 1) * $limit, $filtros);
            $total = $this->relacionamentoModel->countWithFilters($filtros);
        }
        
        $totalPages = max(1, ceil($total / $limit));
        $page = min($page, $totalPages);
        
        // Buscar listas para filtros
        $procedimentos = $this->procedimentoModel->findPaginated(100, 0);
        $cids = $this->cidModel->findPaginated(100, 0);
        
        $this->render('relacionamento/index', [
            'relacionamentos' => $relacionamentos,
            'totalRelacionamentos' => $total,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'filtroProcedimento' => $filtroProcedimento,
            'filtroCid' => $filtroCid,
            'filtroPrincipal' => $filtroPrincipal,
            'procedimentos' => $procedimentos,
            'cids' => $cids,
            'flash' => $this->getFlash()
        ]);
    }
    
    /**
     * Visualiza detalhes de um relacionamento
     */
    public function show($id)
    {
        AuthMiddleware::handle();
        
        $relacionamento = $this->relacionamentoModel->findById($id);
        
        if (!$relacionamento) {
            $this->flash('Relacionamento não encontrado', 'error');
            $this->redirect('/relacionamento');
        }
        
        $this->render('relacionamento/show', [
            'relacionamento' => $relacionamento,
            'flash' => $this->getFlash()
        ]);
    }
    
    /**
     * Exibe formulário de criação
     */
    public function create()
    {
        AuthMiddleware::handle();
        
        $procedimentoId = $_GET['procedimento_id'] ?? null;
        $cidId = $_GET['cid_id'] ?? null;
        
        $procedimento = null;
        $cid = null;
        
        if ($procedimentoId) {
            $procedimento = $this->procedimentoModel->findById($procedimentoId);
        }
        
        if ($cidId) {
            $cid = $this->cidModel->findById($cidId);
        }
        
        $this->render('relacionamento/form', [
            'relacionamento' => null,
            'procedimento' => $procedimento,
            'cid' => $cid,
            'action' => '/relacionamento',
            'method' => 'POST',
            'title' => 'Novo Relacionamento',
            'old' => [],
            'flash' => $this->getFlash()
        ]);
    }
    
    /**
     * Cria novo relacionamento
     */
    public function store()
    {
        AuthMiddleware::handle();
        
        $data = $this->getInput();
        $errors = $this->validateRelacionamento($data);
        
        if (!empty($errors)) {
            \App\Utils\Session::flash('errors', $errors);
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/relacionamento/create');
        }
        
        // Verificar se procedimento existe
        $procedimento = $this->procedimentoModel->findByCodigo($data['co_procedimento']);
        if (!$procedimento) {
            $this->flash('Procedimento não encontrado', 'error');
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/relacionamento/create');
        }
        
        // Verificar se CID existe
        $cid = $this->cidModel->findByCodigo($data['co_cid']);
        if (!$cid) {
            $this->flash('CID não encontrado', 'error');
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/relacionamento/create');
        }
        
        // Verificar se relacionamento já existe
        $existente = $this->relacionamentoModel->findByProcedimentoECid(
            $data['co_procedimento'],
            $data['co_cid']
        );
        
        if ($existente) {
            $this->flash('Relacionamento já existe para este procedimento e CID', 'error');
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/relacionamento/create');
        }
        
        try {
            $id = $this->relacionamentoModel->create($data);
            
            if ($id) {
                $this->flash('Relacionamento criado com sucesso', 'success');
                $this->redirect('/relacionamento/' . $id);
            } else {
                $this->flash('Erro ao criar relacionamento', 'error');
                \App\Utils\Session::flash('old', $data);
                $this->redirect('/relacionamento/create');
            }
        } catch (\Exception $e) {
            error_log('Erro ao criar relacionamento: ' . $e->getMessage());
            $this->flash('Erro ao criar relacionamento: ' . $e->getMessage(), 'error');
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/relacionamento/create');
        }
    }
    
    /**
     * Exibe formulário de edição
     */
    public function edit($id)
    {
        AuthMiddleware::handle();
        
        $relacionamento = $this->relacionamentoModel->findById($id);
        
        if (!$relacionamento) {
            $this->flash('Relacionamento não encontrado', 'error');
            $this->redirect('/relacionamento');
        }
        
        $procedimento = $this->procedimentoModel->findByCodigo($relacionamento['co_procedimento']);
        $cid = $this->cidModel->findByCodigo($relacionamento['co_cid']);
        
        $this->render('relacionamento/form', [
            'relacionamento' => $relacionamento,
            'procedimento' => $procedimento,
            'cid' => $cid,
            'action' => '/relacionamento/' . $id . '/update',
            'method' => 'POST',
            'title' => 'Editar Relacionamento',
            'old' => $relacionamento,
            'flash' => $this->getFlash()
        ]);
    }
    
    /**
     * Atualiza relacionamento
     */
    public function update($id)
    {
        AuthMiddleware::handle();
        
        $relacionamento = $this->relacionamentoModel->findById($id);
        
        if (!$relacionamento) {
            $this->flash('Relacionamento não encontrado', 'error');
            $this->redirect('/relacionamento');
        }
        
        $data = $this->getInput();
        $errors = $this->validateRelacionamento($data);
        
        if (!empty($errors)) {
            \App\Utils\Session::flash('errors', $errors);
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/relacionamento/' . $id . '/edit');
        }
        
        // Verificar se procedimento existe
        $procedimento = $this->procedimentoModel->findByCodigo($data['co_procedimento']);
        if (!$procedimento) {
            $this->flash('Procedimento não encontrado', 'error');
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/relacionamento/' . $id . '/edit');
        }
        
        // Verificar se CID existe
        $cid = $this->cidModel->findByCodigo($data['co_cid']);
        if (!$cid) {
            $this->flash('CID não encontrado', 'error');
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/relacionamento/' . $id . '/edit');
        }
        
        // Verificar se relacionamento duplicado (exceto o atual)
        $existente = $this->relacionamentoModel->findByProcedimentoECid(
            $data['co_procedimento'],
            $data['co_cid']
        );
        
        // Decodificar ID atual para comparar
        $chavesAtuais = \App\Models\RelacionamentoProcedimentoCid::decodificarChave($id);
        
        if ($existente && 
            ($existente['co_procedimento'] !== $chavesAtuais['co_procedimento'] || 
             $existente['co_cid'] !== $chavesAtuais['co_cid'])) {
            $this->flash('Relacionamento já existe para este procedimento e CID', 'error');
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/relacionamento/' . $id . '/edit');
        }
        
        try {
            $resultado = $this->relacionamentoModel->update($id, $data);
            
            if ($resultado) {
                $this->flash('Relacionamento atualizado com sucesso', 'success');
                // Se as chaves mudaram, result pode ser uma nova chave codificada
                $novaChave = is_string($resultado) ? $resultado : $id;
                $this->redirect('/relacionamento/' . $novaChave);
            } else {
                $this->flash('Erro ao atualizar relacionamento', 'error');
                \App\Utils\Session::flash('old', $data);
                $this->redirect('/relacionamento/' . $id . '/edit');
            }
        } catch (\Exception $e) {
            error_log('Erro ao atualizar relacionamento: ' . $e->getMessage());
            $this->flash('Erro ao atualizar relacionamento: ' . $e->getMessage(), 'error');
            \App\Utils\Session::flash('old', $data);
            $this->redirect('/relacionamento/' . $id . '/edit');
        }
    }
    
    /**
     * Exclui relacionamento
     */
    public function delete($id)
    {
        AuthMiddleware::handle();
        
        $relacionamento = $this->relacionamentoModel->findById($id);
        
        if (!$relacionamento) {
            $this->jsonResponse(['success' => false, 'message' => 'Relacionamento não encontrado'], 404);
            return;
        }
        
        try {
            $deleted = $this->relacionamentoModel->delete($id);
            
            if ($deleted) {
                $this->jsonResponse(['success' => true, 'message' => 'Relacionamento excluído com sucesso']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Erro ao excluir relacionamento'], 500);
            }
        } catch (\Exception $e) {
            error_log('Erro ao excluir relacionamento: ' . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Erro ao excluir relacionamento'], 500);
        }
    }
    
    /**
     * Busca AJAX para relacionamentos
     */
    public function ajax_search()
    {
        AuthMiddleware::handle();
        
        $q = $_GET['q'] ?? '';
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        if (strlen($q) === 0) {
            $relacionamentos = $this->relacionamentoModel->findPaginated($limit, $offset);
            $total = $this->relacionamentoModel->countTotal();
        } else {
            $relacionamentos = $this->relacionamentoModel->searchPaginated($q, $limit, $offset);
            $total = $this->relacionamentoModel->searchCount($q);
        }
        
        $totalPages = max(1, ceil($total / $limit));
        $page = min($page, $totalPages);
        
        $this->jsonResponse([
            'success' => true,
            'relacionamentos' => $relacionamentos,
            'total' => $total,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ]);
    }
    
    /**
     * Valida dados do relacionamento
     */
    private function validateRelacionamento($data)
    {
        $errors = [];
        
        if (empty($data['co_procedimento'])) {
            $errors['co_procedimento'] = 'Código do procedimento é obrigatório';
        } elseif (strlen($data['co_procedimento']) > 10) {
            $errors['co_procedimento'] = 'Código do procedimento deve ter no máximo 10 caracteres';
        }
        
        if (empty($data['co_cid'])) {
            $errors['co_cid'] = 'Código do CID é obrigatório';
        } elseif (strlen($data['co_cid']) > 4) {
            $errors['co_cid'] = 'Código do CID deve ter no máximo 4 caracteres';
        }
        
        if (isset($data['st_principal']) && $data['st_principal'] !== '') {
            $principaisValidos = ['S', 'N'];
            if (!in_array($data['st_principal'], $principaisValidos)) {
                $errors['st_principal'] = 'Status principal inválido. Use: S ou N';
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

