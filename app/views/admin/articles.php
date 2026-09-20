<div class="panel">
    <div class="panel-head">
        <form method="get" class="search">
            <input type="search" name="q" placeholder="Başlıkta ara…" value="<?= e($q) ?>">
            <button class="btn btn-ghost btn-sm" type="submit">Ara</button>
        </form>
        <a class="btn btn-primary btn-sm" href="<?= url('/admin/yazilar/yeni') ?>">+ Yeni yazı</a>
    </div>

    <?php if ($articles): ?>
        <table class="table">
            <thead><tr><th>Başlık</th><th>Kategori</th><th>Durum</th><th>Tarih</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($articles as $a): ?>
                <tr>
                    <td>
                        <strong><?= e($a['title']) ?></strong>
                        <div class="muted small">/yazi/<?= e($a['slug']) ?></div>
                    </td>
                    <td><?= e($a['category_name'] ?? '—') ?></td>
                    <td><?= $a['is_published'] ? '<span class="tag tag-ok">Yayında</span>' : '<span class="tag">Taslak</span>' ?></td>
                    <td><?= e(format_date($a['published_at'])) ?></td>
                    <td class="right actions">
                        <?php if ($a['is_published']): ?>
                            <a class="btn btn-ghost btn-sm" href="<?= url('/yazi/' . $a['slug']) ?>" target="_blank">Gör</a>
                        <?php endif; ?>
                        <a class="btn btn-ghost btn-sm" href="<?= url('/admin/yazilar/' . $a['id']) ?>">Düzenle</a>
                        <form method="post" action="<?= url('/admin/yazilar/' . $a['id'] . '/sil') ?>" data-confirm="Bu yazı silinsin mi? Bu işlem geri alınamaz.">
                            <?= csrf_field() ?>
                            <button class="btn btn-danger btn-sm" type="submit">Sil</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="muted">Kayıt bulunamadı.</p>
    <?php endif; ?>
</div>
