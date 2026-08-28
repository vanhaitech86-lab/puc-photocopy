<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

// Check login
if (!is_admin_logged_in()) {
    header('Location: ' . SITE_URL . '/admin/login.php');
    exit;
}

$current_page = basename($_SERVER['PHP_SELF'] ?? 'index.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị - PUC Photocopy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #1E293B;
            --sidebar-color: #f8f9fa;
            --sidebar-hover: #334155;
            --sidebar-active: #0d6efd;
        }
        body {
            background-color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            overflow-x: hidden;
        }
        #wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }
        #sidebar {
            min-width: var(--sidebar-width);
            max-width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: var(--sidebar-color);
            transition: all 0.3s;
            min-height: 100vh;
            position: fixed;
            z-index: 999;
        }
        #sidebar.collapsed {
            margin-left: calc(-1 * var(--sidebar-width));
        }
        #sidebar .sidebar-header {
            padding: 20px;
            background: rgba(0,0,0,0.1);
            text-align: center;
        }
        #sidebar .sidebar-header h3 {
            color: #fff;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        #sidebar ul.components {
            padding: 20px 0;
        }
        #sidebar ul p {
            color: #fff;
            padding: 10px;
        }
        #sidebar ul li a {
            padding: 12px 20px;
            font-size: 15px;
            display: block;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: 0.2s;
        }
        #sidebar ul li a:hover {
            color: #fff;
            background: var(--sidebar-hover);
        }
        #sidebar ul li.active > a {
            color: #fff;
            background: var(--sidebar-active);
            border-left: 4px solid #fff;
        }
        #sidebar ul li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        #content-wrapper {
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            transition: all 0.3s;
            position: absolute;
            top: 0;
            right: 0;
        }
        #content-wrapper.expanded {
            width: 100%;
        }
        
        #topbar {
            background: #fff;
            padding: 15px 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 998;
        }
        
        #content {
            padding: 20px;
        }
        
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            border-radius: 0.5rem;
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            font-weight: 600;
            padding: 15px 20px;
        }
        
        /* Utility */
        .table-responsive {
            overflow-x: auto;
        }
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>PUC Admin</h3>
            </div>
            
            <ul class="list-unstyled components">
                <li class="<?= $current_page == 'index.php' ? 'active' : '' ?>">
                    <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                </li>
                <li class="<?= $current_page == 'products.php' ? 'active' : '' ?>">
                    <a href="products.php"><i class="fas fa-box"></i> Sản phẩm</a>
                </li>
                <li class="<?= $current_page == 'categories.php' ? 'active' : '' ?>">
                    <a href="categories.php"><i class="fas fa-list"></i> Danh mục</a>
                </li>
                <li class="<?= $current_page == 'promotions.php' ? 'active' : '' ?>">
                    <a href="promotions.php"><i class="fas fa-tags"></i> Khuyến mãi</a>
                </li>
                <li class="<?= $current_page == 'banners.php' ? 'active' : '' ?>">
                    <a href="banners.php"><i class="fas fa-image"></i> Banner</a>
                </li>
                <li class="<?= $current_page == 'news.php' ? 'active' : '' ?>">
                    <a href="news.php"><i class="fas fa-newspaper"></i> Bài viết</a>
                </li>
                <li class="<?= $current_page == 'contacts.php' ? 'active' : '' ?>">
                    <a href="contacts.php"><i class="fas fa-envelope"></i> Liên hệ</a>
                </li>
                <li class="<?= $current_page == 'settings.php' ? 'active' : '' ?>">
                    <a href="settings.php"><i class="fas fa-cog"></i> Cài đặt</a>
                </li>
                <li>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content-wrapper">
            <!-- Topbar -->
            <nav id="topbar">
                <div>
                    <button type="button" id="sidebarCollapse" class="btn btn-light">
                        <i class="fas fa-bars"></i>
                    </button>
                    <a href="../" target="_blank" class="btn btn-outline-primary ms-2 btn-sm"><i class="fas fa-globe"></i> Xem website</a>
                </div>
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-muted"></i> Đăng xuất</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            
            <!-- Main Content -->
            <div id="content">
