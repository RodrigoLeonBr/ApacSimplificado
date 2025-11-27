<?php
/**
 * Script de teste para validar o parsing dos arquivos SIGTAP
 * 
 * Uso:
 *   php scripts/testar_importacao.php [cids|procedimentos|relacionamentos]
 * 
 * Este script lê algumas linhas dos arquivos e mostra como estão sendo parseadas
 */

// Carregar classes manualmente
require_once __DIR__ . '/../src/Database/Database.php';
require_once __DIR__ . '/../src/Services/SigtapImportService.php';

use App\Services\SigtapImportService;

$tipo = $argv[1] ?? 'cids';
$basePath = __DIR__ . '/../temp/';

echo "========================================\n";
echo "Teste de Parsing - Arquivos SIGTAP\n";
echo "========================================\n\n";

$service = new SigtapImportService();

switch ($tipo) {
    case 'cids':
        $arquivo = $basePath . 'tb_cid.txt';
        if (!file_exists($arquivo)) {
            die("Arquivo não encontrado: {$arquivo}\n");
        }
        
        echo "Testando parsing de CIDs...\n";
        echo "Arquivo: {$arquivo}\n\n";
        
        $handle = fopen($arquivo, 'r');
        $linhaNum = 0;
        $maxLinhas = 5;
        
        while (($line = fgets($handle)) !== false && $linhaNum < $maxLinhas) {
            $line = rtrim($line, "\r\n");
            $linhaNum++;
            
            echo "Linha {$linhaNum} (tamanho: " . strlen($line) . " caracteres):\n";
            echo "  Conteúdo: " . substr($line, 0, 80) . "...\n";
            
            if (strlen($line) >= 4) {
                $codigo = trim(substr($line, 0, 4));
                $descricao = trim(substr($line, 4, 100));
                $tp_agravo = strlen($line) >= 105 ? substr($line, 104, 1) : 'N/A';
                $tp_sexo = strlen($line) >= 106 ? substr($line, 105, 1) : 'N/A';
                $tp_estadio = strlen($line) >= 107 ? substr($line, 106, 1) : 'N/A';
                $vl_campos_irradiados = strlen($line) >= 111 ? substr($line, 107, 4) : 'N/A';
                
                echo "  CO_CID (1-4): '{$codigo}'\n";
                echo "  NO_CID (5-104): '" . substr($descricao, 0, 50) . "...'\n";
                echo "  TP_AGRAVO (105): '{$tp_agravo}'\n";
                echo "  TP_SEXO (106): '{$tp_sexo}'\n";
                echo "  TP_ESTADIO (107): '{$tp_estadio}'\n";
                echo "  VL_CAMPOS_IRRADIADOS (108-111): '{$vl_campos_irradiados}'\n";
            }
            echo "\n";
        }
        fclose($handle);
        break;
        
    case 'procedimentos':
        $arquivo = $basePath . 'tb_procedimento.txt';
        if (!file_exists($arquivo)) {
            die("Arquivo não encontrado: {$arquivo}\n");
        }
        
        echo "Testando parsing de Procedimentos...\n";
        echo "Arquivo: {$arquivo}\n\n";
        
        $handle = fopen($arquivo, 'r');
        $linhaNum = 0;
        $maxLinhas = 3;
        
        while (($line = fgets($handle)) !== false && $linhaNum < $maxLinhas) {
            $line = rtrim($line, "\r\n");
            $linhaNum++;
            
            echo "Linha {$linhaNum} (tamanho: " . strlen($line) . " caracteres):\n";
            echo "  Conteúdo: " . substr($line, 0, 80) . "...\n";
            
            if (strlen($line) >= 10) {
                $codigo = trim(substr($line, 0, 10));
                $descricao = trim(substr($line, 10, 250));
                $tp_complexidade = strlen($line) >= 261 ? substr($line, 260, 1) : 'N/A';
                $tp_sexo = strlen($line) >= 262 ? substr($line, 261, 1) : 'N/A';
                $qt_maxima_execucao = strlen($line) >= 266 ? substr($line, 262, 4) : 'N/A';
                $vl_sh = strlen($line) >= 292 ? substr($line, 282, 10) : 'N/A';
                $vl_sa = strlen($line) >= 302 ? substr($line, 292, 10) : 'N/A';
                $vl_sp = strlen($line) >= 312 ? substr($line, 302, 10) : 'N/A';
                $dt_competencia = strlen($line) >= 326 ? substr($line, 320, 6) : 'N/A';
                
                echo "  CO_PROCEDIMENTO (1-10): '{$codigo}'\n";
                echo "  NO_PROCEDIMENTO (11-260): '" . substr($descricao, 0, 50) . "...'\n";
                echo "  TP_COMPLEXIDADE (261): '{$tp_complexidade}'\n";
                echo "  TP_SEXO (262): '{$tp_sexo}'\n";
                echo "  QT_MAXIMA_EXECUCAO (263-266): '{$qt_maxima_execucao}'\n";
                echo "  VL_SH (283-292): '{$vl_sh}' = " . ($vl_sh !== 'N/A' ? number_format($vl_sh / 100, 2, ',', '.') : 'N/A') . "\n";
                echo "  VL_SA (293-302): '{$vl_sa}' = " . ($vl_sa !== 'N/A' ? number_format($vl_sa / 100, 2, ',', '.') : 'N/A') . "\n";
                echo "  VL_SP (303-312): '{$vl_sp}' = " . ($vl_sp !== 'N/A' ? number_format($vl_sp / 100, 2, ',', '.') : 'N/A') . "\n";
                echo "  DT_COMPETENCIA (321-326): '{$dt_competencia}'\n";
            }
            echo "\n";
        }
        fclose($handle);
        break;
        
    case 'relacionamentos':
        $arquivo = $basePath . 'rl_procedimento_cid.txt';
        if (!file_exists($arquivo)) {
            die("Arquivo não encontrado: {$arquivo}\n");
        }
        
        echo "Testando parsing de Relacionamentos...\n";
        echo "Arquivo: {$arquivo}\n\n";
        
        $handle = fopen($arquivo, 'r');
        $linhaNum = 0;
        $maxLinhas = 5;
        
        while (($line = fgets($handle)) !== false && $linhaNum < $maxLinhas) {
            $line = rtrim($line, "\r\n");
            $linhaNum++;
            
            echo "Linha {$linhaNum} (tamanho: " . strlen($line) . " caracteres):\n";
            echo "  Conteúdo completo: '{$line}'\n";
            
            if (strlen($line) >= 15) {
                $co_procedimento = trim(substr($line, 0, 10));
                $co_cid = trim(substr($line, 10, 4));
                $st_principal = substr($line, 14, 1);
                $dt_competencia = strlen($line) >= 21 ? substr($line, 15, 6) : 'N/A';
                
                echo "  CO_PROCEDIMENTO (1-10): '{$co_procedimento}'\n";
                echo "  CO_CID (11-14): '{$co_cid}'\n";
                echo "  ST_PRINCIPAL (15): '{$st_principal}'\n";
                echo "  DT_COMPETENCIA (16-21): '{$dt_competencia}'\n";
            }
            echo "\n";
        }
        fclose($handle);
        break;
        
    default:
        echo "Tipo inválido. Use: cids, procedimentos ou relacionamentos\n";
        exit(1);
}

echo "========================================\n";
echo "Teste concluído!\n";
echo "========================================\n";

