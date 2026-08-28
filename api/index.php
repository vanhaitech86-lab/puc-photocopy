<?php
/**
 * Vercel Serverless Front-Controller Router for PUC Photocopy
 */
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// Normalize trailing slash (except root)
if ($uri !== '/' && substr($uri, -1) === '/') {
    $uri = rtrim($uri, '/');
}

// 1. Admin Panel Router
if (strpos($uri, '/admin') === 0) {
    $sub = substr($uri, 6); // Remove /admin
    $sub = trim($sub, '/');
    
    if ($sub === '' || $sub === 'index.php') {
        require __DIR__ . '/../admin/index.php';
    } elseif (file_exists(__DIR__ . '/../admin/' . $sub)) {
        require __DIR__ . '/../admin/' . $sub;
    } elseif (file_exists(__DIR__ . '/../admin/' . $sub . '.php')) {
        require __DIR__ . '/../admin/' . $sub . '.php';
    } else {
        require __DIR__ . '/../admin/index.php';
    }
    exit;
}

// 2. Direct API
if ($uri === '/api.php' || strpos($uri, '/api.php') === 0) {
    require __DIR__ . '/../api.php';
    exit;
}

// 3. Sitemap & Robots
if ($uri === '/sitemap.xml' || $uri === '/sitemap.php') {
    require __DIR__ . '/../sitemap.php';
    exit;
}
if ($uri === '/robots.txt') {
    header('Content-Type: text/plain');
    readfile(__DIR__ . '/../robots.txt');
    exit;
}

// 4. Product Detail (slug.html)
if (preg_match('/^\/([a-z0-9\-]+)\.html$/', $uri, $matches)) {
    $_GET['slug'] = $matches[1];
    require __DIR__ . '/../pages/product-detail.php';
    exit;
}

// 5. News Detail (/tin/slug)
if (preg_match('/^\/tin\/([a-z0-9\-]+)$/', $uri, $matches)) {
    $_GET['slug'] = $matches[1];
    require __DIR__ . '/../pages/news-detail.php';
    exit;
}

// 6. Main Pages
switch ($uri) {
    case '':
    case '/':
        require __DIR__ . '/../index.php';
        break;

    case '/may-photocopy':
    case '/may-photocopy-trang-den':
    case '/may-photocopy-mau':
    case '/may-photocopy-moi':
    case '/may-kho-lon-a0':
    case '/may-in':
    case '/may-ep-ban-cat':
        $_GET['category'] = trim($uri, '/');
        require __DIR__ . '/../pages/products.php';
        break;

    case '/cho-thue-may-photocopy':
        require __DIR__ . '/../pages/rental.php';
        break;
    case '/thue-may-trang-den':
        $_GET['type'] = 'bw';
        require __DIR__ . '/../pages/rental.php';
        break;
    case '/thue-may-mau':
        $_GET['type'] = 'color';
        require __DIR__ . '/../pages/rental.php';
        break;

    case '/vat-tu-linh-kien':
    case '/muc-may-photocopy':
    case '/linh-kien-ricoh':
    case '/linh-kien-toshiba':
    case '/linh-kien-konica':
        if ($uri !== '/vat-tu-linh-kien') {
            $_GET['category'] = trim($uri, '/');
        }
        require __DIR__ . '/../pages/supplies.php';
        break;

    case '/tin-tuc':
        require __DIR__ . '/../pages/news.php';
        break;
    case '/dich-vu':
        $_GET['category'] = 'service';
        require __DIR__ . '/../pages/news.php';
        break;
    case '/tai-lieu':
        $_GET['category'] = 'guide';
        require __DIR__ . '/../pages/news.php';
        break;
    case '/driver':
        $_GET['category'] = 'driver';
        require __DIR__ . '/../pages/news.php';
        break;

    case '/lien-he':
        require __DIR__ . '/../pages/contact.php';
        break;

    case '/search':
    case '/tim-kiem':
        require __DIR__ . '/../pages/search.php';
        break;

    default:
        // Check if direct page request inside pages/
        if (strpos($uri, '/pages/') === 0) {
            $pageFile = substr($uri, 7);
            if (file_exists(__DIR__ . '/../pages/' . $pageFile)) {
                require __DIR__ . '/../pages/' . $pageFile;
                break;
            }
        }
        
        // Category fallback
        $slug = trim($uri, '/');
        $_GET['category'] = $slug;
        require __DIR__ . '/../pages/products.php';
        break;
}
