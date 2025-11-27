<?php
use App\Utils\UrlHelper;

$title = 'Detalhes do Procedimento - Sistema APAC';
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
            <h1 class="text-3xl font-bold text-gray-800">Detalhes do Procedimento</h1>
            <p class="text-gray-600">Visualize as informações completas do procedimento</p>
        </div>
        <div class="flex gap-3">
            <a href="<?= UrlHelper::url('/procedimento/' . $procedimento['id'] . '/edit') ?>" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar
            </a>
            <a href="<?= UrlHelper::url('/procedimento') ?>" class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition">
                Voltar
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Informações Principais -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Dados do Procedimento</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Código do Procedimento</label>
                    <p class="text-gray-900 font-medium text-lg"><?= htmlspecialchars($procedimento['codigo_procedimento'] ?? '-') ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Descrição</label>
                    <p class="text-gray-900"><?= htmlspecialchars($procedimento['descricao'] ?? '-') ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Tabela SUS</label>
                    <p class="text-gray-900"><?= htmlspecialchars($procedimento['tabela_sus'] ?? '-') ?></p>
                </div>
            </div>
        </div>
        
        <!-- Informações SIGTAP -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Informações SIGTAP</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Tipo de Complexidade</label>
                    <p class="text-gray-900">
                        <?php
                        $complexidade = $procedimento['tp_complexidade'] ?? '';
                        $complexidades = ['0' => 'Não se aplica', '1' => 'Atenção Básica', '2' => 'Média Complexidade', '3' => 'Alta Complexidade'];
                        echo htmlspecialchars($complexidade !== '' ? ($complexidade . ' - ' . ($complexidades[$complexidade] ?? '')) : '-');
                        ?>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Tipo de Sexo</label>
                    <p class="text-gray-900">
                        <?php
                        $sexo = $procedimento['tp_sexo'] ?? '';
                        $sexos = ['M' => 'Masculino', 'F' => 'Feminino', 'I' => 'Indiferente/Ambos', 'N' => 'Não se aplica'];
                        echo htmlspecialchars($sexo !== '' ? ($sexo . ' - ' . ($sexos[$sexo] ?? '')) : '-');
                        ?>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Quantidade Máxima de Execução</label>
                    <p class="text-gray-900"><?= htmlspecialchars($procedimento['qt_maxima_execucao'] ?? '-') ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Data de Competência</label>
                    <p class="text-gray-900">
                        <?php
                        $dt = $procedimento['dt_competencia'] ?? '';
                        if ($dt && strlen($dt) === 6) {
                            echo htmlspecialchars(substr($dt, 4, 2) . '/' . substr($dt, 0, 4));
                        } else {
                            echo '-';
                        }
                        ?>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Valor SH (Serviço Hospitalar)</label>
                    <p class="text-gray-900 font-medium">
                        R$ <?= isset($procedimento['vl_sh']) && $procedimento['vl_sh'] !== null ? number_format($procedimento['vl_sh'], 2, ',', '.') : '0,00' ?>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Valor SA (Serviço Ambulatorial)</label>
                    <p class="text-gray-900 font-medium">
                        R$ <?= isset($procedimento['vl_sa']) && $procedimento['vl_sa'] !== null ? number_format($procedimento['vl_sa'], 2, ',', '.') : '0,00' ?>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Valor SP (Serviço Profissional)</label>
                    <p class="text-gray-900 font-medium">
                        R$ <?= isset($procedimento['vl_sp']) && $procedimento['vl_sp'] !== null ? number_format($procedimento['vl_sp'], 2, ',', '.') : '0,00' ?>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Relacionamentos com CIDs -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800">CIDs Relacionados</h2>
                <a href="<?= UrlHelper::url('/relacionamento/create?procedimento_id=' . $procedimento['id']) ?>" class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    + Adicionar CID
                </a>
            </div>
            <?php
            $procedimentoModel = new \App\Models\Procedimento();
            $relacionamentos = $procedimentoModel->findRelacionamentosCid($procedimento['id']);
            if (empty($relacionamentos)):
            ?>
                <p class="text-gray-500 text-sm">Nenhum CID relacionado encontrado.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">CID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Principal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($relacionamentos as $rel): ?>
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900"><?= htmlspecialchars($rel['cid_codigo']) ?></td>
                                <td class="px-4 py-3 text-sm text-gray-600"><?= htmlspecialchars(substr($rel['cid_descricao'], 0, 50)) ?>...</td>
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
                <a href="<?= UrlHelper::url('/procedimento/' . $procedimento['id'] . '/edit') ?>" class="block w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-center rounded-lg transition">
                    Editar Procedimento
                </a>
                <button onclick="confirmarExclusao()" class="block w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-center rounded-lg transition">
                    Excluir Procedimento
                </button>
                <a href="<?= UrlHelper::url('/procedimento') ?>" class="block w-full px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-center rounded-lg transition">
                    Voltar para Lista
                </a>
            </div>
            
            <div class="mt-6 pt-6 border-t">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Informações do Sistema</h3>
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>ID:</span>
                        <span class="font-medium"><?= $procedimento['id'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Cadastrado em:</span>
                        <span class="font-medium"><?= formatarData(substr($procedimento['criada_em'] ?? '', 0, 10)) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarExclusao() {
    if (confirm('Tem certeza que deseja excluir este procedimento? Esta ação não pode ser desfeita.')) {
        fetch('<?= UrlHelper::url('/procedimento/' . $procedimento['id'] . '/delete') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '<?= UrlHelper::url('/procedimento') ?>';
            } else {
                alert('Erro ao excluir procedimento: ' + data.message);
            }
        })
        .catch(error => {
            alert('Erro ao excluir procedimento');
            console.error(error);
        });
    }
}
</script>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>

