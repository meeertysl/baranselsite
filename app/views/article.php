<article class="section page article">
    <div class="container narrow">
        <header class="article-head reveal">
            <div class="card-meta">
                <time datetime="<?= e($article['published_at']) ?>"><?= e(format_date($article['published_at'])) ?></time>
                <?php if ($article['category_name']): ?>
                    <span class="dot"></span>
                    <a class="card-cat" href="<?= url('/yazilar?kategori=' . $article['category_slug']) ?>"><?= e($article['category_name']) ?></a>
                <?php endif; ?>
                <span class="dot"></span>
                <span><?= reading_time($article['content']) ?> dk okuma</span>
            </div>
            <h1><?= e($article['title']) ?></h1>
            <?php if ($article['excerpt']): ?><p class="lead"><?= e($article['excerpt']) ?></p><?php endif; ?>
        </header>

        <?php if ($article['cover_image']): ?>
            <figure class="article-cover">
                <img src="<?= e($article['cover_image']) ?>" alt="<?= e($article['title']) ?>">
            </figure>
        <?php endif; ?>

        <div class="prose reveal" style="--d:.2s">
            <?= $article['content'] ?>
        </div>
        <div class="progress-bar" aria-hidden="true"><span data-progress></span></div>

        <footer class="article-foot">
            <div class="author">
                <?php if (setting('about_photo')): ?>
                    <img src="<?= e(setting('about_photo')) ?>" alt="<?= e(setting('owner_name')) ?>">
                <?php endif; ?>
                <div>
                    <strong><?= e(setting('owner_name')) ?></strong>
                    <span><?= e(setting('owner_title')) ?></span>
                </div>
            </div>
            <a class="btn btn-ghost" href="<?= url('/yazilar') ?>">← Tüm yazılar</a>
        </footer>
    </div>

    <?php if ($related): ?>
        <div class="container related">
            <h2 class="section-title">Diğer yazılar</h2>
            <div class="grid">
                <?php foreach ($related as $i => $article): ?>
                    <?php $delay = $i * 0.12; ?>
                    <?php require __DIR__ . '/partials/article_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</article>
