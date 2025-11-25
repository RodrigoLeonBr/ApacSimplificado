<?php
use App\Utils\UrlHelper;

$title = 'Novo Paciente - Sistema APAC';
ob_start();
?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Novo Paciente</h1>
            <p class="text-gray-600">Cadastre um novo paciente no sistema</p>
        </div>
        <a href="<?= UrlHelper::url('/pacientes') ?>" class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition">
            Voltar
        </a>
    </div>
</div>

<form action="<?= UrlHelper::url('/pacientes') ?>" method="POST" class="bg-white rounded-lg shadow-md p-6" x-data="{
    cns: '',
    cpf: '',
    cep: '',
    cnsValido: false,
    cpfValido: true,
    validandoCns: false,
    validandoCpf: false,
    buscandoCep: false,
    validarCns() {
        const cns = this.cns.replace(/\D/g, '');
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
    },
    buscarCep() {
        const cep = this.cep.replace(/\D/g, '');
        if (cep.length !== 8) return;
        
        this.buscandoCep = true;
        
        // Usar endpoint backend que tenta BrasilAPI primeiro e ViaCEP como fallback
        fetch(`/api/buscar-cep?cep=${cep}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('logradouro').value = data.logradouro || '';
                    document.getElementById('bairro').value = data.bairro || '';
                    document.getElementById('municipio').value = data.municipio || '';
                    // UF não existe mais na tabela, mas mantemos no formulário para referência
                    if (document.getElementById('uf')) {
                        document.getElementById('uf').value = data.uf || '';
                    }
                } else {
                    console.error('Erro ao buscar CEP:', data.message || 'CEP não encontrado');
                }
                this.buscandoCep = false;
            })
            .catch(error => {
                console.error('Erro ao buscar CEP:', error);
                this.buscandoCep = false;
            });
    }
}">
    <!-- Dados Pessoais -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">Dados Pessoais</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                        :class="cnsValido ? 'border-gray-300' : 'border-red-500 bg-red-50'"
                    >
                    <div x-show="validandoCns" class="absolute right-3 top-2.5">
                        <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
                <p x-show="!cnsValido && cns.length > 0" class="text-red-500 text-sm mt-1">CNS inválido</p>
            </div>

            <!-- CPF -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">CPF</label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="cpf" 
                        x-model="cpf"
                        @input.debounce.500ms="validarCpf()"
                        maxlength="14"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                        :class="cpfValido ? 'border-gray-300' : 'border-red-500 bg-red-50'"
                    >
                    <div x-show="validandoCpf" class="absolute right-3 top-2.5">
                        <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
                <p x-show="cpf && !cpfValido" class="text-red-500 text-sm mt-1">CPF inválido</p>
            </div>

            <!-- Nome -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nome Completo <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="nome" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50"
                >
            </div>

            <!-- Data de Nascimento -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Data de Nascimento <span class="text-red-500">*</span>
                </label>
                <input 
                    type="date" 
                    name="data_nascimento" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50"
                >
            </div>

            <!-- Sexo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Sexo <span class="text-red-500">*</span>
                </label>
                <select 
                    name="sexo" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50">
                    <option value="">Selecione</option>
                    <option value="M">Masculino</option>
                    <option value="F">Feminino</option>
                </select>
            </div>

            <!-- Nome da Mãe -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nome da Mãe <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="nome_mae" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50"
                >
            </div>

            <!-- Raça/Cor -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Raça/Cor <span class="text-red-500">*</span>
                </label>
                <select 
                    name="raca_cor" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50">
                    <option value="">Selecione</option>
                    <option value="1">Branca</option>
                    <option value="2">Preta</option>
                    <option value="3">Parda</option>
                    <option value="4">Amarela</option>
                    <option value="5">Indígena</option>
                </select>
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
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50"
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
                    id="logradouro"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50"
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
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50"
                >
            </div>

            <!-- Complemento -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Complemento</label>
                <input 
                    type="text" 
                    name="complemento" 
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
                    id="bairro"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50"
                >
            </div>

            <!-- Município -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Município <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="municipio" 
                    id="municipio"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50"
                >
            </div>

            <!-- UF -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    UF <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="uf" 
                    id="uf"
                    maxlength="2"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50"
                >
            </div>
        </div>
    </div>

    <!-- Botões de Ação -->
    <div class="flex gap-4 justify-end pt-4 border-t">
        <a href="<?= UrlHelper::url('/pacientes') ?>" class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold rounded-lg transition">
            Cancelar
        </a>
        <button 
            type="submit" 
            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition"
            :class="(!cnsValido || (cpf && !cpfValido)) ? 'opacity-50 cursor-not-allowed' : ''">
            Cadastrar Paciente
        </button>
    </div>
</form>

<script>
// Fallback: garantir que o formulário funcione mesmo sem Alpine.js
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action="<?= UrlHelper::url('/pacientes') ?>"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            const cnsInput = form.querySelector('input[name="cns"]');
            if (cnsInput && cnsInput.value.replace(/\D/g, '').length !== 15) {
                e.preventDefault();
                alert('CNS deve ter 15 dígitos');
                return false;
            }
        });
    }
});
</script>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>
