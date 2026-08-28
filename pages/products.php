<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/seo.php';

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$category_slug = isset($_GET['category']) ? trim($_GET['category']) : '';
$brand_slug = isset($_GET['brand']) ? trim($_GET['brand']) : '';
$condition = isset($_GET['condition']) ? trim($_GET['condition']) : '';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'newest';

$current_cat = null;
if ($category_slug) {
    $current_cat = get_category_by_slug($category_slug);
}

$page_title = $current_cat ? ($current_cat['name'] . ' - Máy Photocopy PUC') : 'Tất Cả Sản Phẩm - Máy Photocopy PUC';
$page_description = $current_cat ? ($current_cat['description'] ?: 'Khám phá các dòng sản phẩm ' . $current_cat['name'] . ' tại PUC') : 'Danh mục máy photocopy, máy in, linh kiện và phụ kiện chính hãng tại PUC.';
$page_keywords = 'máy photocopy, bán máy photocopy, ricoh, toshiba, canon, konica, PUC';
$canonical_url = SITE_URL . '/pages/products.php' . ($category_slug ? '?category=' . urlencode($category_slug) : '');

require_once __DIR__ . '/../includes/header.php';

// Prepare options for query
$options = [
    'category_slug' => $category_slug,
    'brand_slug' => $brand_slug,
    'condition_type' => $condition,
    'limit' => PRODUCTS_PER_PAGE,
    'offset' => ($page - 1) * PRODUCTS_PER_PAGE
];

if ($sort === 'price_asc') {
    $options['order_by'] = "COALESCE(p.sale_price, p.price) ASC";
} elseif ($sort === 'price_desc') {
    $options['order_by'] = "COALESCE(p.sale_price, p.price) DESC";
} elseif ($sort === 'views') {
    $options['order_by'] = "p.view_count DESC";
} else {
    $options['order_by'] = "p.is_featured DESC, p.created_at DESC";
}

$products = get_products($options);
$total_products = count_products($options);
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= SITE_URL ?>">Trang Chủ</a></li>
            <?php if ($current_cat): ?>
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/pages/products.php">Sản Phẩm</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($current_cat['name']) ?></li>
            <?php else: ?>
                <li class="breadcrumb-item active" aria-current="page">Tất Cả Sản Phẩm</li>
            <?php endif; ?>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Sidebar Filter -->
        <aside class="col-lg-3">
            <!-- Danh mục -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-list me-2"></i>DANH MỤC SẢN PHẨM
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= SITE_URL ?>/pages/products.php" class="list-group-item list-group-item-action <?= empty($category_slug) ? 'active fw-bold' : '' ?>">
                        Tất cả sản phẩm
                    </a>
                    <?php
                    $all_cats = get_categories();
                    foreach ($all_cats as $cat):
                        if (empty($cat['parent_id'])):
                            $is_active = ($category_slug === $cat['slug']);
                    ?>
                        <a href="<?= SITE_URL ?>/pages/products.php?category=<?= $cat['slug'] ?>" class="list-group-item list-group-item-action <?= $is_active ? 'active fw-bold' : '' ?>">
                            <?= htmlspecialchars($cat['name']) ?>
                        </a>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>

            <!-- Thương hiệu -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-tag me-2"></i>THƯƠNG HIỆU
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= SITE_URL ?>/pages/products.php<?= $category_slug ? '?category=' . urlencode($category_slug) : '' ?>" class="list-group-item list-group-item-action <?= empty($brand_slug) ? 'active fw-bold' : '' ?>">
                        Tất cả hãng
                    </a>
                    <?php
                    $brands = get_brands();
                    foreach ($brands as $b):
                        $b_url = SITE_URL . '/pages/products.php?brand=' . $b['slug'] . ($category_slug ? '&category=' . urlencode($category_slug) : '');
                        $is_b_active = ($brand_slug === $b['slug']);
                    ?>
                        <a href="<?= $b_url ?>" class="list-group-item list-group-item-action <?= $is_b_active ? 'active fw-bold' : '' ?>">
                            <?= htmlspecialchars($b['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Tình trạng -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-filter me-2"></i>TÌNH TRẠNG MÁY
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= SITE_URL ?>/pages/products.php" class="list-group-item list-group-item-action <?= empty($condition) ? 'active fw-bold' : '' ?>">Tất cả</a>
                    <a href="<?= SITE_URL ?>/pages/products.php?condition=new" class="list-group-item list-group-item-action <?= $condition === 'new' ? 'active fw-bold' : '' ?>">Mới 100% Chính Hãng</a>
                    <a href="<?= SITE_URL ?>/pages/products.php?condition=renew" class="list-group-item list-group-item-action <?= $condition === 'renew' ? 'active fw-bold' : '' ?>">Renew 99% (Nhật Bản)</a>
                    <a href="<?= SITE_URL ?>/pages/products.php?condition=used" class="list-group-item list-group-item-action <?= $condition === 'used' ? 'active fw-bold' : '' ?>">Nhập Khẩu Đã Qua Sử Dụng</a>
                </div>
            </div>

            <!-- Hotline Box -->
            <div class="card bg-danger text-white border-0 shadow-sm p-3 text-center">
                <i class="fas fa-headset fa-3x mb-2"></i>
                <h6 class="fw-bold mb-1">CẦN TƯ VẤN NHANH?</h6>
                <p class="small mb-2">Liên hệ ngay để nhận báo giá tốt nhất</p>
                <a href="tel:<?= HOTLINE ?>" class="btn btn-light fw-bold text-danger"><?= HOTLINE ?></a>
            </div>
        </aside>

        <!-- Product Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 bg-white p-3 rounded shadow-sm border">
                <div>
                    <h1 class="h4 mb-1 fw-bold text-primary"><?= htmlspecialchars($current_cat ? $current_cat['name'] : 'Tất cả sản phẩm') ?></h1>
                    <small class="text-muted">Tìm thấy <strong><?= $total_products ?></strong> sản phẩm</small>
                </div>
                <form method="GET" class="d-flex align-items-center gap-2">
                    <?php if($category_slug): ?><input type="hidden" name="category" value="<?= htmlspecialchars($category_slug) ?>"><?php endif; ?>
                    <?php if($brand_slug): ?><input type="hidden" name="brand" value="<?= htmlspecialchars($brand_slug) ?>"><?php endif; ?>
                    <?php if($condition): ?><input type="hidden" name="condition" value="<?= htmlspecialchars($condition) ?>"><?php endif; ?>
                    <label class="text-nowrap small text-muted">Sắp xếp:</label>
                    <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto;">
                        <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                        <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Giá: Thấp đến Cao</option>
                        <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Giá: Cao đến Thấp</option>
                        <option value="views" <?= $sort == 'views' ? 'selected' : '' ?>>Xem nhiều nhất</option>
                    </select>
                </form>
            </div>

            <?php if (!empty($products)): ?>
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-3 g-3 mb-4">
                <?php foreach ($products as $product): 
                    $display_price = $product['sale_price'] ?? $product['price'];
                ?>
                <div class="col">
                    <div class="card h-100 product-card border-0 shadow-sm position-relative">
                        <?php if (!empty($product['sale_price'])): ?>
                            <span class="badge bg-danger position-absolute top-0 start-0 m-2">SALE</span>
                        <?php endif; ?>
                        <?php if ($product['condition_type'] === 'new'): ?>
                            <span class="badge bg-success position-absolute top-0 end-0 m-2">Mới 100%</span>
                        <?php elseif ($product['condition_type'] === 'renew'): ?>
                            <span class="badge bg-info position-absolute top-0 end-0 m-2">Renew 99%</span>
                        <?php endif; ?>

                        <a href="<?= SITE_URL ?>/<?= $product['slug'] ?>.html">
                            <img src="<?= get_image_url($product['image']) ?>" class="card-img-top p-3" alt="<?= htmlspecialchars($product['name']) ?>" style="height: 200px; object-fit: contain;">
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
            $pagination_base = SITE_URL . '/pages/products.php?' . http_build_query(array_filter([
                'category' => $category_slug,
                'brand' => $brand_slug,
                'condition' => $condition,
                'sort' => $sort !== 'newest' ? $sort : null
            ]));
            echo generate_pagination($total_products, PRODUCTS_PER_PAGE, $page, $pagination_base);
            ?>

            <?php else: ?>
            <div class="alert alert-info py-5 text-center bg-white shadow-sm border">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5>Không tìm thấy sản phẩm nào phù hợp</h5>
                <p class="text-muted">Vui lòng thử chọn danh mục hoặc bộ lọc khác.</p>
                <a href="<?= SITE_URL ?>/pages/products.php" class="btn btn-primary mt-2">Xem tất cả sản phẩm</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
