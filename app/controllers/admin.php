<?php
declare(strict_types=1);

// ---------- Giriş / çıkış ----------

function admin_login(): void
{
    if (is_admin()) {
        redirect('/admin/panel');
    }
    view('admin/login', ['title' => 'Yönetici Girişi'], 'layouts/admin_bare');
}

function admin_login_post(): void
{
    csrf_check();
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    // Basit kaba kuvvet koruması: 5 hatalı denemeden sonra 10 dakika bekle.
    $attempts = $_SESSION['login_attempts'] ?? ['n' => 0, 'until' => 0];
    if ($attempts['n'] >= 5 && time() < $attempts['until']) {
        flash('error', 'Çok fazla hatalı deneme. Lütfen birkaç dakika sonra tekrar deneyin.');
        redirect('/admin');
    }

    $stmt = db()->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) $user['id'];
        $_SESSION['admin_name'] = $user['username'];
        unset($_SESSION['login_attempts']);
        redirect('/admin/panel');
    }

    $attempts['n']++;
    $attempts['until'] = time() + 600;
    $_SESSION['login_attempts'] = $attempts;
    flash('error', 'Kullanıcı adı veya şifre hatalı.');
    redirect('/admin');
}

function admin_logout(): void
{
    csrf_check();
    $_SESSION = [];
    session_destroy();
    redirect('/admin');
}

// ---------- Panel ----------

function admin_dashboard(): void
{
    require_admin();
    $db = db();
    view('admin/dashboard', [
        'title' => 'Panel',
        'stats' => [
            'articles' => (int) $db->query('SELECT COUNT(*) FROM articles')->fetchColumn(),
            'published' => (int) $db->query('SELECT COUNT(*) FROM articles WHERE is_published = 1')->fetchColumn(),
            'categories' => (int) $db->query('SELECT COUNT(*) FROM categories')->fetchColumn(),
            'messages' => (int) $db->query('SELECT COUNT(*) FROM messages WHERE is_read = 0')->fetchColumn(),
        ],
        'recent' => $db->query('SELECT a.*, c.name AS category_name FROM articles a
                                LEFT JOIN categories c ON c.id = a.category_id
                                ORDER BY a.updated_at DESC LIMIT 5')->fetchAll(),
    ], 'layouts/admin');
}

// ---------- Yazılar ----------

function admin_articles(): void
{
    require_admin();
    $q = trim((string) ($_GET['q'] ?? ''));
    $sql = 'SELECT a.*, c.name AS category_name FROM articles a
            LEFT JOIN categories c ON c.id = a.category_id';
    $params = [];
    if ($q !== '') {
        $sql .= ' WHERE a.title LIKE ?';
        $params[] = '%' . $q . '%';
    }
    $sql .= ' ORDER BY a.published_at DESC, a.id DESC';
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    view('admin/articles', ['title' => 'Yazılar', 'articles' => $stmt->fetchAll(), 'q' => $q], 'layouts/admin');
}

function admin_article_form(?string $id = null): void
{
    require_admin();
    $article = [
        'id' => null, 'title' => '', 'slug' => '', 'excerpt' => '', 'content' => '',
        'cover_image' => '', 'category_id' => null, 'is_published' => 1,
        'published_at' => date('Y-m-d\TH:i'),
    ];
    if ($id !== null) {
        $stmt = db()->prepare('SELECT * FROM articles WHERE id = ?');
        $stmt->execute([(int) $id]);
        $article = $stmt->fetch();
        if (!$article) {
            abort(404, 'Yazı bulunamadı');
        }
        $article['published_at'] = date('Y-m-d\TH:i', strtotime($article['published_at']));
    }
    view('admin/article_form', [
        'title' => $id ? 'Yazıyı Düzenle' : 'Yeni Yazı',
        'article' => $article,
        'categories' => db()->query('SELECT * FROM categories ORDER BY name')->fetchAll(),
    ], 'layouts/admin');
}

function admin_article_save(?string $id = null): void
{
    require_admin();
    csrf_check();

    $id = $id !== null ? (int) $id : null;
    $title = trim((string) ($_POST['title'] ?? ''));
    $slugInput = trim((string) ($_POST['slug'] ?? ''));
    $excerpt = trim((string) ($_POST['excerpt'] ?? ''));
    $content = (string) ($_POST['content'] ?? '');
    $categoryId = ($_POST['category_id'] ?? '') !== '' ? (int) $_POST['category_id'] : null;
    $isPublished = isset($_POST['is_published']) ? 1 : 0;
    $publishedAt = (string) ($_POST['published_at'] ?? '');
    $existingCover = (string) ($_POST['existing_cover'] ?? '');
    $removeCover = isset($_POST['remove_cover']);

    if ($title === '') {
        flash('error', 'Başlık boş olamaz.');
        redirect($id ? "/admin/yazilar/{$id}" : '/admin/yazilar/yeni');
    }

    $ts = strtotime($publishedAt);
    $publishedAt = $ts ? date('Y-m-d H:i:s', $ts) : date('Y-m-d H:i:s');
    $slug = unique_slug('articles', slugify($slugInput !== '' ? $slugInput : $title), $id);

    $upload = handle_upload('cover_image');
    if ($upload['error']) {
        flash('error', $upload['error']);
        redirect($id ? "/admin/yazilar/{$id}" : '/admin/yazilar/yeni');
    }
    $cover = $upload['path'] ?? ($removeCover ? '' : $existingCover);

    if ($excerpt === '') {
        $excerpt = excerpt_of($content, 180);
    }

    if ($id) {
        db()->prepare('UPDATE articles SET title=?, slug=?, excerpt=?, content=?, cover_image=?, category_id=?,
                       is_published=?, published_at=?, updated_at=datetime(\'now\') WHERE id=?')
            ->execute([$title, $slug, $excerpt, $content, $cover, $categoryId, $isPublished, $publishedAt, $id]);
        flash('success', 'Yazı güncellendi.');
    } else {
        db()->prepare('INSERT INTO articles (title, slug, excerpt, content, cover_image, category_id, is_published, published_at)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)')
            ->execute([$title, $slug, $excerpt, $content, $cover, $categoryId, $isPublished, $publishedAt]);
        $id = (int) db()->lastInsertId();
        flash('success', 'Yazı oluşturuldu.');
    }
    redirect("/admin/yazilar/{$id}");
}

function admin_article_delete(string $id): void
{
    require_admin();
    csrf_check();
    db()->prepare('DELETE FROM articles WHERE id = ?')->execute([(int) $id]);
    flash('success', 'Yazı silindi.');
    redirect('/admin/yazilar');
}

// ---------- Kategoriler ----------

function admin_categories(): void
{
    require_admin();
    $rows = db()->query('SELECT c.*, COUNT(a.id) AS n FROM categories c
                         LEFT JOIN articles a ON a.category_id = c.id
                         GROUP BY c.id ORDER BY c.name')->fetchAll();
    view('admin/categories', ['title' => 'Kategoriler', 'categories' => $rows], 'layouts/admin');
}

function admin_category_save(): void
{
    require_admin();
    csrf_check();
    $name = trim((string) ($_POST['name'] ?? ''));
    $id = (int) ($_POST['id'] ?? 0);
    if ($name === '') {
        flash('error', 'Kategori adı boş olamaz.');
        redirect('/admin/kategoriler');
    }
    $slug = unique_slug('categories', slugify($name), $id ?: null);
    if ($id) {
        db()->prepare('UPDATE categories SET name = ?, slug = ? WHERE id = ?')->execute([$name, $slug, $id]);
        flash('success', 'Kategori güncellendi.');
    } else {
        db()->prepare('INSERT INTO categories (name, slug) VALUES (?, ?)')->execute([$name, $slug]);
        flash('success', 'Kategori eklendi.');
    }
    redirect('/admin/kategoriler');
}

function admin_category_delete(string $id): void
{
    require_admin();
    csrf_check();
    db()->prepare('DELETE FROM categories WHERE id = ?')->execute([(int) $id]);
    flash('success', 'Kategori silindi. Bu kategorideki yazılar kategorisiz kaldı.');
    redirect('/admin/kategoriler');
}

// ---------- Ayarlar ----------

/** Ayarlar formundaki alanlar: anahtar => [etiket, tür]. */
function settings_fields(): array
{
    return [
        'Genel' => [
            'site_title' => ['Site başlığı', 'text'],
            'site_tagline' => ['Slogan / alt başlık', 'text'],
            'owner_name' => ['Ad Soyad', 'text'],
            'owner_title' => ['Unvan (ör. Yazar, Psikolog)', 'text'],
            'seo_description' => ['Arama motoru açıklaması', 'textarea'],
            'footer_text' => ['Alt bilgi metni', 'text'],
        ],
        'Ana sayfa' => [
            'hero_title' => ['Karşılama başlığı', 'text'],
            'hero_text' => ['Karşılama metni', 'textarea'],
            'hero_banner' => ['Tam genişlik afiş görseli (yatay, ör. 1536×1024). Varsa karşılama alanı bu afişle gösterilir', 'image'],
            'hero_image' => ['Karşılama görseli (afiş yoksa kullanılan dikey fotoğraf)', 'image'],
        ],
        'Hakkımda' => [
            'about_short' => ['Kısa tanıtım (ana sayfada görünür)', 'textarea'],
            'about_long' => ['Hakkımda sayfası metni', 'editor'],
            'about_photo' => ['Profil fotoğrafı', 'image'],
        ],
        'İletişim' => [
            'contact_email' => ['E-posta', 'text'],
            'contact_phone' => ['Telefon', 'text'],
            'contact_address' => ['Adres', 'textarea'],
        ],
        'Sosyal medya' => [
            'social_instagram' => ['Instagram adresi', 'text'],
            'social_twitter' => ['X / Twitter adresi', 'text'],
            'social_linkedin' => ['LinkedIn adresi', 'text'],
            'social_youtube' => ['YouTube adresi', 'text'],
            'social_facebook' => ['Facebook adresi', 'text'],
        ],
    ];
}

function admin_settings(): void
{
    require_admin();
    view('admin/settings', ['title' => 'Site Ayarları', 'groups' => settings_fields()], 'layouts/admin');
}

function admin_settings_save(): void
{
    require_admin();
    csrf_check();
    foreach (settings_fields() as $fields) {
        foreach ($fields as $key => [$label, $type]) {
            if ($type === 'image') {
                $upload = handle_upload($key);
                if ($upload['error']) {
                    flash('error', $label . ': ' . $upload['error']);
                    continue;
                }
                if ($upload['path']) {
                    save_setting($key, $upload['path']);
                } elseif (isset($_POST['remove_' . $key])) {
                    save_setting($key, '');
                }
                continue;
            }
            if (array_key_exists($key, $_POST)) {
                save_setting($key, trim((string) $_POST[$key]));
            }
        }
    }
    flash('success', 'Ayarlar kaydedildi.');
    redirect('/admin/ayarlar');
}

// ---------- Mesajlar ----------

function admin_messages(): void
{
    require_admin();
    $rows = db()->query('SELECT * FROM messages ORDER BY created_at DESC')->fetchAll();
    view('admin/messages', ['title' => 'Mesajlar', 'messages' => $rows], 'layouts/admin');
}

function admin_message_read(string $id): void
{
    require_admin();
    csrf_check();
    db()->prepare('UPDATE messages SET is_read = 1 WHERE id = ?')->execute([(int) $id]);
    redirect('/admin/mesajlar');
}

function admin_message_delete(string $id): void
{
    require_admin();
    csrf_check();
    db()->prepare('DELETE FROM messages WHERE id = ?')->execute([(int) $id]);
    flash('success', 'Mesaj silindi.');
    redirect('/admin/mesajlar');
}

// ---------- Şifre ----------

function admin_password(): void
{
    require_admin();
    view('admin/password', ['title' => 'Şifre Değiştir'], 'layouts/admin');
}

function admin_password_save(): void
{
    require_admin();
    csrf_check();
    $current = (string) ($_POST['current'] ?? '');
    $new = (string) ($_POST['new'] ?? '');
    $again = (string) ($_POST['again'] ?? '');

    $stmt = db()->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['admin_id']]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($current, $user['password_hash'])) {
        flash('error', 'Mevcut şifre hatalı.');
    } elseif (mb_strlen($new) < 8) {
        flash('error', 'Yeni şifre en az 8 karakter olmalı.');
    } elseif ($new !== $again) {
        flash('error', 'Yeni şifreler birbiriyle uyuşmuyor.');
    } else {
        db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
            ->execute([password_hash($new, PASSWORD_DEFAULT), $user['id']]);
        flash('success', 'Şifre güncellendi.');
    }
    redirect('/admin/sifre');
}

// ---------- Editör görsel yükleme (JSON) ----------

function admin_upload(): void
{
    require_admin();
    header('Content-Type: application/json; charset=utf-8');
    $sent = $_POST['_csrf'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(403);
        echo json_encode(['error' => 'CSRF']);
        return;
    }
    $upload = handle_upload('image');
    if ($upload['error'] || !$upload['path']) {
        http_response_code(422);
        echo json_encode(['error' => $upload['error'] ?? 'Dosya seçilmedi']);
        return;
    }
    echo json_encode(['url' => $upload['path']]);
}
