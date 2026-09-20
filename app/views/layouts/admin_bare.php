<?php $flashes = take_flashes(); ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Yönetim') ?> · <?= e(setting('site_title')) ?></title>
    <meta name="robots" content="noindex">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('/assets/css/admin.css') ?>">
</head>
<body class="admin-bare">
    <div class="login-wrap">
        <?php foreach ($flashes as $f): ?>
            <div class="flash flash-<?= e($f['type']) ?>"><?= e($f['message']) ?></div>
        <?php endforeach; ?>
        <?= $content ?>
    </div>
</body>
</html>
