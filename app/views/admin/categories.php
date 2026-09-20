<div class="two-col">
    <div class="panel">
        <h2>Kategoriler</h2>
        <?php if ($categories): ?>
            <table class="table">
                <thead><tr><th>Ad</th><th>Bağlantı</th><th>Yazı</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($categories as $c): ?>
                    <tr>
                        <td>
                            <form method="post" action="<?= url('/admin/kategoriler') ?>" class="inline-edit">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                <input type="text" name="name" value="<?= e($c['name']) ?>" required>
                                <button class="btn btn-ghost btn-sm" type="submit">Kaydet</button>
                            </form>
                        </td>
                        <td class="muted small"><?= e($c['slug']) ?></td>
                        <td><?= (int) $c['n'] ?></td>
                        <td class="right">
                            <form method="post" action="<?= url('/admin/kategoriler/' . $c['id'] . '/sil') ?>" data-confirm="Kategori silinsin mi? Yazılar silinmez, kategorisiz kalır.">
                                <?= csrf_field() ?>
                                <button class="btn btn-danger btn-sm" type="submit">Sil</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="muted">Henüz kategori yok.</p>
        <?php endif; ?>
    </div>

    <div class="panel">
        <h2>Yeni kategori</h2>
        <form method="post" action="<?= url('/admin/kategoriler') ?>">
            <?= csrf_field() ?>
            <label class="field">
                <span>Kategori adı</span>
                <input type="text" name="name" required placeholder="ör. Psikoloji">
            </label>
            <button class="btn btn-primary" type="submit">Ekle</button>
        </form>
    </div>
</div>
