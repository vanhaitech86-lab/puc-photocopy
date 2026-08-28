<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/seo.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header("Location: " . SITE_URL . "/pages/products.php");
    exit;
}

$product = get_product_by_slug($slug);
if (!$product) {
    header("HTTP/1.0 404 Not Found");
    $page_title = 'Không tìm thấy sản phẩm - PUC';
    require_once __DIR__ . '/../includes/header.php';
    echo '<div class="container py-5 text-center"><h2>Sản phẩm không tồn tại hoặc đã ngừng kinh doanh.</h2><a href="' . SITE_URL . '" class="btn btn-primary mt-3">Về trang chủ</a></div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Tăng lượt xem
increment_view('products', $product['id']);

$display_price = $product['sale_price'] ?? $product['price'];
$page_title = $product['name'] . ' - Bán & Cho Thuê Máy Photocopy PUC';
$page_description = $product['meta_description'] ?: truncate_text(strip_tags($product['short_description'] ?: $product['description']), 160);
$page_keywords = $product['meta_title'] ?: ($product['name'] . ', máy photocopy ' . ($product['brand_name'] ?? ''));
$canonical_url = SITE_URL . '/' . $product['slug'] . '.html';
$page_image = get_image_url($product['image']);

require_once __DIR__ . '/../includes/header.php';

// JSON-LD Product Schema
echo render_schema_product($product);

// Parse specs
$specs = [];
if (!empty($product['specifications'])) {
    $decoded = json_decode($product['specifications'], true);
    if (is_array($decoded)) {
        $specs = $decoded;
    }
}
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= SITE_URL ?>">Trang Chủ</a></li>
            <?php if (!empty($product['category_name'])): ?>
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/pages/products.php?category=<?= $product['category_slug'] ?>"><?= htmlspecialchars($product['category_name']) ?></a></li>
            <?php endif; ?>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
    </nav>

    <div class="row g-4 mb-5">
        <!-- Ảnh sản phẩm -->
        <div class="col-lg-5 col-md-6">
            <div class="product-gallery bg-white p-3 rounded shadow-sm border text-center">
                <div class="main-image mb-3">
                    <img id="mainProductImg" src="<?= get_image_url($product['image']) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($product['name']) ?>" style="max-height: 400px; object-fit: contain;">
                </div>
                <?php
                $gallery = [];
                if (!empty($product['gallery'])) {
                    $g = json_decode($product['gallery'], true);
                    if (is_array($g)) $gallery = $g;
                }
                if (!empty($gallery)):
                ?>
                <div class="thumbnail-list d-flex gap-2 justify-content-center flex-wrap">
                    <img src="<?= get_image_url($product['image']) ?>" class="img-thumbnail active-thumb" style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;" onclick="document.getElementById('mainProductImg').src=this.src">
                    <?php foreach ($gallery as $thumb): ?>
                        <img src="<?= get_image_url($thumb) ?>" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;" onclick="document.getElementById('mainProductImg').src=this.src">
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Thông tin sản phẩm -->
        <div class="col-lg-7 col-md-6">
            <div class="bg-white p-4 rounded shadow-sm border h-100 d-flex flex-column">
                <h1 class="h3 fw-bold text-dark mb-2"><?= htmlspecialchars($product['name']) ?></h1>
                
                <div class="product-meta d-flex gap-3 mb-3 text-muted small">
                    <?php if (!empty($product['brand_name'])): ?>
                        <span><i class="fas fa-tag me-1"></i>Hãng: <strong class="text-primary"><?= htmlspecialchars($product['brand_name']) ?></strong></span>
                    <?php endif; ?>
                    <?php if (!empty($product['sku'])): ?>
                        <span><i class="fas fa-barcode me-1"></i>Mã SP: <?= htmlspecialchars($product['sku']) ?></span>
                    <?php endif; ?>
                    <span><i class="fas fa-eye me-1"></i><?= number_format($product['view_count']) ?> lượt xem</span>
                </div>

                <div class="price-box bg-light p-3 rounded mb-3">
                    <div class="d-flex align-items-baseline gap-3">
                        <span class="fs-2 fw-bold text-danger"><?= format_price($display_price) ?></span>
                        <?php if (!empty($product['sale_price'])): ?>
                            <span class="text-muted text-decoration-line-through fs-5"><?= format_price($product['price']) ?></span>
                            <span class="badge bg-danger">Tiết kiệm <?= number_format($product['price'] - $product['sale_price'], 0, ',', '.') ?> ₫</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($product['condition_type'] === 'new'): ?>
                        <span class="badge bg-success mt-2">Tình trạng: Mới 100% Chính Hãng</span>
                    <?php elseif ($product['condition_type'] === 'renew'): ?>
                        <span class="badge bg-info mt-2">Tình trạng: Máy Renew 99% Như Mới</span>
                    <?php else: ?>
                        <span class="badge bg-secondary mt-2">Tình trạng: Máy Nhập Khẩu Đã Qua Sử Dụng</span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($product['short_description'])): ?>
                <div class="short-desc mb-4 text-secondary">
                    <?= nl2br(htmlspecialchars($product['short_description'])) ?>
                </div>
                <?php endif; ?>

                <!-- Cam kết ưu đãi -->
                <div class="commitments bg-primary-subtle p-3 rounded border border-primary-subtle mb-4">
                    <h6 class="fw-bold text-primary mb-2"><i class="fas fa-shield-alt me-1"></i>Cam kết từ PUC:</h6>
                    <ul class="list-unstyled mb-0 small text-dark">
                        <li><i class="fas fa-check-circle text-success me-2"></i>Bảo hành chính hãng 12 - 24 tháng tận nơi</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Miễn phí vận chuyển & lắp đặt nội thành Hà Nội</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Tặng kèm mực in và vật tư dự phòng ban đầu</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Hỗ trợ kỹ thuật 24/7 trọn đời máy</li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons mt-auto d-flex gap-3 flex-wrap">
                    <a href="tel:<?= HOTLINE ?>" class="btn btn-danger btn-lg flex-grow-1">
                        <i class="fas fa-phone-alt me-2"></i>Gọi Ngay: <?= HOTLINE ?>
                    </a>
                    <a href="https://zalo.me/<?= HOTLINE ?>" target="_blank" class="btn btn-primary btn-lg flex-grow-1">
                        <i class="fas fa-comment-dots me-2"></i>Chat Zalo Báo Giá
                    </a>
                    <button class="btn btn-outline-dark btn-lg" data-bs-toggle="modal" data-bs-target="#inquiryModal">
                        <i class="fas fa-envelope me-2"></i>Để lại yêu cầu
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Chi tiết & Thông số -->
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header bg-white border-bottom-0 pt-3 px-4">
            <ul class="nav nav-tabs card-header-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button" role="tab">MÔ TẢ CHI TIẾT</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button" role="tab">THÔNG SỐ KỸ THUẬT</button>
                </li>
            </ul>
        </div>
        <div class="card-body p-4 tab-content" id="productTabsContent">
            <!-- Mô tả -->
            <div class="tab-pane fade show active" id="desc" role="tabpanel">
                <?php if (!empty($product['description'])): ?>
                    <div class="product-description-content">
                        <?= $product['description'] ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Đang cập nhật thông tin chi tiết cho sản phẩm này.</p>
                <?php endif; ?>
            </div>

            <!-- Thông số kỹ thuật -->
            <div class="tab-pane fade" id="specs" role="tabpanel">
                <?php if (!empty($specs)): ?>
                    <table class="table table-striped table-bordered mb-0">
                        <tbody>
                            <?php foreach ($specs as $key => $val): ?>
                            <tr>
                                <th style="width: 35%;" class="bg-light"><?= htmlspecialchars(ucfirst($key)) ?></th>
                                <td><?= htmlspecialchars($val) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">Đang cập nhật thông số kỹ thuật.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- SẢN PHẨM LIÊN QUAN -->
    <?php
    $related_products = get_related_products($product['id'], $product['category_id'] ?? 0, 4);
    if (!empty($related_products)):
    ?>
    <div class="related-section mb-5">
        <div class="section-header mb-4">
            <h3 class="section-title"><i class="fas fa-layer-group text-primary me-2"></i>SẢN PHẨM TƯƠNG TỰ</h3>
        </div>
        <div class="row row-cols-2 row-cols-md-4 g-3">
            <?php foreach ($related_products as $rel): 
                $rel_price = $rel['sale_price'] ?? $rel['price'];
            ?>
            <div class="col">
                <div class="card h-100 product-card border-0 shadow-sm">
                    <a href="<?= SITE_URL ?>/<?= $rel['slug'] ?>.html">
                        <img src="<?= get_image_url($rel['image']) ?>" class="card-img-top p-2" alt="<?= htmlspecialchars($rel['name']) ?>" style="height: 180px; object-fit: contain;">
                    </a>
                    <div class="card-body text-center pt-0">
                        <h6 class="card-title mb-2">
                            <a href="<?= SITE_URL ?>/<?= $rel['slug'] ?>.html" class="text-decoration-none text-dark">
                                <?= htmlspecialchars($rel['name']) ?>
                            </a>
                        </h6>
                        <p class="text-danger fw-bold mb-0"><?= format_price($rel_price) ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal để lại yêu cầu báo giá -->
<div class="modal fade" id="inquiryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fas fa-paper-plane me-2"></i>Yêu cầu tư vấn / Báo giá</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= SITE_URL ?>/api.php?action=contact" method="POST" id="inquiryForm">
          <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
          <input type="hidden" name="subject" value="Yêu cầu tư vấn sản phẩm: <?= htmlspecialchars($product['name']) ?>">
          <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Sản phẩm quan tâm:</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="Nguyễn Văn A" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                <input type="tel" name="phone" class="form-control" placeholder="090..." required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email (nếu có)</label>
                <input type="email" name="email" class="form-control" placeholder="email@gmail.com">
            </div>
            <div class="mb-3">
                <label class="form-label">Ghi chú thêm</label>
                <textarea name="message" class="form-control" rows="3" placeholder="Yêu cầu thuê, mua hoặc cần tư vấn thêm..."></textarea>
            </div>
            <div id="inquiryResult"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-send me-1"></i>Gửi yêu cầu</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('inquiryForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const resultDiv = document.getElementById('inquiryResult');
    resultDiv.innerHTML = '<div class="alert alert-info py-2">Đang gửi yêu cầu...</div>';
    
    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = '<div class="alert alert-success py-2">' + data.message + '</div>';
            this.reset();
        } else {
            resultDiv.innerHTML = '<div class="alert alert-danger py-2">' + (data.message || 'Lỗi gửi yêu cầu') + '</div>';
        }
    })
    .catch(() => {
        resultDiv.innerHTML = '<div class="alert alert-danger py-2">Có lỗi kết nối, vui lòng gọi trực tiếp hotline: <?= HOTLINE ?></div>';
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
