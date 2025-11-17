<?php

require_once __DIR__ . '/config/constants.php';

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/src/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

use App\Services\DigitoVerificadorService;

$dvService = new DigitoVerificadorService();

echo "=== Teste de Cálculo de Dígito Verificador ===\n\n";

$testCases = [
    "3525807281111" => "7",
    "3525807281112" => "8",
    "3525807281113" => "9",
    "3525807281114" => "0",
    "3525807281115" => "0",
    "3525807281116" => "1",
    "3525807281117" => "2",
    "3525807281118" => "3",
    "3525807281119" => "4",
    "3525807281120" => "5",
    "3525807281121" => "6",
    "3525807281122" => "7",
];

$passed = 0;
$failed = 0;

foreach ($testCases as $numero13 => $expectedDv) {
    try {
        $calculatedDv = $dvService->calcularDV($numero13);
        $status = ($calculatedDv === $expectedDv) ? "✓ SUCESSO" : "✗ FALHA";
        
        if ($calculatedDv === $expectedDv) {
            $passed++;
        } else {
            $failed++;
        }
        
        echo sprintf(
            "Número: %s | DV Calculado: %s | DV Esperado: %s | %s\n",
            $numero13,
            $calculatedDv,
            $expectedDv,
            $status
        );
    } catch (\InvalidArgumentException $e) {
        echo "Erro ao calcular DV para {$numero13}: " . $e->getMessage() . "\n";
        $failed++;
    }
}

echo "\n=== Teste de Validação de APAC Completa ===\n\n";

$fullApacTestCases = [
    "35258072811117" => true,
    "35258072811128" => true,
    "35258072811139" => true,
    "35258072811140" => true,
    "35258072811150" => true,
    "35258072811161" => true,
    "35258072811172" => true,
    "35258072811183" => true,
    "35258072811194" => true,
    "35258072811205" => true,
    "35258072811216" => true,
    "35258072811227" => true,
    "35258072811118" => false,
    "12345678901234" => false,
];

foreach ($fullApacTestCases as $apacCompleta => $expectedResult) {
    try {
        $isValid = $dvService->validarAPACCompleta($apacCompleta);
        $status = ($isValid === $expectedResult) ? "✓ SUCESSO" : "✗ FALHA";
        
        if ($isValid === $expectedResult) {
            $passed++;
        } else {
            $failed++;
        }
        
        echo sprintf(
            "APAC: %s | Válida: %s | Esperado: %s | %s\n",
            $apacCompleta,
            $isValid ? "Sim" : "Não",
            $expectedResult ? "Sim" : "Não",
            $status
        );
    } catch (\InvalidArgumentException $e) {
        echo "Erro ao validar APAC {$apacCompleta}: " . $e->getMessage() . "\n";
        $failed++;
    }
}

echo "\n=== Teste de Geração de Sequência de Faixa ===\n\n";

try {
    $faixaInicio = "3525807281111";
    $faixaFim = "3525807281116";
    $sequenciaGerada = $dvService->gerarSequenciaFaixa($faixaInicio, $faixaFim);
    
    echo "Faixa de {$faixaInicio} a {$faixaFim}:\n";
    foreach ($sequenciaGerada as $apac) {
        echo "  - {$apac}\n";
        $passed++;
    }
} catch (\InvalidArgumentException $e) {
    echo "Erro ao gerar sequência de faixa: " . $e->getMessage() . "\n";
    $failed++;
}

echo "\n=== Resumo dos Testes ===\n";
echo "Testes Passados: {$passed}\n";
echo "Testes Falhados: {$failed}\n";
echo "Total: " . ($passed + $failed) . "\n";

if ($failed === 0) {
    echo "\n✓ TODOS OS TESTES PASSARAM!\n";
} else {
    echo "\n✗ ALGUNS TESTES FALHARAM!\n";
}
