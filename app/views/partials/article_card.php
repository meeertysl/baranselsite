<article class="card reveal tilt" style="--d:<?= isset($delay) ? $delay : 0 ?>s">
    <div class="card-shine" aria-hidden="true"></div>
    <a class="card-media" href="<?= url('/yazi/' . $article['slug']) ?>">
        <?php if ($article['cover_image']): ?>
            <img src="<?= e($article['cover_image']) ?>" alt="<?= e($article['title']) ?>" loading="lazy">
        <?php else: ?>
            <div class="card-placeholder">
                <svg viewBox="0 0 200 120" aria-hidden="true">
                    <circle cx="150" cy="30" r="18" fill="currentColor" opacity=".35"/>
                    <path d="M0 100c40-30 70-40 110-20s60 10 90-20v60H0z" fill="currentColor" opacity=".25"/>
                    <path d="M0 120c50-40 90-30 130-10s50 0 70-20v30H0z" fill="currentColor" opacity=".4"/>
                </svg>
                <span><?= e(mb_strtoupper(mb_substr($article['title'], 0, 1))) ?></span>
            </div>
        <?php endif; ?>
    </a>
    <div class="card-body">
        <div class="card-meta">
            <time datetime="<?= e($article['published_at']) ?>"><?= e(format_date($article['published_at'])) ?></time>
            <?php if (!empty($article['category_name'])): ?>
                <span class="dot"></span>
                <a class="card-cat" href="<?= url('/yazilar?kategori=' . $article['category_slug']) ?>"><?= e($article['category_name']) ?></a>
            <?php endif; ?>
        </div>
        <h3 class="card-title"><a href="<?= url('/yazi/' . $article['slug']) ?>"><?= e($article['title']) ?></a></h3>
        <p class="card-excerpt"><?= e($article['excerpt'] ?: excerpt_of($article['content'])) ?></p>
        <a class="card-link arrow-link" href="<?= url('/yazi/' . $article['slug']) ?>">Yazıyı oku <span aria-hidden="true">→</span></a>
    </div>
</article>
