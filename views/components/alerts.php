<?php
use App\Utils\Session;
?>
<div class="mb-6">
    <?php if (Session::hasFlash('success')): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
            <p class="font-medium"><?= Session::getFlash('success') ?></p>
        </div>
    <?php endif; ?>
    
    <?php if (Session::hasFlash('error')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded" role="alert">
            <p class="font-medium"><?= Session::getFlash('error') ?></p>
        </div>
    <?php endif; ?>
    
    <?php if (Session::hasFlash('errors')): ?>
        <?php $errors = Session::getFlash('errors'); ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded" role="alert">
            <p class="font-medium mb-2">Erros de validação:</p>
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $field => $error): ?>
                    <li><?= is_numeric($field) ? htmlspecialchars($error) : htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
