<?php
use App\Utils\UrlHelper;

$title = ($procedimento ? 'Editar' : 'Novo') . ' Procedimento - Sistema APAC';
$isEdit = $procedimento !== null;
$old = \App\Utils\Session::getFlash('old', $old ?? []);
$errors = \App\Utils\Session::getFlash('errors', []);
ob_start();
?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"><?= $isEdit ? 'Editar Procedimento' : 'Novo Procedimento' ?></h1>
            <p class="text-gray-600"><?= $isEdit ? 'Atualize os dados do procedimento' : 'Cadastre um novo procedimento no sistema' ?></p>
        </div>
        <a href="<?= UrlHelper::url('/procedimento') ?>" class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition">
            Voltar
        </a>
    </div>
</div>

<form action="<?= $action ?>" method="<?= $method ?>" class="bg-white rounded-lg shadow-md p-6">
    <!-- Código do Procedimento -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Código do Procedimento <span class="text-red-500">*</span>
        </label>
        <input 
            type="text" 
            name="codigo_procedimento" 
            value="<?= htmlspecialchars($old['codigo_procedimento'] ?? $procedimento['codigo_procedimento'] ?? '') ?>"
            required
            maxlength="10"
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 <?= isset($errors['codigo_procedimento']) ? 'border-red-500' : 'border-gray-300' ?>"
            placeholder="Ex: 0301010070"
        >
        <?php if (isset($errors['codigo_procedimento'])): ?>
            <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['codigo_procedimento']) ?></p>
        <?php else: ?>
            <p class="text-xs text-gray-500 mt-1">Código do procedimento SUS</p>
        <?php endif; ?>
    </div>

    <!-- Descrição -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Descrição <span class="text-red-500">*</span>
        </label>
        <textarea 
            name="descricao" 
            rows="4"
            required
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 <?= isset($errors['descricao']) ? 'border-red-500' : 'border-gray-300' ?>"
            placeholder="Descrição completa do procedimento"
        ><?= htmlspecialchars($old['descricao'] ?? $procedimento['descricao'] ?? '') ?></textarea>
        <?php if (isset($errors['descricao'])): ?>
            <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['descricao']) ?></p>
        <?php endif; ?>
    </div>

    <!-- Tabela SUS -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Tabela SUS
        </label>
        <input 
            type="text" 
            name="tabela_sus" 
            value="<?= htmlspecialchars($old['tabela_sus'] ?? $procedimento['tabela_sus'] ?? '') ?>"
            maxlength="50"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            placeholder="Ex: SIA/SUS"
        >
        <p class="text-xs text-gray-500 mt-1">Tabela SUS do procedimento (opcional)</p>
    </div>

    <!-- Botões -->
    <div class="flex items-center justify-end gap-4 pt-4 border-t">
        <a href="<?= UrlHelper::url('/procedimento') ?>" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
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

