<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Models\Faixa;
use App\Models\Apac;
use App\Models\Log;

class DashboardController
{
    private $authService;
    private $faixaModel;
    private $apacModel;
    private $logModel;
    
    public function __construct()
    {
        $this->authService = new AuthService();
        $this->faixaModel = new Faixa();
        $this->apacModel = new Apac();
        $this->logModel = new Log();
    }
    
    public function index()
    {
        $this->authService->guard();
        
        $stats = [
            'total_faixas' => count($this->faixaModel->findAll()),
            'faixas_disponiveis' => count($this->faixaModel->findDisponiveis()),
            'total_apacs' => $this->apacModel->countTotal(),
            'apacs_impressas' => $this->apacModel->countImpressas(),
            'total_logs' => $this->logModel->countTotal()
        ];
        
        $recentApacs = array_slice($this->apacModel->findAll(), 0, 5);
        $recentLogs = $this->logModel->findAll(10);
        
        $user = $this->authService->user();
        
        require VIEWS_PATH . '/dashboard/index.php';
    }
}
