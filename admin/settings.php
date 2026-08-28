<?php
require_once __DIR__ . '/includes/admin-header.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['settings']) && is_array($_POST['settings'])) {
        try {
            foreach ($_POST['settings'] as $key => $value) {
                $stmt = $pdo->prepare("
                    INSERT INTO settings (setting_key, setting_value) 
                    VALUES (?, ?) 
                    ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
                ");
                $stmt->execute([$key, trim($value)]);
            }
            $message = "Cập nhật cài đặt website thành công.";
        } catch (Exception $e) {
            $error = "Lỗi khi lưu cài đặt: " . $e->getMessage();
        }
    }
}

// Fetch all settings
$settings_raw = $pdo->query("SELECT * FROM settings")->fetchAll();
$settings = [];
foreach ($settings_raw as $s) {
    $settings[$s['setting_key']] = $s['setting_value'];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 fw-bold text-gray-800"><i class="fas fa-sliders-h me-2"></i>Cài Đặt Hệ Thống Website</h2>
</div>

<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<form action="" method="POST">
    <div class="row g-4">
        <!-- Thông tin chung & SEO -->
        <div class="col-lg-6">
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-globe me-2"></i>Thông Tin Chung & SEO
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên Website / Thương hiệu</label>
                        <input type="text" name="settings[site_name]" class="form-control" value="<?= htmlspecialchars($settings['site_name'] ?? 'PUC - Máy Photocopy') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả SEO mặc định (Meta Description)</label>
                        <textarea name="settings[site_description]" class="form-control" rows="3"><?= htmlspecialchars($settings['site_description'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Từ khóa SEO (Meta Keywords)</label>
                        <textarea name="settings[site_keywords]" class="form-control" rows="2"><?= htmlspecialchars($settings['site_keywords'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dòng chữ bản quyền (Copyright)</label>
                        <input type="text" name="settings[copyright]" class="form-control" value="<?= htmlspecialchars($settings['copyright'] ?? '© 2026 PUC. All rights reserved.') ?>">
                    </div>
                </div>
            </div>
            
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-share-alt me-2"></i>Mạng Xã Hội
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Facebook URL</label>
                        <input type="text" name="settings[facebook]" class="form-control" value="<?= htmlspecialchars($settings['facebook'] ?? '') ?>" placeholder="https://facebook.com/...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Số điện thoại Zalo</label>
                        <input type="text" name="settings[zalo]" class="form-control" value="<?= htmlspecialchars($settings['zalo'] ?? '0907586969') ?>">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Thông tin liên hệ -->
        <div class="col-lg-6">
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-address-card me-2"></i>Thông Tin Doanh Nghiệp / Liên Hệ
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger">Hotline tư vấn</label>
                        <input type="text" name="settings[hotline]" class="form-control fw-bold text-danger" value="<?= htmlspecialchars($settings['hotline'] ?? '0907586969') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email tiếp nhận</label>
                        <input type="email" name="settings[email]" class="form-control" value="<?= htmlspecialchars($settings['email'] ?? 'phuong86.annguyen@gmail.com') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Địa chỉ trụ sở</label>
                        <textarea name="settings[address]" class="form-control" rows="2"><?= htmlspecialchars($settings['address'] ?? 'Số 21 ngõ 75 Cầu Đất - Cửa Nam - Hà Nội') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Thời gian làm việc</label>
                        <input type="text" name="settings[working_hours]" class="form-control" value="<?= htmlspecialchars($settings['working_hours'] ?? 'Thứ 2 - Thứ 7: 8:00 - 18:00') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Google Maps Embed URL</label>
                        <textarea name="settings[google_maps]" class="form-control" rows="3"><?= htmlspecialchars($settings['google_maps'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow py-3">
                <i class="fas fa-save me-2"></i>LƯU TOÀN BỘ CÀI ĐẶT
            </button>
        </div>
    </div>
</form>

<?php require_once 'includes/admin-footer.php'; ?>
