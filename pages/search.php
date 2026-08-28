<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/seo.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

$page_title = $q ? ('Tìm Kiếm: "' . htmlspecialchars($q) . '" - PUC') : 'Tìm Kiếm Sản Phẩm - PUC';
$page_description = 'Kết quả tìm kiếm sản phẩm máy photocopy cho từ khóa: ' . htmlspecialchars($q);
$canonical_url = SITE_URL . '/pages/search.php' . ($q ? '?q=' . urlencode($q) : '');

require_once __DIR__ . '/../includes/header.php';

$products = [];
$total_products = 0;

if ($q) {
    $options = [
        'search' => $q,
        'limit' => PRODUCTS_PER_PAGE,
        'offset' => ($page - 1) * PRODUCTS_PER_PAGE
    ];
    $products = get_products($options);
    $total_products = count_products($options);
}
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= SITE_URL ?>">Trang Chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tìm Kiếm</li>
        </ol>
    </nav>

    <div class="bg-white p-4 rounded shadow-sm border mb-4">
        <h1 class="h3 fw-bold text-dark mb-3">
            <?php if ($q): ?>
                Kết quả tìm kiếm cho từ khóa: "<span class="text-primary"><?= htmlspecialchars($q) ?></span>"
            <?php else: ?>
                Tìm kiếm sản phẩm
            <?php endif; ?>
        </h1>
        
        <form action="<?= SITE_URL ?>/pages/search.php" method="GET" class="row g-2">
            <div class="col-md-9 col-sm-8">
                <input type="text" name="q" class="form-control form-control-lg" placeholder="Nhập tên máy, mã model hoặc linh kiện (vd: Ricoh 5055, mực 6055, Toshiba...)" value="<?= htmlspecialchars($q) ?>" required>
            </div>
            <div class="col-md-3 col-sm-4">
                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">
                    <i class="fas fa-search me-2"></i>Tìm Kiếm
                </button>
            </div>
        </form>
    </div>

    <?php if ($q): ?>
        <p class="text-muted mb-4">Tìm thấy <strong><?= $total_products ?></strong> sản phẩm phù hợp.</p>

        <?php if (!empty($products)): ?>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 mb-4">
            <?php foreach ($products as $product): 
                $display_price = $product['sale_price'] ?? $product['price'];
            ?>
            <div class="col">
                <div class="card h-100 product-card border-0 shadow-sm position-relative">
                    <?php if (!empty($product['sale_price'])): ?>
                        <span class="badge bg-danger position-absolute top-0 start-0 m-2">SALE</span>
                    <?php endif; ?>
                    <a href="<?= SITE_URL ?>/<?= $product['slug'] ?>.html">
                        <img src="<?= get_image_url($product['image']) ?>" class="card-img-top p-3" alt="<?= htmlspecialchars($product['name']) ?>" style="height: 190px; object-fit: contain;">
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
        $pagination_base = SITE_URL . '/pages/search.php?q=' . urlencode($q);
        echo generate_pagination($total_products, PRODUCTS_PER_PAGE, $page, $pagination_base);
        ?>

        <?php else: ?>
        <div class="alert alert-warning py-5 text-center bg-white shadow-sm border">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h5>Không tìm thấy sản phẩm nào khớp với từ khóa "<?= htmlspecialchars($q) ?>"</h5>
            <p class="text-muted">Gợi ý: Hãy thử từ khóa ngắn hơn như "Ricoh", "Toshiba", "Máy in" hoặc gọi hotline để được hỗ trợ trực tiếp.</p>
            <a href="tel:<?= HOTLINE ?>" class="btn btn-danger mt-2"><i class="fas fa-phone-alt me-2"></i>Gọi Hotline: <?= HOTLINE ?></a>
        </div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
