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
        'jsonLd' => [[
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => setting('site_title'),
            'url' => abs_url('/'),
            'inLanguage' => 'tr-TR',
            'author' => ['@type' => 'Person', 'name' => setting('owner_name'), 'jobTitle' => setting('owner_title')],
        ]],
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

    $desc = $article['excerpt'] ?: excerpt_of($article['content']);
    $canonical = '/yazi/' . $article['slug'];
    $owner = setting('owner_name');
    $published = date('c', strtotime((string) $article['published_at']));
    $modified = date('c', strtotime((string) ($article['updated_at'] ?: $article['published_at'])));
    $articleLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $article['title'],
        'description' => $desc,
        'datePublished' => $published,
        'dateModified' => $modified,
        'inLanguage' => 'tr-TR',
        'mainEntityOfPage' => abs_url($canonical),
        'author' => ['@type' => 'Person', 'name' => $owner],
        'publisher' => ['@type' => 'Person', 'name' => $owner],
    ];
    if ($article['cover_image']) {
        $articleLd['image'] = [abs_url($article['cover_image'])];
    }
    $crumbs = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Ana Sayfa', 'item' => abs_url('/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Yazılar', 'item' => abs_url('/yazilar')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $article['title'], 'item' => abs_url($canonical)],
        ],
    ];

    view('article', [
        'title' => $article['title'],
        'description' => $desc,
        'canonicalPath' => $canonical,
        'ogType' => 'article',
        'ogImage' => $article['cover_image'] ?: null,
        'jsonLd' => [$articleLd, $crumbs],
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

/** Arama motorları için XML site haritası. */
function site_sitemap(): void
{
    $rows = db()->query('SELECT slug, published_at, updated_at FROM articles WHERE is_published = 1 ORDER BY published_at DESC')->fetchAll();
    $urls = [
        [abs_url('/'), null], [abs_url('/hakkimda'), null],
        [abs_url('/yazilar'), $rows ? $rows[0]['published_at'] : null], [abs_url('/iletisim'), null],
    ];
    foreach ($rows as $r) {
        $urls[] = [abs_url('/yazi/' . $r['slug']), $r['updated_at'] ?: $r['published_at']];
    }
    header('Content-Type: application/xml; charset=UTF-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "
" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "
";
    foreach ($urls as [$loc, $mod]) {
        echo '  <url><loc>' . e($loc) . '</loc>' . ($mod ? '<lastmod>' . date('Y-m-d', strtotime((string) $mod)) . '</lastmod>' : '') . "</url>
";
    }
    echo '</urlset>';
}

/** robots.txt: yönetimi dışarıda bırakır, site haritasını bildirir. */
function site_robots(): void
{
    header('Content-Type: text/plain; charset=UTF-8');
    echo "User-agent: *
Disallow: /admin
Allow: /

Sitemap: " . abs_url('/sitemap.xml') . "
";
}
