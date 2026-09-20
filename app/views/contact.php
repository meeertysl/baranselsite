<?php $old = $_SESSION['old'] ?? []; unset($_SESSION['old']); ?>
<section class="section page">
    <div class="container contact">
        <div class="contact-info reveal">
            <p class="eyebrow">İletişim</p>
            <h1 class="split" data-split>Bana ulaşın</h1>
            <p class="lead">Sorularınız ve görüşleriniz için aşağıdaki formu kullanabilir ya da doğrudan iletişim bilgilerimden ulaşabilirsiniz.</p>
            <dl class="contact-list">
                <?php if (setting('contact_email')): ?>
                    <dt>E-posta</dt><dd><a href="mailto:<?= e(setting('contact_email')) ?>"><?= e(setting('contact_email')) ?></a></dd>
                <?php endif; ?>
                <?php if (setting('contact_phone')): ?>
                    <dt>Telefon</dt><dd><a href="tel:<?= e(preg_replace('/\s+/', '', setting('contact_phone'))) ?>"><?= e(setting('contact_phone')) ?></a></dd>
                <?php endif; ?>
                <?php if (setting('contact_address')): ?>
                    <dt>Adres</dt><dd><?= nl2br(e(setting('contact_address'))) ?></dd>
                <?php endif; ?>
            </dl>
        </div>
        <form class="contact-form reveal" style="--d:.15s" method="post" action="<?= url('/iletisim') ?>">
            <?= csrf_field() ?>
            <div class="hp"><label>Web sitesi <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
            <label>Ad Soyad <span class="req">*</span>
                <input type="text" name="name" required maxlength="120" value="<?= e($old['name'] ?? '') ?>">
            </label>
            <label>E-posta <span class="req">*</span>
                <input type="email" name="email" required maxlength="190" value="<?= e($old['email'] ?? '') ?>">
            </label>
            <label>Konu
                <input type="text" name="subject" maxlength="190" value="<?= e($old['subject'] ?? '') ?>">
            </label>
            <label>Mesajınız <span class="req">*</span>
                <textarea name="message" rows="6" required maxlength="5000"><?= e($old['body'] ?? '') ?></textarea>
            </label>
            <button class="btn btn-primary magnetic" type="submit"><span>Gönder</span></button>
        </form>
    </div>
</section>
