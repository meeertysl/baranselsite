<?php if (setting('hero_banner')): ?>
<section class="hero hero-banner" data-hero>
    <div class="banner reveal">
        <img src="<?= e(setting('hero_banner')) ?>" alt="<?= e(setting('owner_name')) ?>" data-depth="0.012" fetchpriority="high">
        <div class="banner-actions reveal" style="--d:.4s">
            <a class="btn btn-primary magnetic" href="<?= url('/yazilar') ?>"><span>Yazıları keşfet</span></a>
            <a class="btn btn-ghost magnetic banner-ghost" href="<?= url('/hakkimda') ?>"><span>Hakkımda</span></a>
        </div>
    </div>
    <div class="container banner-text">
        <p class="eyebrow reveal"><?= e(setting('owner_name')) ?><?= setting('owner_title') ? ' · ' . e(setting('owner_title')) : '' ?></p>
        <h1 class="split" data-split><?= e(setting('hero_title')) ?></h1>
        <p class="lead reveal" style="--d:.3s"><?= e(setting('hero_text')) ?></p>
    </div>
    <svg class="wave wave-hero" viewBox="0 0 1440 80" preserveAspectRatio="none" aria-hidden="true"><path d="M0 40c240 40 480-40 720 0s480 40 720 0v40H0z" fill="currentColor"/></svg>
</section>
<?php else: ?>
<section class="hero" data-hero>
    <div class="hero-deco" aria-hidden="true">
        <svg class="deco deco-wave" viewBox="0 0 400 120" data-depth="0.03"><path d="M0 60c50-40 100-40 150 0s100 40 150 0 70-30 100 0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="draw"/></svg>
        <svg class="deco deco-star" viewBox="0 0 60 60" data-depth="0.08"><path d="M30 2l6 20 20 6-20 6-6 20-6-20-20-6 20-6z" fill="currentColor"/></svg>
        <svg class="deco deco-circle" viewBox="0 0 120 120" data-depth="0.05"><circle cx="60" cy="60" r="50" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="60" cy="60" r="8" fill="currentColor"/></svg>
        <svg class="deco deco-lines" viewBox="0 0 120 120" data-depth="0.06"><path d="M10 110L110 10M30 110L110 30M50 110L110 50M70 110L110 70" stroke="currentColor" stroke-width="2" stroke-linecap="round" fill="none"/></svg>
    </div>
    <div class="container hero-inner">
        <div class="hero-text">
            <p class="eyebrow reveal"><?= e(setting('owner_name')) ?><?= setting('owner_title') ? ' · ' . e(setting('owner_title')) : '' ?></p>
            <h1 class="split" data-split><?= e(setting('hero_title')) ?></h1>
            <p class="lead reveal" style="--d:.35s"><?= e(setting('hero_text')) ?></p>
            <div class="hero-actions reveal" style="--d:.5s">
                <a class="btn btn-primary magnetic" href="<?= url('/yazilar') ?>"><span>Yazıları keşfet</span></a>
                <a class="btn btn-ghost magnetic" href="<?= url('/hakkimda') ?>"><span>Hakkımda</span></a>
            </div>
        </div>
        <div class="hero-media reveal" style="--d:.25s" data-depth="0.02">
            <div class="hero-frame">
                <?php $img = setting('hero_image') ?: setting('about_photo'); ?>
                <?php if ($img): ?>
                    <img src="<?= e($img) ?>" alt="<?= e(setting('owner_name')) ?>">
                <?php else: ?>
                    <div class="hero-placeholder"><?= e(mb_strtoupper(mb_substr(setting('owner_name'), 0, 1))) ?></div>
                <?php endif; ?>
            </div>
            <svg class="hero-orbit" viewBox="0 0 400 400" aria-hidden="true"><circle cx="200" cy="200" r="190" fill="none" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4 12"/><circle class="orbit-dot" cx="200" cy="10" r="6" fill="currentColor"/></svg>
        </div>
    </div>
    <svg class="wave wave-hero" viewBox="0 0 1440 80" preserveAspectRatio="none" aria-hidden="true"><path d="M0 40c240 40 480-40 720 0s480 40 720 0v40H0z" fill="currentColor"/></svg>
</section>
<?php endif; ?>

<section class="section about-strip">
    <div class="container about-strip-inner">
        <div class="reveal">
            <p class="eyebrow">Tanışalım</p>
            <h2 class="section-title">Hakkımda</h2>
            <p><?= e(setting('about_short')) ?></p>
            <a class="text-link arrow-link" href="<?= url('/hakkimda') ?>">Daha fazlası <span aria-hidden="true">→</span></a>
        </div>
        <div class="about-strip-visual reveal" style="--d:.2s" aria-hidden="true">
            <svg viewBox="0 0 300 300">
                <circle cx="150" cy="150" r="120" fill="none" stroke="currentColor" stroke-width="1" opacity=".3"/>
                <circle cx="150" cy="150" r="90" fill="none" stroke="currentColor" stroke-width="1" opacity=".5"/>
                <circle cx="150" cy="150" r="60" fill="none" stroke="currentColor" stroke-width="1.5"/>
                <circle class="pulse" cx="150" cy="150" r="20" fill="currentColor"/>
                <g class="spin-slow"><circle cx="150" cy="30" r="5" fill="currentColor"/><circle cx="270" cy="150" r="4" fill="currentColor"/><circle cx="150" cy="270" r="5" fill="currentColor"/><circle cx="30" cy="150" r="4" fill="currentColor"/></g>
            </svg>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head reveal">
            <div>
                <p class="eyebrow">Arşiv</p>
                <h2 class="section-title">Son Yazılar</h2>
                <p class="muted">Toplam <?= (int) $total ?> yazı</p>
            </div>
            <a class="btn btn-ghost magnetic" href="<?= url('/yazilar') ?>"><span>Tümünü göster</span></a>
        </div>
        <?php if ($articles): ?>
            <div class="grid">
                <?php foreach ($articles as $i => $article): ?>
                    <?php $delay = ($i % 3) * 0.12; ?>
                    <?php require __DIR__ . '/partials/article_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="muted">Henüz yazı yayınlanmadı.</p>
        <?php endif; ?>
    </div>
</section>
