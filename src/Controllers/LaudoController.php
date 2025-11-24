<?php

namespace App\Controllers;

use App\Models\Laudo;
use App\Models\Paciente;
use App\Models\Cid;
use App\Models\Procedimento;
use App\Models\Estabelecimento;
use App\Models\Profissional;
use App\Models\CaraterAtendimento;
use App\Middleware\AuthMiddleware;

class LaudoController extends BaseController
{
    private $laudoModel;
    private $pacienteModel;
    private $cidModel;
    private $procedimentoModel;
    private $estabelecimentoModel;
    private $profissionalModel;
    private $caraterModel;
    
    public function __construct($db = null)
    {
        parent::__construct($db);
        $this->laudoModel = new Laudo();
        $this->pacienteModel = new Paciente();
        $this->cidModel = new Cid();
        $this->procedimentoModel = new Procedimento();
        $this->estabelecimentoModel = new Estabelecimento();
        $this->profissionalModel = new Profissional();
        $this->caraterModel = new CaraterAtendimento();
    }
    
    public function index()
    {
        AuthMiddleware::handle();
        
        $laudos = $this->laudoModel->findAll();
        
        $this->render('laudos.index', [
            'laudos' => $laudos,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function show($id)
    {
        AuthMiddleware::handle();
        
        $laudo = $this->laudoModel->findById($id);
        
        if (!$laudo) {
            $this->flash('Laudo não encontrado', 'error');
            $this->redirect('/laudos');
        }
        
        $this->render('laudos.show', [
            'laudo' => $laudo,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function create()
    {
        AuthMiddleware::handle();
        
        $pacientes = $this->pacienteModel->findAll();
        $cids = $this->cidModel->findAll();
        $procedimentos = $this->procedimentoModel->findAll();
        $estabelecimentos = $this->estabelecimentoModel->findAll();
        $profissionais = $this->profissionalModel->findAll();
        $caracteres = $this->caraterModel->findAll();
        
        $this->render('laudos.create', [
            'pacientes' => $pacientes,
            'cids' => $cids,
            'procedimentos' => $procedimentos,
            'estabelecimentos' => $estabelecimentos,
            'profissionais' => $profissionais,
            'caracteres' => $caracteres,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function store()
    {
        AuthMiddleware::handle();
        
        $data = $this->getInput();
        $data['usuario_id'] = AuthMiddleware::getUserId();
        
        $errors = $this->validateLaudo($data);
        if (!empty($errors)) {
            $this->flash(implode(', ', $errors), 'error');
            $this->redirect('/laudos/create');
        }
        
        $id = $this->laudoModel->create($data);
        
        if ($id) {
            $this->flash('Laudo cadastrado com sucesso', 'success');
            $this->redirect('/laudos/' . $id);
        } else {
            $this->flash('Erro ao cadastrar laudo', 'error');
            $this->redirect('/laudos/create');
        }
    }
    
    public function edit($id)
    {
        AuthMiddleware::handle();
        
        $laudo = $this->laudoModel->findById($id);
        
        if (!$laudo) {
            $this->flash('Laudo não encontrado', 'error');
            $this->redirect('/laudos');
        }
        
        $pacientes = $this->pacienteModel->findAll();
        $cids = $this->cidModel->findAll();
        $procedimentos = $this->procedimentoModel->findAll();
        $estabelecimentos = $this->estabelecimentoModel->findAll();
        $profissionais = $this->profissionalModel->findAll();
        $caracteres = $this->caraterModel->findAll();
        
        $this->render('laudos.edit', [
            'laudo' => $laudo,
            'pacientes' => $pacientes,
            'cids' => $cids,
            'procedimentos' => $procedimentos,
            'estabelecimentos' => $estabelecimentos,
            'profissionais' => $profissionais,
            'caracteres' => $caracteres,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function update($id)
    {
        AuthMiddleware::handle();
        
        $laudo = $this->laudoModel->findById($id);
        
        if (!$laudo) {
            $this->flash('Laudo não encontrado', 'error');
            $this->redirect('/laudos');
        }
        
        $data = $this->getInput();
        
        $errors = $this->validateLaudo($data);
        if (!empty($errors)) {
            $this->flash(implode(', ', $errors), 'error');
            $this->redirect('/laudos/' . $id . '/edit');
        }
        
        $updated = $this->laudoModel->update($id, $data);
        
        if ($updated) {
            $this->flash('Laudo atualizado com sucesso', 'success');
            $this->redirect('/laudos/' . $id);
        } else {
            $this->flash('Erro ao atualizar laudo', 'error');
            $this->redirect('/laudos/' . $id . '/edit');
        }
    }
    
    public function delete($id)
    {
        AuthMiddleware::handle();
        
        $laudo = $this->laudoModel->findById($id);
        
        if (!$laudo) {
            $this->jsonResponse(['success' => false, 'message' => 'Laudo não encontrado'], 404);
        }
        
        $deleted = $this->laudoModel->delete($id);
        
        if ($deleted) {
            $this->flash('Laudo excluído com sucesso', 'success');
            $this->jsonResponse(['success' => true, 'message' => 'Laudo excluído com sucesso']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Erro ao excluir laudo'], 500);
        }
    }
    
    public function ajax_search()
    {
        AuthMiddleware::handle();
        
        $termo = $this->getInput('termo', '');
        
        if (strlen($termo) < 3) {
            $this->jsonResponse(['success' => false, 'message' => 'Digite ao menos 3 caracteres']);
        }
        
        $laudos = $this->laudoModel->search($termo);
        
        $this->jsonResponse([
            'success' => true,
            'data' => $laudos
        ]);
    }
    
    public function ajax_list()
    {
        AuthMiddleware::handle();
        
        $page = (int) $this->getInput('page', 1);
        $limit = (int) $this->getInput('limit', 10);
        $offset = ($page - 1) * $limit;
        
        $laudos = $this->laudoModel->findAll($limit, $offset);
        $total = $this->laudoModel->count();
        
        $this->jsonResponse([
            'success' => true,
            'data' => $laudos,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }
    
    private function validateLaudo($data)
    {
        $errors = [];
        
        if (empty($data['paciente_id'])) {
            $errors[] = 'Paciente é obrigatório';
        }
        
        if (empty($data['cid_id'])) {
            $errors[] = 'CID é obrigatório';
        }
        
        if (empty($data['procedimento_id'])) {
            $errors[] = 'Procedimento é obrigatório';
        }
        
        if (empty($data['estabelecimento_id'])) {
            $errors[] = 'Estabelecimento é obrigatório';
        }
        
        if (empty($data['profissional_id'])) {
            $errors[] = 'Profissional é obrigatório';
        }
        
        if (empty($data['carater_atendimento_id'])) {
            $errors[] = 'Caráter de atendimento é obrigatório';
        }
        
        if (empty($data['data_solicitacao'])) {
            $errors[] = 'Data de solicitação é obrigatória';
        }
        
        return $errors;
    }
}
