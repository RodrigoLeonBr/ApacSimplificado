<?php
use App\Utils\Session;
use App\Utils\UrlHelper;

$title = 'Login - Sistema APAC';
$old = Session::getFlash('old', []);
ob_start();
?>

<div class="w-full max-w-md">
    <div class="bg-white shadow-2xl rounded-lg px-8 pt-6 pb-8 mb-4">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Sistema APAC</h2>
            <p class="text-gray-600 mt-2">Autorização de Procedimentos de Alta Complexidade</p>
        </div>
        
        <?php require VIEWS_PATH . '/components/alerts.php'; ?>
        
        <form method="POST" action="<?= UrlHelper::url('/login') ?>">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    E-mail
                </label>
                <input 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="email" 
                    name="email" 
                    type="email" 
                    placeholder="usuario@exemplo.com"
                    value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                    required
                >
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Senha
                </label>
                <input 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline"
                    id="password" 
                    name="password" 
                    type="password" 
                    placeholder="********"
                    required
                >
            </div>
            <div class="flex items-center justify-between">
                <button 
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full"
                    type="submit"
                >
                    Entrar
                </button>
            </div>
        </form>
        <div class="mt-6 text-center text-sm text-gray-600">
            <p>Usuário padrão: <strong>admin@apac.com</strong></p>
            <p>Senha: <strong>admin123</strong></p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/auth.php';
?>
