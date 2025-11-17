<?php
$title = 'Dashboard - Sistema APAC';
ob_start();
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
    <p class="text-gray-600">Bem-vindo ao Sistema de Emissão de APAC</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total de Faixas</p>
                <p class="text-3xl font-bold text-blue-600"><?= $stats['total_faixas'] ?></p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Faixas Disponíveis</p>
                <p class="text-3xl font-bold text-green-600"><?= $stats['faixas_disponiveis'] ?></p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">APACs Emitidas</p>
                <p class="text-3xl font-bold text-purple-600"><?= $stats['total_apacs'] ?></p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">APACs Impressas</p>
                <p class="text-3xl font-bold text-orange-600"><?= $stats['apacs_impressas'] ?></p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">APACs Recentes</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2 text-sm font-semibold text-gray-700">Número</th>
                        <th class="text-left py-2 text-sm font-semibold text-gray-700">Data</th>
                        <th class="text-left py-2 text-sm font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentApacs)): ?>
                        <tr>
                            <td colspan="3" class="text-center py-4 text-gray-500">Nenhuma APAC emitida</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentApacs as $apac): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2 text-sm"><?= htmlspecialchars($apac['numero_14dig']) ?></td>
                                <td class="py-2 text-sm"><?= date('d/m/Y H:i', strtotime($apac['data_emissao'])) ?></td>
                                <td class="py-2 text-sm">
                                    <?php if ($apac['impresso']): ?>
                                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Impressa</span>
                                    <?php else: ?>
                                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Pendente</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Log de Atividades</h2>
        <div class="space-y-3 max-h-80 overflow-y-auto">
            <?php if (empty($recentLogs)): ?>
                <p class="text-center py-4 text-gray-500">Nenhum log registrado</p>
            <?php else: ?>
                <?php foreach ($recentLogs as $log): ?>
                    <div class="border-l-4 border-blue-500 pl-4 py-2">
                        <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($log['acao']) ?></p>
                        <p class="text-xs text-gray-500">
                            <?= htmlspecialchars($log['usuario_nome'] ?? 'Sistema') ?> - 
                            <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
                        </p>
                        <?php if ($log['detalhes']): ?>
                            <p class="text-xs text-gray-600 mt-1"><?= htmlspecialchars($log['detalhes']) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>
