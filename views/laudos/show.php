<?php
$title = 'Visualizar Laudo - Sistema APAC';
ob_start();
?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Laudo #<?= htmlspecialchars($laudo['id'] ?? '') ?></h1>
            <p class="text-gray-600">Detalhes completos do laudo</p>
        </div>
        <div class="space-x-2">
            <a href="/laudos/<?= $laudo['id'] ?>/edit" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar
            </a>
            <a href="/laudos" class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition">
                Voltar
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Informações Principais -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Dados do Laudo -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Dados do Laudo</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Número do Prontuário</p>
                    <p class="font-medium"><?= htmlspecialchars($laudo['numero_prontuario'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Número do Laudo</p>
                    <p class="font-medium"><?= htmlspecialchars($laudo['numero_laudo'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Data do Laudo</p>
                    <p class="font-medium"><?= date('d/m/Y', strtotime($laudo['data_laudo'] ?? 'now')) ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <p class="font-medium">
                        <?php
                        $statusColors = [
                            'rascunho' => 'bg-gray-100 text-gray-800',
                            'emitido' => 'bg-blue-100 text-blue-800',
                            'autorizado' => 'bg-green-100 text-green-800',
                            'cancelado' => 'bg-red-100 text-red-800'
                        ];
                        $status = $laudo['status'] ?? 'rascunho';
                        $colorClass = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full <?= $colorClass ?>">
                            <?= ucfirst($status) ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Dados do Paciente -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Dados do Paciente</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <p class="text-sm text-gray-500">Nome</p>
                    <p class="font-medium"><?= htmlspecialchars($laudo['paciente_nome'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">CNS</p>
                    <p class="font-medium"><?= htmlspecialchars($laudo['paciente_cns'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">CPF</p>
                    <p class="font-medium"><?= htmlspecialchars($laudo['paciente_cpf'] ?? 'N/A') ?></p>
                </div>
            </div>
        </div>

        <!-- Procedimentos -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Procedimentos</h2>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500">CID</p>
                    <p class="font-medium"><?= htmlspecialchars($laudo['cid_codigo'] ?? 'N/A') ?> - <?= htmlspecialchars($laudo['cid_descricao'] ?? '') ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Procedimento Solicitado</p>
                    <p class="font-medium"><?= htmlspecialchars($laudo['procedimento_solicitado'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Procedimento Autorizado</p>
                    <p class="font-medium"><?= htmlspecialchars($laudo['procedimento_autorizado'] ?? 'N/A') ?></p>
                </div>
            </div>
        </div>

        <!-- Observações -->
        <?php if (!empty($laudo['observacoes'])): ?>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Observações</h2>
            <p class="text-gray-700 whitespace-pre-wrap"><?= htmlspecialchars($laudo['observacoes']) ?></p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Barra Lateral -->
    <div class="space-y-6">
        <!-- Informações Adicionais -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Informações</h2>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-gray-500">Criado em</p>
                    <p class="font-medium"><?= date('d/m/Y H:i', strtotime($laudo['created_at'] ?? 'now')) ?></p>
                </div>
                <div>
                    <p class="text-gray-500">Atualizado em</p>
                    <p class="font-medium"><?= date('d/m/Y H:i', strtotime($laudo['updated_at'] ?? 'now')) ?></p>
                </div>
                <div>
                    <p class="text-gray-500">Estabelecimento Solicitante</p>
                    <p class="font-medium"><?= htmlspecialchars($laudo['estabelecimento_solicitante'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <p class="text-gray-500">Estabelecimento Executante</p>
                    <p class="font-medium"><?= htmlspecialchars($laudo['estabelecimento_executante'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <p class="text-gray-500">Profissional</p>
                    <p class="font-medium"><?= htmlspecialchars($laudo['profissional'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <p class="text-gray-500">Caráter do Atendimento</p>
                    <p class="font-medium"><?= htmlspecialchars($laudo['carater_atendimento'] ?? 'N/A') ?></p>
                </div>
            </div>
        </div>

        <!-- Ações -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Ações</h2>
            <div class="space-y-2">
                <?php if ($status !== 'autorizado'): ?>
                <a href="/apacs/create?laudo_id=<?= $laudo['id'] ?>" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center font-bold py-2 px-4 rounded-lg transition">
                    Emitir APAC
                </a>
                <?php endif; ?>
                <button onclick="window.print()" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                    Imprimir
                </button>
                <button onclick="confirmarExclusao(<?= $laudo['id'] ?>)" class="block w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition">
                    Excluir
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarExclusao(id) {
    if (confirm('Tem certeza que deseja excluir este laudo?')) {
        fetch(`/laudos/${id}/delete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/laudos';
            } else {
                alert('Erro ao excluir laudo: ' + data.message);
            }
        });
    }
}
</script>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>
