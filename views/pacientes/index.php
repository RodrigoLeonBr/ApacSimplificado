<?php
$title = 'Pacientes - Sistema APAC';
ob_start();
?>

<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Pacientes</h1>
            <p class="text-gray-600">Gerencie os pacientes cadastrados no sistema</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="/pacientes/create" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Novo Paciente
            </a>
        </div>
    </div>
</div>

<!-- Busca em Tempo Real -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6" 
     x-data="{
         searchTerm: '', 
         loading: false,
         pacientes: [],
         totalPacientes: 0,
         currentPage: 1,
         totalPages: 1,
         init() {
             this.pacientes = <?= json_encode($pacientes ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
             this.totalPacientes = <?= $totalPacientes ?? 0 ?>;
             this.currentPage = <?= $currentPage ?? 1 ?>;
             this.totalPages = <?= $totalPages ?? 1 ?>;
         },
         formatarData(data) {
             if (!data) return '-';
             const partes = data.split('-');
             if (partes.length === 3) {
                 return partes[2] + '/' + partes[1] + '/' + partes[0];
             }
             return data;
         },
         buscarPacientes() {
             this.loading = true;
             fetch('/pacientes/ajax/search?q=' + encodeURIComponent(this.searchTerm) + '&page=' + this.currentPage)
                 .then(response => response.json())
                 .then(data => {
                     this.pacientes = data.pacientes || [];
                     this.totalPacientes = data.total || 0;
                     this.totalPages = data.totalPages || 1;
                     this.currentPage = data.currentPage || 1;
                     this.loading = false;
                 })
                 .catch(error => {
                     console.error('Erro ao buscar pacientes:', error);
                     this.loading = false;
                 });
         },
         carregarPagina(page) {
             if (page < 1 || page > this.totalPages) return;
             this.currentPage = page;
             this.buscarPacientes();
         },
         confirmarExclusao(id) {
             if (confirm('Tem certeza que deseja excluir este paciente? Esta ação não pode ser desfeita.')) {
                 fetch('/pacientes/' + id + '/delete', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json'
                     }
                 })
                 .then(response => response.json())
                 .then(data => {
                     if (data.success) {
                         window.location.reload();
                     } else {
                         alert('Erro ao excluir paciente: ' + data.message);
                     }
                 })
                 .catch(error => {
                     alert('Erro ao excluir paciente');
                     console.error(error);
                 });
             }
         }
     }">
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Paciente</label>
        <div class="relative">
            <input 
                type="text" 
                x-model="searchTerm"
                @input.debounce.500ms="buscarPacientes()"
                placeholder="Digite CNS, CPF ou Nome do paciente..."
                class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
            <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <div x-show="loading" class="absolute right-3 top-3">
                <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Tabela de Pacientes -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CNS</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CPF</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Nascimento</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Município</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-if="pacientes.length === 0">
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <p class="text-lg font-medium">Nenhum paciente encontrado</p>
                            <p class="mt-2">Clique em "Novo Paciente" para cadastrar</p>
                        </td>
                    </tr>
                </template>
                <template x-for="paciente in pacientes" :key="paciente.id">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="paciente.cns"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="paciente.cpf || '-'"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="paciente.nome"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="$data.formatarData(paciente.data_nascimento)"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="paciente.municipio"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a :href="'/pacientes/' + paciente.id" class="text-blue-600 hover:text-blue-900 mr-3" title="Visualizar">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a :href="'/pacientes/' + paciente.id + '/edit'" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Editar">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <button @click="confirmarExclusao(paciente.id)" class="text-red-600 hover:text-red-900" title="Excluir">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div x-show="totalPages > 1" class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mt-4">
        <div class="flex-1 flex justify-between sm:hidden">
            <button 
                @click="carregarPagina(currentPage - 1)"
                :disabled="currentPage === 1"
                :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Anterior
            </button>
            <button 
                @click="carregarPagina(currentPage + 1)"
                :disabled="currentPage === totalPages"
                :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Próximo
            </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Mostrando 
                    <span class="font-medium" x-text="((currentPage - 1) * 10) + 1"></span> a 
                    <span class="font-medium" x-text="Math.min(currentPage * 10, totalPacientes)"></span> de 
                    <span class="font-medium" x-text="totalPacientes"></span> resultados
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <button 
                        @click="carregarPagina(currentPage - 1)"
                        :disabled="currentPage === 1"
                        :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''"
                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <template x-for="page in totalPages" :key="page">
                        <button 
                            @click="carregarPagina(page)"
                            :class="page === currentPage ? 'bg-blue-50 text-blue-600' : 'bg-white text-gray-700 hover:bg-gray-50'"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium"
                            x-text="page">
                        </button>
                    </template>
                    <button 
                        @click="carregarPagina(currentPage + 1)"
                        :disabled="currentPage === totalPages"
                        :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''"
                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </nav>
            </div>
        </div>
    </div>
</div>


<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>
