<?php
use App\Utils\UrlHelper;

$title = 'Faixas de APAC - Sistema APAC';
ob_start();
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Faixas de APAC</h1>
        <p class="text-gray-600">Gerenciamento de faixas de números</p>
    </div>
    <a href="<?= UrlHelper::url('/faixas/create') ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium">
        Nova Faixa
    </a>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inicial</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Final</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emitidas</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% Uso</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (empty($faixas)): ?>
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">Nenhuma faixa cadastrada</td>
                </tr>
            <?php else: ?>
                <?php foreach ($faixas as $faixa): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($faixa['id']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900"><?= htmlspecialchars($faixa['numero_inicial']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900"><?= htmlspecialchars($faixa['numero_final']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= number_format($faixa['total']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= number_format($faixa['utilizados']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: <?= $faixa['percentual_uso'] ?>%"></div>
                                </div>
                                <span><?= htmlspecialchars($faixa['percentual_uso']) ?>%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
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
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                <?= htmlspecialchars($statusLabel) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="<?= UrlHelper::url('/faixas/' . $faixa['id']) ?>" class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                            <?php if ($faixa['utilizados'] == 0): ?>
                                <form method="POST" action="<?= UrlHelper::url('/faixas/' . $faixa['id'] . '/delete') ?>" style="display: inline;">
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Deseja realmente excluir esta faixa?')">Excluir</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>
