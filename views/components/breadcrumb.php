<?php
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
$pathParts = array_filter(explode('/', parse_url($currentPath, PHP_URL_PATH)));

// Mapeamento de rotas para nomes amigáveis
$routeNames = [
    'dashboard' => 'Dashboard',
    'laudos' => 'Laudos',
    'apacs' => 'APACs',
    'pacientes' => 'Pacientes',
    'faixas' => 'Faixas de APAC',
    'estabelecimentos' => 'Estabelecimentos',
    'profissionais' => 'Profissionais',
    'cids' => 'CIDs',
    'procedimentos' => 'Procedimentos',
    'carater-atendimento' => 'Caráter de Atendimento',
    'relatorios' => 'Relatórios',
    'logs' => 'Logs de Atividade',
    'usuarios' => 'Usuários',
    'perfil' => 'Meu Perfil',
    'senha' => 'Alterar Senha',
    'preferencias' => 'Preferências',
    'create' => 'Novo',
    'edit' => 'Editar',
    'show' => 'Visualizar',
    'delete' => 'Excluir',
    'ajax' => 'Busca',
    'search' => 'Buscar',
    'list' => 'Listar',
];

// Mapeamento de contexto para IDs numéricos
$contextNames = [
    'laudos' => 'Laudo',
    'apacs' => 'APAC',
    'pacientes' => 'Paciente',
    'faixas' => 'Faixa',
    'estabelecimentos' => 'Estabelecimento',
    'profissionais' => 'Profissional',
    'cids' => 'CID',
    'procedimentos' => 'Procedimento',
    'carater-atendimento' => 'Caráter',
    'usuarios' => 'Usuário',
];

$breadcrumbs = [
    ['name' => 'Início', 'url' => '/dashboard']
];

$currentUrl = '';
$pathArray = array_values($pathParts);
$previousPart = null;

foreach ($pathArray as $index => $part) {
    $currentUrl .= '/' . $part;
    
    // Verifica se é um ID numérico
    if (is_numeric($part)) {
        // Se houver uma parte anterior, usa o contexto para criar um nome amigável
        if ($previousPart && isset($contextNames[$previousPart])) {
            $name = $contextNames[$previousPart];
        } else {
            $name = 'Detalhes';
        }
    } else {
        $name = $routeNames[$part] ?? ucfirst(str_replace('-', ' ', $part));
    }
    
    // Se for o último item, não adiciona link
    if ($index === count($pathArray) - 1) {
        $breadcrumbs[] = ['name' => $name, 'url' => null];
    } else {
        $breadcrumbs[] = ['name' => $name, 'url' => $currentUrl];
    }
    
    $previousPart = $part;
}
?>

<?php if (count($breadcrumbs) > 1): ?>
<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
            <li class="inline-flex items-center">
                <?php if ($index > 0): ?>
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                <?php endif; ?>
                
                <?php if ($breadcrumb['url']): ?>
                    <a href="<?= htmlspecialchars($breadcrumb['url']) ?>" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <?php if ($index === 0): ?>
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                        <?php endif; ?>
                        <?= htmlspecialchars($breadcrumb['name']) ?>
                    </a>
                <?php else: ?>
                    <span class="text-sm font-medium text-gray-500">
                        <?= htmlspecialchars($breadcrumb['name']) ?>
                    </span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
<?php endif; ?>
