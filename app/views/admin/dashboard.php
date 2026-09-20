<div class="stats">
    <a class="stat" href="<?= url('/admin/yazilar') ?>">
        <span class="stat-n"><?= $stats['articles'] ?></span>
        <span class="stat-l">Toplam yazı</span>
    </a>
    <div class="stat">
        <span class="stat-n"><?= $stats['published'] ?></span>
        <span class="stat-l">Yayında</span>
    </div>
    <a class="stat" href="<?= url('/admin/kategoriler') ?>">
        <span class="stat-n"><?= $stats['categories'] ?></span>
        <span class="stat-l">Kategori</span>
    </a>
    <a class="stat" href="<?= url('/admin/mesajlar') ?>">
        <span class="stat-n"><?= $stats['messages'] ?></span>
        <span class="stat-l">Okunmamış mesaj</span>
    </a>
</div>

<div class="panel">
    <div class="panel-head">
        <h2>Son düzenlenen yazılar</h2>
        <a class="btn btn-primary btn-sm" href="<?= url('/admin/yazilar/yeni') ?>">+ Yeni yazı</a>
    </div>
    <?php if ($recent): ?>
        <table class="table">
            <thead><tr><th>Başlık</th><th>Kategori</th><th>Durum</th><th>Tarih</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($recent as $a): ?>
                <tr>
                    <td><strong><?= e($a['title']) ?></strong></td>
                    <td><?= e($a['category_name'] ?? '—') ?></td>
                    <td><?= $a['is_published'] ? '<span class="tag tag-ok">Yayında</span>' : '<span class="tag">Taslak</span>' ?></td>
                    <td><?= e(format_date($a['published_at'])) ?></td>
                    <td class="right"><a class="btn btn-ghost btn-sm" href="<?= url('/admin/yazilar/' . $a['id']) ?>">Düzenle</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="muted">Henüz yazı yok.</p>
    <?php endif; ?>
</div>

<div class="panel">
    <h2>Hızlı erişim</h2>
    <div class="quick">
        <a href="<?= url('/admin/yazilar/yeni') ?>">Yeni yazı ekle</a>
        <a href="<?= url('/admin/ayarlar') ?>">Hakkımda metnini ve fotoğrafı değiştir</a>
        <a href="<?= url('/admin/ayarlar') ?>">İletişim bilgilerini güncelle</a>
        <a href="<?= url('/admin/sifre') ?>">Şifremi değiştir</a>
    </div>
</div>
