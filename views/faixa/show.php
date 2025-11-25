<?php
use App\Utils\UrlHelper;

$title = 'Detalhes da Faixa - Sistema APAC';
ob_start();
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Detalhes da Faixa #<?= $faixa['id'] ?></h1>
    <p class="text-gray-600">Informações detalhadas da faixa de APAC</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Informações da Faixa</h2>
        
        <div class="space-y-3">
            <div class="flex justify-between border-b pb-2">
                <span class="font-medium text-gray-700">ID:</span>
                <span class="text-gray-900"><?= htmlspecialchars($faixa['id']) ?></span>
            </div>
            
            <div class="flex justify-between border-b pb-2">
                <span class="font-medium text-gray-700">Número Inicial:</span>
                <span class="text-gray-900 font-mono"><?= htmlspecialchars($faixa['numero_inicial']) ?></span>
            </div>
            
            <div class="flex justify-between border-b pb-2">
                <span class="font-medium text-gray-700">Número Final:</span>
                <span class="text-gray-900 font-mono"><?= htmlspecialchars($faixa['numero_final']) ?></span>
            </div>
            
            <div class="flex justify-between border-b pb-2">
                <span class="font-medium text-gray-700">Quantidade Total:</span>
                <span class="text-gray-900"><?= number_format($faixa['total']) ?></span>
            </div>
            
            <div class="flex justify-between border-b pb-2">
                <span class="font-medium text-gray-700">APACs Emitidas:</span>
                <span class="text-gray-900"><?= number_format($faixa['utilizados']) ?></span>
            </div>
            
            <div class="flex justify-between border-b pb-2">
                <span class="font-medium text-gray-700">APACs Disponíveis:</span>
                <span class="text-gray-900"><?= number_format($faixa['total'] - $faixa['utilizados']) ?></span>
            </div>
            
            <div class="flex justify-between border-b pb-2">
                <span class="font-medium text-gray-700">Status:</span>
                <span>
                    <?php
                    $statusClass = [
                        'disponivel' => 'bg-green-100 text-green-800',
                        'em_uso' => 'bg-yellow-100 text-yellow-800',
                        'esgotada' => 'bg-red-100 text-red-800'
                    ][$faixa['status']] ?? 'bg-gray-100 text-gray-800';
                    
                    $statusLabel = [
                        'disponivel' => 'Disponível',
                        'em_uso' => 'Em Uso',
                        'esgotada' => 'Esgotada'
                    ][$faixa['status']] ?? $faixa['status'];
                    ?>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $statusClass ?>">
                        <?= htmlspecialchars($statusLabel) ?>
                    </span>
                </span>
            </div>
            
            <div class="flex justify-between border-b pb-2">
                <span class="font-medium text-gray-700">Data de Criação:</span>
                <span class="text-gray-900"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($faixa['criada_em']))) ?></span>
            </div>
        </div>
        
        <div class="mt-6 flex space-x-3">
            <a href="<?= UrlHelper::url('/faixas') ?>" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white text-center py-2 px-4 rounded">
                Voltar
            </a>
            <?php if ($faixa['status'] != 'esgotada'): ?>
                <a href="<?= UrlHelper::url('/apacs/create') ?>" class="flex-1 bg-green-500 hover:bg-green-600 text-white text-center py-2 px-4 rounded">
                    Emitir APAC
                </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Progresso de Uso</h2>
        
        <div class="mb-6">
            <div class="flex justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Percentual de Uso</span>
                <span class="text-sm font-medium text-gray-700">
                    <?= htmlspecialchars(round(($faixa['utilizados'] / $faixa['total']) * 100, 2)) ?>%
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4">
                <div 
                    class="bg-blue-600 h-4 rounded-full transition-all" 
                    style="width: <?= round(($faixa['utilizados'] / $faixa['total']) * 100, 2) ?>%"
                ></div>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mt-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Total</p>
                <p class="text-2xl font-bold text-blue-600"><?= number_format($faixa['total']) ?></p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Emitidas</p>
                <p class="text-2xl font-bold text-green-600"><?= number_format($faixa['utilizados']) ?></p>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Disponíveis</p>
                <p class="text-2xl font-bold text-yellow-600"><?= number_format($faixa['total'] - $faixa['utilizados']) ?></p>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">% Uso</p>
                <p class="text-2xl font-bold text-purple-600"><?= htmlspecialchars(round(($faixa['utilizados'] / $faixa['total']) * 100, 2)) ?>%</p>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>
