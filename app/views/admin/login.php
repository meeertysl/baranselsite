<form class="login-card" method="post" action="<?= url('/admin') ?>">
    <?= csrf_field() ?>
    <h1><?= e(setting('site_title')) ?></h1>
    <p class="muted">Yönetim paneline giriş yapın</p>
    <label>Kullanıcı adı
        <input type="text" name="username" required autofocus autocomplete="username">
    </label>
    <label>Şifre
        <input type="password" name="password" required autocomplete="current-password">
    </label>
    <button class="btn btn-primary" type="submit">Giriş yap</button>
    <a class="back" href="<?= url('/') ?>">← Siteye dön</a>
</form>
