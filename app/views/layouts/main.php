<?php
$siteTitle = setting('site_title');
$pageTitle = isset($title) && $title !== $siteTitle ? $title . ' · ' . $siteTitle : $siteTitle;
$desc = $description ?? setting('seo_description');
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$nav = [
    '/' => 'Ana Sayfa',
    '/hakkimda' => 'Hakkımda',
    '/yazilar' => 'Yazılar',
    '/iletisim' => 'İletişim',
];
$socials = [
    'social_instagram' => ['Instagram', 'M12 2.2c3.2 0 3.6 0 4.8.1 3.3.1 4.8 1.7 4.9 4.9.1 1.3.1 1.6.1 4.8s0 3.6-.1 4.8c-.1 3.2-1.7 4.8-4.9 4.9-1.3.1-1.6.1-4.8.1s-3.6 0-4.8-.1c-3.3-.1-4.8-1.7-4.9-4.9C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.8C2.4 3.9 4 2.4 7.2 2.3 8.4 2.2 8.8 2.2 12 2.2zM12 0C8.7 0 8.3 0 7.1.1 2.7.3.3 2.7.1 7.1 0 8.3 0 8.7 0 12s0 3.7.1 4.9c.2 4.4 2.6 6.8 7 7 1.2.1 1.6.1 4.9.1s3.7 0 4.9-.1c4.4-.2 6.8-2.6 7-7 .1-1.2.1-1.6.1-4.9s0-3.7-.1-4.9c-.2-4.4-2.6-6.8-7-7C15.7 0 15.3 0 12 0zm0 5.8a6.2 6.2 0 1 0 0 12.4 6.2 6.2 0 0 0 0-12.4zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.4-11.8a1.4 1.4 0 1 0 0 2.9 1.4 1.4 0 0 0 0-2.9z'],
    'social_twitter' => ['X', 'M18.2 2h3.4l-7.4 8.5L23 22h-6.8l-5.3-7-6.1 7H1.4l7.9-9.1L1 2h7l4.8 6.4L18.2 2zm-1.2 18h1.9L7.1 3.9H5.1L17 20z'],
    'social_linkedin' => ['LinkedIn', 'M20.4 20.4h-3.5v-5.6c0-1.3 0-3-1.9-3s-2.1 1.4-2.1 2.9v5.7H9.4V9h3.4v1.6h.1c.5-.9 1.6-1.9 3.4-1.9 3.6 0 4.3 2.4 4.3 5.5v6.2zM5.3 7.4a2.1 2.1 0 1 1 0-4.1 2.1 2.1 0 0 1 0 4.1zM7.1 20.4H3.6V9h3.5v11.4zM22.2 0H1.8C.8 0 0 .8 0 1.7v20.6c0 .9.8 1.7 1.8 1.7h20.4c1 0 1.8-.8 1.8-1.7V1.7C24 .8 23.2 0 22.2 0z'],
    'social_youtube' => ['YouTube', 'M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.6 12 3.6 12 3.6s-7.5 0-9.4.5A3 3 0 0 0 .5 6.2 31 31 0 0 0 0 12a31 31 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c1.9.5 9.4.5 9.4.5s7.5 0 9.4-.5a3 3 0 0 0 2.1-2.1A31 31 0 0 0 24 12a31 31 0 0 0-.5-5.8zM9.6 15.6V8.4l6.2 3.6-6.2 3.6z'],
    'social_facebook' => ['Facebook', 'M24 12a12 12 0 1 0-13.9 11.9v-8.4H7.1V12h3V9.4c0-3 1.8-4.7 4.5-4.7 1.3 0 2.7.2 2.7.2v3h-1.5c-1.5 0-2 .9-2 1.9V12h3.3l-.5 3.5h-2.8v8.4A12 12 0 0 0 24 12z'],
];
$flashes = take_flashes();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($desc) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('/assets/css/style.css') ?>">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="<?= url('/') ?>">
            <span class="brand-name"><?= e($siteTitle) ?></span>
            <?php if (setting('site_tagline')): ?><span class="brand-tagline"><?= e(setting('site_tagline')) ?></span><?php endif; ?>
        </a>
        <button class="nav-toggle" aria-label="Menüyü aç" aria-expanded="false" data-nav-toggle>
            <span></span><span></span><span></span>
        </button>
        <nav class="site-nav" data-nav>
            <?php foreach ($nav as $href => $label): ?>
                <?php $active = $href === '/' ? $path === '/' : str_starts_with($path, $href) || ($href === '/yazilar' && str_starts_with($path, '/yazi/')); ?>
                <a href="<?= url($href) ?>" class="<?= $active ? 'active' : '' ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
        </nav>
    </div>
</header>

<?php if ($flashes): ?>
    <div class="container">
        <?php foreach ($flashes as $f): ?>
            <div class="flash flash-<?= e($f['type']) ?>"><?= e($f['message']) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<main>
    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="container footer-inner">
        <div>
            <div class="footer-name"><?= e(setting('owner_name')) ?></div>
            <?php if (setting('owner_title')): ?><div class="footer-title"><?= e(setting('owner_title')) ?></div><?php endif; ?>
        </div>
        <div class="socials">
            <?php foreach ($socials as $key => [$label, $d]): ?>
                <?php if (setting($key)): ?>
                    <a href="<?= e(setting($key)) ?>" target="_blank" rel="noopener" aria-label="<?= e($label) ?>" title="<?= e($label) ?>">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="<?= $d ?>"/></svg>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div class="footer-copy"><?= e(setting('footer_text')) ?></div>
    </div>
</footer>
<script src="<?= url('/assets/js/main.js') ?>"></script>
</body>
</html>
