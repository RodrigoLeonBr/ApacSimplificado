<?php
use App\Utils\UrlHelper;

$title = 'Detalhes do Caráter de Atendimento - Sistema APAC';
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
            <h1 class="text-3xl font-bold text-gray-800">Detalhes do Caráter de Atendimento</h1>
            <p class="text-gray-600">Visualize as informações completas do caráter de atendimento</p>
        </div>
        <div class="flex gap-3">
            <a href="<?= UrlHelper::url('/carater/' . $carater['id'] . '/edit') ?>" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar
            </a>
            <a href="<?= UrlHelper::url('/carater') ?>" class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition">
                Voltar
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Informações Principais -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Dados do Caráter de Atendimento</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Código</label>
                    <p class="text-gray-900 font-medium text-lg"><?= htmlspecialchars($carater['codigo'] ?? '-') ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Descrição</label>
                    <p class="text-gray-900"><?= htmlspecialchars($carater['descricao'] ?? '-') ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar de Ações -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Ações</h2>
            <div class="space-y-3">
                <a href="<?= UrlHelper::url('/carater/' . $carater['id'] . '/edit') ?>" class="block w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-center rounded-lg transition">
                    Editar Caráter
                </a>
                <button onclick="confirmarExclusao()" class="block w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-center rounded-lg transition">
                    Excluir Caráter
                </button>
                <a href="<?= UrlHelper::url('/carater') ?>" class="block w-full px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-center rounded-lg transition">
                    Voltar para Lista
                </a>
            </div>
            
            <div class="mt-6 pt-6 border-t">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Informações do Sistema</h3>
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>ID:</span>
                        <span class="font-medium"><?= $carater['id'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Cadastrado em:</span>
                        <span class="font-medium"><?= formatarData(substr($carater['criada_em'] ?? '', 0, 10)) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarExclusao() {
    if (confirm('Tem certeza que deseja excluir este caráter de atendimento? Esta ação não pode ser desfeita.')) {
        fetch('<?= UrlHelper::url('/carater/' . $carater['id'] . '/delete') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '<?= UrlHelper::url('/carater') ?>';
            } else {
                alert('Erro ao excluir caráter: ' + data.message);
            }
        })
        .catch(error => {
            alert('Erro ao excluir caráter');
            console.error(error);
        });
    }
}
</script>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>

