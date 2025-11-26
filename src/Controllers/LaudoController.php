<?php

namespace App\Controllers;

use App\Models\Laudo;
use App\Models\Paciente;
use App\Models\Cid;
use App\Models\Procedimento;
use App\Models\Estabelecimento;
use App\Models\Profissional;
use App\Models\CaraterAtendimento;
use App\Models\Log;
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
    private $logModel;
    
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
        $this->logModel = new Log();
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
        
        // Buscar APAC vinculada ao laudo
        $apacVinculada = $this->laudoModel->findApacVinculada($id);
        
        // Buscar logs do laudo (últimas 3 ações)
        $logs = $this->logModel->findByTabelaRegistro('laudos', $id, 3);
        
        $this->render('laudos.show', [
            'laudo' => $laudo,
            'apacVinculada' => $apacVinculada,
            'logs' => $logs,
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
        
        $input = $this->getInput();
        
        // Processar paciente se for novo paciente
        $pacienteId = null;
        if (!empty($input['paciente_id'])) {
            $pacienteId = $input['paciente_id'];
        } elseif (!empty($input['paciente']) && is_array($input['paciente'])) {
            // Criar novo paciente
            $pacienteData = $input['paciente'];
            
            // Validar dados do paciente
            if (empty($pacienteData['nome']) || empty($pacienteData['cns']) || empty($pacienteData['data_nascimento'])) {
                $this->flash('Dados do paciente incompletos. Nome, CNS e Data de Nascimento são obrigatórios.', 'error');
                $this->redirect('/laudos/create');
            }
            
            // Verificar se paciente já existe pelo CNS
            $pacienteExistente = $this->pacienteModel->findByCns($pacienteData['cns']);
            if ($pacienteExistente) {
                $pacienteId = $pacienteExistente['id'];
            } else {
                // Criar novo paciente
                $pacienteId = $this->pacienteModel->create($pacienteData);
                if (!$pacienteId) {
                    $this->flash('Erro ao cadastrar paciente', 'error');
                    $this->redirect('/laudos/create');
                }
            }
        }
        
        if (!$pacienteId) {
            $this->flash('Paciente é obrigatório', 'error');
            $this->redirect('/laudos/create');
        }
        
        // Preparar dados do laudo (apenas campos da tabela laudos)
        $data = [
            'paciente_id' => $pacienteId,
            'numero_prontuario' => $input['numero_prontuario'] ?? '',
            'numero_laudo' => $input['numero_laudo'] ?? '',
            'data_laudo' => $input['data_laudo'] ?? date('Y-m-d'),
            'cid_id' => $input['cid_id'] ?? null,
            'procedimento_solicitado_id' => $input['procedimento_solicitado_id'] ?? null,
            'procedimento_autorizado_id' => $input['procedimento_autorizado_id'] ?? null,
            'estabelecimento_solicitante_id' => $input['estabelecimento_solicitante_id'] ?? null,
            'estabelecimento_executante_id' => $input['estabelecimento_executante_id'] ?? null,
            'profissional_solicitante_id' => $input['profissional_solicitante_id'] ?? null,
            'carater_atendimento_id' => $input['carater_atendimento_id'] ?? null,
            'observacoes' => $input['observacoes'] ?? null,
            'status' => 'rascunho',
            'usuario_id' => AuthMiddleware::getUserId()
        ];
        
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
        
        // Buscar APAC vinculada ao laudo
        $apacVinculada = $this->laudoModel->findApacVinculada($id);
        
        $pacientes = $this->pacienteModel->findAll();
        $cids = $this->cidModel->findAll();
        $procedimentos = $this->procedimentoModel->findAll();
        $estabelecimentos = $this->estabelecimentoModel->findAll();
        $profissionais = $this->profissionalModel->findAll();
        $caracteres = $this->caraterModel->findAll();
        
        $this->render('laudos.edit', [
            'laudo' => $laudo,
            'apacVinculada' => $apacVinculada,
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
        
        $input = $this->getInput();
        
        // Preparar dados do laudo (apenas campos da tabela laudos)
        $data = [
            'paciente_id' => $input['paciente_id'] ?? $laudo['paciente_id'],
            'numero_prontuario' => $input['numero_prontuario'] ?? $laudo['numero_prontuario'],
            'numero_laudo' => $input['numero_laudo'] ?? $laudo['numero_laudo'],
            'data_laudo' => $input['data_laudo'] ?? $laudo['data_laudo'],
            'cid_id' => $input['cid_id'] ?? $laudo['cid_id'],
            'procedimento_solicitado_id' => $input['procedimento_solicitado_id'] ?? $laudo['procedimento_solicitado_id'],
            'procedimento_autorizado_id' => $input['procedimento_autorizado_id'] ?? $laudo['procedimento_autorizado_id'],
            'estabelecimento_solicitante_id' => $input['estabelecimento_solicitante_id'] ?? $laudo['estabelecimento_solicitante_id'],
            'estabelecimento_executante_id' => $input['estabelecimento_executante_id'] ?? $laudo['estabelecimento_executante_id'],
            'profissional_solicitante_id' => $input['profissional_solicitante_id'] ?? $laudo['profissional_solicitante_id'],
            'carater_atendimento_id' => $input['carater_atendimento_id'] ?? $laudo['carater_atendimento_id'],
            'observacoes' => $input['observacoes'] ?? $laudo['observacoes']
        ];
        
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
        
        try {
            $laudo = $this->laudoModel->findById($id);
            
            if (!$laudo) {
                $this->jsonResponse(['success' => false, 'message' => 'Laudo não encontrado'], 404);
                return;
            }
            
            // Verificar se o laudo pode ser excluído (não pode ter APAC vinculada)
            $apacVinculada = $this->laudoModel->findApacVinculada($id);
            if ($apacVinculada) {
                $this->jsonResponse(['success' => false, 'message' => 'Não é possível excluir um laudo que possui APAC vinculada'], 400);
                return;
            }
            
            $deleted = $this->laudoModel->delete($id);
            
            if ($deleted) {
                // Registrar log da exclusão
                $this->logModel->create([
                    'usuario_id' => AuthMiddleware::getUserId(),
                    'acao' => 'excluir',
                    'tabela' => 'laudos',
                    'registro_id' => $id,
                    'dados_anteriores' => json_encode($laudo)
                ]);
                
                $this->jsonResponse(['success' => true, 'message' => 'Laudo excluído com sucesso']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Erro ao excluir laudo'], 500);
            }
        } catch (\Exception $e) {
            error_log('Erro ao excluir laudo: ' . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Erro ao excluir laudo: ' . $e->getMessage()], 500);
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
        
        if (empty($data['numero_prontuario'])) {
            $errors[] = 'Número do prontuário é obrigatório';
        }
        
        if (empty($data['numero_laudo'])) {
            $errors[] = 'Número do laudo é obrigatório';
        }
        
        if (empty($data['data_laudo'])) {
            $errors[] = 'Data do laudo é obrigatória';
        }
        
        if (empty($data['cid_id'])) {
            $errors[] = 'CID é obrigatório';
        }
        
        if (empty($data['procedimento_solicitado_id'])) {
            $errors[] = 'Procedimento solicitado é obrigatório';
        }
        
        if (empty($data['procedimento_autorizado_id'])) {
            $errors[] = 'Procedimento autorizado é obrigatório';
        }
        
        if (empty($data['estabelecimento_solicitante_id'])) {
            $errors[] = 'Estabelecimento solicitante é obrigatório';
        }
        
        if (empty($data['estabelecimento_executante_id'])) {
            $errors[] = 'Estabelecimento executante é obrigatório';
        }
        
        if (empty($data['profissional_solicitante_id'])) {
            $errors[] = 'Profissional solicitante é obrigatório';
        }
        
        if (empty($data['carater_atendimento_id'])) {
            $errors[] = 'Caráter de atendimento é obrigatório';
        }
        
        return $errors;
    }
}
