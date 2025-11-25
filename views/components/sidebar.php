<?php
use App\Utils\UrlHelper;

$currentPath = $_SERVER['REQUEST_URI'] ?? '';

function isActive($path, $currentPath) {
    return str_starts_with($currentPath, $path);
}

function isMenuOpen($paths, $currentPath) {
    foreach ($paths as $path) {
        if (str_starts_with($currentPath, $path)) {
            return true;
        }
    }
    return false;
}
?>

<aside x-data="{ 
    mobileMenuOpen: false,
    laudosOpen: <?= isMenuOpen(['/laudos', '/apacs'], $currentPath) ? 'true' : 'false' ?>,
    cadastrosOpen: <?= isMenuOpen(['/pacientes', '/faixas', '/estabelecimentos', '/profissional', '/cid', '/procedimento', '/carater'], $currentPath) ? 'true' : 'false' ?>,
    gerencialOpen: <?= isMenuOpen(['/relatorios', '/logs', '/usuarios'], $currentPath) ? 'true' : 'false' ?>,
    configOpen: <?= isMenuOpen(['/perfil', '/senha', '/preferencias'], $currentPath) ? 'true' : 'false' ?>
}" class="w-64 bg-white shadow-lg min-h-screen hidden lg:block">
    <div class="p-6">
        <nav class="space-y-1">
            <!-- Dashboard -->
            <a href="<?= UrlHelper::url('/dashboard') ?>" class="flex items-center px-4 py-3 rounded <?= isActive('/dashboard', $currentPath) ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' ?> transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>

            <!-- Laudos e APACs -->
            <div>
                <button @click="laudosOpen = !laudosOpen" class="w-full flex items-center justify-between px-4 py-3 rounded text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="font-medium">Laudos e APACs</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': laudosOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="laudosOpen" x-collapse class="ml-8 mt-1 space-y-1">
                    <a href="<?= UrlHelper::url('/laudos/create') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/laudos/create', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Novo Laudo</a>
                    <a href="<?= UrlHelper::url('/laudos') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/laudos', $currentPath) && !isActive('/laudos/create', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Listar Laudos</a>
                    <a href="<?= UrlHelper::url('/apacs') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/apacs', $currentPath) && !isActive('/apacs/create', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Listar APACs</a>
                    <a href="<?= UrlHelper::url('/apacs/create') ?>" class="block px-4 py-2 text-sm rounded bg-green-500 hover:bg-green-600 text-white font-medium">✨ Emitir APAC</a>
                </div>
            </div>

            <!-- Cadastros -->
            <div>
                <button @click="cadastrosOpen = !cadastrosOpen" class="w-full flex items-center justify-between px-4 py-3 rounded text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span class="font-medium">Cadastros</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': cadastrosOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="cadastrosOpen" x-collapse class="ml-8 mt-1 space-y-1">
                    <a href="<?= UrlHelper::url('/pacientes') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/pacientes', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Pacientes</a>
                    <a href="<?= UrlHelper::url('/faixas') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/faixas', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Faixas de APAC</a>
                    <a href="<?= UrlHelper::url('/estabelecimentos') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/estabelecimentos', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Estabelecimentos</a>
                    <a href="<?= UrlHelper::url('/profissional') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/profissional', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Profissionais</a>
                    <a href="<?= UrlHelper::url('/cid') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/cid', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">CIDs</a>
                    <a href="<?= UrlHelper::url('/procedimento') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/procedimento', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Procedimentos</a>
                    <a href="<?= UrlHelper::url('/carater') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/carater', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Caráter de Atendimento</a>
                </div>
            </div>

            <!-- Gerencial -->
            <div>
                <button @click="gerencialOpen = !gerencialOpen" class="w-full flex items-center justify-between px-4 py-3 rounded text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span class="font-medium">Gerencial</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': gerencialOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="gerencialOpen" x-collapse class="ml-8 mt-1 space-y-1">
                    <a href="<?= UrlHelper::url('/relatorios') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/relatorios', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Relatórios</a>
                    <a href="<?= UrlHelper::url('/logs') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/logs', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Logs de Atividade</a>
                    <a href="<?= UrlHelper::url('/usuarios') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/usuarios', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Usuários</a>
                </div>
            </div>

            <!-- Configurações -->
            <div>
                <button @click="configOpen = !configOpen" class="w-full flex items-center justify-between px-4 py-3 rounded text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="font-medium">Configurações</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': configOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="configOpen" x-collapse class="ml-8 mt-1 space-y-1">
                    <a href="<?= UrlHelper::url('/perfil') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/perfil', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Meu Perfil</a>
                    <a href="<?= UrlHelper::url('/senha') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/senha', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Alterar Senha</a>
                    <a href="<?= UrlHelper::url('/preferencias') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/preferencias', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Preferências</a>
                </div>
            </div>
        </nav>
    </div>
</aside>

<!-- Mobile Sidebar -->
<div x-data="{ 
    mobileMenuOpen: false,
    laudosOpen: <?= isMenuOpen(['/laudos', '/apacs'], $currentPath) ? 'true' : 'false' ?>,
    cadastrosOpen: <?= isMenuOpen(['/pacientes', '/faixas', '/estabelecimentos', '/profissional', '/cid', '/procedimento', '/carater'], $currentPath) ? 'true' : 'false' ?>,
    gerencialOpen: <?= isMenuOpen(['/relatorios', '/logs', '/usuarios'], $currentPath) ? 'true' : 'false' ?>,
    configOpen: <?= isMenuOpen(['/perfil', '/senha', '/preferencias'], $currentPath) ? 'true' : 'false' ?>
}" class="lg:hidden">
    <!-- Mobile Menu Button -->
    <button @click="mobileMenuOpen = !mobileMenuOpen" class="fixed top-20 left-4 z-50 bg-blue-600 text-white p-3 rounded-lg shadow-lg">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <!-- Mobile Sidebar Overlay -->
    <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false" x-collapse:enter="transition-opacity ease-linear duration-300" x-collapse:enter-start="opacity-0" x-collapse:enter-end="opacity-100" x-collapse:leave="transition-opacity ease-linear duration-300" x-collapse:leave-start="opacity-100" x-collapse:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 z-40" style="display: none;"></div>

    <!-- Mobile Sidebar -->
    <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" x-collapse:enter="transition ease-in-out duration-300 transform" x-collapse:enter-start="-translate-x-full" x-collapse:enter-end="translate-x-0" x-collapse:leave="transition ease-in-out duration-300 transform" x-collapse:leave-start="translate-x-0" x-collapse:leave-end="-translate-x-full" class="fixed inset-y-0 left-0 w-64 bg-white shadow-xl z-50 overflow-y-auto" style="display: none;">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-800">Menu</h2>
                <button @click="mobileMenuOpen = false" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <nav class="space-y-1">
                <!-- Dashboard -->
                <a href="<?= UrlHelper::url('/dashboard') ?>" class="flex items-center px-4 py-3 rounded <?= isActive('/dashboard', $currentPath) ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' ?> transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="font-medium">Dashboard</span>
                </a>

                <!-- Laudos e APACs -->
                <div>
                    <button @click="laudosOpen = !laudosOpen" class="w-full flex items-center justify-between px-4 py-3 rounded text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="font-medium">Laudos e APACs</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': laudosOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="laudosOpen" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="<?= UrlHelper::url('/laudos/create') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/laudos/create', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Novo Laudo</a>
                        <a href="<?= UrlHelper::url('/laudos') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/laudos', $currentPath) && !isActive('/laudos/create', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Listar Laudos</a>
                        <a href="<?= UrlHelper::url('/apacs') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/apacs', $currentPath) && !isActive('/apacs/create', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Listar APACs</a>
                        <a href="<?= UrlHelper::url('/apacs/create') ?>" class="block px-4 py-2 text-sm rounded bg-green-500 hover:bg-green-600 text-white font-medium">✨ Emitir APAC</a>
                    </div>
                </div>

                <!-- Cadastros -->
                <div>
                    <button @click="cadastrosOpen = !cadastrosOpen" class="w-full flex items-center justify-between px-4 py-3 rounded text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <span class="font-medium">Cadastros</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': cadastrosOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="cadastrosOpen" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="<?= UrlHelper::url('/pacientes') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/pacientes', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Pacientes</a>
                        <a href="<?= UrlHelper::url('/faixas') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/faixas', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Faixas de APAC</a>
                        <a href="<?= UrlHelper::url('/estabelecimentos') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/estabelecimentos', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Estabelecimentos</a>
                        <a href="<?= UrlHelper::url('/profissional') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/profissional', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Profissionais</a>
                        <a href="<?= UrlHelper::url('/cid') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/cid', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">CIDs</a>
                        <a href="<?= UrlHelper::url('/procedimento') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/procedimento', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Procedimentos</a>
                        <a href="<?= UrlHelper::url('/carater') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/carater', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Caráter de Atendimento</a>
                    </div>
                </div>

                <!-- Gerencial -->
                <div>
                    <button @click="gerencialOpen = !gerencialOpen" class="w-full flex items-center justify-between px-4 py-3 rounded text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span class="font-medium">Gerencial</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': gerencialOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="gerencialOpen" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="<?= UrlHelper::url('/relatorios') ?>" class="block px-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Relatórios</a>
                        <a href="<?= UrlHelper::url('/logs') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/logs', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Logs de Atividade</a>
                        <a href="<?= UrlHelper::url('/usuarios') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/usuarios', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Usuários</a>
                    </div>
                </div>

                <!-- Configurações -->
                <div>
                    <button @click="configOpen = !configOpen" class="w-full flex items-center justify-between px-4 py-3 rounded text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="font-medium">Configurações</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': configOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="configOpen" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="<?= UrlHelper::url('/perfil') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/perfil', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Meu Perfil</a>
                        <a href="<?= UrlHelper::url('/senha') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/senha', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Alterar Senha</a>
                        <a href="<?= UrlHelper::url('/preferencias') ?>" class="block px-4 py-2 text-sm rounded <?= isActive('/preferencias', $currentPath) ? 'bg-blue-100 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' ?>">Preferências</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</div>
