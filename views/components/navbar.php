<?php
use App\Utils\Session;
use App\Utils\UrlHelper;

$userName = Session::get('user_nome', 'Usuário');
$userRole = Session::get('user_role', 'user');
?>
<nav class="bg-blue-600 text-white shadow-lg">
    <div class="container mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <h1 class="text-2xl font-bold">Sistema APAC</h1>
                <span class="ml-4 text-sm opacity-75">v1.0</span>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm"><?= htmlspecialchars($userName) ?></span>
                <?php if ($userRole === 'admin'): ?>
                    <span class="bg-yellow-500 text-xs px-2 py-1 rounded">Admin</span>
                <?php endif; ?>
                <a href="<?= UrlHelper::url('/logout') ?>" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded text-sm">Sair</a>
            </div>
        </div>
    </div>
</nav>
