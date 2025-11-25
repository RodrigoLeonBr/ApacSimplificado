<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Models\Faixa;
use App\Models\Log;
use App\Utils\Session;
use App\Utils\Validation;
use App\Services\DigitoVerificadorService;

class FaixaController extends BaseController
{
    private $authService;
    private $faixaModel;
    private $logModel;
    private $dvService;
    
    public function __construct()
    {
        $this->authService = new AuthService();
        $this->faixaModel = new Faixa();
        $this->logModel = new Log();
        $this->dvService = new DigitoVerificadorService();
    }
    
    public function index()
    {
        $this->authService->guard();
        
        $faixas = $this->faixaModel->findAll();
        
        foreach ($faixas as &$faixa) {
            $faixa['apacs_emitidas'] = $this->faixaModel->countApacsEmitidas($faixa['id']);
            $faixa['percentual_uso'] = ($faixa['quantidade'] > 0) 
                ? round(($faixa['apacs_emitidas'] / $faixa['quantidade']) * 100, 2) 
                : 0;
        }
        
        $user = $this->authService->user();
        
        require VIEWS_PATH . '/faixa/index.php';
    }
    
    public function create()
    {
        $this->authService->guard();
        $user = $this->authService->user();
        
        require VIEWS_PATH . '/faixa/create.php';
    }
    
    public function store()
    {
        $this->authService->guard();
        
        // Mapear campos do formulário para os nomes esperados
        $data = [
            'numero_inicial' => $_POST['numero_inicial'] ?? '',
            'numero_final' => $_POST['numero_final'] ?? ''
        ];
        
        $errors = Validation::errors($data, [
            'numero_inicial' => 'required|numeric',
            'numero_final' => 'required|numeric'
        ]);
        
        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('old', $_POST);
            $this->redirect('/faixas/create');
        }
        
        $inicial = preg_replace('/[^0-9]/', '', $data['numero_inicial']);
        $final = preg_replace('/[^0-9]/', '', $data['numero_final']);
        
        if (!Validation::validateAPAC13($inicial) || !Validation::validateAPAC13($final)) {
            Session::flash('error', 'Os números devem ter exatamente 13 dígitos.');
            Session::flash('old', $_POST);
            $this->redirect('/faixas/create');
        }
        
        if ((int)$inicial > (int)$final) {
            Session::flash('error', 'O número inicial não pode ser maior que o final.');
            Session::flash('old', $_POST);
            $this->redirect('/faixas/create');
        }
        
        $quantidade = (int)$final - (int)$inicial + 1;
        
        try {
            $faixaId = $this->faixaModel->create([
                'numero_inicial' => $inicial,
                'numero_final' => $final,
                'quantidade' => $quantidade,
                'status' => 'disponivel'
            ]);
            
            $user = $this->authService->user();
            $this->logModel->create([
                'acao' => 'Cadastro de faixa',
                'usuario_id' => $user['id'],
                'tabela_afetada' => 'faixas',
                'registro_id' => $faixaId,
                'detalhes' => "Faixa de {$inicial} a {$final} ({$quantidade} números)"
            ]);
            
            Session::flash('success', 'Faixa cadastrada com sucesso!');
            $this->redirect('/faixas');
        } catch (\Exception $e) {
            Session::flash('error', 'Erro ao cadastrar faixa: ' . $e->getMessage());
            Session::flash('old', $_POST);
            $this->redirect('/faixas/create');
        }
    }
    
    public function show($id)
    {
        $this->authService->guard();
        
        $faixa = $this->faixaModel->findById($id);
        
        if (!$faixa) {
            Session::flash('error', 'Faixa não encontrada.');
            $this->redirect('/faixas');
        }
        
        $faixa['apacs_emitidas'] = $this->faixaModel->countApacsEmitidas($faixa['id']);
        $user = $this->authService->user();
        
        require VIEWS_PATH . '/faixa/show.php';
    }
    
    public function delete($id)
    {
        $this->authService->guard();
        
        $faixa = $this->faixaModel->findById($id);
        
        if (!$faixa) {
            Session::flash('error', 'Faixa não encontrada.');
            $this->redirect('/faixas');
        }
        
        $apacsEmitidas = $this->faixaModel->countApacsEmitidas($id);
        
        if ($apacsEmitidas > 0) {
            Session::flash('error', 'Não é possível excluir uma faixa que já possui APACs emitidas.');
            $this->redirect('/faixas');
        }
        
        $this->faixaModel->delete($id);
        
        $user = $this->authService->user();
        $this->logModel->create([
            'acao' => 'Exclusão de faixa',
            'usuario_id' => $user['id'],
            'tabela_afetada' => 'faixas',
            'registro_id' => $id,
            'detalhes' => "Faixa de {$faixa['numero_inicial']} a {$faixa['numero_final']} excluída"
        ]);
        
        Session::flash('success', 'Faixa excluída com sucesso!');
        $this->redirect('/faixas');
    }
}
