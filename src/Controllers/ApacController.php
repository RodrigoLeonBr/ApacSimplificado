<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\EmissaoService;
use App\Models\Apac;
use App\Models\Faixa;
use App\Models\Laudo;
use App\Models\ApacLaudo;
use App\Models\Estabelecimento;
use App\Models\Profissional;
use App\Models\Paciente;
use App\Utils\Session;
use App\Utils\Router;

class ApacController
{
    private $authService;
    private $emissaoService;
    private $apacModel;
    private $faixaModel;
    private $laudoModel;
    private $apacLaudoModel;
    private $estabelecimentoModel;
    private $profissionalModel;
    private $pacienteModel;
    
    public function __construct()
    {
        $this->authService = new AuthService();
        $this->emissaoService = new EmissaoService();
        $this->apacModel = new Apac();
        $this->faixaModel = new Faixa();
        $this->laudoModel = new Laudo();
        $this->apacLaudoModel = new ApacLaudo();
        $this->estabelecimentoModel = new Estabelecimento();
        $this->profissionalModel = new Profissional();
        $this->pacienteModel = new Paciente();
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
        
        $laudoId = $_GET['laudo_id'] ?? null;
        $laudo = null;
        $apacVinculada = null;
        $erro = null;
        
        // Laudo é obrigatório para emissão de APAC
        if (!$laudoId) {
            Session::flash('error', 'É necessário selecionar um laudo para emitir APAC.');
            Router::redirect('/laudos');
            exit;
        }
        
        // Buscar dados do laudo
        if ($laudoId) {
            $laudo = $this->laudoModel->findById($laudoId);
            
            if (!$laudo) {
                Session::flash('error', 'Laudo não encontrado.');
                Router::redirect('/laudos');
                exit;
            }
            
            // Validar status do laudo
            $status = $laudo['status'] ?? 'rascunho';
            if ($status === 'cancelado') {
                Session::flash('error', 'Não é possível emitir APAC para um laudo cancelado.');
                Router::redirect('/laudos/' . $laudoId);
                exit;
            }
            
            // Verificar se laudo já possui APAC vinculada
            $apacVinculada = $this->apacLaudoModel->laudoPossuiApac($laudoId);
            if ($apacVinculada) {
                $erro = 'Este laudo já possui uma APAC vinculada.';
            }
        }
        
        $faixas = $this->faixaModel->findDisponiveis();
        $user = $this->authService->user();
        
        require VIEWS_PATH . '/apac/create.php';
    }
    
    public function store()
    {
        $this->authService->guard();
        
        $faixaId = $_POST['faixa_id'] ?? null;
        $laudoId = $_POST['laudo_id'] ?? null;
        
        if (!$faixaId) {
            Session::flash('error', 'Selecione uma faixa válida.');
            Router::redirect('/apacs/create?laudo_id=' . ($laudoId ?? ''));
            exit;
        }
        
        // Laudo é obrigatório
        if (!$laudoId) {
            Session::flash('error', 'Laudo é obrigatório para emissão de APAC.');
            Router::redirect('/laudos');
            exit;
        }
        
        // Validar laudo antes de emitir
        if ($laudoId) {
            $laudo = $this->laudoModel->findById($laudoId);
            
            if (!$laudo) {
                Session::flash('error', 'Laudo não encontrado.');
                Router::redirect('/laudos');
                exit;
            }
            
            // Validar status do laudo
            $status = $laudo['status'] ?? 'rascunho';
            if ($status === 'cancelado') {
                Session::flash('error', 'Não é possível emitir APAC para um laudo cancelado.');
                Router::redirect('/laudos/' . $laudoId);
                exit;
            }
            
            // Verificar se laudo já possui APAC vinculada
            $apacVinculada = $this->apacLaudoModel->laudoPossuiApac($laudoId);
            if ($apacVinculada) {
                Session::flash('error', 'Este laudo já possui uma APAC vinculada.');
                Router::redirect('/laudos/' . $laudoId);
                exit;
            }
        }
        
        $user = $this->authService->user();
        $result = $this->emissaoService->emitirAPAC($faixaId, $user['id'], $laudoId);
        
        if ($result['success']) {
            Session::flash('success', 'APAC emitida com sucesso! Número: ' . $result['numero_14dig'] . ' vinculada ao laudo #' . $laudoId);
            // Redirecionar para impressão da APAC
            Router::redirect('/apacs/' . $result['apac_id'] . '/imprimir');
        } else {
            Session::flash('error', $result['message']);
            Router::redirect('/apacs/create?laudo_id=' . $laudoId);
        }
        exit;
    }
    
    public function imprimir($id)
    {
        $this->authService->guard();
        
        $apac = $this->apacModel->findById($id);
        
        if (!$apac) {
            Session::flash('error', 'APAC não encontrada.');
            Router::redirect('/apacs');
            exit;
        }
        
        // Buscar laudo vinculado
        $laudoVinculado = $this->apacLaudoModel->findByApacId($id);
        if (empty($laudoVinculado)) {
            Session::flash('error', 'Laudo não encontrado para esta APAC.');
            Router::redirect('/apacs');
            exit;
        }
        
        $laudoId = $laudoVinculado[0]['laudo_id'];
        $laudo = $this->laudoModel->findById($laudoId);
        
        if (!$laudo) {
            Session::flash('error', 'Laudo não encontrado.');
            Router::redirect('/apacs');
            exit;
        }
        
        // Buscar dados completos
        $paciente = $this->pacienteModel->findById($laudo['paciente_id']);
        $estabelecimentoSolicitante = $this->estabelecimentoModel->findById($laudo['estabelecimento_solicitante_id'] ?? null);
        $estabelecimentoExecutante = $this->estabelecimentoModel->findById($laudo['estabelecimento_executante_id'] ?? null);
        $profissional = $this->profissionalModel->findById($laudo['profissional_solicitante_id'] ?? null);
        
        require VIEWS_PATH . '/apac/imprimir.php';
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
        
        Router::redirect('/apacs');
    }
    
    /**
     * Método auxiliar para redirecionamento (compatibilidade).
     * 
     * @param string $url
     */
    private function redirect($url)
    {
        Router::redirect($url);
    }
}
