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
            <?= $isEdit ? 'readonly' : '' ?>
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 <?= isset($errors['codigo_procedimento']) ? 'border-red-500' : 'border-gray-300' ?> <?= $isEdit ? 'bg-gray-100 cursor-not-allowed' : '' ?>"
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

    <!-- Campos SIGTAP -->
    <div class="mb-6 border-t pt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informações SIGTAP</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Tipo de Complexidade -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tipo de Complexidade
                </label>
                <select 
                    name="tp_complexidade" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 <?= isset($errors['tp_complexidade']) ? 'border-red-500' : '' ?>"
                >
                    <option value="">Selecione...</option>
                    <option value="0" <?= ($old['tp_complexidade'] ?? $procedimento['tp_complexidade'] ?? '') === '0' ? 'selected' : '' ?>>0 - Não se aplica</option>
                    <option value="1" <?= ($old['tp_complexidade'] ?? $procedimento['tp_complexidade'] ?? '') === '1' ? 'selected' : '' ?>>1 - Atenção Básica</option>
                    <option value="2" <?= ($old['tp_complexidade'] ?? $procedimento['tp_complexidade'] ?? '') === '2' ? 'selected' : '' ?>>2 - Média Complexidade</option>
                    <option value="3" <?= ($old['tp_complexidade'] ?? $procedimento['tp_complexidade'] ?? '') === '3' ? 'selected' : '' ?>>3 - Alta Complexidade</option>
                </select>
                <?php if (isset($errors['tp_complexidade'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['tp_complexidade']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Tipo de Sexo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tipo de Sexo
                </label>
                <select 
                    name="tp_sexo" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 <?= isset($errors['tp_sexo']) ? 'border-red-500' : '' ?>"
                >
                    <option value="">Selecione...</option>
                    <option value="M" <?= ($old['tp_sexo'] ?? $procedimento['tp_sexo'] ?? '') === 'M' ? 'selected' : '' ?>>M - Masculino</option>
                    <option value="F" <?= ($old['tp_sexo'] ?? $procedimento['tp_sexo'] ?? '') === 'F' ? 'selected' : '' ?>>F - Feminino</option>
                    <option value="I" <?= ($old['tp_sexo'] ?? $procedimento['tp_sexo'] ?? '') === 'I' ? 'selected' : '' ?>>I - Indiferente/Ambos</option>
                    <option value="N" <?= ($old['tp_sexo'] ?? $procedimento['tp_sexo'] ?? '') === 'N' ? 'selected' : '' ?>>N - Não se aplica</option>
                </select>
                <?php if (isset($errors['tp_sexo'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['tp_sexo']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Quantidade Máxima de Execução -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Quantidade Máxima de Execução
                </label>
                <input 
                    type="number" 
                    name="qt_maxima_execucao" 
                    value="<?= htmlspecialchars($old['qt_maxima_execucao'] ?? $procedimento['qt_maxima_execucao'] ?? '') ?>"
                    min="1"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 <?= isset($errors['qt_maxima_execucao']) ? 'border-red-500' : '' ?>"
                    placeholder="Ex: 1"
                >
                <?php if (isset($errors['qt_maxima_execucao'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['qt_maxima_execucao']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Data de Competência -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Data de Competência
                </label>
                <input 
                    type="text" 
                    name="dt_competencia" 
                    value="<?= htmlspecialchars($old['dt_competencia'] ?? $procedimento['dt_competencia'] ?? '') ?>"
                    maxlength="6"
                    pattern="\d{6}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 <?= isset($errors['dt_competencia']) ? 'border-red-500' : '' ?>"
                    placeholder="YYYYMM (ex: 202511)"
                >
                <?php if (isset($errors['dt_competencia'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['dt_competencia']) ?></p>
                <?php else: ?>
                    <p class="text-xs text-gray-500 mt-1">Formato: YYYYMM (ex: 202511)</p>
                <?php endif; ?>
            </div>

            <!-- Valor SH (Serviço Hospitalar) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Valor SH (Serviço Hospitalar)
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">R$</span>
                    <input 
                        type="number" 
                        name="vl_sh" 
                        step="0.01"
                        min="0"
                        value="<?= htmlspecialchars($old['vl_sh'] ?? (isset($procedimento['vl_sh']) && $procedimento['vl_sh'] !== null && $procedimento['vl_sh'] !== '' ? number_format((float)$procedimento['vl_sh'], 2, '.', '') : '')) ?>"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 <?= isset($errors['vl_sh']) ? 'border-red-500' : '' ?>"
                        placeholder="0.00"
                    >
                </div>
                <?php if (isset($errors['vl_sh'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['vl_sh']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Valor SA (Serviço Ambulatorial) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Valor SA (Serviço Ambulatorial)
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">R$</span>
                    <input 
                        type="number" 
                        name="vl_sa" 
                        step="0.01"
                        min="0"
                        value="<?= htmlspecialchars($old['vl_sa'] ?? (isset($procedimento['vl_sa']) && $procedimento['vl_sa'] !== null && $procedimento['vl_sa'] !== '' ? number_format((float)$procedimento['vl_sa'], 2, '.', '') : '')) ?>"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 <?= isset($errors['vl_sa']) ? 'border-red-500' : '' ?>"
                        placeholder="0.00"
                    >
                </div>
                <?php if (isset($errors['vl_sa'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['vl_sa']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Valor SP (Serviço Profissional) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Valor SP (Serviço Profissional)
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">R$</span>
                    <input 
                        type="number" 
                        name="vl_sp" 
                        step="0.01"
                        min="0"
                        value="<?= htmlspecialchars($old['vl_sp'] ?? (isset($procedimento['vl_sp']) && $procedimento['vl_sp'] !== null && $procedimento['vl_sp'] !== '' ? number_format((float)$procedimento['vl_sp'], 2, '.', '') : '')) ?>"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 <?= isset($errors['vl_sp']) ? 'border-red-500' : '' ?>"
                        placeholder="0.00"
                    >
                </div>
                <?php if (isset($errors['vl_sp'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['vl_sp']) ?></p>
                <?php endif; ?>
            </div>
        </div>
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

