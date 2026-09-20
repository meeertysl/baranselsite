<div class="panel">
    <h2>Gelen mesajlar</h2>
    <?php if ($messages): ?>
        <div class="messages">
            <?php foreach ($messages as $m): ?>
                <article class="message <?= $m['is_read'] ? '' : 'unread' ?>">
                    <div class="message-head">
                        <div>
                            <strong><?= e($m['name']) ?></strong>
                            <a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a>
                            <?php if ($m['subject']): ?><span class="muted">· <?= e($m['subject']) ?></span><?php endif; ?>
                        </div>
                        <time class="muted small"><?= e(date('d.m.Y H:i', strtotime($m['created_at']))) ?></time>
                    </div>
                    <p><?= nl2br(e($m['body'])) ?></p>
                    <div class="actions">
                        <?php if (!$m['is_read']): ?>
                            <form method="post" action="<?= url('/admin/mesajlar/' . $m['id'] . '/okundu') ?>">
                                <?= csrf_field() ?>
                                <button class="btn btn-ghost btn-sm" type="submit">Okundu işaretle</button>
                            </form>
                        <?php endif; ?>
                        <form method="post" action="<?= url('/admin/mesajlar/' . $m['id'] . '/sil') ?>" data-confirm="Mesaj silinsin mi?">
                            <?= csrf_field() ?>
                            <button class="btn btn-danger btn-sm" type="submit">Sil</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="muted">Henüz mesaj yok.</p>
    <?php endif; ?>
</div>
