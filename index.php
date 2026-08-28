<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/seo.php';

$page_title = 'PUC - Chuyên Bán & Cho Thuê Máy Photocopy Chính Hãng Tại Hà Nội';
$page_description = 'PUC cung cấp máy photocopy chính hãng Ricoh, Toshiba, Canon, Konica. Dịch vụ cho thuê máy photocopy uy tín, giá tốt nhất Hà Nội. Hotline: 0907 586 969';
$page_keywords = 'máy photocopy, bán máy photocopy, cho thuê máy photocopy, máy photocopy Hà Nội, PUC, Ricoh, Toshiba, Canon';
$canonical_url = SITE_URL;

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section: Sidebar Danh Mục Sản Phẩm + Banner Slider -->
<section class="hero-section py-3">
    <div class="container">
        <div class="row g-3">
            <!-- Cột trái: DANH MỤC SẢN PHẨM (Desktop) -->
            <div class="col-lg-3 d-none d-lg-block">
                <div class="card border-0 shadow-sm category-sidebar-menu h-100">
                    <div class="card-header text-white py-3 fw-bold text-uppercase d-flex align-items-center">
                        <i class="fas fa-bars me-2"></i> DANH MỤC SẢN PHẨM
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="<?= SITE_URL ?>/may-photocopy-trang-den" class="list-group-item list-group-item-action d-flex align-items-center py-2 px-3">
                            <i class="fas fa-print text-primary me-3 fa-fw"></i> Máy Photocopy Trắng Đen
                        </a>
                        <a href="<?= SITE_URL ?>/may-photocopy-mau" class="list-group-item list-group-item-action d-flex align-items-center py-2 px-3">
                            <i class="fas fa-palette text-danger me-3 fa-fw"></i> Máy Photocopy Màu
                        </a>
                        <a href="<?= SITE_URL ?>/may-photocopy-moi" class="list-group-item list-group-item-action d-flex align-items-center py-2 px-3">
                            <i class="fas fa-star text-warning me-3 fa-fw"></i> Máy Photocopy Mới 100%
                        </a>
                        <a href="<?= SITE_URL ?>/may-kho-lon-a0" class="list-group-item list-group-item-action d-flex align-items-center py-2 px-3">
                            <i class="fas fa-expand text-info me-3 fa-fw"></i> Máy Khổ Lớn A0
                        </a>
                        <a href="<?= SITE_URL ?>/cho-thue-may-photocopy" class="list-group-item list-group-item-action d-flex align-items-center py-2 px-3">
                            <i class="fas fa-handshake text-success me-3 fa-fw"></i> Cho Thuê Máy Photocopy
                        </a>
                        <a href="<?= SITE_URL ?>/may-in" class="list-group-item list-group-item-action d-flex align-items-center py-2 px-3">
                            <i class="fas fa-file-alt text-secondary me-3 fa-fw"></i> Máy In Văn Phòng
                        </a>
                        <a href="<?= SITE_URL ?>/vat-tu-linh-kien" class="list-group-item list-group-item-action d-flex align-items-center py-2 px-3">
                            <i class="fas fa-cogs text-dark me-3 fa-fw"></i> Vật Tư & Linh Kiện
                        </a>
                        <a href="<?= SITE_URL ?>/may-ep-ban-cat" class="list-group-item list-group-item-action d-flex align-items-center py-2 px-3">
                            <i class="fas fa-cut text-primary me-3 fa-fw"></i> Máy Ép - Bàn Cắt Giấy
                        </a>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Hero Banner Slider -->
            <div class="col-lg-9 col-12">
                <div class="swiper heroSwiper rounded overflow-hidden shadow-sm h-100">
                    <div class="swiper-wrapper">
                        <?php
                        $banners = get_banners('home_slider');
                        if ($banners):
                            foreach ($banners as $banner):
                        ?>
                            <div class="swiper-slide">
                                <a href="<?= $banner['link'] ?: '#' ?>">
                                    <img src="<?= SITE_URL ?>/assets/<?= $banner['image'] ?>" 
                                         class="d-block w-100" 
                                         style="min-height: 360px; max-height: 400px; object-fit: cover;"
                                         alt="<?= htmlspecialchars($banner['title']) ?>"
                                         loading="lazy">
                                </a>
                            </div>
                        <?php endforeach; else: ?>
                            <div class="swiper-slide">
                                <div class="banner-placeholder bg-primary d-flex align-items-center justify-content-center" style="min-height:360px;">
                                    <div class="text-center text-white p-4">
                                        <h1 class="display-6 fw-bold">PUC - Máy Photocopy</h1>
                                        <p class="fs-6 mb-3">Chuyên bán & cho thuê máy photocopy chính hãng - Hotline: 0907 586 969</p>
                                        <a href="<?= SITE_URL ?>/may-photocopy" class="btn btn-warning fw-bold px-4">Xem Sản Phẩm</a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Khối Icon Grid: DANH MỤC SẢN PHẨM -->
<section class="category-section py-4 bg-light">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
            <h2 class="h5 fw-bold text-uppercase mb-0 text-dark">
                <i class="fas fa-th-large text-primary me-2"></i>DANH MỤC SẢN PHẨM
            </h2>
            <a href="<?= SITE_URL ?>/may-photocopy" class="text-primary text-decoration-none small fw-bold">
                Xem tất cả danh mục <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="category-grid">
            <a href="<?= SITE_URL ?>/may-photocopy-trang-den" class="category-item text-decoration-none">
                <div class="cat-thumb"><i class="fas fa-print fa-2x text-primary"></i></div>
                <span class="cat-label">Máy Trắng Đen</span>
            </a>
            <a href="<?= SITE_URL ?>/may-photocopy-mau" class="category-item text-decoration-none">
                <div class="cat-thumb"><i class="fas fa-palette fa-2x text-danger"></i></div>
                <span class="cat-label">Máy Màu</span>
            </a>
            <a href="<?= SITE_URL ?>/may-photocopy-moi" class="category-item text-decoration-none">
                <div class="cat-thumb"><i class="fas fa-star fa-2x text-warning"></i></div>
                <span class="cat-label">Máy Mới 100%</span>
            </a>
            <a href="<?= SITE_URL ?>/may-kho-lon-a0" class="category-item text-decoration-none">
                <div class="cat-thumb"><i class="fas fa-expand fa-2x text-info"></i></div>
                <span class="cat-label">Máy Khổ A0</span>
            </a>
            <a href="<?= SITE_URL ?>/cho-thue-may-photocopy" class="category-item text-decoration-none">
                <div class="cat-thumb"><i class="fas fa-handshake fa-2x text-success"></i></div>
                <span class="cat-label">Cho Thuê Máy</span>
            </a>
            <a href="<?= SITE_URL ?>/may-in" class="category-item text-decoration-none">
                <div class="cat-thumb"><i class="fas fa-file-alt fa-2x text-secondary"></i></div>
                <span class="cat-label">Máy In</span>
            </a>
            <a href="<?= SITE_URL ?>/vat-tu-linh-kien" class="category-item text-decoration-none">
                <div class="cat-thumb"><i class="fas fa-cogs fa-2x text-dark"></i></div>
                <span class="cat-label">Vật Tư & LK</span>
            </a>
            <a href="<?= SITE_URL ?>/may-ep-ban-cat" class="category-item text-decoration-none">
                <div class="cat-thumb"><i class="fas fa-cut fa-2x text-primary"></i></div>
                <span class="cat-label">Máy Ép - Cắt</span>
            </a>
        </div>
    </div>
</section>

<!-- SẢN PHẨM NỔI BẬT -->
<section class="py-5">
    <div class="container">
        <div class="section-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="section-title"><i class="fas fa-fire text-danger me-2"></i>SẢN PHẨM NỔI BẬT</h2>
                <a href="<?= SITE_URL ?>/may-photocopy" class="btn btn-outline-primary btn-sm">Xem tất cả <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
            <?php
            $featured = get_featured_products(8);
            foreach ($featured as $product):
                $display_price = $product['sale_price'] ?? $product['price'];
            ?>
            <div class="col">
                <div class="card product-card h-100 border-0 shadow-sm">
                    <?php if ($product['sale_price']): ?>
                        <span class="badge bg-danger position-absolute top-0 start-0 m-2">SALE</span>
                    <?php endif; ?>
                    <?php if ($product['condition_type'] === 'new'): ?>
                        <span class="badge bg-success position-absolute top-0 end-0 m-2">Mới 100%</span>
                    <?php elseif ($product['condition_type'] === 'renew'): ?>
                        <span class="badge bg-info position-absolute top-0 end-0 m-2">Renew 99%</span>
                    <?php endif; ?>
                    <a href="<?= SITE_URL ?>/<?= $product['slug'] ?>.html">
                        <img src="<?= $product['image'] ? SITE_URL . '/assets/' . $product['image'] : SITE_URL . '/assets/images/no-image.jpg' ?>" 
                             class="card-img-top p-2" 
                             alt="<?= htmlspecialchars($product['name']) ?>"
                             loading="lazy">
                    </a>
                    <div class="card-body pt-0">
                        <h6 class="card-title mb-2">
                            <a href="<?= SITE_URL ?>/<?= $product['slug'] ?>.html" class="text-decoration-none text-dark">
                                <?= htmlspecialchars($product['name']) ?>
                            </a>
                        </h6>
                        <div class="price-group">
                            <span class="text-danger fw-bold fs-6"><?= format_price($display_price) ?></span>
                            <?php if ($product['sale_price']): ?>
                                <br><small class="text-muted text-decoration-line-through"><?= format_price($product['price']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <a href="tel:0907586969" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-phone me-1"></i>Liên hệ ngay
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- SẢN PHẨM BÁN CHẠY -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="section-title"><i class="fas fa-bolt text-warning me-2"></i>SẢN PHẨM BÁN CHẠY</h2>
                <a href="<?= SITE_URL ?>/may-photocopy" class="btn btn-outline-primary btn-sm">Xem tất cả <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
            <?php
            $hot = get_hot_products(8);
            foreach ($hot as $product):
                $display_price = $product['sale_price'] ?? $product['price'];
            ?>
            <div class="col">
                <div class="card product-card h-100 border-0 shadow-sm">
                    <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-2">HOT</span>
                    <a href="<?= SITE_URL ?>/<?= $product['slug'] ?>.html">
                        <img src="<?= $product['image'] ? SITE_URL . '/assets/' . $product['image'] : SITE_URL . '/assets/images/no-image.jpg' ?>" 
                             class="card-img-top p-2" 
                             alt="<?= htmlspecialchars($product['name']) ?>"
                             loading="lazy">
                    </a>
                    <div class="card-body pt-0">
                        <h6 class="card-title mb-2">
                            <a href="<?= SITE_URL ?>/<?= $product['slug'] ?>.html" class="text-decoration-none text-dark">
                                <?= htmlspecialchars($product['name']) ?>
                            </a>
                        </h6>
                        <div class="price-group">
                            <span class="text-danger fw-bold fs-6"><?= format_price($display_price) ?></span>
                            <?php if ($product['sale_price']): ?>
                                <br><small class="text-muted text-decoration-line-through"><?= format_price($product['price']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- KHUYẾN MÃI -->
<?php $promotions = get_promotions(); if ($promotions): ?>
<section class="py-5 flash-sale-section">
    <div class="container">
        <div class="section-header mb-4">
            <h2 class="section-title text-danger"><i class="fas fa-tags me-2"></i>KHUYẾN MÃI HOT</h2>
        </div>
        <div class="row g-4">
            <?php foreach ($promotions as $promo): ?>
            <div class="col-md-6">
                <div class="card border-danger shadow">
                    <div class="card-body">
                        <h4 class="text-danger"><i class="fas fa-fire me-2"></i><?= htmlspecialchars($promo['title']) ?></h4>
                        <p class="mb-2"><?= htmlspecialchars($promo['description']) ?></p>
                        <p class="mb-1">
                            <strong>Giảm: </strong>
                            <?php if ($promo['discount_type'] === 'percent'): ?>
                                <span class="badge bg-danger fs-6"><?= $promo['discount_value'] ?>%</span>
                            <?php else: ?>
                                <span class="badge bg-danger fs-6"><?= format_price($promo['discount_value']) ?></span>
                            <?php endif; ?>
                        </p>
                        <?php if ($promo['end_date']): ?>
                        <p class="text-muted mb-0"><i class="far fa-clock me-1"></i>Hết hạn: <?= date('d/m/Y', strtotime($promo['end_date'])) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CHO THUÊ MÁY PHOTOCOPY -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="section-title"><i class="fas fa-handshake text-success me-2"></i>DỊCH VỤ CHO THUÊ MÁY PHOTOCOPY</h2>
                <a href="<?= SITE_URL ?>/cho-thue-may-photocopy" class="btn btn-outline-success btn-sm">Xem chi tiết <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php
            $packages = get_rental_packages();
            $packages = array_slice($packages, 0, 3);
            foreach ($packages as $i => $pkg):
                $isPopular = ($i === 1);
            ?>
            <div class="col">
                <div class="card h-100 text-center shadow-sm <?= $isPopular ? 'border-primary border-2' : '' ?>">
                    <?php if ($isPopular): ?>
                        <div class="card-header bg-primary text-white py-2">
                            <small class="fw-bold"><i class="fas fa-crown me-1"></i>PHỔ BIẾN NHẤT</small>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title fw-bold"><?= htmlspecialchars($pkg['name']) ?></h5>
                        <p class="text-muted"><?= htmlspecialchars($pkg['machine_type']) ?> - <?= $pkg['speed'] ?></p>
                        <h3 class="text-primary fw-bold"><?= format_price($pkg['monthly_price']) ?><small class="fs-6 text-muted">/tháng</small></h3>
                        <hr>
                        <ul class="list-unstyled text-start">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Định mức: <?= number_format($pkg['included_pages']) ?> trang/tháng</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Phí vượt: <?= format_price($pkg['extra_page_price']) ?>/trang</li>
                            <?php 
                            $features = explode(',', $pkg['features'] ?? '');
                            foreach (array_slice($features, 0, 3) as $f): 
                            ?>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i><?= trim($f) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="tel:0907586969" class="btn <?= $isPopular ? 'btn-primary' : 'btn-outline-primary' ?> w-100">
                            <i class="fas fa-phone me-1"></i>Gọi ngay: 0907 586 969
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- TIN TỨC MỚI -->
<section class="py-5">
    <div class="container">
        <div class="section-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="section-title"><i class="fas fa-newspaper me-2"></i>TIN TỨC MỚI NHẤT</h2>
                <a href="<?= SITE_URL ?>/tin-tuc" class="btn btn-outline-primary btn-sm">Xem tất cả <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php
            $articles = get_news(3, 0);
            foreach ($articles as $news):
            ?>
            <div class="col">
                <div class="card h-100 border-0 shadow-sm news-card">
                    <?php if ($news['image']): ?>
                    <img src="<?= SITE_URL ?>/assets/<?= $news['image'] ?>" class="card-img-top" alt="<?= htmlspecialchars($news['title']) ?>" loading="lazy">
                    <?php endif; ?>
                    <div class="card-body">
                        <p class="text-muted small mb-2">
                            <i class="far fa-calendar-alt me-1"></i><?= date('d/m/Y', strtotime($news['created_at'])) ?>
                        </p>
                        <h5 class="card-title">
                            <a href="<?= SITE_URL ?>/tin/<?= $news['slug'] ?>" class="text-decoration-none text-dark">
                                <?= htmlspecialchars($news['title']) ?>
                            </a>
                        </h5>
                        <p class="card-text text-muted"><?= truncate_text(strip_tags($news['excerpt'] ?: $news['content']), 120) ?></p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="<?= SITE_URL ?>/tin/<?= $news['slug'] ?>" class="text-primary">Đọc thêm <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- THƯƠNG HIỆU ĐỐI TÁC -->
<section class="py-4 bg-light">
    <div class="container">
        <h3 class="text-center text-muted mb-4">THƯƠNG HIỆU ĐỐI TÁC</h3>
        <div class="d-flex justify-content-center align-items-center flex-wrap gap-4">
            <?php
            $brands = get_brands();
            foreach ($brands as $brand):
            ?>
            <a href="<?= SITE_URL ?>/may-photocopy?brand=<?= $brand['slug'] ?>" class="text-decoration-none">
                <span class="badge bg-white text-dark border px-4 py-3 fs-6 shadow-sm"><?= htmlspecialchars($brand['name']) ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- VÌ SAO CHỌN PUC -->
<section class="py-5 bg-primary text-white why-choose-section">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold">VÌ SAO CHỌN PUC?</h2>
        <div class="row text-center g-4">
            <div class="col-6 col-md-3">
                <div class="p-3">
                    <i class="fas fa-medal fa-3x mb-3"></i>
                    <h5>Uy Tín</h5>
                    <p class="small opacity-75">Hơn 10 năm kinh nghiệm trong lĩnh vực máy văn phòng</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3">
                    <i class="fas fa-tags fa-3x mb-3"></i>
                    <h5>Giá Tốt Nhất</h5>
                    <p class="small opacity-75">Cam kết mức giá cạnh tranh nhất thị trường</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3">
                    <i class="fas fa-shield-alt fa-3x mb-3"></i>
                    <h5>Bảo Hành</h5>
                    <p class="small opacity-75">Chính sách bảo hành rõ ràng, nhanh chóng</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3">
                    <i class="fas fa-headset fa-3x mb-3"></i>
                    <h5>Hỗ Trợ 24/7</h5>
                    <p class="small opacity-75">Đội ngũ kỹ thuật viên luôn sẵn sàng hỗ trợ</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
