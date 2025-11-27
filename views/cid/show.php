<?php
use App\Utils\UrlHelper;

$title = 'Detalhes do CID - Sistema APAC';
ob_start();

function formatarData($data) {
    if (!$data) return '-';
    $partes = explode('-', $data);
    if (count($partes) === 3) {
        return $partes[2] . '/' . $partes[1] . '/' . $partes[0];
    }
    return $data;
}
?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Detalhes do CID</h1>
            <p class="text-gray-600">Visualize as informações completas do CID</p>
        </div>
        <div class="flex gap-3">
            <a href="<?= UrlHelper::url('/cid/' . $cid['id'] . '/edit') ?>" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar
            </a>
            <a href="<?= UrlHelper::url('/cid') ?>" class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition">
                Voltar
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Informações Principais -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Dados do CID</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Código</label>
                    <p class="text-gray-900 font-medium text-lg"><?= htmlspecialchars($cid['codigo'] ?? '-') ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Descrição</label>
                    <p class="text-gray-900"><?= htmlspecialchars($cid['descricao'] ?? '-') ?></p>
                </div>
            </div>
        </div>
        
        <!-- Informações SIGTAP -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Informações SIGTAP</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Tipo de Agravo</label>
                    <p class="text-gray-900">
                        <?php
                        $agravo = $cid['tp_agravo'] ?? '';
                        $agravos = ['0' => 'Sem Agravo', '1' => 'Agravo de notificação', '2' => 'Agravo de bloqueio'];
                        echo htmlspecialchars($agravo !== '' ? ($agravo . ' - ' . ($agravos[$agravo] ?? '')) : '-');
                        ?>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Tipo de Sexo</label>
                    <p class="text-gray-900">
                        <?php
                        $sexo = $cid['tp_sexo'] ?? '';
                        $sexos = ['M' => 'Masculino', 'F' => 'Feminino', 'I' => 'Indiferente/Ambos'];
                        echo htmlspecialchars($sexo !== '' ? ($sexo . ' - ' . ($sexos[$sexo] ?? '')) : '-');
                        ?>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Tipo de Estádio</label>
                    <p class="text-gray-900">
                        <?php
                        $estadio = $cid['tp_estadio'] ?? '';
                        $estadios = ['S' => 'Sim', 'N' => 'Não'];
                        echo htmlspecialchars($estadio !== '' ? ($estadio . ' - ' . ($estadios[$estadio] ?? '')) : '-');
                        ?>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Valor Campos Irradiados</label>
                    <p class="text-gray-900"><?= htmlspecialchars($cid['vl_campos_irradiados'] ?? '0') ?></p>
                </div>
            </div>
        </div>
        
        <!-- Relacionamentos com Procedimentos -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800">Procedimentos Relacionados</h2>
                <a href="<?= UrlHelper::url('/relacionamento/create?cid_id=' . $cid['id']) ?>" class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    + Adicionar Procedimento
                </a>
            </div>
            <?php
            $cidModel = new \App\Models\Cid();
            $relacionamentos = $cidModel->findRelacionamentosProcedimento($cid['id']);
            if (empty($relacionamentos)):
            ?>
                <p class="text-gray-500 text-sm">Nenhum procedimento relacionado encontrado.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Principal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($relacionamentos as $rel): ?>
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900"><?= htmlspecialchars($rel['codigo_procedimento']) ?></td>
                                <td class="px-4 py-3 text-sm text-gray-600"><?= htmlspecialchars(substr($rel['procedimento_descricao'], 0, 50)) ?>...</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-2 py-1 text-xs rounded-full <?= $rel['st_principal'] === 'S' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                        <?= $rel['st_principal'] === 'S' ? 'Sim' : 'Não' ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="<?= UrlHelper::url('/relacionamento/' . $rel['id']) ?>" class="text-blue-600 hover:text-blue-800">Ver</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sidebar de Ações -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Ações</h2>
            <div class="space-y-3">
                <a href="<?= UrlHelper::url('/cid/' . $cid['id'] . '/edit') ?>" class="block w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-center rounded-lg transition">
                    Editar CID
                </a>
                <button onclick="confirmarExclusao()" class="block w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-center rounded-lg transition">
                    Excluir CID
                </button>
                <a href="<?= UrlHelper::url('/cid') ?>" class="block w-full px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-center rounded-lg transition">
                    Voltar para Lista
                </a>
            </div>
            
            <div class="mt-6 pt-6 border-t">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Informações do Sistema</h3>
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>ID:</span>
                        <span class="font-medium"><?= $cid['id'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Cadastrado em:</span>
                        <span class="font-medium"><?= formatarData(substr($cid['criada_em'] ?? '', 0, 10)) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarExclusao() {
    if (confirm('Tem certeza que deseja excluir este CID? Esta ação não pode ser desfeita.')) {
        fetch('<?= UrlHelper::url('/cid/' . $cid['id'] . '/delete') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '<?= UrlHelper::url('/cid') ?>';
            } else {
                alert('Erro ao excluir CID: ' + data.message);
            }
        })
        .catch(error => {
            alert('Erro ao excluir CID');
            console.error(error);
        });
    }
}
</script>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>

