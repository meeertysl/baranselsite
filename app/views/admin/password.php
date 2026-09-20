<div class="panel narrow-panel">
    <h2>Şifre değiştir</h2>
    <form method="post" action="<?= url('/admin/sifre') ?>">
        <?= csrf_field() ?>
        <label class="field">
            <span>Mevcut şifre</span>
            <input type="password" name="current" required autocomplete="current-password">
        </label>
        <label class="field">
            <span>Yeni şifre <small class="muted">en az 8 karakter</small></span>
            <input type="password" name="new" required minlength="8" autocomplete="new-password">
        </label>
        <label class="field">
            <span>Yeni şifre (tekrar)</span>
            <input type="password" name="again" required minlength="8" autocomplete="new-password">
        </label>
        <button class="btn btn-primary" type="submit">Güncelle</button>
    </form>
</div>
