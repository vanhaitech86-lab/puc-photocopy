<?php
/**
 * Dynamic Sitemap Generator
 * Access: /sitemap.xml
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';

header('Content-Type: application/xml; charset=UTF-8');

$baseUrl = SITE_URL;

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Trang chủ -->
    <url>
        <loc><?= $baseUrl ?>/</loc>
        <lastmod><?= date('Y-m-d') ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <!-- Trang danh mục -->
<?php
$categories = $pdo->query("SELECT slug, created_at FROM categories WHERE is_active = 1")->fetchAll();
foreach ($categories as $cat):
?>
    <url>
        <loc><?= $baseUrl ?>/<?= $cat['slug'] ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($cat['created_at'])) ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
<?php endforeach; ?>

    <!-- Trang sản phẩm -->
<?php
$products = $pdo->query("SELECT slug, updated_at FROM products WHERE is_active = 1")->fetchAll();
foreach ($products as $prod):
?>
    <url>
        <loc><?= $baseUrl ?>/<?= $prod['slug'] ?>.html</loc>
        <lastmod><?= date('Y-m-d', strtotime($prod['updated_at'])) ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
<?php endforeach; ?>

    <!-- Trang tin tức -->
<?php
$news = $pdo->query("SELECT slug, updated_at FROM news WHERE is_active = 1")->fetchAll();
foreach ($news as $article):
?>
    <url>
        <loc><?= $baseUrl ?>/tin/<?= $article['slug'] ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($article['updated_at'])) ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
<?php endforeach; ?>

    <!-- Trang tĩnh -->
    <url>
        <loc><?= $baseUrl ?>/cho-thue-may-photocopy</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= $baseUrl ?>/vat-tu-linh-kien</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc><?= $baseUrl ?>/tin-tuc</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc><?= $baseUrl ?>/lien-he</loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
</urlset>
