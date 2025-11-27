<?php
use App\Utils\UrlHelper;

$title = 'Procedimentos - Sistema APAC';
ob_start();
?>

<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Procedimentos</h1>
            <p class="text-gray-600">Gerencie os procedimentos cadastrados no sistema</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="<?= UrlHelper::url('/procedimento/create') ?>" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Novo Procedimento
            </a>
        </div>
    </div>
</div>

<!-- Busca em Tempo Real -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6" 
     x-data="procedimentoListData()">
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Procedimento</label>
        <div class="relative">
            <input 
                type="text" 
                x-model="searchTerm"
                @input.debounce.500ms="buscarProcedimentos()"
                placeholder="Digite código, descrição ou tabela SUS..."
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

    <!-- Tabela de Procedimentos -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 table-fixed">
            <thead class="bg-gray-50">
                <tr>
                    <th class="w-32 px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                    <th class="w-64 px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                    <th class="w-28 px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tabela SUS</th>
                    <th class="w-32 px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-if="loading">
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <svg class="animate-spin h-8 w-8 mx-auto text-blue-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="mt-2">Carregando procedimentos...</p>
                        </td>
                    </tr>
                </template>
                <template x-if="!loading && procedimentos.length === 0">
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="mt-2 text-lg font-medium">Nenhum procedimento encontrado</p>
                            <p class="text-sm text-gray-400">Tente ajustar os termos da busca</p>
                        </td>
                    </tr>
                </template>
                <template x-if="!loading && procedimentos.length > 0">
                    <template x-for="proc in procedimentos" :key="proc.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-2 py-2 whitespace-nowrap text-sm font-medium text-gray-900" x-text="proc.codigo_procedimento"></td>
                            <td class="px-2 py-2 text-sm text-gray-900">
                                <div class="max-w-xs truncate" :title="proc.descricao" x-text="proc.descricao"></div>
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500" x-text="proc.tabela_sus || '-'"></td>
                            <td class="px-2 py-2 whitespace-nowrap text-right text-sm font-medium">
                                <a :href="'<?= UrlHelper::url('/procedimento/') ?>' + proc.id" class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                                <a :href="'<?= UrlHelper::url('/procedimento/') ?>' + proc.id + '/edit'" class="text-green-600 hover:text-green-900 mr-3">Editar</a>
                                <button @click="confirmarExclusao(proc.id)" class="text-red-600 hover:text-red-900">Excluir</button>
                            </td>
                        </tr>
                    </template>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="mt-6 flex items-center justify-between border-t border-gray-200 pt-4">
        <div class="flex-1 flex justify-between sm:hidden">
            <button 
                @click="carregarPagina(currentPage - 1)"
                :disabled="currentPage === 1"
                :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white">
                Anterior
            </button>
            <button 
                @click="carregarPagina(currentPage + 1)"
                :disabled="currentPage === totalPages"
                :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white">
                Próxima
            </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <p class="text-sm text-gray-700">
                    Mostrando 
                    <span class="font-medium" x-text="procedimentos.length > 0 ? ((currentPage - 1) * perPage + 1) : 0"></span>
                    a 
                    <span class="font-medium" x-text="Math.min(currentPage * perPage, totalProcedimentos)"></span>
                    de 
                    <span class="font-medium" x-text="totalProcedimentos"></span>
                    procedimentos
                </p>
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-700">Registros por página:</label>
                    <select 
                        x-model="perPage"
                        @change="alterarRegistrosPorPagina()"
                        class="px-3 py-1 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <!-- Botão Primeiro -->
                    <button 
                        @click="carregarPagina(1)"
                        :disabled="currentPage === 1"
                        :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'"
                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500"
                        title="Primeira página">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M15.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 010 1.414zm-6 0a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 011.414 1.414L5.414 10l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    
                    <!-- Botão Anterior -->
                    <button 
                        @click="carregarPagina(currentPage - 1)"
                        :disabled="currentPage === 1"
                        :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'"
                        class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500"
                        title="Página anterior">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    
                    <!-- Páginas -->
                    <template x-for="(item, index) in getPagesArray()" :key="index">
                        <template x-if="item === '...'">
                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                ...
                            </span>
                        </template>
                        <template x-if="item !== '...'">
                            <button
                                @click="carregarPagina(item)"
                                :class="item === currentPage ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'"
                                class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                                x-text="item">
                            </button>
                        </template>
                    </template>
                    
                    <!-- Botão Próximo -->
                    <button 
                        @click="carregarPagina(currentPage + 1)"
                        :disabled="currentPage === totalPages"
                        :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'"
                        class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500"
                        title="Próxima página">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    
                    <!-- Botão Último -->
                    <button 
                        @click="carregarPagina(totalPages)"
                        :disabled="currentPage === totalPages"
                        :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'"
                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500"
                        title="Última página">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 15.707a1 1 0 010-1.414L8.586 11H2a1 1 0 110-2h6.586l-4.293-4.293a1 1 0 011.414-1.414l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0zm6 0a1 1 0 010-1.414L14.586 11H8a1 1 0 110-2h6.586l-4.293-4.293a1 1 0 111.414-1.414l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </nav>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('procedimentoListData', () => ({
        searchTerm: '', 
        loading: false,
        procedimentos: [],
        totalProcedimentos: 0,
        currentPage: 1,
        totalPages: 1,
        perPage: 10,
        init() {
            this.perPage = parseInt(localStorage.getItem('procedimento_perPage')) || 10;
            // Se o perPage do localStorage for diferente de 10, recarrega os dados
            if (this.perPage !== 10) {
                this.buscarProcedimentos();
            } else {
                this.procedimentos = <?= json_encode($procedimentos ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
                this.totalProcedimentos = <?= $totalProcedimentos ?? 0 ?>;
                this.currentPage = <?= $currentPage ?? 1 ?>;
                this.totalPages = <?= $totalPages ?? 1 ?>;
            }
        },
        buscarProcedimentos() {
            this.loading = true;
            localStorage.setItem('procedimento_perPage', this.perPage);
            fetch('<?= UrlHelper::url('/procedimento/ajax/search') ?>?q=' + encodeURIComponent(this.searchTerm) + '&page=' + this.currentPage + '&limit=' + this.perPage)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.procedimentos = data.procedimentos || [];
                        this.totalProcedimentos = data.total || 0;
                        this.totalPages = data.totalPages || 1;
                        this.currentPage = data.currentPage || 1;
                    }
                    this.loading = false;
                })
                .catch(error => {
                    console.error('Erro ao buscar procedimentos:', error);
                    this.loading = false;
                });
        },
        carregarPagina(page) {
            if (page < 1 || page > this.totalPages) return;
            this.currentPage = page;
            this.buscarProcedimentos();
        },
        confirmarExclusao(id) {
            if (confirm('Tem certeza que deseja excluir este procedimento? Esta ação não pode ser desfeita.')) {
                fetch('<?= UrlHelper::url('/procedimento/') ?>' + id + '/delete', {
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
                        alert('Erro ao excluir procedimento: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Erro ao excluir procedimento');
                    console.error(error);
                });
            }
        },
        getPagesArray() {
            const pages = [];
            const current = this.currentPage;
            const total = this.totalPages;
            
            if (total <= 5) {
                // Se tem 5 ou menos páginas, mostra todas
                for (let i = 1; i <= total; i++) {
                    pages.push(i);
                }
            } else {
                // Mostra apenas 2-3 páginas entre Anterior e Próximo
                if (current <= 2) {
                    // Primeiras páginas: mostra 1, 2, 3
                    pages.push(1);
                    pages.push(2);
                    if (total >= 3) pages.push(3);
                } else if (current >= total - 1) {
                    // Últimas páginas: mostra total-2, total-1, total
                    if (total >= 3) pages.push(total - 2);
                    pages.push(total - 1);
                    pages.push(total);
                } else {
                    // Páginas do meio: mostra current-1, current, current+1
                    pages.push(current - 1);
                    pages.push(current);
                    pages.push(current + 1);
                }
            }
            
            return pages;
        },
        alterarRegistrosPorPagina() {
            this.currentPage = 1;
            this.buscarProcedimentos();
        }
    }));
});
</script>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>

