<?php
use App\Utils\UrlHelper;

$title = ($carater ? 'Editar' : 'Novo') . ' Caráter de Atendimento - Sistema APAC';
$isEdit = $carater !== null;
$old = \App\Utils\Session::getFlash('old', $old ?? []);
$errors = \App\Utils\Session::getFlash('errors', []);
ob_start();
?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"><?= $isEdit ? 'Editar Caráter de Atendimento' : 'Novo Caráter de Atendimento' ?></h1>
            <p class="text-gray-600"><?= $isEdit ? 'Atualize os dados do caráter de atendimento' : 'Cadastre um novo caráter de atendimento no sistema' ?></p>
        </div>
        <a href="<?= UrlHelper::url('/carater') ?>" class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition">
            Voltar
        </a>
    </div>
</div>

<form action="<?= $action ?>" method="<?= $method ?>" class="bg-white rounded-lg shadow-md p-6">
    <!-- Código -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Código <span class="text-red-500">*</span>
        </label>
        <input 
            type="text" 
            name="codigo" 
            value="<?= htmlspecialchars($old['codigo'] ?? $carater['codigo'] ?? '') ?>"
            required
            maxlength="10"
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 <?= isset($errors['codigo']) ? 'border-red-500' : 'border-gray-300' ?>"
            placeholder="Ex: 01, 02, 03"
        >
        <?php if (isset($errors['codigo'])): ?>
            <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['codigo']) ?></p>
        <?php else: ?>
            <p class="text-xs text-gray-500 mt-1">Código do caráter de atendimento (ex: 01, 02, 03)</p>
        <?php endif; ?>
    </div>

    <!-- Descrição -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Descrição <span class="text-red-500">*</span>
        </label>
        <input 
            type="text" 
            name="descricao" 
            value="<?= htmlspecialchars($old['descricao'] ?? $carater['descricao'] ?? '') ?>"
            required
            maxlength="255"
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 <?= isset($errors['descricao']) ? 'border-red-500' : 'border-gray-300' ?>"
            placeholder="Ex: Eletivo, Urgência, Emergência"
        >
        <?php if (isset($errors['descricao'])): ?>
            <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['descricao']) ?></p>
        <?php endif; ?>
    </div>

    <!-- Botões -->
    <div class="flex items-center justify-end gap-4 pt-4 border-t">
        <a href="<?= UrlHelper::url('/carater') ?>" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
            Cancelar
        </a>
        <button 
            type="submit" 
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            <?= $isEdit ? 'Atualizar' : 'Cadastrar' ?>
        </button>
    </div>
</form>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>

