<section class="section page">
    <div class="container">
        <div class="page-head reveal">
            <p class="eyebrow">Arşiv</p>
            <h1 class="split" data-split>Yazılar</h1>
            <p class="lead">Tarih sırasına göre tüm yazılar. Toplam <?= (int) $pager['total'] ?> yazı.</p>
        </div>

        <?php if (count($categories) > 1): ?>
            <div class="chips">
                <a class="chip <?= !$activeCategory ? 'active' : '' ?>" href="<?= url('/yazilar') ?>">Tümü</a>
                <?php foreach ($categories as $c): ?>
                    <?php if ((int) $c['n'] === 0) continue; ?>
                    <a class="chip <?= $activeCategory === $c['slug'] ? 'active' : '' ?>" href="<?= url('/yazilar?kategori=' . $c['slug']) ?>">
                        <?= e($c['name']) ?> <small><?= (int) $c['n'] ?></small>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($articles): ?>
            <div class="grid">
                <?php foreach ($articles as $i => $article): ?>
                    <?php $delay = ($i % 3) * 0.12; ?>
                    <?php require __DIR__ . '/partials/article_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="muted">Bu kategoride henüz yazı yok.</p>
        <?php endif; ?>

        <?php if ($pager['pages'] > 1): ?>
            <nav class="pagination" aria-label="Sayfalar">
                <?php
                $base = '/yazilar?' . ($activeCategory ? 'kategori=' . urlencode($activeCategory) . '&' : '');
                for ($p = 1; $p <= $pager['pages']; $p++): ?>
                    <a class="<?= $p === $pager['page'] ? 'active' : '' ?>" href="<?= url($base . 'sayfa=' . $p) ?>"><?= $p ?></a>
                <?php endfor; ?>
            </nav>
        <?php endif; ?>
    </div>
</section>
