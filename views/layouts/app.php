<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Sistema APAC' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100">
    <?php require VIEWS_PATH . '/components/navbar.php'; ?>
    
    <div class="flex">
        <?php require VIEWS_PATH . '/components/sidebar.php'; ?>
        
        <main class="flex-1 p-8">
            <?php require VIEWS_PATH . '/components/alerts.php'; ?>
            
            <?= $content ?? '' ?>
        </main>
    </div>
</body>
</html>
