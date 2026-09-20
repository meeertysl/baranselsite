<form method="post" enctype="multipart/form-data" class="form-grid" data-editor-form>
    <?= csrf_field() ?>
    <div class="form-main">
        <div class="panel">
            <label class="field">
                <span>Başlık</span>
                <input type="text" name="title" required value="<?= e($article['title']) ?>" placeholder="Yazının başlığı">
            </label>
            <label class="field">
                <span>Bağlantı (slug) <small class="muted">boş bırakılırsa başlıktan üretilir</small></span>
                <input type="text" name="slug" value="<?= e($article['slug']) ?>" placeholder="ornek-yazi-basligi">
            </label>
            <label class="field">
                <span>Özet <small class="muted">listelerde görünür, boş bırakılırsa içerikten alınır</small></span>
                <textarea name="excerpt" rows="3"><?= e($article['excerpt']) ?></textarea>
            </label>
            <div class="field">
                <span>İçerik</span>
                <div id="editor"><?= $article['content'] ?></div>
                <input type="hidden" name="content" id="content-input">
            </div>
        </div>
    </div>

    <aside class="form-side">
        <div class="panel">
            <h3>Yayın</h3>
            <label class="check">
                <input type="checkbox" name="is_published" value="1" <?= $article['is_published'] ? 'checked' : '' ?>>
                Yayında
            </label>
            <label class="field">
                <span>Tarih</span>
                <input type="datetime-local" name="published_at" value="<?= e($article['published_at']) ?>">
            </label>
            <label class="field">
                <span>Kategori</span>
                <select name="category_id">
                    <option value="">— Seçiniz —</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= (int) $article['category_id'] === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button class="btn btn-primary wide" type="submit">Kaydet</button>
            <?php if ($article['id'] && $article['is_published']): ?>
                <a class="btn btn-ghost wide" href="<?= url('/yazi/' . $article['slug']) ?>" target="_blank">Sitede gör ↗</a>
            <?php endif; ?>
        </div>

        <div class="panel">
            <h3>Kapak görseli</h3>
            <?php if ($article['cover_image']): ?>
                <img class="preview" src="<?= e($article['cover_image']) ?>" alt="">
                <input type="hidden" name="existing_cover" value="<?= e($article['cover_image']) ?>">
                <label class="check"><input type="checkbox" name="remove_cover" value="1"> Görseli kaldır</label>
            <?php endif; ?>
            <input type="file" name="cover_image" accept="image/*">
            <p class="muted small">JPG, PNG, WEBP veya GIF. En fazla 8 MB.</p>
        </div>
    </aside>
</form>

<?php if ($article['id']): ?>
    <form method="post" action="<?= url('/admin/yazilar/' . $article['id'] . '/sil') ?>" class="danger-zone" data-confirm="Bu yazı silinsin mi? Bu işlem geri alınamaz.">
        <?= csrf_field() ?>
        <button class="btn btn-danger btn-sm" type="submit">Yazıyı sil</button>
    </form>
<?php endif; ?>
