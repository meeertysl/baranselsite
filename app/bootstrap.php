<?php
declare(strict_types=1);

$config = require __DIR__ . '/../config.php';
date_default_timezone_set($config['timezone']);
mb_internal_encoding('UTF-8');

session_name($config['session_name']);
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

require __DIR__ . '/helpers.php';

/** Uygulama genelinde paylaşılan yapılandırmayı döndürür. */
function config(string $key): mixed
{
    global $config;
    return $config[$key] ?? null;
}

/** Tek bir PDO bağlantısı döndürür; ilk çağrıda şemayı ve başlangıç verisini kurar. */
function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $path = config('db_path');
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $fresh = !file_exists($path);

    $pdo = new PDO('sqlite:' . $path, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec('PRAGMA journal_mode = WAL');

    $pdo->exec(file_get_contents(__DIR__ . '/../data/schema.sql'));

    if ($fresh || (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn() === 0) {
        seed_database($pdo);
    }

    return $pdo;
}

/** İlk kurulumda yönetici hesabı, örnek kategori ve varsayılan ayarları oluşturur. */
function seed_database(PDO $pdo): void
{
    $pdo->prepare('INSERT OR IGNORE INTO users (username, password_hash) VALUES (?, ?)')
        ->execute([config('admin_username'), password_hash(config('admin_password'), PASSWORD_DEFAULT)]);

    $defaults = [
        'site_title' => 'Baran Bey',
        'site_tagline' => 'Yazılar, makaleler ve düşünceler',
        'owner_name' => 'Baran Bey',
        'owner_title' => 'Yazar',
        'seo_description' => 'Baran Bey\'in kişisel web sitesi. Yazılar, makaleler ve düşünceler.',
        'hero_title' => 'Düşünceler, yazılar ve makaleler',
        'hero_text' => 'Bu sitede kaleme aldığım yazıları başlık ve tarih sırasıyla bulabilirsiniz.',
        'hero_image' => '',
        'about_short' => 'Hakkımda kısa bir tanıtım metni. Bu alanı yönetim panelinden değiştirebilirsiniz.',
        'about_long' => '<p>Hakkımda sayfasının uzun metni. Yönetim panelindeki Ayarlar bölümünden bu içeriği düzenleyebilirsiniz.</p>',
        'about_photo' => '',
        'contact_email' => '',
        'contact_phone' => '',
        'contact_address' => '',
        'social_instagram' => '',
        'social_twitter' => '',
        'social_linkedin' => '',
        'social_youtube' => '',
        'social_facebook' => '',
        'footer_text' => '© ' . date('Y') . ' Tüm hakları saklıdır.',
    ];
    $stmt = $pdo->prepare('INSERT OR IGNORE INTO settings (key, value) VALUES (?, ?)');
    foreach ($defaults as $key => $value) {
        $stmt->execute([$key, $value]);
    }

    $pdo->prepare('INSERT OR IGNORE INTO categories (name, slug) VALUES (?, ?)')->execute(['Genel', 'genel']);

    $count = (int) $pdo->query('SELECT COUNT(*) FROM articles')->fetchColumn();
    if ($count === 0) {
        $catId = (int) $pdo->query("SELECT id FROM categories WHERE slug = 'genel'")->fetchColumn();
        $pdo->prepare('INSERT INTO articles (title, slug, excerpt, content, category_id, is_published, published_at)
                       VALUES (?, ?, ?, ?, ?, 1, ?)')
            ->execute([
                'Siteye hoş geldiniz',
                'siteye-hos-geldiniz',
                'Bu, sitenin ilk örnek yazısıdır. Yönetim panelinden silebilir veya düzenleyebilirsiniz.',
                '<p>Bu, sitenin ilk örnek yazısıdır. Yönetim paneline <strong>/admin</strong> adresinden giriş yaparak yeni yazılar ekleyebilir, bu yazıyı düzenleyebilir veya silebilirsiniz.</p><p>Yazılar başlık, tarih, kategori, özet ve kapak görseli ile yayınlanır.</p>',
                $catId,
                date('Y-m-d H:i:s'),
            ]);
    }
}
