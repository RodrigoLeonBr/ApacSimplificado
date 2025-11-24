<?php
use App\Utils\Session;
$title = 'Nova Faixa - Sistema APAC';
$old = Session::getFlash('old', []);
ob_start();
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Nova Faixa de APAC</h1>
    <p class="text-gray-600">Cadastre uma nova faixa de números de APAC</p>
</div>

<div class="bg-white rounded-lg shadow-md p-6 max-w-2xl">
    <form method="POST" action="/faixas">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="numero_inicial">
                Número Inicial (13 dígitos)
            </label>
            <input 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline font-mono"
                id="numero_inicial" 
                name="numero_inicial" 
                type="text" 
                placeholder="0000000000000"
                maxlength="13"
                pattern="[0-9]{13}"
                value="<?= htmlspecialchars($old['numero_inicial'] ?? '') ?>"
                required
            >
            <p class="text-xs text-gray-500 mt-1">Digite exatamente 13 dígitos numéricos</p>
        </div>
        
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="numero_final">
                Número Final (13 dígitos)
            </label>
            <input 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline font-mono"
                id="numero_final" 
                name="numero_final" 
                type="text" 
                placeholder="0000000000000"
                maxlength="13"
                pattern="[0-9]{13}"
                value="<?= htmlspecialchars($old['numero_final'] ?? '') ?>"
                required
            >
            <p class="text-xs text-gray-500 mt-1">Digite exatamente 13 dígitos numéricos</p>
        </div>
        
        <div class="flex items-center justify-between">
            <a href="/faixas" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                Cancelar
            </a>
            <button 
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                type="submit"
            >
                Cadastrar Faixa
            </button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>
