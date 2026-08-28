<?php
/**
 * PUC - Cấu hình website
 */

// Database Configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'puc_photocopy');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_CHARSET', 'utf8mb4');

// Dynamic Site URL (Works on localhost, cPanel, custom domain & Vercel)
if (getenv('SITE_URL')) {
    $site_url = rtrim(getenv('SITE_URL'), '/');
} else {
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script_dir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
    if ($script_dir === '/' || $script_dir === '\\' || $script_dir === '.') {
        $script_dir = '';
    }
    // If inside /pages or /admin, remove subfolder
    $script_dir = preg_replace('/(\/pages|\/admin)$/', '', $script_dir);
    $site_url = rtrim($protocol . '://' . $host . $script_dir, '/');
}
define('SITE_URL', $site_url);

define('SITE_NAME', 'PUC - Máy Photocopy');
define('SITE_DESCRIPTION', 'PUC chuyên bán và cho thuê máy photocopy chính hãng, giá tốt nhất tại Hà Nội');

// Contact Info
define('HOTLINE', '0907586969');
define('EMAIL', 'phuong86.annguyen@gmail.com');
define('ADDRESS', 'Số 21 ngõ 75 Cầu Đất - Cửa Nam - Hà Nội');

// File paths
define('ROOT_PATH', __DIR__);
define('UPLOAD_PATH', ROOT_PATH . '/assets/uploads/');
define('UPLOAD_URL', SITE_URL . '/assets/uploads/');

// Admin & Pagination
define('ADMIN_PER_PAGE', 20);
define('PRODUCTS_PER_PAGE', 12);
define('NEWS_PER_PAGE', 9);

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Error reporting (hide in production / vercel unless debugging)
if (getenv('VERCEL') || getenv('APP_ENV') === 'production') {
    error_reporting(0);
    ini_set('display_errors', '0');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}
