<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/seo.php';

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$category_slug = isset($_GET['category']) ? trim($_GET['category']) : '';
$brand_slug = isset($_GET['brand']) ? trim($_GET['brand']) : '';

$page_title = 'Vật Tư & Linh Kiện Máy Photocopy Chính Hãng - PUC';
$page_description = 'Cung cấp mực máy photocopy, linh kiện Ricoh, Toshiba, Canon, Konica, cụm sấy, trống gạt chính hãng, giá tốt tại Hà Nội.';
$page_keywords = 'mực máy photocopy, linh kiện ricoh, linh kiện toshiba, vật tư photocopy, PUC';
$canonical_url = SITE_URL . '/pages/supplies.php';

require_once __DIR__ . '/../includes/header.php';

// Supplies categories
$supplies_categories = [
    'muc-may-photocopy' => 'Mực Máy Photocopy',
    'linh-kien-ricoh' => 'Linh Kiện Ricoh',
    'linh-kien-toshiba' => 'Linh Kiện Toshiba',
    'linh-kien-konica' => 'Linh Kiện Konica',
    'linh-kien-canon' => 'Linh Kiện Canon - HP'
];

$options = [
    'category_slug' => $category_slug ?: 'vat-tu-linh-kien',
    'brand_slug' => $brand_slug,
    'limit' => PRODUCTS_PER_PAGE,
    'offset' => ($page - 1) * PRODUCTS_PER_PAGE
];

$products = get_products($options);
$total_products = count_products($options);

// If empty because category_slug wasn't matched strictly, fallback to all supplies-related
if (empty($products)) {
    $options['category_slug'] = '';
    $options['search'] = 'mực';
    $products = get_products($options);
    $total_products = count_products($options);
}
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= SITE_URL ?>">Trang Chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Vật Tư & Linh Kiện</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Sidebar -->
        <aside class="col-lg-3">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-cogs me-2"></i>DANH MỤC VẬT TƯ
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= SITE_URL ?>/pages/supplies.php" class="list-group-item list-group-item-action <?= empty($category_slug) ? 'active fw-bold' : '' ?>">
                        Tất cả vật tư & linh kiện
                    </a>
                    <?php foreach ($supplies_categories as $c_slug => $c_name): ?>
                        <a href="<?= SITE_URL ?>/pages/supplies.php?category=<?= $c_slug ?>" class="list-group-item list-group-item-action <?= $category_slug === $c_slug ? 'active fw-bold' : '' ?>">
                            <?= $c_name ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Hotline Box -->
            <div class="card bg-danger text-white border-0 shadow-sm p-3 text-center">
                <i class="fas fa-tools fa-3x mb-2"></i>
                <h6 class="fw-bold mb-1">TÌM LINH KIỆN HIẾM?</h6>
                <p class="small mb-2">Gửi mã máy cho kỹ thuật viên tìm hàng ngay</p>
                <a href="tel:<?= HOTLINE ?>" class="btn btn-light fw-bold text-danger"><?= HOTLINE ?></a>
            </div>
        </aside>

        <!-- Product Grid -->
        <div class="col-lg-9">
            <div class="bg-white p-3 rounded shadow-sm border mb-4">
                <h1 class="h4 mb-1 fw-bold text-primary">VẬT TƯ & LINH KIỆN CHÍNH HÃNG</h1>
                <p class="text-muted small mb-0">Mực in, trống drum, gạt mực, lô sấy, bánh xe kéo giấy cho mọi dòng máy photocopy Ricoh, Toshiba, Canon, Konica.</p>
            </div>

            <?php if (!empty($products)): ?>
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-3 g-3 mb-4">
                <?php foreach ($products as $product): 
                    $display_price = $product['sale_price'] ?? $product['price'];
                ?>
                <div class="col">
                    <div class="card h-100 product-card border-0 shadow-sm position-relative">
                        <a href="<?= SITE_URL ?>/<?= $product['slug'] ?>.html">
                            <img src="<?= get_image_url($product['image']) ?>" class="card-img-top p-3" alt="<?= htmlspecialchars($product['name']) ?>" style="height: 180px; object-fit: contain;">
                        </a>
                        <div class="card-body pt-0 text-center d-flex flex-column">
                            <h6 class="card-title mb-2">
                                <a href="<?= SITE_URL ?>/<?= $product['slug'] ?>.html" class="text-decoration-none text-dark fw-semibold">
                                    <?= htmlspecialchars($product['name']) ?>
                                </a>
                            </h6>
                            <div class="mt-auto">
                                <div class="price-group mb-2">
                                    <span class="text-danger fw-bold fs-6"><?= format_price($display_price) ?></span>
                                    <?php if (!empty($product['sale_price'])): ?>
                                        <br><small class="text-muted text-decoration-line-through"><?= format_price($product['price']) ?></small>
                                    <?php endif; ?>
                                </div>
                                <a href="<?= SITE_URL ?>/<?= $product['slug'] ?>.html" class="btn btn-outline-primary btn-sm w-100">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Phân trang -->
            <?php 
            $pagination_base = SITE_URL . '/pages/supplies.php?' . http_build_query(array_filter([
                'category' => $category_slug,
                'brand' => $brand_slug
            ]));
            echo generate_pagination($total_products, PRODUCTS_PER_PAGE, $page, $pagination_base);
            ?>

            <?php else: ?>
            <div class="alert alert-info py-5 text-center bg-white shadow-sm border">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h5>Đang cập nhật danh sách vật tư</h5>
                <p class="text-muted">Vui lòng liên hệ trực tiếp hotline để được báo giá linh kiện nhanh nhất.</p>
                <a href="tel:<?= HOTLINE ?>" class="btn btn-danger">Gọi ngay: <?= HOTLINE ?></a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
