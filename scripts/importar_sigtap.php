<?php
/**
 * Script CLI para importação de arquivos SIGTAP
 * 
 * Uso:
 *   php scripts/importar_sigtap.php [cids|procedimentos|relacionamentos|tudo]
 * 
 * Exemplos:
 *   php scripts/importar_sigtap.php cids
 *   php scripts/importar_sigtap.php tudo
 */

// Verificar se está sendo executado via CLI
if (php_sapi_name() !== 'cli') {
    die("Este script só pode ser executado via linha de comando (CLI).\n");
}

// Carregar classes manualmente (projeto não usa Composer)
require_once __DIR__ . '/../src/Database/Database.php';
require_once __DIR__ . '/../src/Services/SigtapImportService.php';

use App\Services\SigtapImportService;

// Obter tipo de importação do argumento
$tipo = $argv[1] ?? 'tudo';

$tiposValidos = ['cids', 'procedimentos', 'relacionamentos', 'tudo'];
if (!in_array($tipo, $tiposValidos)) {
    echo "Tipo inválido. Use: " . implode('|', $tiposValidos) . "\n";
    exit(1);
}

$basePath = __DIR__ . '/../temp/';
$service = new SigtapImportService();

echo "========================================\n";
echo "Importador SIGTAP - Sistema APAC\n";
echo "========================================\n\n";

try {
    $inicio = microtime(true);
    
    switch ($tipo) {
        case 'cids':
            echo "Importando CIDs...\n";
            $arquivo = $basePath . 'tb_cid.txt';
            if (!file_exists($arquivo)) {
                die("Erro: Arquivo não encontrado: {$arquivo}\n");
            }
            $resultado = $service->importarCids($arquivo);
            echo "✓ CIDs importados: {$resultado['importados']}\n";
            echo "✓ CIDs atualizados: {$resultado['atualizados']}\n";
            if ($resultado['erros'] > 0) {
                echo "⚠ Erros: {$resultado['erros']}\n";
            }
            break;
            
        case 'procedimentos':
            echo "Importando Procedimentos...\n";
            $arquivo = $basePath . 'tb_procedimento.txt';
            if (!file_exists($arquivo)) {
                die("Erro: Arquivo não encontrado: {$arquivo}\n");
            }
            $resultado = $service->importarProcedimentos($arquivo);
            echo "✓ Procedimentos importados: {$resultado['importados']}\n";
            echo "✓ Procedimentos atualizados: {$resultado['atualizados']}\n";
            if ($resultado['erros'] > 0) {
                echo "⚠ Erros: {$resultado['erros']}\n";
            }
            break;
            
        case 'relacionamentos':
            echo "Importando Relacionamentos...\n";
            $arquivo = $basePath . 'rl_procedimento_cid.txt';
            if (!file_exists($arquivo)) {
                die("Erro: Arquivo não encontrado: {$arquivo}\n");
            }
            $resultado = $service->importarRelacionamentos($arquivo);
            echo "✓ Relacionamentos importados: {$resultado['importados']}\n";
            if ($resultado['erros'] > 0) {
                echo "⚠ Erros: {$resultado['erros']}\n";
            }
            break;
            
        case 'tudo':
            echo "Importando todos os arquivos...\n\n";
            
            // 1. CIDs
            echo "[1/3] Importando CIDs...\n";
            $arquivoCids = $basePath . 'tb_cid.txt';
            if (file_exists($arquivoCids)) {
                $resultadoCids = $service->importarCids($arquivoCids);
                echo "  ✓ CIDs importados: {$resultadoCids['importados']}\n";
                echo "  ✓ CIDs atualizados: {$resultadoCids['atualizados']}\n";
                if ($resultadoCids['erros'] > 0) {
                    echo "  ⚠ Erros: {$resultadoCids['erros']}\n";
                }
            } else {
                echo "  ⚠ Arquivo não encontrado: {$arquivoCids}\n";
            }
            
            echo "\n";
            
            // 2. Procedimentos
            echo "[2/3] Importando Procedimentos...\n";
            $arquivoProcedimentos = $basePath . 'tb_procedimento.txt';
            if (file_exists($arquivoProcedimentos)) {
                $resultadoProcedimentos = $service->importarProcedimentos($arquivoProcedimentos);
                echo "  ✓ Procedimentos importados: {$resultadoProcedimentos['importados']}\n";
                echo "  ✓ Procedimentos atualizados: {$resultadoProcedimentos['atualizados']}\n";
                if ($resultadoProcedimentos['erros'] > 0) {
                    echo "  ⚠ Erros: {$resultadoProcedimentos['erros']}\n";
                }
            } else {
                echo "  ⚠ Arquivo não encontrado: {$arquivoProcedimentos}\n";
            }
            
            echo "\n";
            
            // 3. Relacionamentos
            echo "[3/3] Importando Relacionamentos...\n";
            $arquivoRelacionamentos = $basePath . 'rl_procedimento_cid.txt';
            if (file_exists($arquivoRelacionamentos)) {
                $resultadoRelacionamentos = $service->importarRelacionamentos($arquivoRelacionamentos);
                echo "  ✓ Relacionamentos importados: {$resultadoRelacionamentos['importados']}\n";
                if ($resultadoRelacionamentos['erros'] > 0) {
                    echo "  ⚠ Erros: {$resultadoRelacionamentos['erros']}\n";
                }
            } else {
                echo "  ⚠ Arquivo não encontrado: {$arquivoRelacionamentos}\n";
            }
            break;
    }
    
    $fim = microtime(true);
    $tempo = round($fim - $inicio, 2);
    
    echo "\n========================================\n";
    echo "Importação concluída em {$tempo} segundos\n";
    echo "========================================\n";
    
} catch (\Exception $e) {
    echo "\n❌ ERRO: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

