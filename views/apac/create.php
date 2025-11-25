<?php
use App\Utils\UrlHelper;

$title = 'Emitir APAC - Sistema APAC';
ob_start();
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Emitir Nova APAC</h1>
    <p class="text-gray-600">Selecione uma faixa para emitir uma nova APAC</p>
</div>

<div class="bg-white rounded-lg shadow-md p-6 max-w-2xl">
    <?php if (empty($faixas)): ?>
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
            <p class="font-medium">Não há faixas disponíveis para emissão de APAC.</p>
            <p class="text-sm mt-2">Por favor, cadastre uma nova faixa antes de emitir APACs.</p>
        </div>
        <a href="<?= UrlHelper::url('/faixas/create') ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg inline-block">
            Cadastrar Nova Faixa
        </a>
    <?php else: ?>
        <form method="POST" action="<?= UrlHelper::url('/apacs') ?>">
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="faixa_id">
                    Selecione a Faixa
                </label>
                <select 
                    class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="faixa_id" 
                    name="faixa_id" 
                    required
                >
                    <option value="">Selecione uma faixa...</option>
                    <?php foreach ($faixas as $faixa): ?>
                        <option value="<?= htmlspecialchars($faixa['id']) ?>">
                            Faixa #<?= htmlspecialchars($faixa['id']) ?> - 
                            <?= htmlspecialchars($faixa['numero_inicial']) ?> a <?= htmlspecialchars($faixa['numero_final']) ?> 
                            (<?= htmlspecialchars($faixa['status'] === 'disponivel' ? 'Disponível' : 'Em Uso') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-gray-500 mt-1">Apenas faixas disponíveis e em uso são exibidas</p>
            </div>
            
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <p class="text-sm text-blue-700">
                    <strong>Informação:</strong> O número da APAC (14 dígitos) será gerado automaticamente, 
                    incluindo o dígito verificador calculado pelo algoritmo do sistema.
                </p>
            </div>
            
            <div class="flex items-center justify-between">
                <a href="<?= UrlHelper::url('/apacs') ?>" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Cancelar
                </a>
                <button 
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="submit"
                >
                    Emitir APAC
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>
