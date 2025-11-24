<?php
$title = ($estabelecimento ? 'Editar' : 'Novo') . ' Estabelecimento - Sistema APAC';
$isEdit = $estabelecimento !== null;
ob_start();
?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"><?= $title ?? 'Estabelecimento' ?></h1>
            <p class="text-gray-600"><?= $isEdit ? 'Atualize os dados do estabelecimento' : 'Cadastre um novo estabelecimento de saúde' ?></p>
        </div>
        <a href="/estabelecimentos" class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition">
            Voltar
        </a>
    </div>
</div>

<form action="<?= $action ?>" method="<?= $method ?>" class="bg-white rounded-lg shadow-md p-6" x-data="{
    cnes: '<?= htmlspecialchars($estabelecimento['cnes'] ?? '') ?>',
    cnpj: '<?= htmlspecialchars($estabelecimento['cnpj'] ?? '') ?>',
    cep: '<?= htmlspecialchars($estabelecimento['cep'] ?? '') ?>',
    cnpjValido: true,
    validandoCnpj: false,
    buscandoCep: false,
    validarCnpj() {
        if (this.cnpj.replace(/\D/g, '').length !== 14) {
            this.cnpjValido = true;
            return;
        }
        this.validandoCnpj = true;
        fetch('/api/validar-cnpj?cnpj=' + encodeURIComponent(this.cnpj))
            .then(response => response.json())
            .then(data => {
                this.cnpjValido = data.valido;
                this.validandoCnpj = false;
            })
            .catch(() => {
                this.validandoCnpj = false;
            });
    },
    buscarCep() {
        const cepLimpo = this.cep.replace(/\D/g, '');
        if (cepLimpo.length !== 8) return;
        
        this.buscandoCep = true;
        fetch(`https://viacep.com.br/ws/${cepLimpo}/json/`)
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    document.querySelector('[name=logradouro]').value = data.logradouro || '';
                    document.querySelector('[name=bairro]').value = data.bairro || '';
                    document.querySelector('[name=municipio]').value = data.localidade || '';
                    document.querySelector('[name=uf]').value = data.uf || '';
                }
                this.buscandoCep = false;
            })
            .catch(() => {
                this.buscandoCep = false;
            });
    }
}">
    <!-- Dados do Estabelecimento -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Dados do Estabelecimento</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- CNES -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    CNES <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="cnes" 
                    value="<?= htmlspecialchars($estabelecimento['cnes'] ?? '') ?>"
                    maxlength="7"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 <?= $isEdit ? 'bg-gray-100' : '' ?>"
                    <?= $isEdit ? 'readonly' : '' ?>
                >
                <p class="text-xs text-gray-500 mt-1">Cadastro Nacional de Estabelecimentos de Saúde (7 dígitos)</p>
            </div>

            <!-- CNPJ -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    CNPJ <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="cnpj" 
                        x-model="cnpj"
                        @input.debounce.500ms="validarCnpj()"
                        maxlength="18"
                        required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                        :class="cnpjValido ? 'border-gray-300' : 'border-red-500 bg-red-50'"
                        placeholder="00.000.000/0000-00"
                    >
                    <div x-show="validandoCnpj" class="absolute right-3 top-2.5">
                        <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
                <p x-show="!cnpjValido" class="text-red-500 text-sm mt-1">CNPJ inválido</p>
            </div>

            <!-- Razão Social -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Razão Social <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="razao_social" 
                    value="<?= htmlspecialchars($estabelecimento['razao_social'] ?? '') ?>"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <!-- Nome Fantasia -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nome Fantasia
                </label>
                <input 
                    type="text" 
                    name="nome_fantasia" 
                    value="<?= htmlspecialchars($estabelecimento['nome_fantasia'] ?? '') ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
            </div>
        </div>
    </div>

    <!-- Endereço -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Endereço</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- CEP -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    CEP <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="cep" 
                        x-model="cep"
                        @input.debounce.500ms="buscarCep()"
                        maxlength="9"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="00000-000"
                    >
                    <div x-show="buscandoCep" class="absolute right-3 top-2.5">
                        <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Logradouro -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Logradouro <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="logradouro" 
                    value="<?= htmlspecialchars($estabelecimento['logradouro'] ?? '') ?>"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <!-- Número -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Número <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="numero" 
                    value="<?= htmlspecialchars($estabelecimento['numero'] ?? '') ?>"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <!-- Complemento -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Complemento
                </label>
                <input 
                    type="text" 
                    name="complemento" 
                    value="<?= htmlspecialchars($estabelecimento['complemento'] ?? '') ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <!-- Bairro -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Bairro <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="bairro" 
                    value="<?= htmlspecialchars($estabelecimento['bairro'] ?? '') ?>"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <!-- Município -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Município <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="municipio" 
                    value="<?= htmlspecialchars($estabelecimento['municipio'] ?? '') ?>"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <!-- UF -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    UF <span class="text-red-500">*</span>
                </label>
                <select 
                    name="uf" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Selecione</option>
                    <?php
                    $ufs = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
                    foreach ($ufs as $uf):
                    ?>
                        <option value="<?= $uf ?>" <?= ($estabelecimento['uf'] ?? '') === $uf ? 'selected' : '' ?>><?= $uf ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Contato -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Contato</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Telefone -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Telefone
                </label>
                <input 
                    type="text" 
                    name="telefone" 
                    value="<?= htmlspecialchars($estabelecimento['telefone'] ?? '') ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="(00) 0000-0000"
                >
            </div>

            <!-- Email -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    E-mail
                </label>
                <input 
                    type="email" 
                    name="email" 
                    value="<?= htmlspecialchars($estabelecimento['email'] ?? '') ?>"
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
                    Status do Estabelecimento <span class="text-red-500">*</span>
                </label>
                <select 
                    name="status" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="ativo" <?= ($estabelecimento['status'] ?? 'ativo') === 'ativo' ? 'selected' : '' ?>>Ativo</option>
                    <option value="inativo" <?= ($estabelecimento['status'] ?? '') === 'inativo' ? 'selected' : '' ?>>Inativo</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Botões -->
    <div class="flex items-center justify-end gap-4 pt-4 border-t">
        <a href="/estabelecimentos" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
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
require __DIR__ . '/../layout.php';
?>
