<section class="section page">
    <div class="container about-page">
        <div class="about-photo reveal">
            <div class="hero-frame">
                <?php if (setting('about_photo')): ?>
                    <img src="<?= e(setting('about_photo')) ?>" alt="<?= e(setting('owner_name')) ?>">
                <?php else: ?>
                    <div class="hero-placeholder"><?= e(mb_strtoupper(mb_substr(setting('owner_name'), 0, 1))) ?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="about-text reveal" style="--d:.15s">
            <p class="eyebrow">Hakkımda</p>
            <h1 class="split" data-split><?= e(setting('owner_name')) ?></h1>
            <?php if (setting('owner_title')): ?><p class="lead"><?= e(setting('owner_title')) ?></p><?php endif; ?>
            <div class="prose">
                <?= setting('about_long') ?>
            </div>
            <div class="hero-actions">
                <a class="btn btn-primary magnetic" href="<?= url('/yazilar') ?>"><span>Yazılarım</span></a>
                <a class="btn btn-ghost magnetic" href="<?= url('/iletisim') ?>"><span>İletişime geç</span></a>
            </div>
        </div>
    </div>
</section>
