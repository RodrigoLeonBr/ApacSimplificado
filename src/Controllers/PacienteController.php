<?php

namespace App\Controllers;

use App\Models\Paciente;
use App\Middleware\AuthMiddleware;

class PacienteController extends BaseController
{
    private $pacienteModel;
    
    public function __construct($db = null)
    {
        parent::__construct($db);
        $this->pacienteModel = new Paciente();
    }
    
    public function index()
    {
        AuthMiddleware::handle();
        
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 10;
        $total = $this->pacienteModel->countTotal();
        $totalPages = max(1, ceil($total / $limit));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $limit;
        
        $pacientes = $this->pacienteModel->findPaginated($limit, $offset);
        
        $this->render('pacientes/index', [
            'pacientes' => $pacientes,
            'totalPacientes' => $total,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function show($id)
    {
        AuthMiddleware::handle();
        
        $paciente = $this->pacienteModel->findById($id);
        
        if (!$paciente) {
            $this->flash('Paciente não encontrado', 'error');
            $this->redirect('/pacientes');
        }
        
        $laudos = $this->pacienteModel->getLaudos($id);
        
        $this->render('pacientes/show', [
            'paciente' => $paciente,
            'laudos' => $laudos,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function create()
    {
        AuthMiddleware::handle();
        
        $this->render('pacientes/create', [
            'flash' => $this->getFlash()
        ]);
    }
    
    public function store()
    {
        AuthMiddleware::handle();
        
        $data = $this->getInput();
        
        // Limpar e preparar dados
        $data = $this->prepareData($data);
        
        $errors = $this->validatePaciente($data);
        if (!empty($errors)) {
            $this->flash(implode(', ', $errors), 'error');
            $this->redirect('/pacientes/create');
        }
        
        if ($this->pacienteModel->findByCns($data['cns'])) {
            $this->flash('CNS já cadastrado no sistema', 'error');
            $this->redirect('/pacientes/create');
        }
        
        try {
            $id = $this->pacienteModel->create($data);
            
            if ($id) {
                $this->flash('Paciente cadastrado com sucesso', 'success');
                $this->redirect('/pacientes/' . $id);
            } else {
                $this->flash('Erro ao cadastrar paciente', 'error');
                $this->redirect('/pacientes/create');
            }
        } catch (\Exception $e) {
            error_log('Erro ao cadastrar paciente: ' . $e->getMessage());
            $this->flash('Erro ao cadastrar paciente: ' . $e->getMessage(), 'error');
            $this->redirect('/pacientes/create');
        }
    }
    
    private function prepareData($data)
    {
        // Limpar CPF (remover caracteres não numéricos)
        if (!empty($data['cpf'])) {
            $cpfLimpo = preg_replace('/[^0-9]/', '', $data['cpf']);
            $data['cpf'] = !empty($cpfLimpo) ? $cpfLimpo : null;
        } else {
            $data['cpf'] = null;
        }
        
        // Limpar CNS (remover caracteres não numéricos)
        if (!empty($data['cns'])) {
            $data['cns'] = preg_replace('/[^0-9]/', '', $data['cns']);
        }
        
        // Limpar CEP (remover caracteres não numéricos)
        if (!empty($data['cep'])) {
            $data['cep'] = preg_replace('/[^0-9]/', '', $data['cep']);
        }
        
        // Tratar complemento (pode ser vazio)
        if (empty($data['complemento'])) {
            $data['complemento'] = null;
        }
        
        // Mapear raca_cor de número para texto (conforme ENUM da tabela)
        $racaMap = [
            '1' => 'Branca',
            '2' => 'Preta',
            '3' => 'Parda',
            '4' => 'Amarela',
            '5' => 'Indigena'
        ];
        if (!empty($data['raca_cor']) && isset($racaMap[$data['raca_cor']])) {
            $data['raca_cor'] = $racaMap[$data['raca_cor']];
        }
        
        // Remover campos que não existem na tabela
        unset($data['uf']); // A tabela não tem coluna 'uf'
        
        return $data;
    }
    
    public function edit($id)
    {
        AuthMiddleware::handle();
        
        $paciente = $this->pacienteModel->findById($id);
        
        if (!$paciente) {
            $this->flash('Paciente não encontrado', 'error');
            $this->redirect('/pacientes');
        }
        
        $this->render('pacientes/edit', [
            'paciente' => $paciente,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function update($id)
    {
        AuthMiddleware::handle();
        
        $paciente = $this->pacienteModel->findById($id);
        
        if (!$paciente) {
            $this->flash('Paciente não encontrado', 'error');
            $this->redirect('/pacientes');
        }
        
        $data = $this->getInput();
        
        $errors = $this->validatePaciente($data);
        if (!empty($errors)) {
            $this->flash(implode(', ', $errors), 'error');
            $this->redirect('/pacientes/' . $id . '/edit');
        }
        
        $existing = $this->pacienteModel->findByCns($data['cns']);
        if ($existing && $existing['id'] != $id) {
            $this->flash('CNS já cadastrado para outro paciente', 'error');
            $this->redirect('/pacientes/' . $id . '/edit');
        }
        
        $updated = $this->pacienteModel->update($id, $data);
        
        if ($updated) {
            $this->flash('Paciente atualizado com sucesso', 'success');
            $this->redirect('/pacientes/' . $id);
        } else {
            $this->flash('Erro ao atualizar paciente', 'error');
            $this->redirect('/pacientes/' . $id . '/edit');
        }
    }
    
    public function delete($id)
    {
        AuthMiddleware::handle();
        
        $paciente = $this->pacienteModel->findById($id);
        
        if (!$paciente) {
            $this->jsonResponse(['success' => false, 'message' => 'Paciente não encontrado'], 404);
        }
        
        $deleted = $this->pacienteModel->delete($id);
        
        if ($deleted) {
            $this->flash('Paciente excluído com sucesso', 'success');
            $this->jsonResponse(['success' => true, 'message' => 'Paciente excluído com sucesso']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Erro ao excluir paciente'], 500);
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
            $pacientes = $this->pacienteModel->findPaginated($limit, $offset);
            $total = $this->pacienteModel->countTotal();
        } else {
            $pacientes = $this->pacienteModel->searchPaginated($q, $limit, $offset);
            $total = $this->pacienteModel->searchCount($q);
        }
        
        $totalPages = max(1, ceil($total / $limit));
        $page = min($page, $totalPages);
        
        $this->jsonResponse([
            'success' => true,
            'pacientes' => $pacientes,
            'total' => $total,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ]);
    }
    
    public function ajax_list()
    {
        AuthMiddleware::handle();
        
        $page = (int) $this->getInput('page', 1);
        $limit = (int) $this->getInput('limit', 10);
        $offset = ($page - 1) * $limit;
        
        $pacientes = $this->pacienteModel->findAll($limit, $offset);
        $total = $this->pacienteModel->count();
        
        $this->jsonResponse([
            'success' => true,
            'data' => $pacientes,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }
    
    private function validatePaciente($data)
    {
        $errors = [];
        
        if (empty($data['nome'])) {
            $errors[] = 'Nome é obrigatório';
        }
        
        if (empty($data['cns'])) {
            $errors[] = 'CNS é obrigatório';
        } elseif (strlen($data['cns']) != 15) {
            $errors[] = 'CNS deve ter 15 dígitos';
        }
        
        if (!empty($data['cpf'])) {
            $cpfLimpo = preg_replace('/[^0-9]/', '', $data['cpf']);
            if (strlen($cpfLimpo) != 11) {
                $errors[] = 'CPF deve ter 11 dígitos';
            }
        }
        
        if (empty($data['data_nascimento'])) {
            $errors[] = 'Data de nascimento é obrigatória';
        }
        
        if (empty($data['sexo']) || !in_array($data['sexo'], ['M', 'F'])) {
            $errors[] = 'Sexo é obrigatório (M ou F)';
        }
        
        if (empty($data['nome_mae'])) {
            $errors[] = 'Nome da mãe é obrigatório';
        }
        
        if (empty($data['raca_cor'])) {
            $errors[] = 'Raça/Cor é obrigatória';
        }
        
        if (empty($data['cep'])) {
            $errors[] = 'CEP é obrigatório';
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
        
        if (empty($data['municipio'])) {
            $errors[] = 'Município é obrigatório';
        }
        
        // UF não existe na tabela, removido da validação
        
        return $errors;
    }
}
