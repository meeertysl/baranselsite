<?php
declare(strict_types=1);

/** HTML kaçışlama. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Site içi yol üretir. */
function url(string $path = ''): string
{
    return '/' . ltrim($path, '/');
}

/** Statik dosya yolu; önbelleği atlatmak için dosya değişim zamanını ekler. */
function asset(string $path): string
{
    $file = __DIR__ . '/../public/' . ltrim($path, '/');
    $v = is_file($file) ? (string) filemtime($file) : '1';
    return url($path) . '?v=' . $v;
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

/** CSRF belirteci üretir / döndürür. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

/** POST isteğinde CSRF doğrulaması yapar; geçersizse 403 verir. */
function csrf_check(): void
{
    $sent = $_POST['_csrf'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(403);
        exit('Geçersiz istek (CSRF).');
    }
}

/** Tek seferlik mesaj bırakır. */
function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

/** Bekleyen tüm mesajları alıp temizler. */
function take_flashes(): array
{
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

/** Tüm ayarları tek seferde yükleyip önbelleğe alır. */
function settings(): array
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (db()->query('SELECT key, value FROM settings') as $row) {
            $cache[$row['key']] = $row['value'];
        }
    }
    return $cache;
}

function setting(string $key, string $default = ''): string
{
    return settings()[$key] ?? $default;
}

function save_setting(string $key, string $value): void
{
    db()->prepare('INSERT INTO settings (key, value) VALUES (?, ?)
                   ON CONFLICT(key) DO UPDATE SET value = excluded.value')
        ->execute([$key, $value]);
}

/** Türkçe karakterleri de düzgün çeviren slug üretici. */
function slugify(string $text): string
{
    $map = ['ç' => 'c', 'Ç' => 'c', 'ğ' => 'g', 'Ğ' => 'g', 'ı' => 'i', 'I' => 'i', 'İ' => 'i',
            'ö' => 'o', 'Ö' => 'o', 'ş' => 's', 'Ş' => 's', 'ü' => 'u', 'Ü' => 'u'];
    $text = strtr($text, $map);
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[^a-z0-9]+/u', '-', $text) ?? '';
    $text = trim($text, '-');
    return $text !== '' ? $text : 'yazi';
}

/** Aynı slug varsa sonuna sayı ekleyerek benzersiz hale getirir. */
function unique_slug(string $table, string $slug, ?int $ignoreId = null): string
{
    $base = $slug;
    $i = 2;
    while (true) {
        $sql = "SELECT COUNT(*) FROM {$table} WHERE slug = ?" . ($ignoreId ? ' AND id != ?' : '');
        $stmt = db()->prepare($sql);
        $stmt->execute($ignoreId ? [$slug, $ignoreId] : [$slug]);
        if ((int) $stmt->fetchColumn() === 0) {
            return $slug;
        }
        $slug = $base . '-' . $i++;
    }
}

/** "20 Eylül 2026" biçiminde Türkçe tarih. */
function format_date(?string $datetime): string
{
    if (!$datetime) {
        return '';
    }
    $months = ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
    $ts = strtotime($datetime);
    if ($ts === false) {
        return $datetime;
    }
    return date('j', $ts) . ' ' . $months[(int) date('n', $ts) - 1] . ' ' . date('Y', $ts);
}

/** Yazı içeriğinden yaklaşık okuma süresi (dakika). */
function reading_time(string $html): int
{
    $words = str_word_count(strip_tags($html));
    return max(1, (int) ceil($words / 200));
}

/** Okunma sayısını binlik ayraçla yazar: 1.248 */
function format_count(int $n): string
{
    return number_format($n, 0, ',', '.');
}

/** Okunma sayısını göz simgesiyle birlikte gösteren küçük parça. */
function views_badge(int $n): string
{
    return '<span class="views" title="' . format_count($n) . ' görüntülenme">'
        . '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M1.5 12S5 5.5 12 5.5 22.5 12 22.5 12 19 18.5 12 18.5 1.5 12 1.5 12Z"/><circle cx="12" cy="12" r="3.2"/></svg>'
        . format_count($n) . '</span>';
}

/** Metni belirli uzunlukta kısaltır. */
function excerpt_of(string $html, int $length = 160): string
{
    $text = trim(preg_replace('/\s+/u', ' ', strip_tags($html)) ?? '');
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return rtrim(mb_substr($text, 0, $length)) . '…';
}

function is_admin(): bool
{
    return !empty($_SESSION['admin_id']);
}

function require_admin(): void
{
    if (!is_admin()) {
        redirect('/admin');
    }
}

/** Görsel yükleme; başarıda web yolunu döndürür, hata varsa string hata mesajı. */
function handle_upload(string $field): array
{
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return ['path' => null, 'error' => null];
    }
    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['path' => null, 'error' => 'Dosya yüklenemedi (hata kodu ' . $file['error'] . ').'];
    }
    if ($file['size'] > 8 * 1024 * 1024) {
        return ['path' => null, 'error' => 'Dosya 8 MB sınırını aşıyor.'];
    }
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($allowed[$mime])) {
        return ['path' => null, 'error' => 'Sadece JPG, PNG, WEBP ve GIF yüklenebilir.'];
    }
    $dir = config('upload_dir');
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $name = date('Ymd') . '-' . bin2hex(random_bytes(6)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
        return ['path' => null, 'error' => 'Dosya kaydedilemedi. Klasör izinlerini kontrol edin.'];
    }
    return ['path' => config('upload_url') . '/' . $name, 'error' => null];
}

/** Görünüm dosyasını verilen değişkenlerle çalıştırıp çıktıyı döndürür. */
function render(string $view, array $data = []): string
{
    extract($data, EXTR_SKIP);
    ob_start();
    require __DIR__ . '/views/' . $view . '.php';
    return (string) ob_get_clean();
}

/** Görünümü ana şablon içine yerleştirip ekrana basar. */
function view(string $view, array $data = [], string $layout = 'layouts/main'): void
{
    $data['content'] = render($view, $data);
    echo render($layout, $data);
}

function abort(int $code = 404, string $message = 'Sayfa bulunamadı'): never
{
    http_response_code($code);
    view('errors/404', ['title' => $message, 'message' => $message, 'code' => $code]);
    exit;
}

/** Sayfalama bilgisi. */
function paginate(int $total, int $page, int $perPage): array
{
    $pages = max(1, (int) ceil($total / $perPage));
    $page = min(max(1, $page), $pages);
    return [
        'page' => $page,
        'pages' => $pages,
        'total' => $total,
        'offset' => ($page - 1) * $perPage,
        'per_page' => $perPage,
    ];
}
