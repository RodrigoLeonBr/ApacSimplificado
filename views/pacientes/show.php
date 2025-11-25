<?php
use App\Utils\UrlHelper;

$title = 'Visualizar Paciente - Sistema APAC';
ob_start();
?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Paciente: <?= htmlspecialchars($paciente['nome'] ?? '') ?></h1>
            <p class="text-gray-600">CNS: <?= htmlspecialchars($paciente['cns'] ?? '') ?></p>
        </div>
        <div class="space-x-2">
            <a href="<?= UrlHelper::url('/pacientes/' . $paciente['id'] . '/edit') ?>" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar
            </a>
            <a href="<?= UrlHelper::url('/laudos/create?paciente_id=' . $paciente['id']) ?>" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Novo Laudo
            </a>
            <a href="<?= UrlHelper::url('/pacientes') ?>" class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition">
                Voltar
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Informações do Paciente -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Dados Pessoais -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Dados Pessoais</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">CNS</p>
                    <p class="font-medium"><?= htmlspecialchars($paciente['cns'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">CPF</p>
                    <p class="font-medium"><?= htmlspecialchars($paciente['cpf'] ?? '-') ?></p>
                </div>
                <div class="col-span-2">
                    <p class="text-sm text-gray-500">Nome Completo</p>
                    <p class="font-medium"><?= htmlspecialchars($paciente['nome'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Data de Nascimento</p>
                    <p class="font-medium"><?= date('d/m/Y', strtotime($paciente['data_nascimento'] ?? 'now')) ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Sexo</p>
                    <p class="font-medium">
                        <?php
                        $sexos = ['M' => 'Masculino', 'F' => 'Feminino'];
                        echo $sexos[$paciente['sexo'] ?? ''] ?? 'N/A';
                        ?>
                    </p>
                </div>
                <div class="col-span-2">
                    <p class="text-sm text-gray-500">Nome da Mãe</p>
                    <p class="font-medium"><?= htmlspecialchars($paciente['nome_mae'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Raça/Cor</p>
                    <p class="font-medium">
                        <?php
                        $racas = [
                            '1' => 'Branca',
                            '2' => 'Preta',
                            '3' => 'Parda',
                            '4' => 'Amarela',
                            '5' => 'Indígena'
                        ];
                        echo $racas[$paciente['raca_cor'] ?? ''] ?? 'N/A';
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Endereço -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Endereço</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">CEP</p>
                    <p class="font-medium"><?= htmlspecialchars($paciente['cep'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">UF</p>
                    <p class="font-medium"><?= htmlspecialchars($paciente['uf'] ?? 'N/A') ?></p>
                </div>
                <div class="col-span-2">
                    <p class="text-sm text-gray-500">Logradouro</p>
                    <p class="font-medium"><?= htmlspecialchars($paciente['logradouro'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Número</p>
                    <p class="font-medium"><?= htmlspecialchars($paciente['numero'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Complemento</p>
                    <p class="font-medium"><?= htmlspecialchars($paciente['complemento'] ?? '-') ?></p>
                </div>
                <div class="col-span-2">
                    <p class="text-sm text-gray-500">Bairro</p>
                    <p class="font-medium"><?= htmlspecialchars($paciente['bairro'] ?? 'N/A') ?></p>
                </div>
                <div class="col-span-2">
                    <p class="text-sm text-gray-500">Município</p>
                    <p class="font-medium"><?= htmlspecialchars($paciente['municipio'] ?? 'N/A') ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra Lateral -->
    <div class="space-y-6">
        <!-- Informações Adicionais -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Informações</h2>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-gray-500">Cadastrado em</p>
                    <p class="font-medium"><?= date('d/m/Y H:i', strtotime($paciente['created_at'] ?? 'now')) ?></p>
                </div>
                <div>
                    <p class="text-gray-500">Última atualização</p>
                    <p class="font-medium"><?= date('d/m/Y H:i', strtotime($paciente['updated_at'] ?? 'now')) ?></p>
                </div>
            </div>
        </div>

        <!-- Histórico de Laudos -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Histórico de Laudos</h2>
            
            <?php if (empty($laudos)): ?>
                <div class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-sm">Nenhum laudo registrado</p>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach (array_slice($laudos ?? [], 0, 5) as $laudo): ?>
                        <a href="<?= UrlHelper::url('/laudos/' . $laudo['id']) ?>" class="block p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-sm text-gray-900">
                                        Laudo #<?= htmlspecialchars($laudo['numero_laudo'] ?? $laudo['id']) ?>
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        <?= date('d/m/Y', strtotime($laudo['data_laudo'] ?? 'now')) ?>
                                    </p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full <?php
                                    $statusColors = [
                                        'rascunho' => 'bg-gray-200 text-gray-700',
                                        'emitido' => 'bg-blue-200 text-blue-700',
                                        'autorizado' => 'bg-green-200 text-green-700',
                                        'cancelado' => 'bg-red-200 text-red-700'
                                    ];
                                    echo $statusColors[$laudo['status'] ?? 'rascunho'] ?? 'bg-gray-200 text-gray-700';
                                ?>">
                                    <?= ucfirst($laudo['status'] ?? 'Rascunho') ?>
                                </span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
                
                <?php if (count($laudos ?? []) > 5): ?>
                    <a href="<?= UrlHelper::url('/laudos?paciente_id=' . $paciente['id']) ?>" class="block mt-3 text-center text-sm text-blue-600 hover:text-blue-800">
                        Ver todos os laudos (<?= count($laudos) ?>)
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Ações -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Ações</h2>
            <div class="space-y-2">
                <button onclick="window.print()" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                    Imprimir
                </button>
                <button onclick="confirmarExclusao(<?= $paciente['id'] ?>)" class="block w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition">
                    Excluir Paciente
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarExclusao(id) {
    if (confirm('Tem certeza que deseja excluir este paciente? Esta ação excluirá todos os laudos associados e não pode ser desfeita.')) {
        fetch(`/pacientes/${id}/delete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '<?= UrlHelper::url('/pacientes') ?>';
            } else {
                alert('Erro ao excluir paciente: ' + data.message);
            }
        })
        .catch(error => {
            alert('Erro ao excluir paciente');
            console.error(error);
        });
    }
}
</script>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>
