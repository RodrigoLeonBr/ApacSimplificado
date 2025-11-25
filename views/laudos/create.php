<?php
$title = 'Novo Laudo - Sistema APAC';
ob_start();
?>

<div x-data="{
    currentTab: 1,
    formData: {
        // Aba 1: Paciente
        paciente_id: '',
        novo_paciente: false,
        paciente_nome: '',
        paciente_cns: '',
        paciente_cpf: '',
        paciente_data_nascimento: '',
        paciente_sexo: '',
        paciente_nome_mae: '',
        paciente_raca_cor: '',
        paciente_logradouro: '',
        paciente_numero: '',
        paciente_complemento: '',
        paciente_bairro: '',
        paciente_cep: '',
        paciente_municipio: '',
        // Aba 2: Laudo
        numero_prontuario: '',
        numero_laudo: '',
        data_laudo: '',
        cid_id: '',
        procedimento_solicitado_id: '',
        procedimento_autorizado_id: '',
        // Aba 3: Autorização
        estabelecimento_solicitante_id: '',
        estabelecimento_executante_id: '',
        profissional_solicitante_id: '',
        carater_atendimento_id: '',
        observacoes: ''
    },
    nextTab() {
        if (this.currentTab < 4) this.currentTab++;
    },
    previousTab() {
        if (this.currentTab > 1) this.currentTab--;
    },
    async buscarCep() {
        const cep = this.formData.paciente_cep.replace(/\D/g, '');
        if (cep.length === 8) {
            try {
                const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                const data = await response.json();
                if (!data.erro) {
                    this.formData.paciente_logradouro = data.logradouro;
                    this.formData.paciente_bairro = data.bairro;
                    this.formData.paciente_municipio = data.localidade;
                }
            } catch (error) {
                console.error('Erro ao buscar CEP:', error);
            }
        }
    }
}" class="bg-white rounded-lg shadow-md">
    <!-- Header com Abas -->
    <div class="border-b border-gray-200">
        <div class="flex space-x-1 p-4 overflow-x-auto">
            <button @click="currentTab = 1" 
                    :class="currentTab === 1 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-6 py-3 rounded-t-lg font-medium whitespace-nowrap transition">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    1. Paciente
                </span>
            </button>
            <button @click="currentTab = 2"
                    :class="currentTab === 2 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-6 py-3 rounded-t-lg font-medium whitespace-nowrap transition">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    2. Laudo
                </span>
            </button>
            <button @click="currentTab = 3"
                    :class="currentTab === 3 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-6 py-3 rounded-t-lg font-medium whitespace-nowrap transition">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    3. Autorização
                </span>
            </button>
            <button @click="currentTab = 4"
                    :class="currentTab === 4 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-6 py-3 rounded-t-lg font-medium whitespace-nowrap transition">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    4. Resumo
                </span>
            </button>
        </div>
    </div>

<?php
use App\Utils\UrlHelper;
?>
    <form action="<?= UrlHelper::url('/laudos') ?>" method="POST" class="p-6">
        <!-- Aba 1: Paciente -->
        <div x-show="currentTab === 1" x-cloak>
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Dados do Paciente</h2>
            
            <!-- Seleção: Paciente Existente ou Novo -->
            <div class="mb-6">
                <label class="inline-flex items-center mr-6">
                    <input type="radio" name="tipo_paciente" value="existente" x-model="formData.novo_paciente" :checked="!formData.novo_paciente" @change="formData.novo_paciente = false" class="form-radio text-blue-600">
                    <span class="ml-2">Paciente Existente</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="tipo_paciente" value="novo" x-model="formData.novo_paciente" @change="formData.novo_paciente = true" class="form-radio text-blue-600">
                    <span class="ml-2">Novo Paciente</span>
                </label>
            </div>

            <!-- Paciente Existente -->
            <div x-show="!formData.novo_paciente" class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Paciente <span class="text-red-500">*</span>
                </label>
                <select name="paciente_id" x-model="formData.paciente_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Selecione um paciente</option>
                    <?php foreach ($pacientes as $paciente): ?>
                    <option value="<?= $paciente['id'] ?>"><?= htmlspecialchars($paciente['nome']) ?> - CNS: <?= htmlspecialchars($paciente['cns'] ?? 'N/A') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Novo Paciente -->
            <div x-show="formData.novo_paciente" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nome Completo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="paciente[nome]" x-model="formData.paciente_nome" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            CNS <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="paciente[cns]" x-model="formData.paciente_cns" maxlength="15" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">CPF</label>
                        <input type="text" name="paciente[cpf]" x-model="formData.paciente_cpf" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Data de Nascimento <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="paciente[data_nascimento]" x-model="formData.paciente_data_nascimento" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Sexo <span class="text-red-500">*</span>
                        </label>
                        <select name="paciente[sexo]" x-model="formData.paciente_sexo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50">
                            <option value="">Selecione</option>
                            <option value="M">Masculino</option>
                            <option value="F">Feminino</option>
                            <option value="O">Outro</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nome da Mãe <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="paciente[nome_mae]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Raça/Cor <span class="text-red-500">*</span>
                        </label>
                        <select name="paciente[raca_cor]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50">
                            <option value="">Selecione</option>
                            <option value="Branca">Branca</option>
                            <option value="Preta">Preta</option>
                            <option value="Parda">Parda</option>
                            <option value="Amarela">Amarela</option>
                            <option value="Indigena">Indígena</option>
                            <option value="Sem Informacao">Sem Informação</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            CEP <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="paciente[cep]" x-model="formData.paciente_cep" @blur="buscarCep()" maxlength="9" placeholder="00000-000" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Logradouro <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="paciente[logradouro]" x-model="formData.paciente_logradouro" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Número <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="paciente[numero]" x-model="formData.paciente_numero" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Complemento</label>
                        <input type="text" name="paciente[complemento]" x-model="formData.paciente_complemento" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Bairro <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="paciente[bairro]" x-model="formData.paciente_bairro" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Município <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="paciente[municipio]" x-model="formData.paciente_municipio" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50">
                    </div>
                </div>
            </div>

            <div class="mt-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 text-sm">
                <p class="font-medium">Campos obrigatórios marcados com <span class="text-red-500">*</span> possuem fundo amarelo</p>
            </div>
        </div>

        <!-- Aba 2: Laudo -->
        <div x-show="currentTab === 2" x-cloak>
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Dados do Laudo</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Número do Prontuário <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="numero_prontuario" x-model="formData.numero_prontuario" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Número do Laudo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="numero_laudo" x-model="formData.numero_laudo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Data do Laudo <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="data_laudo" x-model="formData.data_laudo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    CID (Código Internacional de Doenças) <span class="text-red-500">*</span>
                </label>
                <select name="cid_id" x-model="formData.cid_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50">
                    <option value="">Selecione o CID</option>
                    <?php foreach ($cids as $cid): ?>
                    <option value="<?= $cid['id'] ?>"><?= htmlspecialchars($cid['codigo']) ?> - <?= htmlspecialchars($cid['descricao']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Procedimento Solicitado <span class="text-red-500">*</span>
                    </label>
                    <select name="procedimento_solicitado_id" x-model="formData.procedimento_solicitado_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50">
                        <option value="">Selecione</option>
                        <?php foreach ($procedimentos as $proc): ?>
                        <option value="<?= $proc['id'] ?>"><?= htmlspecialchars($proc['codigo_procedimento']) ?> - <?= htmlspecialchars($proc['descricao']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Procedimento Autorizado <span class="text-red-500">*</span>
                    </label>
                    <select name="procedimento_autorizado_id" x-model="formData.procedimento_autorizado_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50">
                        <option value="">Selecione</option>
                        <?php foreach ($procedimentos as $proc): ?>
                        <option value="<?= $proc['id'] ?>"><?= htmlspecialchars($proc['codigo_procedimento']) ?> - <?= htmlspecialchars($proc['descricao']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Aba 3: Autorização -->
        <div x-show="currentTab === 3" x-cloak>
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Dados de Autorização</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Estabelecimento Solicitante <span class="text-red-500">*</span>
                    </label>
                    <select name="estabelecimento_solicitante_id" x-model="formData.estabelecimento_solicitante_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50">
                        <option value="">Selecione</option>
                        <?php foreach ($estabelecimentos as $estab): ?>
                        <option value="<?= $estab['id'] ?>"><?= htmlspecialchars($estab['cnes'] ?? $estab['codigo'] ?? 'N/A') ?> - <?= htmlspecialchars($estab['razao_social'] ?? $estab['nome'] ?? 'N/A') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Estabelecimento Executante <span class="text-red-500">*</span>
                    </label>
                    <select name="estabelecimento_executante_id" x-model="formData.estabelecimento_executante_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50">
                        <option value="">Selecione</option>
                        <?php foreach ($estabelecimentos as $estab): ?>
                        <option value="<?= $estab['id'] ?>"><?= htmlspecialchars($estab['cnes'] ?? $estab['codigo'] ?? 'N/A') ?> - <?= htmlspecialchars($estab['razao_social'] ?? $estab['nome'] ?? 'N/A') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Profissional Solicitante <span class="text-red-500">*</span>
                    </label>
                    <select name="profissional_solicitante_id" x-model="formData.profissional_solicitante_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50">
                        <option value="">Selecione</option>
                        <?php foreach ($profissionais as $prof): ?>
                        <option value="<?= $prof['id'] ?>"><?= htmlspecialchars($prof['nome']) ?> - <?= htmlspecialchars($prof['especialidade'] ?? 'N/A') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Caráter do Atendimento <span class="text-red-500">*</span>
                    </label>
                    <select name="carater_atendimento_id" x-model="formData.carater_atendimento_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-yellow-50">
                        <option value="">Selecione</option>
                        <?php foreach ($caracteres as $car): ?>
                        <option value="<?= $car['id'] ?>"><?= htmlspecialchars($car['codigo']) ?> - <?= htmlspecialchars($car['descricao']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Observações</label>
                <textarea name="observacoes" x-model="formData.observacoes" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Observações adicionais sobre o laudo..."></textarea>
            </div>
        </div>

        <!-- Aba 4: Resumo -->
        <div x-show="currentTab === 4" x-cloak>
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Resumo do Laudo</h2>
            
            <div class="bg-gray-50 rounded-lg p-6 space-y-4">
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Paciente</h3>
                    <p class="text-gray-600" x-show="!formData.novo_paciente">
                        <span x-text="document.querySelector('select[name=paciente_id] option:checked')?.text || 'Não selecionado'"></span>
                    </p>
                    <p class="text-gray-600" x-show="formData.novo_paciente">
                        <span x-text="formData.paciente_nome || 'Nome não informado'"></span> - CNS: <span x-text="formData.paciente_cns || 'N/A'"></span>
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Laudo</h3>
                    <p class="text-gray-600">
                        Prontuário: <span x-text="formData.numero_prontuario || 'N/A'"></span><br>
                        Número do Laudo: <span x-text="formData.numero_laudo || 'N/A'"></span><br>
                        Data: <span x-text="formData.data_laudo || 'N/A'"></span>
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Procedimentos</h3>
                    <p class="text-gray-600">
                        Solicitado: <span x-text="document.querySelector('select[name=procedimento_solicitado_id] option:checked')?.text || 'Não selecionado'"></span><br>
                        Autorizado: <span x-text="document.querySelector('select[name=procedimento_autorizado_id] option:checked')?.text || 'Não selecionado'"></span>
                    </p>
                </div>

                <div class="pt-4 border-t border-gray-300">
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition">
                        ✅ Confirmar e Salvar Laudo
                    </button>
                </div>
            </div>
        </div>

        <!-- Botões de Navegação -->
        <div class="flex justify-between mt-8 pt-6 border-t border-gray-200">
            <button type="button" @click="previousTab()" x-show="currentTab > 1" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-lg transition">
                ← Anterior
            </button>
            <div x-show="currentTab === 1"></div>
            <button type="button" @click="nextTab()" x-show="currentTab < 4" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition ml-auto">
                Próximo →
            </button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>
