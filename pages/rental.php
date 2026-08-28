<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/seo.php';

$type = isset($_GET['type']) ? trim($_GET['type']) : '';

$page_title = 'Bảng Giá Cho Thuê Máy Photocopy Uy Tín Tại Hà Nội - PUC';
$page_description = 'Dịch vụ cho thuê máy photocopy màu, đen trắng trọn gói, bảo trì miễn phí, không lo tiền mực, chỉ từ 2.500.000đ/tháng. Hotline: 0907 586 969';
$page_keywords = 'cho thuê máy photocopy, thuê máy photocopy hà nội, thuê máy photocopy ricoh, bảng giá thuê máy photocopy, PUC';
$canonical_url = SITE_URL . '/pages/rental.php';

require_once __DIR__ . '/../includes/header.php';

$bw_packages = get_rental_packages('bw');
$color_packages = get_rental_packages('color');
if (empty($bw_packages) && empty($color_packages)) {
    $all_packages = get_rental_packages();
}
?>

<!-- Hero Banner Rental -->
<div class="bg-primary text-white py-5 text-center position-relative" style="background: linear-gradient(135deg, #1E40AF 0%, #2563EB 100%);">
    <div class="container py-3">
        <span class="badge bg-warning text-dark px-3 py-2 fs-6 mb-3"><i class="fas fa-star me-1"></i>DỊCH VỤ TRỌN GÓI - TIẾT KIỆM TỐI ĐA</span>
        <h1 class="display-5 fw-bold mb-3">CHO THUÊ MÁY PHOTOCOPY CHUYÊN NGHIỆP</h1>
        <p class="lead max-w-700 mx-auto opacity-90">Không cần vốn đầu tư ban đầu. Miễn phí 100% chi phí bảo dưỡng, sửa chữa và thay mực in. Đổi máy mới nếu máy gặp sự cố.</p>
        <div class="mt-4 d-flex justify-content-center gap-3 flex-wrap">
            <a href="tel:<?= HOTLINE ?>" class="btn btn-danger btn-lg px-4 fw-bold">
                <i class="fas fa-phone-alt me-2"></i>Tư Vấn Miễn Phí: <?= HOTLINE ?>
            </a>
            <a href="#pricingSection" class="btn btn-outline-light btn-lg px-4">
                <i class="fas fa-arrow-down me-2"></i>Xem Bảng Giá
            </a>
        </div>
    </div>
</div>

<div class="container py-5" id="pricingSection">
    <!-- Lợi ích khi thuê máy tại PUC -->
    <div class="row g-4 mb-5 text-center">
        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm p-3">
                <i class="fas fa-hand-holding-usd fa-3x text-primary mb-3"></i>
                <h6 class="fw-bold">Chi Phí Cố Định</h6>
                <p class="small text-muted mb-0">Chỉ trả phí thuê hàng tháng, không phát sinh chi phí ẩn</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm p-3">
                <i class="fas fa-tools fa-3x text-success mb-3"></i>
                <h6 class="fw-bold">Bảo Trì Miễn Phí</h6>
                <p class="small text-muted mb-0">Kỹ thuật viên định kỳ kiểm tra và thay mực tận nơi</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm p-3">
                <i class="fas fa-sync-alt fa-3x text-warning mb-3"></i>
                <h6 class="fw-bold">Đổi Máy Nhanh Chóng</h6>
                <p class="small text-muted mb-0">Đổi ngay máy mới hoặc nâng cấp cấu hình khi cần</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm p-3">
                <i class="fas fa-headset fa-3x text-danger mb-3"></i>
                <h6 class="fw-bold">Hỗ Trợ 24/7</h6>
                <p class="small text-muted mb-0">Có mặt xử lý sự cố trong vòng 30 - 60 phút tại Hà Nội</p>
            </div>
        </div>
    </div>

    <!-- Gói máy Trắng Đen -->
    <div class="mb-5">
        <div class="section-header mb-4 text-center">
            <h2 class="fw-bold text-primary"><i class="fas fa-print me-2"></i>BẢNG GIÁ THUÊ MÁY PHOTOCOPY TRẮNG ĐEN</h2>
            <p class="text-muted">Phù hợp cho văn phòng, trường học, công ty có nhu cầu in ấn tài liệu thường xuyên</p>
        </div>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php 
            $bw = !empty($bw_packages) ? $bw_packages : get_rental_packages();
            foreach ($bw as $index => $pkg):
                $isPopular = ($index === 1);
            ?>
            <div class="col">
                <div class="card h-100 text-center shadow-sm position-relative <?= $isPopular ? 'border-primary border-2 shadow' : 'border' ?>">
                    <?php if ($isPopular): ?>
                        <div class="badge bg-danger position-absolute top-0 end-0 m-2 px-3 py-2">GÓI PHỔ BIẾN</div>
                    <?php endif; ?>
                    <div class="card-header <?= $isPopular ? 'bg-primary text-white' : 'bg-light' ?> py-3">
                        <h4 class="my-0 fw-bold"><?= htmlspecialchars($pkg['name']) ?></h4>
                        <small><?= htmlspecialchars($pkg['machine_type']) ?></small>
                    </div>
                    <div class="card-body d-flex flex-column p-4">
                        <div class="mb-3">
                            <span class="display-6 fw-bold text-danger"><?= format_price($pkg['monthly_price']) ?></span>
                            <span class="text-muted small">/tháng</span>
                        </div>
                        <ul class="list-unstyled text-start mb-4 flex-grow-1">
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Tốc độ in/copy: <strong><?= htmlspecialchars($pkg['speed']) ?></strong></li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Định mức trang: <strong><?= number_format($pkg['included_pages']) ?> trang/tháng</strong></li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Phí vượt định mức: <strong><?= format_price($pkg['extra_page_price']) ?>/trang</strong></li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Đặt cọc: <strong><?= format_price($pkg['deposit']) ?></strong></li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Bảo trì, sửa chữa, thay mực <strong>miễn phí 100%</strong></li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Miễn phí lắp đặt nội thành Hà Nội</li>
                        </ul>
                        <button class="btn <?= $isPopular ? 'btn-primary' : 'btn-outline-primary' ?> btn-lg w-100" data-bs-toggle="modal" data-bs-target="#rentalModal" onclick="document.getElementById('rentalPackageName').value='<?= htmlspecialchars($pkg['name'] . ' - ' . $pkg['machine_type']) ?>'">
                            <i class="fas fa-handshake me-2"></i>Đăng Ký Thuê Gói Này
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Gói máy Màu -->
    <?php if (!empty($color_packages)): ?>
    <div class="mb-5">
        <div class="section-header mb-4 text-center">
            <h2 class="fw-bold text-danger"><i class="fas fa-palette me-2"></i>BẢNG GIÁ THUÊ MÁY PHOTOCOPY MÀU</h2>
            <p class="text-muted">Giải pháp in ấn màu sắc nét, chất lượng cao cho phòng thiết kế, marketing, văn phòng cao cấp</p>
        </div>

        <div class="row row-cols-1 row-cols-md-2 g-4 justify-content-center">
            <?php foreach ($color_packages as $pkg): ?>
            <div class="col-md-5">
                <div class="card h-100 text-center shadow-sm border-danger border-2">
                    <div class="card-header bg-danger text-white py-3">
                        <h4 class="my-0 fw-bold"><?= htmlspecialchars($pkg['name']) ?></h4>
                        <small><?= htmlspecialchars($pkg['machine_type']) ?></small>
                    </div>
                    <div class="card-body d-flex flex-column p-4">
                        <div class="mb-3">
                            <span class="display-6 fw-bold text-danger"><?= format_price($pkg['monthly_price']) ?></span>
                            <span class="text-muted small">/tháng</span>
                        </div>
                        <ul class="list-unstyled text-start mb-4 flex-grow-1">
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Tốc độ: <strong><?= htmlspecialchars($pkg['speed']) ?></strong></li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Định mức: <strong><?= number_format($pkg['included_pages']) ?> trang/tháng</strong></li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Phí vượt: <strong><?= format_price($pkg['extra_page_price']) ?>/trang</strong></li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Mực in màu & bảo trì miễn phí</li>
                        </ul>
                        <button class="btn btn-danger btn-lg w-100" data-bs-toggle="modal" data-bs-target="#rentalModal" onclick="document.getElementById('rentalPackageName').value='<?= htmlspecialchars($pkg['name'] . ' - ' . $pkg['machine_type']) ?>'">
                            <i class="fas fa-handshake me-2"></i>Đăng Ký Thuê Gói Màu
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Quy trình thuê 4 bước -->
    <div class="bg-light p-5 rounded border mb-5">
        <h3 class="text-center fw-bold mb-4 text-primary">QUY TRÌNH THUÊ MÁY PHOTOCOPY TẠI PUC</h3>
        <div class="row text-center g-4">
            <div class="col-md-3">
                <div class="bg-white p-4 rounded shadow-sm h-100">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; font-size: 24px; font-weight: bold;">1</div>
                    <h5 class="fw-bold">Tiếp Nhận & Tư Vấn</h5>
                    <p class="text-muted small mb-0">Khách hàng liên hệ qua Hotline/Zalo hoặc form, PUC tư vấn gói phù hợp.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="bg-white p-4 rounded shadow-sm h-100">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; font-size: 24px; font-weight: bold;">2</div>
                    <h5 class="fw-bold">Ký Kết Hợp Đồng</h5>
                    <p class="text-muted small mb-0">Thỏa thuận thời hạn thuê, định mức trang và các điều khoản rõ ràng.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="bg-white p-4 rounded shadow-sm h-100">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; font-size: 24px; font-weight: bold;">3</div>
                    <h5 class="fw-bold">Giao Máy Tận Nơi</h5>
                    <p class="text-muted small mb-0">Kỹ thuật viên giao máy, cài đặt mạng, máy tính và hướng dẫn sử dụng.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="bg-white p-4 rounded shadow-sm h-100">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; font-size: 24px; font-weight: bold;">4</div>
                    <h5 class="fw-bold">Bảo Trì Định Kỳ</h5>
                    <p class="text-muted small mb-0">Hàng tháng kỹ thuật viên đến bảo dưỡng, châm mực và hỗ trợ kỹ thuật.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Đăng Ký Thuê -->
<div class="modal fade" id="rentalModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fas fa-handshake me-2"></i>Đăng ký thuê máy photocopy</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= SITE_URL ?>/api.php?action=contact" method="POST" id="rentalForm">
          <input type="hidden" name="subject" id="rentalPackageName" value="Đăng ký thuê máy photocopy">
          <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Gói máy quan tâm:</label>
                <input type="text" id="displayPackage" class="form-control" readonly value="Tư vấn gói máy photocopy">
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
                <label class="form-label">Địa chỉ lắp đặt</label>
                <input type="text" name="address" class="form-control" placeholder="Quận/Huyện, Hà Nội">
            </div>
            <div class="mb-3">
                <label class="form-label">Nhu cầu in ấn (số bản/tháng dự kiến)</label>
                <textarea name="message" class="form-control" rows="2" placeholder="Ví dụ: Khoảng 5.000 - 10.000 trang/tháng..."></textarea>
            </div>
            <div id="rentalResult"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-send me-1"></i>Gửi Đăng Ký</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('rentalForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const resultDiv = document.getElementById('rentalResult');
    resultDiv.innerHTML = '<div class="alert alert-info py-2">Đang gửi thông tin...</div>';
    
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
            resultDiv.innerHTML = '<div class="alert alert-danger py-2">' + (data.message || 'Lỗi gửi thông tin') + '</div>';
        }
    })
    .catch(() => {
        resultDiv.innerHTML = '<div class="alert alert-danger py-2">Có lỗi kết nối, vui lòng gọi trực tiếp hotline: <?= HOTLINE ?></div>';
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
