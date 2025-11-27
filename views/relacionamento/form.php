<?php
use App\Utils\UrlHelper;

$title = ($relacionamento ? 'Editar' : 'Novo') . ' Relacionamento - Sistema APAC';
$isEdit = $relacionamento !== null;
$old = \App\Utils\Session::getFlash('old', $old ?? []);
$errors = \App\Utils\Session::getFlash('errors', []);
ob_start();
?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"><?= $isEdit ? 'Editar Relacionamento' : 'Novo Relacionamento' ?></h1>
            <p class="text-gray-600"><?= $isEdit ? 'Atualize os dados do relacionamento' : 'Vincule um procedimento a um CID' ?></p>
        </div>
        <a href="<?= UrlHelper::url('/relacionamento') ?>" class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition">
            Voltar
        </a>
    </div>
</div>

<form action="<?= $action ?>" method="<?= $method ?>" class="bg-white rounded-lg shadow-md p-6" x-data="relacionamentoForm()">
    <!-- Procedimento -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Procedimento <span class="text-red-500">*</span>
        </label>
        <?php if ($procedimento): ?>
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <p class="font-medium text-gray-900"><?= htmlspecialchars($procedimento['codigo_procedimento']) ?></p>
                <p class="text-sm text-gray-600"><?= htmlspecialchars($procedimento['descricao']) ?></p>
            </div>
            <input type="hidden" name="co_procedimento" value="<?= htmlspecialchars($procedimento['codigo_procedimento']) ?>">
        <?php else: ?>
            <div class="relative">
                <input 
                    type="text" 
                    x-model="procedimentoSearch"
                    @input.debounce.300ms="buscarProcedimentos()"
                    placeholder="Digite o código ou descrição do procedimento..."
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 <?= isset($errors['co_procedimento']) ? 'border-red-500' : 'border-gray-300' ?>"
                    required
                >
                <input type="hidden" name="co_procedimento" x-model="procedimentoSelecionado">
                <div x-show="procedimentos.length > 0" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto">
                    <template x-for="proc in procedimentos" :key="proc.id">
                        <div @click="selecionarProcedimento(proc)" class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200">
                            <p class="font-medium text-gray-900" x-text="proc.codigo_procedimento"></p>
                            <p class="text-sm text-gray-600" x-text="proc.descricao"></p>
                        </div>
                    </template>
                </div>
            </div>
            <?php if (isset($errors['co_procedimento'])): ?>
                <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['co_procedimento']) ?></p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- CID -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            CID <span class="text-red-500">*</span>
        </label>
        <?php if ($cid): ?>
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <p class="font-medium text-gray-900"><?= htmlspecialchars($cid['codigo']) ?></p>
                <p class="text-sm text-gray-600"><?= htmlspecialchars($cid['descricao']) ?></p>
            </div>
            <input type="hidden" name="co_cid" value="<?= htmlspecialchars($cid['codigo']) ?>">
        <?php else: ?>
            <div class="relative">
                <input 
                    type="text" 
                    x-model="cidSearch"
                    @input.debounce.300ms="buscarCids()"
                    placeholder="Digite o código ou descrição do CID..."
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 <?= isset($errors['co_cid']) ? 'border-red-500' : 'border-gray-300' ?>"
                    required
                >
                <input type="hidden" name="co_cid" x-model="cidSelecionado">
                <div x-show="cids.length > 0" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto">
                    <template x-for="cid in cids" :key="cid.id">
                        <div @click="selecionarCid(cid)" class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200">
                            <p class="font-medium text-gray-900" x-text="cid.codigo"></p>
                            <p class="text-sm text-gray-600" x-text="cid.descricao"></p>
                        </div>
                    </template>
                </div>
            </div>
            <?php if (isset($errors['co_cid'])): ?>
                <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['co_cid']) ?></p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Status Principal -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Status Principal <span class="text-red-500">*</span>
            </label>
            <select 
                name="st_principal" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 <?= isset($errors['st_principal']) ? 'border-red-500' : '' ?>"
                required
            >
                <option value="N" <?= ($old['st_principal'] ?? $relacionamento['st_principal'] ?? 'N') === 'N' ? 'selected' : '' ?>>Não</option>
                <option value="S" <?= ($old['st_principal'] ?? $relacionamento['st_principal'] ?? '') === 'S' ? 'selected' : '' ?>>Sim</option>
            </select>
            <?php if (isset($errors['st_principal'])): ?>
                <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['st_principal']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Data de Competência -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Data de Competência <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                name="dt_competencia" 
                value="<?= htmlspecialchars($old['dt_competencia'] ?? $relacionamento['dt_competencia'] ?? date('Ym')) ?>"
                maxlength="6"
                pattern="\d{6}"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 <?= isset($errors['dt_competencia']) ? 'border-red-500' : '' ?>"
                placeholder="YYYYMM (ex: 202511)"
            >
            <?php if (isset($errors['dt_competencia'])): ?>
                <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['dt_competencia']) ?></p>
            <?php else: ?>
                <p class="text-xs text-gray-500 mt-1">Formato: YYYYMM (ex: 202511)</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Botões -->
    <div class="flex items-center justify-end gap-4 pt-4 border-t mt-6">
        <a href="<?= UrlHelper::url('/relacionamento') ?>" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
            Cancelar
        </a>
        <button 
            type="submit" 
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            <?= $isEdit ? 'Atualizar' : 'Cadastrar' ?>
        </button>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function relacionamentoForm() {
    return {
        procedimentoSearch: '',
        procedimentoSelecionado: '<?= htmlspecialchars($old['co_procedimento'] ?? $relacionamento['co_procedimento'] ?? '') ?>',
        procedimentos: [],
        cidSearch: '',
        cidSelecionado: '<?= htmlspecialchars($old['co_cid'] ?? $relacionamento['co_cid'] ?? '') ?>',
        cids: [],
        
        async buscarProcedimentos() {
            if (this.procedimentoSearch.length < 2) {
                this.procedimentos = [];
                return;
            }
            try {
                const response = await fetch('<?= UrlHelper::url('/procedimento/ajax/search') ?>?q=' + encodeURIComponent(this.procedimentoSearch));
                const data = await response.json();
                this.procedimentos = data.procedimentos || [];
            } catch (error) {
                console.error('Erro ao buscar procedimentos:', error);
            }
        },
        
        selecionarProcedimento(proc) {
            this.procedimentoSelecionado = proc.codigo_procedimento;
            this.procedimentoSearch = proc.codigo_procedimento + ' - ' + proc.descricao;
            this.procedimentos = [];
        },
        
        async buscarCids() {
            if (this.cidSearch.length < 2) {
                this.cids = [];
                return;
            }
            try {
                const response = await fetch('<?= UrlHelper::url('/cid/ajax/search') ?>?q=' + encodeURIComponent(this.cidSearch));
                const data = await response.json();
                this.cids = data.cids || [];
            } catch (error) {
                console.error('Erro ao buscar CIDs:', error);
            }
        },
        
        selecionarCid(cid) {
            this.cidSelecionado = cid.codigo;
            this.cidSearch = cid.codigo + ' - ' + cid.descricao;
            this.cids = [];
        }
    }
}
</script>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>
