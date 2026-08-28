<?php
// includes/header.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/seo.php';

$site_url = defined('SITE_URL') ? SITE_URL : '';
$page_title = $page_title ?? 'PUC - Bán & Cho Thuê Máy Photocopy Uy Tín Tại Hà Nội';
$page_description = $page_description ?? 'PUC chuyên cung cấp và cho thuê máy photocopy Ricoh, Toshiba, Canon chính hãng giá tốt nhất tại Hà Nội. Hotline: 0907 586 969';
$page_keywords = $page_keywords ?? 'máy photocopy, thuê máy photocopy, máy photocopy ricoh, PUC, hà nội';
$canonical_url = $canonical_url ?? ($site_url . $_SERVER['REQUEST_URI']);
$page_image = $page_image ?? ($site_url . '/assets/images/logo.png');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?= render_meta_tags($page_title, $page_description, $page_keywords, $canonical_url, $page_image); ?>
    <?= render_og_tags($page_title, $page_description, $canonical_url, $page_image); ?>
    <?= render_schema_organization(); ?>
    <?= render_schema_website(); ?>
    
    <link rel="icon" href="<?= $site_url ?>/assets/images/logo.png" type="image/png">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $site_url ?>/assets/css/style.css">
</head>
<body>

<!-- Top Bar -->
<div class="top-bar">
    <div class="container-fluid px-lg-4 px-2">
        <div class="d-flex align-items-center justify-content-between">
            <div class="top-bar-marquee-wrapper flex-grow-1 overflow-hidden me-2">
                <div class="top-bar-marquee-content">
                    <span class="marquee-item me-4">
                        <i class="fas fa-phone-alt text-warning me-1"></i> <strong>Hotline / Zalo:</strong> <a href="tel:<?= HOTLINE ?>" class="fw-bold text-warning text-decoration-none ms-1"><?= HOTLINE ?></a>
                    </span>
                    <span class="marquee-item me-4">
                        <i class="fas fa-map-marker-alt text-warning me-1"></i> <strong>Địa chỉ:</strong> <span class="ms-1"><?= ADDRESS ?></span>
                    </span>
                    <span class="marquee-item me-4">
                        <i class="fas fa-envelope text-warning me-1"></i> <strong>Email:</strong> <a href="mailto:<?= EMAIL ?>" class="text-white text-decoration-none ms-1"><?= EMAIL ?></a>
                    </span>
                    <span class="marquee-item me-4">
                        <i class="fas fa-clock text-warning me-1"></i> <strong>Giờ làm việc:</strong> <span class="ms-1">T2 - T7: 8:00 - 18:00 (Hỗ trợ kỹ thuật 24/7)</span>
                    </span>
                    <span class="marquee-item me-4">
                        <i class="fas fa-award text-warning me-1"></i> <strong>PUC:</strong> <span class="ms-1 text-info fw-bold">Chuyên Bán & Cho Thuê Máy Photocopy Ricoh, Toshiba, Canon Chính Hãng - Giá Tốt Nhất Hà Nội!</span>
                    </span>
                </div>
            </div>
            <div class="top-bar-social d-none d-lg-flex align-items-center flex-shrink-0">
                <a href="tel:<?= HOTLINE ?>" class="btn btn-sm btn-warning text-dark py-0 px-2 me-3 fw-bold small"><i class="fas fa-phone-alt me-1"></i> <?= HOTLINE ?></a>
                <a href="https://facebook.com" target="_blank" class="text-white me-2" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="https://zalo.me/<?= HOTLINE ?>" target="_blank" class="text-white me-2" title="Zalo"><i class="fas fa-comment-dots"></i></a>
                <a href="https://youtube.com" target="_blank" class="text-white" title="YouTube"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Main Header -->
<header class="main-header py-2 bg-white sticky-top shadow-sm">
    <div class="container">
        <div class="row align-items-center">
            <!-- Logo -->
            <div class="col-6 col-lg-3 col-md-4">
                <a href="<?= $site_url ?>/" class="d-flex align-items-center text-decoration-none">
                    <img src="<?= $site_url ?>/assets/images/logo.png" alt="PUC Logo" class="img-fluid logo-img" style="height: 52px; object-fit: contain;">
                    <div class="ms-2">
                        <span class="fs-4 fw-bold text-primary d-block lh-1">PUC</span>
                        <small class="text-muted text-uppercase" style="font-size: 10px; letter-spacing: 1px;">Photocopy Uy Tín</small>
                    </div>
                </a>
            </div>
            
            <!-- Search Bar -->
            <div class="col-12 col-lg-5 col-md-4 order-3 order-md-2 mt-2 mt-md-0 position-relative">
                <form action="<?= $site_url ?>/pages/search.php" method="GET" class="search-form d-flex position-relative">
                    <input type="text" name="q" id="searchInput" class="form-control rounded-pill pe-5 shadow-none" placeholder="Tìm kiếm máy photocopy, mã máy, linh kiện..." autocomplete="off" required>
                    <button type="submit" class="btn btn-search-icon position-absolute end-0 top-0 bottom-0 border-0 rounded-pill px-3 text-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                <!-- Ajax suggestions dropdown -->
                <div id="searchSuggestions" class="search-suggestions position-absolute start-0 end-0 bg-white rounded shadow-lg border mt-1 d-none" style="z-index: 1050; max-height: 380px; overflow-y: auto;"></div>
            </div>
            
            <!-- Hotline & Contact -->
            <div class="col-6 col-lg-4 col-md-4 order-2 order-md-3 text-end d-flex justify-content-end align-items-center">
                <div class="hotline-box d-none d-xl-flex align-items-center me-3 text-start">
                    <div class="icon-circle bg-danger text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div>
                        <small class="d-block text-muted lh-1" style="font-size: 11px;">Hotline 24/7</small>
                        <a href="tel:<?= HOTLINE ?>" class="fw-bold text-danger text-decoration-none fs-6"><?= HOTLINE ?></a>
                    </div>
                </div>

                <a href="https://zalo.me/<?= HOTLINE ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 d-none d-sm-inline-block me-2">
                    <i class="fas fa-comment-dots me-1"></i>Chat Zalo
                </a>

                <!-- Mobile Menu Button -->
                <button class="btn btn-light d-lg-none border shadow-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                    <i class="fas fa-bars fs-5 text-primary"></i>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Main Navigation (Desktop) -->
<nav class="main-nav bg-primary text-white d-none d-lg-block">
    <div class="container">
        <ul class="nav-menu d-flex list-unstyled mb-0 align-items-center">
            <li class="nav-item">
                <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>" href="<?= $site_url ?>/">
                    <i class="fas fa-home me-1"></i>TRANG CHỦ
                </a>
            </li>

            <!-- Mega Menu: Máy Photocopy -->
            <li class="nav-item dropdown-mega position-relative">
                <a class="nav-link" href="<?= $site_url ?>/pages/products.php?category=may-photocopy">
                    MÁY PHOTOCOPY <i class="fas fa-chevron-down ms-1 small"></i>
                </a>
                <div class="mega-menu bg-white text-dark shadow-lg rounded-bottom p-4">
                    <div class="row g-3">
                        <div class="col-3">
                            <h6 class="fw-bold text-primary border-bottom pb-2 mb-2">MÁY TRẮNG ĐEN</h6>
                            <ul class="list-unstyled mega-links">
                                <li><a href="<?= $site_url ?>/pages/products.php?category=may-photocopy-trang-den&brand=ricoh">Ricoh Trắng Đen</a></li>
                                <li><a href="<?= $site_url ?>/pages/products.php?category=may-photocopy-trang-den&brand=toshiba">Toshiba Trắng Đen</a></li>
                                <li><a href="<?= $site_url ?>/pages/products.php?category=may-photocopy-trang-den&condition=renew">Ricoh Renew 99%</a></li>
                                <li><a href="<?= $site_url ?>/pages/products.php?category=may-photocopy-trang-den&condition=renew">Toshiba Renew 99%</a></li>
                            </ul>
                        </div>
                        <div class="col-3">
                            <h6 class="fw-bold text-primary border-bottom pb-2 mb-2">MÁY PHOTOCOPY MÀU</h6>
                            <ul class="list-unstyled mega-links">
                                <li><a href="<?= $site_url ?>/pages/products.php?category=may-photocopy-mau&brand=ricoh">Ricoh Màu Nhập Khẩu</a></li>
                                <li><a href="<?= $site_url ?>/pages/products.php?category=may-photocopy-mau&brand=toshiba">Toshiba Màu Nhập Khẩu</a></li>
                                <li><a href="<?= $site_url ?>/pages/products.php?category=may-photocopy-mau&brand=konica-minolta">Konica Minolta Màu</a></li>
                                <li><a href="<?= $site_url ?>/pages/products.php?category=may-photocopy-mau&condition=renew">Máy Màu Renew 99%</a></li>
                            </ul>
                        </div>
                        <div class="col-3">
                            <h6 class="fw-bold text-primary border-bottom pb-2 mb-2">MÁY MỚI 100%</h6>
                            <ul class="list-unstyled mega-links">
                                <li><a href="<?= $site_url ?>/pages/products.php?condition=new&brand=ricoh">Ricoh Mới 100%</a></li>
                                <li><a href="<?= $site_url ?>/pages/products.php?condition=new&brand=toshiba">Toshiba Mới 100%</a></li>
                                <li><a href="<?= $site_url ?>/pages/products.php?condition=new&brand=canon">Canon Mới 100%</a></li>
                                <li><a href="<?= $site_url ?>/pages/products.php?condition=new&brand=fujifilm">Fujifilm Mới 100%</a></li>
                            </ul>
                        </div>
                        <div class="col-3">
                            <h6 class="fw-bold text-primary border-bottom pb-2 mb-2">MÁY KHỔ LỚN A0</h6>
                            <ul class="list-unstyled mega-links">
                                <li><a href="<?= $site_url ?>/pages/products.php?category=may-kho-lon-a0">Ricoh Khổ A0</a></li>
                                <li><a href="<?= $site_url ?>/pages/products.php?category=may-kho-lon-a0&brand=hp">HP Khổ A0</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </li>

            <!-- Cho thuê -->
            <li class="nav-item">
                <a class="nav-link" href="<?= $site_url ?>/pages/rental.php">
                    CHO THUÊ MÁY PHOTOCOPY
                </a>
            </li>

            <!-- Máy in -->
            <li class="nav-item">
                <a class="nav-link" href="<?= $site_url ?>/pages/products.php?category=may-in">
                    MÁY IN
                </a>
            </li>

            <!-- Vật tư -->
            <li class="nav-item dropdown position-relative">
                <a class="nav-link" href="<?= $site_url ?>/pages/supplies.php">
                    VẬT TƯ & LINH KIỆN <i class="fas fa-chevron-down ms-1 small"></i>
                </a>
                <ul class="dropdown-menu shadow">
                    <li><a class="dropdown-item" href="<?= $site_url ?>/pages/supplies.php?category=muc-may-photocopy">Mực Máy Photocopy</a></li>
                    <li><a class="dropdown-item" href="<?= $site_url ?>/pages/supplies.php?category=linh-kien-ricoh">Linh Kiện Ricoh</a></li>
                    <li><a class="dropdown-item" href="<?= $site_url ?>/pages/supplies.php?category=linh-kien-toshiba">Linh Kiện Toshiba</a></li>
                    <li><a class="dropdown-item" href="<?= $site_url ?>/pages/supplies.php?category=linh-kien-konica">Linh Kiện Konica</a></li>
                    <li><a class="dropdown-item" href="<?= $site_url ?>/pages/supplies.php?category=linh-kien-canon">Linh Kiện Canon / HP</a></li>
                </ul>
            </li>

            <!-- Tin tức -->
            <li class="nav-item">
                <a class="nav-link" href="<?= $site_url ?>/pages/news.php">
                    TIN TỨC
                </a>
            </li>

            <!-- Liên hệ -->
            <li class="nav-item">
                <a class="nav-link" href="<?= $site_url ?>/pages/contact.php">
                    LIÊN HỆ
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- Mobile Menu Offcanvas -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu">
    <div class="offcanvas-header bg-primary text-white">
        <div class="d-flex align-items-center">
            <img src="<?= $site_url ?>/assets/images/logo.png" alt="PUC" style="height: 35px;" class="bg-white p-1 rounded me-2">
            <h5 class="offcanvas-title fw-bold mb-0">MENU PUC</h5>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        <!-- Hotline bar inside mobile -->
        <div class="p-3 bg-light border-bottom">
            <small class="text-muted d-block">Hotline hỗ trợ 24/7:</small>
            <a href="tel:<?= HOTLINE ?>" class="text-danger fw-bold fs-5 text-decoration-none"><?= HOTLINE ?></a>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><a href="<?= $site_url ?>/" class="text-decoration-none text-dark fw-semibold d-block py-2"><i class="fas fa-home text-primary me-2"></i>TRANG CHỦ</a></li>
            
            <li class="list-group-item">
                <a href="#mobPhoto" class="text-decoration-none text-dark fw-semibold d-flex justify-content-between align-items-center py-2" data-bs-toggle="collapse">
                    <span><i class="fas fa-print text-primary me-2"></i>MÁY PHOTOCOPY</span>
                    <i class="fas fa-chevron-down small text-muted"></i>
                </a>
                <div class="collapse" id="mobPhoto">
                    <ul class="list-unstyled ps-4 py-2 border-top mt-1 bg-light">
                        <li class="py-1"><a href="<?= $site_url ?>/pages/products.php?category=may-photocopy-trang-den" class="text-secondary text-decoration-none d-block">Máy Trắng Đen</a></li>
                        <li class="py-1"><a href="<?= $site_url ?>/pages/products.php?category=may-photocopy-mau" class="text-secondary text-decoration-none d-block">Máy Màu</a></li>
                        <li class="py-1"><a href="<?= $site_url ?>/pages/products.php?condition=new" class="text-secondary text-decoration-none d-block">Máy Mới 100%</a></li>
                        <li class="py-1"><a href="<?= $site_url ?>/pages/products.php?category=may-kho-lon-a0" class="text-secondary text-decoration-none d-block">Máy Khổ A0</a></li>
                    </ul>
                </div>
            </li>

            <li class="list-group-item"><a href="<?= $site_url ?>/pages/rental.php" class="text-decoration-none text-dark fw-semibold d-block py-2"><i class="fas fa-handshake text-success me-2"></i>CHO THUÊ MÁY PHOTOCOPY</a></li>
            <li class="list-group-item"><a href="<?= $site_url ?>/pages/products.php?category=may-in" class="text-decoration-none text-dark fw-semibold d-block py-2"><i class="fas fa-file-alt text-primary me-2"></i>MÁY IN</a></li>
            <li class="list-group-item"><a href="<?= $site_url ?>/pages/supplies.php" class="text-decoration-none text-dark fw-semibold d-block py-2"><i class="fas fa-cogs text-warning me-2"></i>VẬT TƯ & LINH KIỆN</a></li>
            <li class="list-group-item"><a href="<?= $site_url ?>/pages/news.php" class="text-decoration-none text-dark fw-semibold d-block py-2"><i class="fas fa-newspaper text-info me-2"></i>TIN TỨC</a></li>
            <li class="list-group-item"><a href="<?= $site_url ?>/pages/contact.php" class="text-decoration-none text-dark fw-semibold d-block py-2"><i class="fas fa-envelope text-danger me-2"></i>LIÊN HỆ</a></li>
        </ul>
    </div>
</div>
