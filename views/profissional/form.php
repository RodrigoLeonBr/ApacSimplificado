<?php
use App\Utils\UrlHelper;

$title = ($profissional ? 'Editar' : 'Novo') . ' Profissional - Sistema APAC';
$isEdit = $profissional !== null;
$old = \App\Utils\Session::getFlash('old', $old ?? []);
$errors = \App\Utils\Session::getFlash('errors', []);
ob_start();

// Lista de especialidades comuns
$especialidades = [
    'Clínico Geral',
    'Cardiologista',
    'Pediatra',
    'Ginecologista',
    'Ortopedista',
    'Neurologista',
    'Psiquiatra',
    'Dermatologista',
    'Oftalmologista',
    'Otorrinolaringologista',
    'Urologista',
    'Cirurgião Geral',
    'Oncologista',
    'Endocrinologista',
    'Pneumologista',
    'Gastroenterologista',
    'Nefrologista',
    'Reumatologista',
    'Hematologista',
    'Infectologista',
    'Anestesiologista',
    'Radiologista',
    'Patologista',
    'Outra'
];

$ufs = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"><?= $isEdit ? 'Editar Profissional' : 'Novo Profissional' ?></h1>
            <p class="text-gray-600"><?= $isEdit ? 'Atualize os dados do profissional' : 'Cadastre um novo profissional no sistema' ?></p>
        </div>
        <a href="<?= UrlHelper::url('/profissional') ?>" class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition">
            Voltar
        </a>
    </div>
</div>

<form action="<?= $action ?>" method="<?= $method ?>" class="bg-white rounded-lg shadow-md p-6" x-data="{
    cns: '<?= htmlspecialchars($old['cns'] ?? $profissional['cns'] ?? '') ?>',
    cpf: '<?= htmlspecialchars($old['cpf'] ?? $profissional['cpf'] ?? '') ?>',
    cnsValido: true,
    cpfValido: true,
    validandoCns: false,
    validandoCpf: false,
    validarCns() {
        const cns = this.cns.replace(/\D/g, '');
        if (!cns) {
            this.cnsValido = true;
            return;
        }
        if (cns.length !== 15) {
            this.cnsValido = false;
            return;
        }
        this.validandoCns = true;
        fetch(`/api/validar-cns?cns=${cns}`)
            .then(response => response.json())
            .then(data => {
                this.cnsValido = data.valido;
                this.validandoCns = false;
            })
            .catch(() => {
                this.cnsValido = false;
                this.validandoCns = false;
            });
    },
    validarCpf() {
        const cpf = this.cpf.replace(/\D/g, '');
        if (!cpf) {
            this.cpfValido = true;
            return;
        }
        if (cpf.length !== 11) {
            this.cpfValido = false;
            return;
        }
        this.validandoCpf = true;
        fetch(`/api/validar-cpf?cpf=${cpf}`)
            .then(response => response.json())
            .then(data => {
                this.cpfValido = data.valido;
                this.validandoCpf = false;
            })
            .catch(() => {
                this.cpfValido = false;
                this.validandoCpf = false;
            });
    }
}">
    <!-- Dados Pessoais -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Dados Pessoais</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nome -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nome Completo <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="nome" 
                    value="<?= htmlspecialchars($old['nome'] ?? $profissional['nome'] ?? '') ?>"
                    required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 <?= isset($errors['nome']) ? 'border-red-500' : 'border-gray-300' ?>"
                >
                <?php if (isset($errors['nome'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['nome']) ?></p>
                <?php endif; ?>
            </div>

            <!-- CNS -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    CNS <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="cns" 
                        x-model="cns"
                        @input.debounce.500ms="validarCns()"
                        maxlength="15"
                        required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 <?= isset($errors['cns']) ? 'border-red-500' : (isset($cnsValido) && !$cnsValido ? 'border-red-500 bg-red-50' : 'border-gray-300') ?>"
                        placeholder="000000000000000"
                    >
                    <div x-show="validandoCns" class="absolute right-3 top-2.5">
                        <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
                <?php if (isset($errors['cns'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['cns']) ?></p>
                <?php elseif (isset($cnsValido) && !$cnsValido): ?>
                    <p class="text-red-500 text-xs mt-1">CNS inválido</p>
                <?php else: ?>
                    <p class="text-xs text-gray-500 mt-1">Cartão Nacional de Saúde (15 dígitos)</p>
                <?php endif; ?>
            </div>

            <!-- Matrícula -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Matrícula
                </label>
                <input 
                    type="text" 
                    name="matricula" 
                    value="<?= htmlspecialchars($old['matricula'] ?? $profissional['matricula'] ?? '') ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="Matrícula profissional"
                >
            </div>

            <!-- CPF -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    CPF
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="cpf" 
                        x-model="cpf"
                        @input.debounce.500ms="validarCpf()"
                        maxlength="14"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 <?= isset($errors['cpf']) ? 'border-red-500' : (isset($cpfValido) && !$cpfValido ? 'border-red-500 bg-red-50' : 'border-gray-300') ?>"
                        placeholder="000.000.000-00"
                    >
                    <div x-show="validandoCpf" class="absolute right-3 top-2.5">
                        <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
                <?php if (isset($errors['cpf'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['cpf']) ?></p>
                <?php elseif (isset($cpfValido) && !$cpfValido): ?>
                    <p class="text-red-500 text-xs mt-1">CPF inválido</p>
                <?php endif; ?>
            </div>

            <!-- Especialidade -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Especialidade
                </label>
                <select 
                    name="especialidade" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Selecione uma especialidade</option>
                    <?php foreach ($especialidades as $esp): ?>
                        <option value="<?= htmlspecialchars($esp) ?>" <?= ($old['especialidade'] ?? $profissional['especialidade'] ?? '') === $esp ? 'selected' : '' ?>>
                            <?= htmlspecialchars($esp) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Contato -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Contato</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Telefone -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Telefone
                </label>
                <input 
                    type="text" 
                    name="telefone" 
                    value="<?= htmlspecialchars($old['telefone'] ?? $profissional['telefone'] ?? '') ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="(00) 00000-0000"
                >
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    E-mail
                </label>
                <input 
                    type="email" 
                    name="email" 
                    value="<?= htmlspecialchars($old['email'] ?? $profissional['email'] ?? '') ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 <?= isset($errors['email']) ? 'border-red-500' : 'border-gray-300' ?>"
                >
                <?php if (isset($errors['email'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['email']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Localização -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Localização</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- UF -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    UF
                </label>
                <select 
                    name="uf" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Selecione</option>
                    <?php foreach ($ufs as $uf): ?>
                        <option value="<?= $uf ?>" <?= ($old['uf'] ?? $profissional['uf'] ?? '') === $uf ? 'selected' : '' ?>><?= $uf ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Município -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Município
                </label>
                <input 
                    type="text" 
                    name="municipio" 
                    value="<?= htmlspecialchars($old['municipio'] ?? $profissional['municipio'] ?? '') ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
            </div>
        </div>
    </div>

    <!-- Status -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Status</h2>
        <div class="grid grid-cols-1 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Status do Profissional <span class="text-red-500">*</span>
                </label>
                <select 
                    name="status" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="ativo" <?= ($old['status'] ?? $profissional['status'] ?? 'ativo') === 'ativo' ? 'selected' : '' ?>>Ativo</option>
                    <option value="inativo" <?= ($old['status'] ?? $profissional['status'] ?? '') === 'inativo' ? 'selected' : '' ?>>Inativo</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Botões -->
    <div class="flex items-center justify-end gap-4 pt-4 border-t">
        <a href="<?= UrlHelper::url('/profissional') ?>" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
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

