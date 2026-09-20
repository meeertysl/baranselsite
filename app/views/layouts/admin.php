<?php
$flashes = take_flashes();
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$unread = (int) db()->query('SELECT COUNT(*) FROM messages WHERE is_read = 0')->fetchColumn();
$menu = [
    '/admin/panel' => 'Panel',
    '/admin/yazilar' => 'Yazılar',
    '/admin/kategoriler' => 'Kategoriler',
    '/admin/ayarlar' => 'Site Ayarları',
    '/admin/mesajlar' => 'Mesajlar',
    '/admin/sifre' => 'Şifre',
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Yönetim') ?> · Yönetim</title>
    <meta name="robots" content="noindex">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/quill/2.0.2/quill.snow.min.css">
    <link rel="stylesheet" href="<?= url('/assets/css/admin.css') ?>">
</head>
<body class="admin">
<aside class="sidebar">
    <div class="sidebar-brand">
        <strong><?= e(setting('site_title')) ?></strong>
        <span>Yönetim Paneli</span>
    </div>
    <nav>
        <?php foreach ($menu as $href => $label): ?>
            <a href="<?= url($href) ?>" class="<?= str_starts_with($path, $href) ? 'active' : '' ?>">
                <?= e($label) ?>
                <?php if ($href === '/admin/mesajlar' && $unread): ?><span class="badge"><?= $unread ?></span><?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-foot">
        <a href="<?= url('/') ?>" target="_blank">Siteyi görüntüle ↗</a>
        <form method="post" action="<?= url('/admin/cikis') ?>">
            <?= csrf_field() ?>
            <button type="submit" class="link">Çıkış yap</button>
        </form>
    </div>
</aside>
<div class="main">
    <header class="topbar">
        <button class="menu-btn" data-sidebar-toggle aria-label="Menü">☰</button>
        <h1><?= e($title ?? '') ?></h1>
        <span class="who"><?= e($_SESSION['admin_name'] ?? '') ?></span>
    </header>
    <div class="content">
        <?php foreach ($flashes as $f): ?>
            <div class="flash flash-<?= e($f['type']) ?>"><?= e($f['message']) ?></div>
        <?php endforeach; ?>
        <?= $content ?>
    </div>
</div>
<script>window.CSRF = <?= json_encode(csrf_token()) ?>;</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/2.0.2/quill.min.js"></script>
<script src="<?= url('/assets/js/admin.js') ?>"></script>
</body>
</html>
