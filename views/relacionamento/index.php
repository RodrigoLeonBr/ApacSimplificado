<?php
use App\Utils\UrlHelper;

$title = 'Relacionamentos Procedimento x CID - Sistema APAC';
ob_start();
?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Relacionamentos Procedimento x CID</h1>
            <p class="text-gray-600">Gerencie os relacionamentos entre procedimentos e CIDs</p>
        </div>
        <a href="<?= UrlHelper::url('/relacionamento/create') ?>" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Novo Relacionamento
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" action="<?= UrlHelper::url('/relacionamento') ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
            <input 
                type="text" 
                name="search" 
                value="<?= htmlspecialchars($search ?? '') ?>"
                placeholder="Código ou descrição..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            >
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Procedimento</label>
            <select name="procedimento_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Todos</option>
                <?php foreach ($procedimentos as $proc): ?>
                    <option value="<?= $proc['id'] ?>" <?= ($filtroProcedimento ?? '') == $proc['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($proc['codigo_procedimento'] . ' - ' . substr($proc['descricao'], 0, 30)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">CID</label>
            <select name="cid_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Todos</option>
                <?php foreach ($cids as $cid): ?>
                    <option value="<?= $cid['id'] ?>" <?= ($filtroCid ?? '') == $cid['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cid['codigo'] . ' - ' . substr($cid['descricao'], 0, 30)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Principal</label>
            <select name="st_principal" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Todos</option>
                <option value="S" <?= ($filtroPrincipal ?? '') === 'S' ? 'selected' : '' ?>>Sim</option>
                <option value="N" <?= ($filtroPrincipal ?? '') === 'N' ? 'selected' : '' ?>>Não</option>
            </select>
        </div>
        <div class="md:col-span-4 flex gap-2">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Filtrar
            </button>
            <a href="<?= UrlHelper::url('/relacionamento') ?>" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                Limpar
            </a>
        </div>
    </form>
</div>

<!-- Lista -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <p class="text-sm text-gray-600">
            Total: <span class="font-medium"><?= $totalRelacionamentos ?></span> relacionamento(s)
        </p>
    </div>
    
    <?php if (empty($relacionamentos)): ?>
        <div class="p-12 text-center">
            <p class="text-gray-500">Nenhum relacionamento encontrado.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Procedimento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Principal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Competência</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($relacionamentos as $rel): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($rel['codigo_procedimento']) ?></div>
                            <div class="text-sm text-gray-500"><?= htmlspecialchars(substr($rel['procedimento_descricao'], 0, 50)) ?>...</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($rel['cid_codigo']) ?></div>
                            <div class="text-sm text-gray-500"><?= htmlspecialchars(substr($rel['cid_descricao'], 0, 50)) ?>...</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $rel['st_principal'] === 'S' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                <?= $rel['st_principal'] === 'S' ? 'Sim' : 'Não' ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php
                            $dt = $rel['dt_competencia'] ?? '';
                            if ($dt && strlen($dt) === 6) {
                                echo htmlspecialchars(substr($dt, 4, 2) . '/' . substr($dt, 0, 4));
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="<?= UrlHelper::url('/relacionamento/' . $rel['id']) ?>" class="text-blue-600 hover:text-blue-900 mr-4">Ver</a>
                            <a href="<?= UrlHelper::url('/relacionamento/' . $rel['id'] . '/edit') ?>" class="text-green-600 hover:text-green-900 mr-4">Editar</a>
                            <button onclick="confirmarExclusao(<?= $rel['id'] ?>)" class="text-red-600 hover:text-red-900">Excluir</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Paginação -->
        <?php if ($totalPages > 1): ?>
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Página <span class="font-medium"><?= $currentPage ?></span> de <span class="font-medium"><?= $totalPages ?></span>
            </div>
            <div class="flex gap-2">
                <?php if ($currentPage > 1): ?>
                    <a href="<?= UrlHelper::url('/relacionamento?page=' . ($currentPage - 1) . '&search=' . urlencode($search ?? '') . '&procedimento_id=' . urlencode($filtroProcedimento ?? '') . '&cid_id=' . urlencode($filtroCid ?? '') . '&st_principal=' . urlencode($filtroPrincipal ?? '')) ?>" 
                       class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Anterior
                    </a>
                <?php endif; ?>
                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?= UrlHelper::url('/relacionamento?page=' . ($currentPage + 1) . '&search=' . urlencode($search ?? '') . '&procedimento_id=' . urlencode($filtroProcedimento ?? '') . '&cid_id=' . urlencode($filtroCid ?? '') . '&st_principal=' . urlencode($filtroPrincipal ?? '')) ?>" 
                       class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Próxima
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
function confirmarExclusao(id) {
    if (confirm('Tem certeza que deseja excluir este relacionamento? Esta ação não pode ser desfeita.')) {
        fetch('<?= UrlHelper::url('/relacionamento/') ?>' + id + '/delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Erro ao excluir relacionamento: ' + data.message);
            }
        })
        .catch(error => {
            alert('Erro ao excluir relacionamento');
            console.error(error);
        });
    }
}
</script>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>

