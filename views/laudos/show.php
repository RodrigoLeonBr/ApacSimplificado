<?php
use App\Utils\UrlHelper;

$title = 'Visualizar Laudo - Sistema APAC';
ob_start();

$status = $laudo['status'] ?? 'rascunho';
$temApacVinculada = isset($apacVinculada) && $apacVinculada;
$statusColors = [
    'rascunho' => 'bg-yellow-100 text-yellow-800',
    'emitido' => 'bg-blue-100 text-blue-800',
    'autorizado' => 'bg-green-100 text-green-800',
    'cancelado' => 'bg-red-100 text-red-800'
];
$statusColor = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Laudo #<?= htmlspecialchars($laudo['id'] ?? '') ?></h1>
            <p class="text-gray-600">Detalhes completos do laudo</p>
        </div>
        <div class="space-x-2">
            <a href="<?= UrlHelper::url('/laudos/' . $laudo['id'] . '/edit') ?>" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar
            </a>
            <a href="<?= UrlHelper::url('/laudos') ?>" class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition">
                Voltar
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Informações Principais -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Banner APAC se vinculada -->
        <?php if ($temApacVinculada): ?>
        <div class="bg-gradient-to-r from-green-50 to-blue-50 border-l-4 border-green-500 rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-green-500 rounded-full p-3">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">APAC Vinculada</h3>
                        <p class="text-2xl font-bold text-green-600"><?= htmlspecialchars($apacVinculada['numero_apac'] ?? 'N/A') ?></p>
                        <?php if (!empty($apacVinculada['apac_data_emissao'])): ?>
                        <p class="text-sm text-gray-600 mt-1">
                            Emitida em: <?= date('d/m/Y H:i', strtotime($apacVinculada['apac_data_emissao'])) ?>
                        </p>
                        <?php endif; ?>
                        <?php if (!empty($apacVinculada['data_validade_inicio']) && !empty($apacVinculada['data_validade_fim'])): ?>
                        <p class="text-sm text-gray-600">
                            Validade: <?= date('d/m/Y', strtotime($apacVinculada['data_validade_inicio'])) ?> até <?= date('d/m/Y', strtotime($apacVinculada['data_validade_fim'])) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (!empty($apacVinculada['numero_apac'])): ?>
                <div class="text-center">
                    <svg id="barcode-<?= htmlspecialchars($apacVinculada['numero_apac']) ?>"></svg>
                    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
                    <script>
                        JsBarcode("#barcode-<?= htmlspecialchars($apacVinculada['numero_apac']) ?>", "<?= htmlspecialchars($apacVinculada['numero_apac']) ?>", {
                            format: "CODE128",
                            width: 2,
                            height: 50,
                            displayValue: true
                        });
                    </script>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

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
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full <?= $statusColor ?>">
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
                    <p class="text-sm text-gray-500">Procedimento Autorizado</p>
                    <p class="font-medium"><?= htmlspecialchars($laudo['procedimento_codigo'] ?? 'N/A') ?> - <?= htmlspecialchars($laudo['procedimento_descricao'] ?? 'N/A') ?></p>
                </div>
            </div>
        </div>

        <!-- Histórico de Ações -->
        <?php if (!empty($logs)): ?>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Histórico de Ações</h2>
            <div class="space-y-3">
                <?php foreach ($logs as $log): ?>
                <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                    <div class="flex-shrink-0">
                        <?php
                        $acaoCor = [
                            'criado' => 'text-green-600',
                            'atualizado' => 'text-blue-600',
                            'excluido' => 'text-red-600'
                        ];
                        $cor = $acaoCor[strtolower($log['acao'])] ?? 'text-gray-600';
                        ?>
                        <svg class="w-5 h-5 <?= $cor ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars(ucfirst($log['acao'])) ?></p>
                        <p class="text-xs text-gray-500">
                            <?= date('d/m/Y H:i', strtotime($log['criada_em'] ?? $log['created_at'] ?? 'now')) ?>
                            <?php if (!empty($log['usuario_nome'])): ?>
                            por <?= htmlspecialchars($log['usuario_nome']) ?>
                            <?php endif; ?>
                        </p>
                        <?php if (!empty($log['detalhes'])): ?>
                        <p class="text-xs text-gray-600 mt-1"><?= htmlspecialchars($log['detalhes']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

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
            </div>
        </div>

        <!-- Ações -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Ações</h2>
            <div class="space-y-2">
                <?php if ($status !== 'autorizado' && !$temApacVinculada): ?>
                <a href="<?= UrlHelper::url('/apacs/create?laudo_id=' . $laudo['id']) ?>" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center font-bold py-2 px-4 rounded-lg transition">
                    Emitir APAC
                </a>
                <?php elseif ($temApacVinculada): ?>
                <div class="block w-full bg-gray-400 text-white text-center font-bold py-2 px-4 rounded-lg cursor-not-allowed" title="Este laudo já possui APAC vinculada">
                    APAC já Emitida
                </div>
                <?php endif; ?>
                <?php if ($temApacVinculada && !empty($apacVinculada['apac_id'])): ?>
                <a href="<?= UrlHelper::url('/apacs/' . $apacVinculada['apac_id'] . '/imprimir') ?>" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center font-bold py-2 px-4 rounded-lg transition">
                    Imprimir APAC
                </a>
                <?php endif; ?>
                <button onclick="window.print()" class="block w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition">
                    Imprimir Laudo
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
        fetch(`<?= UrlHelper::url('/laudos/') ?>${id}/delete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '<?= UrlHelper::url('/laudos') ?>';
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
