<?php
$title = 'APACs Emitidas - Sistema APAC';
ob_start();
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">APACs Emitidas</h1>
        <p class="text-gray-600">Listagem de todas as APACs emitidas</p>
    </div>
    <a href="/apacs/create" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium">
        Emitir Nova APAC
    </a>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número APAC (14 dig)</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DV</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Faixa</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emitido Por</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Emissão</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (empty($apacs)): ?>
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">Nenhuma APAC emitida</td>
                </tr>
            <?php else: ?>
                <?php foreach ($apacs as $apac): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($apac['id']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold text-blue-600"><?= htmlspecialchars($apac['numero_apac']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900"><?= htmlspecialchars($apac['digito_verificador']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#<?= htmlspecialchars($apac['faixa_id']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($apac['usuario_nome'] ?? 'Sistema') ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($apac['criada_em']))) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($apac['impresso']): ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Impressa
                                </span>
                            <?php else: ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pendente
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <?php if (!$apac['impresso']): ?>
                                <form method="POST" action="/apacs/<?= htmlspecialchars($apac['id']) ?>/imprimir" style="display: inline;">
                                    <button type="submit" class="text-green-600 hover:text-green-900">Marcar Impressa</button>
                                </form>
                            <?php else: ?>
                                <span class="text-gray-400">-</span>
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
