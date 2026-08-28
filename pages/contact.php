<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/seo.php';

$page_title = 'Liên Hệ Với Công Ty Máy Photocopy PUC Tại Hà Nội';
$page_description = 'Thông tin liên hệ, hotline tư vấn, địa chỉ và bản đồ chỉ đường đến công ty máy photocopy PUC tại Hà Nội. Hotline: 0907 586 969';
$page_keywords = 'liên hệ PUC, máy photocopy Cầu Đất, máy photocopy Cửa Nam Hà Nội, địa chỉ bán máy photocopy';
$canonical_url = SITE_URL . '/pages/contact.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean_input($_POST['name'] ?? '');
    $phone = clean_input($_POST['phone'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $subject = clean_input($_POST['subject'] ?? '');
    $message = clean_input($_POST['message'] ?? '');

    if ($name && $phone && $message) {
        $saved = save_contact([
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'subject' => $subject ?: 'Liên hệ từ website',
            'message' => $message
        ]);
        if ($saved) {
            $msg = '<div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fas fa-check-circle me-2"></i>Cảm ơn bạn! Chúng tôi đã nhận được thông tin và sẽ liên hệ lại trong thời gian sớm nhất.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        } else {
            $msg = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="fas fa-exclamation-circle me-2"></i>Đã có lỗi xảy ra khi lưu thông tin. Vui lòng gọi trực tiếp hotline: ' . HOTLINE . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        }
    } else {
        $msg = '<div class="alert alert-warning alert-dismissible fade show" role="alert"><i class="fas fa-exclamation-triangle me-2"></i>Vui lòng điền đầy đủ Họ tên, Số điện thoại và Nội dung liên hệ.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= SITE_URL ?>">Trang Chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Liên Hệ</li>
        </ol>
    </nav>

    <div class="text-center mb-5">
        <h1 class="h2 fw-bold text-primary mb-2">LIÊN HỆ VỚI PUC</h1>
        <p class="text-muted">Đội ngũ tư vấn viên và kỹ thuật viên giàu kinh nghiệm luôn sẵn sàng phục vụ quý khách 24/7</p>
    </div>

    <div class="row g-4 mb-5">
        <!-- Thông tin liên hệ -->
        <div class="col-lg-5">
            <div class="bg-white p-4 rounded shadow-sm border h-100">
                <h4 class="fw-bold text-primary mb-4 border-bottom pb-2">THÔNG TIN TRỤ SỞ</h4>
                
                <div class="d-flex mb-4">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="fas fa-map-marker-alt fs-5"></i>
                    </div>
                    <div>
                        <strong class="d-block text-dark">Địa Chỉ:</strong>
                        <span class="text-secondary"><?= ADDRESS ?></span>
                    </div>
                </div>

                <div class="d-flex mb-4">
                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="fas fa-phone-alt fs-5"></i>
                    </div>
                    <div>
                        <strong class="d-block text-dark">Hotline / Zalo:</strong>
                        <a href="tel:<?= HOTLINE ?>" class="text-danger fw-bold fs-5 text-decoration-none"><?= HOTLINE ?></a>
                    </div>
                </div>

                <div class="d-flex mb-4">
                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="fas fa-envelope fs-5"></i>
                    </div>
                    <div>
                        <strong class="d-block text-dark">Email Hỗ Trợ:</strong>
                        <a href="mailto:<?= EMAIL ?>" class="text-secondary text-decoration-none"><?= EMAIL ?></a>
                    </div>
                </div>

                <div class="d-flex mb-4">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="fas fa-clock fs-5"></i>
                    </div>
                    <div>
                        <strong class="d-block text-dark">Thời Gian Làm Việc:</strong>
                        <span class="text-secondary">Thứ 2 - Thứ 7: 8h00 - 18h00 (Kỹ thuật hỗ trợ 24/7)</span>
                    </div>
                </div>

                <!-- Google Maps Embed -->
                <h5 class="fw-bold mb-3 mt-4 text-dark"><i class="fas fa-map me-2 text-primary"></i>Bản Đồ Chỉ Đường</h5>
                <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm border">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.1130635747425!2d105.8413628!3d21.0281617!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab958253a669%3A0xc07ce9b015eebefb!2zMjEgTmcuIDc1IFAuIEPhuqd1IMSQ4bqldCwgQ-G7rWEgTmFtLCBIb8OgbiBLaeG6v20sIEjDoCBO4buZaSwgVmnhu4d0IE5hbQ!5e0!3m2!1svi!2s!4v1700000000000!5m2!1svi!2s" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>

        <!-- Form gửi tin nhắn -->
        <div class="col-lg-7">
            <div class="bg-white p-4 p-md-5 rounded shadow-sm border h-100">
                <h4 class="fw-bold text-primary mb-3 border-bottom pb-2">GỬI TIN NHẮN TƯ VẤN & BÁO GIÁ</h4>
                <p class="text-muted small mb-4">Điền thông tin bên dưới, chúng tôi sẽ liên hệ lại ngay trong vòng 15 phút.</p>
                
                <?= $msg ?>

                <form method="POST" action="" class="needs-validation">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Ví dụ: Nguyễn Văn A" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" placeholder="Ví dụ: 0907 586 969" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Địa chỉ Email</label>
                            <input type="email" name="email" class="form-control" placeholder="name@company.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nhu cầu</label>
                            <select name="subject" class="form-select">
                                <option value="Báo giá mua máy photocopy">Báo giá mua máy photocopy</option>
                                <option value="Báo giá thuê máy photocopy">Báo giá thuê máy photocopy</option>
                                <option value="Mua mực in & linh kiện">Mua mực in & linh kiện</option>
                                <option value="Yêu cầu sửa chữa & bảo trì">Yêu cầu sửa chữa & bảo trì</option>
                                <option value="Tư vấn khác">Tư vấn khác</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nội dung chi tiết <span class="text-danger">*</span></label>
                            <textarea name="message" rows="5" class="form-control" placeholder="Nêu rõ model máy cần tìm, số lượng trang in dự kiến, hoặc tình trạng máy cần sửa..." required></textarea>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-5 fw-bold">
                                <i class="fas fa-paper-plane me-2"></i>Gửi Thông Tin Ngay
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
