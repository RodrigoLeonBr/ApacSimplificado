<?php

namespace App\Controllers;

use App\Services\SigtapImportService;
use App\Middleware\AuthMiddleware;

class ImportacaoController extends BaseController
{
    private $importService;
    
    public function __construct()
    {
        parent::__construct();
        $this->importService = new SigtapImportService();
    }
    
    /**
     * Página inicial de importação
     */
    public function index()
    {
        AuthMiddleware::handle();
        
        $basePath = __DIR__ . '/../../temp/';
        $arquivos = [
            'cids' => [
                'nome' => 'CIDs (Classificação Internacional de Doenças)',
                'arquivo' => $basePath . 'tb_cid.txt',
                'existe' => file_exists($basePath . 'tb_cid.txt'),
                'tamanho' => file_exists($basePath . 'tb_cid.txt') ? filesize($basePath . 'tb_cid.txt') : 0
            ],
            'procedimentos' => [
                'nome' => 'Procedimentos do SUS',
                'arquivo' => $basePath . 'tb_procedimento.txt',
                'existe' => file_exists($basePath . 'tb_procedimento.txt'),
                'tamanho' => file_exists($basePath . 'tb_procedimento.txt') ? filesize($basePath . 'tb_procedimento.txt') : 0
            ],
            'relacionamentos' => [
                'nome' => 'Relacionamentos Procedimento x CID',
                'arquivo' => $basePath . 'rl_procedimento_cid.txt',
                'existe' => file_exists($basePath . 'rl_procedimento_cid.txt'),
                'tamanho' => file_exists($basePath . 'rl_procedimento_cid.txt') ? filesize($basePath . 'rl_procedimento_cid.txt') : 0
            ]
        ];
        
        $this->render('importacao.index', [
            'arquivos' => $arquivos,
            'flash' => $this->getFlash()
        ]);
    }
    
    /**
     * Importa CIDs
     */
    public function importarCids()
    {
        AuthMiddleware::handle();
        
        try {
            $arquivo = __DIR__ . '/../../temp/tb_cid.txt';
            
            if (!file_exists($arquivo)) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Arquivo tb_cid.txt não encontrado na pasta temp/'
                ], 404);
                return;
            }
            
            $this->importService->reset();
            $resultado = $this->importService->importarCids($arquivo);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Importação de CIDs concluída com sucesso',
                'data' => $resultado
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao importar CIDs: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Importa Procedimentos
     */
    public function importarProcedimentos()
    {
        AuthMiddleware::handle();
        
        try {
            $arquivo = __DIR__ . '/../../temp/tb_procedimento.txt';
            
            if (!file_exists($arquivo)) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Arquivo tb_procedimento.txt não encontrado na pasta temp/'
                ], 404);
                return;
            }
            
            $this->importService->reset();
            $resultado = $this->importService->importarProcedimentos($arquivo);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Importação de Procedimentos concluída com sucesso',
                'data' => $resultado
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao importar Procedimentos: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Importa Relacionamentos
     */
    public function importarRelacionamentos()
    {
        AuthMiddleware::handle();
        
        try {
            $arquivo = __DIR__ . '/../../temp/rl_procedimento_cid.txt';
            
            if (!file_exists($arquivo)) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Arquivo rl_procedimento_cid.txt não encontrado na pasta temp/'
                ], 404);
                return;
            }
            
            $this->importService->reset();
            $resultado = $this->importService->importarRelacionamentos($arquivo);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Importação de Relacionamentos concluída com sucesso',
                'data' => $resultado
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao importar Relacionamentos: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Importa tudo em sequência (CIDs -> Procedimentos -> Relacionamentos)
     */
    public function importarTudo()
    {
        AuthMiddleware::handle();
        
        try {
            $resultados = [];
            $this->importService->reset();
            
            // 1. Importar CIDs
            $arquivoCids = __DIR__ . '/../../temp/tb_cid.txt';
            if (file_exists($arquivoCids)) {
                $resultados['cids'] = $this->importService->importarCids($arquivoCids);
            } else {
                $resultados['cids'] = ['sucesso' => false, 'erro' => 'Arquivo não encontrado'];
            }
            
            // 2. Importar Procedimentos
            $arquivoProcedimentos = __DIR__ . '/../../temp/tb_procedimento.txt';
            if (file_exists($arquivoProcedimentos)) {
                $resultados['procedimentos'] = $this->importService->importarProcedimentos($arquivoProcedimentos);
            } else {
                $resultados['procedimentos'] = ['sucesso' => false, 'erro' => 'Arquivo não encontrado'];
            }
            
            // 3. Importar Relacionamentos
            $arquivoRelacionamentos = __DIR__ . '/../../temp/rl_procedimento_cid.txt';
            if (file_exists($arquivoRelacionamentos)) {
                $resultados['relacionamentos'] = $this->importService->importarRelacionamentos($arquivoRelacionamentos);
            } else {
                $resultados['relacionamentos'] = ['sucesso' => false, 'erro' => 'Arquivo não encontrado'];
            }
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Importação completa concluída',
                'data' => $resultados,
                'stats' => $this->importService->getStats()
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao importar dados: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Faz upload de arquivo SIGTAP
     */
    public function upload()
    {
        AuthMiddleware::handle();
        
        try {
            if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Erro no upload do arquivo'
                ], 400);
                return;
            }
            
            $arquivo = $_FILES['arquivo'];
            $tipo = $_POST['tipo'] ?? '';
            
            // Validar tipo
            $tiposValidos = ['cids', 'procedimentos', 'relacionamentos'];
            if (!in_array($tipo, $tiposValidos)) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Tipo de arquivo inválido'
                ], 400);
                return;
            }
            
            // Validar extensão
            $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
            if ($extensao !== 'txt') {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Apenas arquivos .txt são permitidos'
                ], 400);
                return;
            }
            
            // Validar tamanho (máximo 100MB)
            $tamanhoMaximo = 100 * 1024 * 1024; // 100MB
            if ($arquivo['size'] > $tamanhoMaximo) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Arquivo muito grande. Tamanho máximo: 100MB'
                ], 400);
                return;
            }
            
            // Definir nome do arquivo de destino
            $nomesArquivos = [
                'cids' => 'tb_cid.txt',
                'procedimentos' => 'tb_procedimento.txt',
                'relacionamentos' => 'rl_procedimento_cid.txt'
            ];
            
            $nomeDestino = $nomesArquivos[$tipo];
            $caminhoDestino = __DIR__ . '/../../temp/' . $nomeDestino;
            $caminhoBackup = __DIR__ . '/../../temp/uploads/' . $nomeDestino . '.' . date('YmdHis') . '.backup';
            
            // Criar backup do arquivo existente se houver
            if (file_exists($caminhoDestino)) {
                if (!copy($caminhoDestino, $caminhoBackup)) {
                    $this->jsonResponse([
                        'success' => false,
                        'message' => 'Erro ao criar backup do arquivo existente'
                    ], 500);
                    return;
                }
            }
            
            // Mover arquivo para destino
            if (!move_uploaded_file($arquivo['tmp_name'], $caminhoDestino)) {
                // Restaurar backup em caso de erro
                if (file_exists($caminhoBackup)) {
                    copy($caminhoBackup, $caminhoDestino);
                }
                
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Erro ao salvar arquivo'
                ], 500);
                return;
            }
            
            // Obter informações do arquivo
            $tamanho = filesize($caminhoDestino);
            $dataModificacao = date('d/m/Y H:i:s', filemtime($caminhoDestino));
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Arquivo enviado e substituído com sucesso',
                'data' => [
                    'nome' => $nomeDestino,
                    'tamanho' => $tamanho,
                    'data_modificacao' => $dataModificacao,
                    'backup_criado' => file_exists($caminhoBackup)
                ]
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao fazer upload: ' . $e->getMessage()
            ], 500);
        }
    }
}

