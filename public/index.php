<?php
declare(strict_types=1);

// PHP'nin yerleşik sunucusu ile çalışırken statik dosyaları doğrudan sun.
if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($file) && !str_ends_with($file, '.php')) {
        return false;
    }
}

require __DIR__ . '/../app/bootstrap.php';
require __DIR__ . '/../app/controllers/site.php';
require __DIR__ . '/../app/controllers/admin.php';

$path = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/', '/') ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

$routes = [
    // Ziyaretçi sayfaları
    ['GET',  '#^/$#',                         'site_home'],
    ['GET',  '#^/hakkimda$#',                 'site_about'],
    ['GET',  '#^/yazilar$#',                  'site_articles'],
    ['GET',  '#^/yazi/([a-z0-9\-]+)$#',       'site_article'],
    ['GET',  '#^/iletisim$#',                 'site_contact'],
    ['POST', '#^/iletisim$#',                 'site_contact_send'],

    // Yönetim paneli
    ['GET',  '#^/admin$#',                    'admin_login'],
    ['POST', '#^/admin$#',                    'admin_login_post'],
    ['POST', '#^/admin/cikis$#',              'admin_logout'],
    ['GET',  '#^/admin/panel$#',              'admin_dashboard'],
    ['GET',  '#^/admin/yazilar$#',            'admin_articles'],
    ['GET',  '#^/admin/yazilar/yeni$#',       'admin_article_form'],
    ['POST', '#^/admin/yazilar/yeni$#',       'admin_article_save'],
    ['GET',  '#^/admin/yazilar/(\d+)$#',      'admin_article_form'],
    ['POST', '#^/admin/yazilar/(\d+)$#',      'admin_article_save'],
    ['POST', '#^/admin/yazilar/(\d+)/sil$#',  'admin_article_delete'],
    ['GET',  '#^/admin/kategoriler$#',        'admin_categories'],
    ['POST', '#^/admin/kategoriler$#',        'admin_category_save'],
    ['POST', '#^/admin/kategoriler/(\d+)/sil$#', 'admin_category_delete'],
    ['GET',  '#^/admin/ayarlar$#',            'admin_settings'],
    ['POST', '#^/admin/ayarlar$#',            'admin_settings_save'],
    ['GET',  '#^/admin/mesajlar$#',           'admin_messages'],
    ['POST', '#^/admin/mesajlar/(\d+)/sil$#', 'admin_message_delete'],
    ['POST', '#^/admin/mesajlar/(\d+)/okundu$#', 'admin_message_read'],
    ['GET',  '#^/admin/sifre$#',              'admin_password'],
    ['POST', '#^/admin/sifre$#',              'admin_password_save'],
    ['POST', '#^/admin/yukle$#',              'admin_upload'],
];

foreach ($routes as [$routeMethod, $pattern, $handler]) {
    if ($routeMethod === $method && preg_match($pattern, $path, $m)) {
        array_shift($m);
        $handler(...$m);
        exit;
    }
}

abort(404);
