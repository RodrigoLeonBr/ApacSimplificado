<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Models\Faixa;
use App\Models\Log;
use App\Utils\Session;
use App\Utils\Validation;
use App\Services\DigitoVerificadorService;

class FaixaController
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
        
        $errors = Validation::errors($_POST, [
            'inicial_13dig' => 'required|numeric',
            'final_13dig' => 'required|numeric'
        ]);
        
        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('old', $_POST);
            header('Location: /faixas/create');
            exit;
        }
        
        $inicial = $_POST['inicial_13dig'];
        $final = $_POST['final_13dig'];
        
        if (!Validation::validateAPAC13($inicial) || !Validation::validateAPAC13($final)) {
            Session::flash('error', 'Os números devem ter exatamente 13 dígitos.');
            Session::flash('old', $_POST);
            header('Location: /faixas/create');
            exit;
        }
        
        if ((int)$inicial > (int)$final) {
            Session::flash('error', 'O número inicial não pode ser maior que o final.');
            Session::flash('old', $_POST);
            header('Location: /faixas/create');
            exit;
        }
        
        $quantidade = (int)$final - (int)$inicial + 1;
        
        try {
            $faixaId = $this->faixaModel->create([
                'inicial_13dig' => $inicial,
                'final_13dig' => $final,
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
            header('Location: /faixas');
        } catch (\Exception $e) {
            Session::flash('error', 'Erro ao cadastrar faixa: ' . $e->getMessage());
            Session::flash('old', $_POST);
            header('Location: /faixas/create');
        }
        exit;
    }
    
    public function show($id)
    {
        $this->authService->guard();
        
        $faixa = $this->faixaModel->findById($id);
        
        if (!$faixa) {
            Session::flash('error', 'Faixa não encontrada.');
            header('Location: /faixas');
            exit;
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
            header('Location: /faixas');
            exit;
        }
        
        $apacsEmitidas = $this->faixaModel->countApacsEmitidas($id);
        
        if ($apacsEmitidas > 0) {
            Session::flash('error', 'Não é possível excluir uma faixa que já possui APACs emitidas.');
            header('Location: /faixas');
            exit;
        }
        
        $this->faixaModel->delete($id);
        
        $user = $this->authService->user();
        $this->logModel->create([
            'acao' => 'Exclusão de faixa',
            'usuario_id' => $user['id'],
            'tabela_afetada' => 'faixas',
            'registro_id' => $id,
            'detalhes' => "Faixa de {$faixa['inicial_13dig']} a {$faixa['final_13dig']} excluída"
        ]);
        
        Session::flash('success', 'Faixa excluída com sucesso!');
        header('Location: /faixas');
        exit;
    }
}
