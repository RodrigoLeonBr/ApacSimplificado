<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\EmissaoService;
use App\Models\Apac;
use App\Models\Faixa;
use App\Utils\Session;

class ApacController
{
    private $authService;
    private $emissaoService;
    private $apacModel;
    private $faixaModel;
    
    public function __construct()
    {
        $this->authService = new AuthService();
        $this->emissaoService = new EmissaoService();
        $this->apacModel = new Apac();
        $this->faixaModel = new Faixa();
    }
    
    public function index()
    {
        $this->authService->guard();
        
        $apacs = $this->apacModel->findAll();
        $user = $this->authService->user();
        
        require VIEWS_PATH . '/apac/index.php';
    }
    
    public function create()
    {
        $this->authService->guard();
        
        $faixas = $this->faixaModel->findDisponiveis();
        $user = $this->authService->user();
        
        require VIEWS_PATH . '/apac/create.php';
    }
    
    public function store()
    {
        $this->authService->guard();
        
        $faixaId = $_POST['faixa_id'] ?? null;
        
        if (!$faixaId) {
            Session::flash('error', 'Selecione uma faixa válida.');
            header('Location: /apacs/create');
            exit;
        }
        
        $user = $this->authService->user();
        $result = $this->emissaoService->emitirAPAC($faixaId, $user['id']);
        
        if ($result['success']) {
            Session::flash('success', 'APAC emitida com sucesso! Número: ' . $result['numero_14dig']);
            header('Location: /apacs');
        } else {
            Session::flash('error', $result['message']);
            header('Location: /apacs/create');
        }
        exit;
    }
    
    public function marcarImpresso($id)
    {
        $this->authService->guard();
        
        $user = $this->authService->user();
        $result = $this->emissaoService->marcarComoImpresso($id, $user['id']);
        
        if ($result) {
            Session::flash('success', 'APAC marcada como impressa!');
        } else {
            Session::flash('error', 'Erro ao marcar APAC como impressa.');
        }
        
        header('Location: /apacs');
        exit;
    }
}
