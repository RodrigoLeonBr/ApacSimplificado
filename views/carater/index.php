<?php
use App\Utils\UrlHelper;

$title = 'Caráter de Atendimento - Sistema APAC';
ob_start();
?>

<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Caráter de Atendimento</h1>
            <p class="text-gray-600">Gerencie os caráteres de atendimento cadastrados no sistema</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="<?= UrlHelper::url('/carater/create') ?>" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Novo Caráter
            </a>
        </div>
    </div>
</div>

<!-- Busca em Tempo Real -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6" 
     x-data="caraterListData()">
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Caráter de Atendimento</label>
        <div class="relative">
            <input 
                type="text" 
                x-model="searchTerm"
                @input.debounce.500ms="buscarCarateres()"
                placeholder="Digite código ou descrição..."
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

    <!-- Tabela de Caráteres -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-if="loading">
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                            <svg class="animate-spin h-8 w-8 mx-auto text-blue-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="mt-2">Carregando caráteres...</p>
                        </td>
                    </tr>
                </template>
                <template x-if="!loading && carateres.length === 0">
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="mt-2 text-lg font-medium">Nenhum caráter encontrado</p>
                            <p class="text-sm text-gray-400">Tente ajustar os termos da busca</p>
                        </td>
                    </tr>
                </template>
                <template x-if="!loading && carateres.length > 0">
                    <template x-for="carater in carateres" :key="carater.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="carater.codigo"></td>
                            <td class="px-6 py-4 text-sm text-gray-900" x-text="carater.descricao"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a :href="'<?= UrlHelper::url('/carater/') ?>' + carater.id" class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                                <a :href="'<?= UrlHelper::url('/carater/') ?>' + carater.id + '/edit'" class="text-green-600 hover:text-green-900 mr-3">Editar</a>
                                <button @click="confirmarExclusao(carater.id)" class="text-red-600 hover:text-red-900">Excluir</button>
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
            <div>
                <p class="text-sm text-gray-700">
                    Mostrando 
                    <span class="font-medium" x-text="carateres.length > 0 ? ((currentPage - 1) * 10 + 1) : 0"></span>
                    a 
                    <span class="font-medium" x-text="Math.min(currentPage * 10, totalCarateres)"></span>
                    de 
                    <span class="font-medium" x-text="totalCarateres"></span>
                    caráteres
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <button 
                        @click="carregarPagina(currentPage - 1)"
                        :disabled="currentPage === 1"
                        :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'"
                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    
                    <template x-for="page in getPagesArray()" :key="page">
                        <button
                            @click="carregarPagina(page)"
                            :class="page === currentPage ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'"
                            class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                            x-text="page">
                        </button>
                    </template>
                    
                    <button 
                        @click="carregarPagina(currentPage + 1)"
                        :disabled="currentPage === totalPages"
                        :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'"
                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </nav>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('caraterListData', () => ({
        searchTerm: '', 
        loading: false,
        carateres: [],
        totalCarateres: 0,
        currentPage: 1,
        totalPages: 1,
        init() {
            this.carateres = <?= json_encode($carateres ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
            this.totalCarateres = <?= $totalCarateres ?? 0 ?>;
            this.currentPage = <?= $currentPage ?? 1 ?>;
            this.totalPages = <?= $totalPages ?? 1 ?>;
        },
        buscarCarateres() {
            this.loading = true;
            fetch('<?= UrlHelper::url('/carater/ajax/search') ?>?q=' + encodeURIComponent(this.searchTerm) + '&page=' + this.currentPage)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.carateres = data.carateres || [];
                        this.totalCarateres = data.total || 0;
                        this.totalPages = data.totalPages || 1;
                        this.currentPage = data.currentPage || 1;
                    }
                    this.loading = false;
                })
                .catch(error => {
                    console.error('Erro ao buscar caráteres:', error);
                    this.loading = false;
                });
        },
        carregarPagina(page) {
            if (page < 1 || page > this.totalPages) return;
            this.currentPage = page;
            this.buscarCarateres();
        },
        confirmarExclusao(id) {
            if (confirm('Tem certeza que deseja excluir este caráter de atendimento? Esta ação não pode ser desfeita.')) {
                fetch('<?= UrlHelper::url('/carater/') ?>' + id + '/delete', {
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
                        alert('Erro ao excluir caráter: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Erro ao excluir caráter');
                    console.error(error);
                });
            }
        },
        getPagesArray() {
            const pages = [];
            for (let i = 1; i <= this.totalPages; i++) {
                pages.push(i);
            }
            return pages;
        }
    }));
});
</script>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>

