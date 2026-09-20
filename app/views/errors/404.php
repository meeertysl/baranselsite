<section class="section page">
    <div class="container narrow center">
        <p class="eyebrow"><?= (int) ($code ?? 404) ?></p>
        <h1><?= e($message ?? 'Sayfa bulunamadı') ?></h1>
        <p class="lead">Aradığınız sayfa taşınmış ya da hiç var olmamış olabilir.</p>
        <a class="btn btn-primary" href="<?= url('/') ?>">Ana sayfaya dön</a>
    </div>
</section>
