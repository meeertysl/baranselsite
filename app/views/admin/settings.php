<form method="post" enctype="multipart/form-data" data-editor-form>
    <?= csrf_field() ?>
    <?php foreach ($groups as $groupName => $fields): ?>
        <div class="panel">
            <h2><?= e($groupName) ?></h2>
            <?php foreach ($fields as $key => [$label, $type]): ?>
                <?php $value = setting($key); ?>
                <?php if ($type === 'text'): ?>
                    <label class="field">
                        <span><?= e($label) ?></span>
                        <input type="text" name="<?= e($key) ?>" value="<?= e($value) ?>">
                    </label>
                <?php elseif ($type === 'textarea'): ?>
                    <label class="field">
                        <span><?= e($label) ?></span>
                        <textarea name="<?= e($key) ?>" rows="3"><?= e($value) ?></textarea>
                    </label>
                <?php elseif ($type === 'editor'): ?>
                    <div class="field">
                        <span><?= e($label) ?></span>
                        <div id="editor"><?= $value ?></div>
                        <input type="hidden" name="<?= e($key) ?>" id="content-input">
                    </div>
                <?php elseif ($type === 'image'): ?>
                    <div class="field">
                        <span><?= e($label) ?></span>
                        <?php if ($value): ?>
                            <img class="preview small-preview" src="<?= e($value) ?>" alt="">
                            <label class="check"><input type="checkbox" name="remove_<?= e($key) ?>" value="1"> Görseli kaldır</label>
                        <?php endif; ?>
                        <input type="file" name="<?= e($key) ?>" accept="image/*">
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
    <div class="sticky-save">
        <button class="btn btn-primary" type="submit">Ayarları kaydet</button>
    </div>
</form>
