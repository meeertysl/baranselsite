<section class="hero">
    <div class="container hero-inner">
        <div class="hero-text">
            <p class="eyebrow"><?= e(setting('owner_name')) ?><?= setting('owner_title') ? ' · ' . e(setting('owner_title')) : '' ?></p>
            <h1><?= e(setting('hero_title')) ?></h1>
            <p class="lead"><?= e(setting('hero_text')) ?></p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="<?= url('/yazilar') ?>">Yazıları keşfet</a>
                <a class="btn btn-ghost" href="<?= url('/hakkimda') ?>">Hakkımda</a>
            </div>
        </div>
        <div class="hero-media">
            <?php $img = setting('hero_image') ?: setting('about_photo'); ?>
            <?php if ($img): ?>
                <img src="<?= e($img) ?>" alt="<?= e(setting('owner_name')) ?>">
            <?php else: ?>
                <div class="hero-placeholder"><?= e(mb_strtoupper(mb_substr(setting('owner_name'), 0, 1))) ?></div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section about-strip">
    <div class="container about-strip-inner">
        <div>
            <h2 class="section-title">Hakkımda</h2>
            <p><?= e(setting('about_short')) ?></p>
            <a class="text-link" href="<?= url('/hakkimda') ?>">Daha fazlası <span aria-hidden="true">→</span></a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <h2 class="section-title">Son Yazılar</h2>
                <p class="muted">Toplam <?= (int) $total ?> yazı</p>
            </div>
            <a class="btn btn-ghost" href="<?= url('/yazilar') ?>">Tümünü göster</a>
        </div>
        <?php if ($articles): ?>
            <div class="grid">
                <?php foreach ($articles as $article): ?>
                    <?php require __DIR__ . '/partials/article_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="muted">Henüz yazı yayınlanmadı.</p>
        <?php endif; ?>
    </div>
</section>
