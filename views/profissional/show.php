<?php
use App\Utils\UrlHelper;

$title = 'Detalhes do Profissional - Sistema APAC';
ob_start();

function formatarCpf($cpf) {
    if (!$cpf || strlen($cpf) !== 11) return $cpf;
    return preg_replace('/^(\d{3})(\d{3})(\d{3})(\d{2})$/', '$1.$2.$3-$4', $cpf);
}

function formatarTelefone($telefone) {
    if (!$telefone) return '-';
    $telefone = preg_replace('/[^0-9]/', '', $telefone);
    if (strlen($telefone) === 11) {
        return preg_replace('/^(\d{2})(\d{5})(\d{4})$/', '($1) $2-$3', $telefone);
    } elseif (strlen($telefone) === 10) {
        return preg_replace('/^(\d{2})(\d{4})(\d{4})$/', '($1) $2-$3', $telefone);
    }
    return $telefone;
}

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
            <h1 class="text-3xl font-bold text-gray-800">Detalhes do Profissional</h1>
            <p class="text-gray-600">Visualize as informações completas do profissional</p>
        </div>
        <div class="flex gap-3">
            <a href="<?= UrlHelper::url('/profissional/' . $profissional['id'] . '/edit') ?>" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar
            </a>
            <a href="<?= UrlHelper::url('/profissional') ?>" class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition">
                Voltar
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Informações Principais -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Dados do Profissional -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b flex items-center justify-between">
                Dados do Profissional
                <span class="px-3 py-1 text-sm rounded-full <?= ($profissional['status'] ?? 'ativo') === 'ativo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                    <?= ($profissional['status'] ?? 'ativo') === 'ativo' ? 'Ativo' : 'Inativo' ?>
                </span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Nome Completo</label>
                    <p class="text-gray-900 font-medium"><?= htmlspecialchars($profissional['nome'] ?? '-') ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">CNS</label>
                    <p class="text-gray-900 font-medium"><?= htmlspecialchars($profissional['cns'] ?? '-') ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Matrícula</label>
                    <p class="text-gray-900"><?= htmlspecialchars($profissional['matricula'] ?? '-') ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">CPF</label>
                    <p class="text-gray-900 font-medium"><?= formatarCpf($profissional['cpf'] ?? '') ?></p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500">Especialidade</label>
                    <p class="text-gray-900"><?= htmlspecialchars($profissional['especialidade'] ?? '-') ?></p>
                </div>
            </div>
        </div>

        <!-- Contato -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Contato</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Telefone</label>
                    <p class="text-gray-900"><?= formatarTelefone($profissional['telefone'] ?? '') ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">E-mail</label>
                    <p class="text-gray-900"><?= htmlspecialchars($profissional['email'] ?? '-') ?></p>
                </div>
            </div>
        </div>

        <!-- Localização -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Localização</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">UF</label>
                    <p class="text-gray-900"><?= htmlspecialchars($profissional['uf'] ?? '-') ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Município</label>
                    <p class="text-gray-900"><?= htmlspecialchars($profissional['municipio'] ?? '-') ?></p>
                </div>
            </div>
        </div>

        <!-- Histórico de Laudos -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Histórico de Laudos (Últimos 5)</h2>
            <?php if (empty($laudos)): ?>
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="mt-2 text-gray-500">Nenhum laudo registrado para este profissional</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nº Laudo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">APAC</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($laudos as $laudo): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($laudo['numero_laudo'] ?? '-') ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        <?= htmlspecialchars($laudo['paciente_nome'] ?? '-') ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        <?= formatarData($laudo['data_laudo'] ?? '') ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        <?= htmlspecialchars($laudo['numero_apac'] ?? '-') ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?= ($laudo['status'] ?? '') === 'emitido' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                            <?= htmlspecialchars(ucfirst($laudo['status'] ?? '-')) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm">
                                        <a href="<?= UrlHelper::url('/laudos/' . $laudo['id']) ?>" class="text-blue-600 hover:text-blue-900">Ver</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sidebar de Ações e Estatísticas -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-md p-6 sticky top-6 space-y-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Ações</h2>
                <div class="space-y-3">
                    <a href="<?= UrlHelper::url('/profissional/' . $profissional['id'] . '/edit') ?>" class="block w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-center rounded-lg transition">
                        Editar Profissional
                    </a>
                    <button onclick="confirmarExclusao()" class="block w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-center rounded-lg transition">
                        Excluir Profissional
                    </a>
                    <a href="<?= UrlHelper::url('/profissional') ?>" class="block w-full px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-center rounded-lg transition">
                        Voltar para Lista
                    </a>
                </div>
            </div>
            
            <div class="pt-6 border-t">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Estatísticas</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                        <span class="text-gray-600">Laudos este mês:</span>
                        <span class="font-bold text-blue-600"><?= $totalLaudosMes ?? 0 ?></span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                        <span class="text-gray-600">Laudos este ano:</span>
                        <span class="font-bold text-green-600"><?= $totalLaudosAno ?? 0 ?></span>
                    </div>
                </div>
            </div>
            
            <div class="pt-6 border-t">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Informações do Sistema</h3>
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>ID:</span>
                        <span class="font-medium"><?= $profissional['id'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Cadastrado em:</span>
                        <span class="font-medium"><?= formatarData(substr($profissional['created_at'] ?? '', 0, 10)) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Atualizado em:</span>
                        <span class="font-medium"><?= formatarData(substr($profissional['updated_at'] ?? '', 0, 10)) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarExclusao() {
    if (confirm('Tem certeza que deseja excluir este profissional? Esta ação não pode ser desfeita.')) {
        fetch('<?= UrlHelper::url('/profissional/' . $profissional['id'] . '/delete') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '<?= UrlHelper::url('/profissional') ?>';
            } else {
                alert('Erro ao excluir profissional: ' + data.message);
            }
        })
        .catch(error => {
            alert('Erro ao excluir profissional');
            console.error(error);
        });
    }
}
</script>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>

