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
        
        $page = (int) ($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $pacientes = $this->pacienteModel->findPaginated($limit, $offset);
        $total = $this->pacienteModel->countTotal();
        $totalPages = ceil($total / $limit);
        
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
        
        $errors = $this->validatePaciente($data);
        if (!empty($errors)) {
            $this->flash(implode(', ', $errors), 'error');
            $this->redirect('/pacientes/create');
        }
        
        if ($this->pacienteModel->findByCns($data['cns'])) {
            $this->flash('CNS já cadastrado no sistema', 'error');
            $this->redirect('/pacientes/create');
        }
        
        $id = $this->pacienteModel->create($data);
        
        if ($id) {
            $this->flash('Paciente cadastrado com sucesso', 'success');
            $this->redirect('/pacientes/' . $id);
        } else {
            $this->flash('Erro ao cadastrar paciente', 'error');
            $this->redirect('/pacientes/create');
        }
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
        $page = (int) ($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        if (strlen($q) === 0) {
            $pacientes = $this->pacienteModel->findPaginated($limit, $offset);
            $total = $this->pacienteModel->countTotal();
        } else {
            $pacientes = $this->pacienteModel->searchPaginated($q, $limit, $offset);
            $total = $this->pacienteModel->searchCount($q);
        }
        
        $totalPages = ceil($total / $limit);
        
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
        
        if (empty($data['cns']) || strlen($data['cns']) != 15) {
            $errors[] = 'CNS é obrigatório e deve ter 15 dígitos';
        }
        
        if (!empty($data['cpf']) && strlen($data['cpf']) != 11) {
            $errors[] = 'CPF deve ter 11 dígitos';
        }
        
        if (empty($data['data_nascimento'])) {
            $errors[] = 'Data de nascimento é obrigatória';
        }
        
        if (empty($data['sexo']) || !in_array($data['sexo'], ['M', 'F'])) {
            $errors[] = 'Sexo é obrigatório (M ou F)';
        }
        
        return $errors;
    }
}
