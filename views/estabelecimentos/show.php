<?php
use App\Utils\UrlHelper;

$title = 'Detalhes do Estabelecimento - Sistema APAC';
ob_start();

function formatarCnpj($cnpj) {
    if (!$cnpj || strlen($cnpj) !== 14) return $cnpj;
    return preg_replace('/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/', '$1.$2.$3/$4-$5', $cnpj);
}

function formatarCep($cep) {
    if (!$cep || strlen($cep) !== 8) return $cep;
    return preg_replace('/^(\d{5})(\d{3})$/', '$1-$2', $cep);
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
            <h1 class="text-3xl font-bold text-gray-800">Detalhes do Estabelecimento</h1>
            <p class="text-gray-600">Visualize as informações completas do estabelecimento</p>
        </div>
        <div class="flex gap-3">
            <a href="<?= UrlHelper::url('/estabelecimentos/' . $estabelecimento['id'] . '/edit') ?>" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar
            </a>
            <a href="<?= UrlHelper::url('/estabelecimentos') ?>" class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition">
                Voltar
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Informações Principais -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Dados do Estabelecimento -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b flex items-center justify-between">
                Dados do Estabelecimento
                <span class="px-3 py-1 text-sm rounded-full <?= $estabelecimento['status'] === 'ativo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                    <?= $estabelecimento['status'] === 'ativo' ? 'Ativo' : 'Inativo' ?>
                </span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">CNES</label>
                    <p class="text-gray-900 font-medium"><?= htmlspecialchars($estabelecimento['cnes'] ?? '-') ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">CNPJ</label>
                    <p class="text-gray-900 font-medium"><?= formatarCnpj($estabelecimento['cnpj'] ?? '') ?></p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500">Razão Social</label>
                    <p class="text-gray-900 font-medium"><?= htmlspecialchars($estabelecimento['razao_social'] ?? '-') ?></p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500">Nome Fantasia</label>
                    <p class="text-gray-900"><?= htmlspecialchars($estabelecimento['nome_fantasia'] ?? '-') ?></p>
                </div>
            </div>
        </div>

        <!-- Endereço -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Endereço</h2>
            <div class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">CEP</label>
                        <p class="text-gray-900"><?= formatarCep($estabelecimento['cep'] ?? '') ?></p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500">Logradouro</label>
                        <p class="text-gray-900"><?= htmlspecialchars($estabelecimento['logradouro'] ?? '-') ?></p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Número</label>
                        <p class="text-gray-900"><?= htmlspecialchars($estabelecimento['numero'] ?? '-') ?></p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500">Complemento</label>
                        <p class="text-gray-900"><?= htmlspecialchars($estabelecimento['complemento'] ?? '-') ?></p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Bairro</label>
                        <p class="text-gray-900"><?= htmlspecialchars($estabelecimento['bairro'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Município</label>
                        <p class="text-gray-900"><?= htmlspecialchars($estabelecimento['municipio'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">UF</label>
                        <p class="text-gray-900"><?= htmlspecialchars($estabelecimento['uf'] ?? '-') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contato -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Contato</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Telefone</label>
                    <p class="text-gray-900"><?= htmlspecialchars($estabelecimento['telefone'] ?? '-') ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">E-mail</label>
                    <p class="text-gray-900"><?= htmlspecialchars($estabelecimento['email'] ?? '-') ?></p>
                </div>
            </div>
        </div>

        <!-- Histórico de Laudos -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Histórico de Laudos (Últimos 10)</h2>
            <?php if (empty($laudos)): ?>
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="mt-2 text-gray-500">Nenhum laudo registrado para este estabelecimento</p>
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
                                            <?= $laudo['status'] === 'emitido' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
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

    <!-- Sidebar de Ações -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Ações</h2>
            <div class="space-y-3">
                <a href="<?= UrlHelper::url('/estabelecimentos/' . $estabelecimento['id'] . '/edit') ?>" class="block w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-center rounded-lg transition">
                    Editar Estabelecimento
                </a>
                <a href="<?= UrlHelper::url('/laudos/create?estabelecimento_id=' . $estabelecimento['id']) ?>" class="block w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-center rounded-lg transition">
                    Emitir Novo Laudo
                </a>
                <a href="<?= UrlHelper::url('/estabelecimentos') ?>" class="block w-full px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-center rounded-lg transition">
                    Voltar para Lista
                </a>
            </div>
            
            <div class="mt-6 pt-6 border-t">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Informações do Sistema</h3>
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>ID:</span>
                        <span class="font-medium"><?= $estabelecimento['id'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Cadastrado em:</span>
                        <span class="font-medium"><?= formatarData(substr($estabelecimento['created_at'] ?? '', 0, 10)) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Atualizado em:</span>
                        <span class="font-medium"><?= formatarData(substr($estabelecimento['updated_at'] ?? '', 0, 10)) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>
