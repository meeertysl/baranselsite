<?php
declare(strict_types=1);

/** Yayında olan yazıları kategori adıyla birlikte getiren ortak sorgu. */
function published_articles(int $limit, int $offset = 0, ?string $categorySlug = null): array
{
    $sql = 'SELECT a.*, c.name AS category_name, c.slug AS category_slug
            FROM articles a LEFT JOIN categories c ON c.id = a.category_id
            WHERE a.is_published = 1';
    $params = [];
    if ($categorySlug) {
        $sql .= ' AND c.slug = ?';
        $params[] = $categorySlug;
    }
    $sql .= ' ORDER BY a.published_at DESC, a.id DESC LIMIT ? OFFSET ?';
    $stmt = db()->prepare($sql);
    $i = 1;
    foreach ($params as $p) {
        $stmt->bindValue($i++, $p);
    }
    $stmt->bindValue($i++, $limit, PDO::PARAM_INT);
    $stmt->bindValue($i, $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function site_home(): void
{
    $articles = published_articles(6);
    $total = (int) db()->query('SELECT COUNT(*) FROM articles WHERE is_published = 1')->fetchColumn();
    view('home', [
        'title' => setting('site_title'),
        'articles' => $articles,
        'total' => $total,
    ]);
}

function site_about(): void
{
    view('about', ['title' => 'Hakkımda']);
}

function site_articles(): void
{
    $category = isset($_GET['kategori']) ? (string) $_GET['kategori'] : null;
    $page = (int) ($_GET['sayfa'] ?? 1);
    $perPage = (int) config('per_page');

    if ($category) {
        $stmt = db()->prepare('SELECT COUNT(*) FROM articles a JOIN categories c ON c.id = a.category_id
                               WHERE a.is_published = 1 AND c.slug = ?');
        $stmt->execute([$category]);
    } else {
        $stmt = db()->query('SELECT COUNT(*) FROM articles WHERE is_published = 1');
    }
    $pager = paginate((int) $stmt->fetchColumn(), $page, $perPage);

    $categories = db()->query('SELECT c.*, COUNT(a.id) AS n FROM categories c
                               LEFT JOIN articles a ON a.category_id = c.id AND a.is_published = 1
                               GROUP BY c.id ORDER BY c.name')->fetchAll();

    view('articles', [
        'title' => 'Yazılar',
        'articles' => published_articles($perPage, $pager['offset'], $category),
        'pager' => $pager,
        'categories' => $categories,
        'activeCategory' => $category,
    ]);
}

function site_article(string $slug): void
{
    $stmt = db()->prepare('SELECT a.*, c.name AS category_name, c.slug AS category_slug
                           FROM articles a LEFT JOIN categories c ON c.id = a.category_id
                           WHERE a.slug = ? AND a.is_published = 1');
    $stmt->execute([$slug]);
    $article = $stmt->fetch();
    if (!$article) {
        abort(404, 'Yazı bulunamadı');
    }

    $article['view_count'] = (int) $article['view_count'] + count_article_view((int) $article['id']);

    $stmt = db()->prepare('SELECT a.*, c.name AS category_name FROM articles a
                           LEFT JOIN categories c ON c.id = a.category_id
                           WHERE a.is_published = 1 AND a.id != ? ORDER BY a.published_at DESC LIMIT 3');
    $stmt->execute([$article['id']]);

    view('article', [
        'title' => $article['title'],
        'description' => $article['excerpt'] ?: excerpt_of($article['content']),
        'article' => $article,
        'related' => $stmt->fetchAll(),
    ]);
}

/**
 * Yazının okunma sayısını artırır. Aynı ziyaretçi aynı yazıyı oturum boyunca
 * bir kez sayılır; yönetici kendi ziyaretlerini şişirmesin diye hiç sayılmaz.
 * Sayaç artırıldıysa 1, artırılmadıysa 0 döner.
 */
function count_article_view(int $articleId): int
{
    if (is_admin()) {
        return 0;
    }
    $seen = $_SESSION['seen_articles'] ?? [];
    if (in_array($articleId, $seen, true)) {
        return 0;
    }
    $seen[] = $articleId;
    $_SESSION['seen_articles'] = array_slice($seen, -200); // oturum çerezi sınırsız büyümesin
    db()->prepare('UPDATE articles SET view_count = view_count + 1 WHERE id = ?')->execute([$articleId]);
    return 1;
}

function site_contact(): void
{
    view('contact', ['title' => 'İletişim']);
}

function site_contact_send(): void
{
    csrf_check();
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $subject = trim((string) ($_POST['subject'] ?? ''));
    $body = trim((string) ($_POST['message'] ?? ''));
    $honeypot = trim((string) ($_POST['website'] ?? ''));

    if ($honeypot !== '') {
        redirect('/iletisim'); // bot
    }
    if ($name === '' || $body === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('error', 'Lütfen adınızı, geçerli bir e-posta adresini ve mesajınızı yazın.');
        $_SESSION['old'] = compact('name', 'email', 'subject', 'body');
        redirect('/iletisim');
    }

    db()->prepare('INSERT INTO messages (name, email, subject, body) VALUES (?, ?, ?, ?)')
        ->execute([mb_substr($name, 0, 120), mb_substr($email, 0, 190), mb_substr($subject, 0, 190), mb_substr($body, 0, 5000)]);

    flash('success', 'Mesajınız iletildi. Teşekkürler.');
    redirect('/iletisim');
}
