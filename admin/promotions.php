<?php
require_once __DIR__ . '/includes/admin-header.php';

$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// Handle Delete
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $pdo->prepare("DELETE FROM promotions WHERE id = ?")->execute([$id]);
        $message = "Đã xóa chương trình khuyến mãi.";
    } catch (Exception $e) {
        $error = "Lỗi khi xóa: " . $e->getMessage();
    }
    $action = 'list';
}

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = clean_input($_POST['title'] ?? '');
    $slug = !empty($_POST['slug']) ? create_slug($_POST['slug']) : create_slug($title);
    $description = $_POST['description'] ?? '';
    $discount_type = $_POST['discount_type'] ?? 'percent';
    $discount_value = (float)($_POST['discount_value'] ?? 0);
    $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-d');
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if (empty($title)) {
        $error = "Vui lòng nhập tên chương trình khuyến mãi.";
    } else {
        try {
            if ($action === 'add_submit') {
                $stmt = $pdo->prepare("INSERT INTO promotions (title, slug, description, discount_type, discount_value, start_date, end_date, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$title, $slug, $description, $discount_type, $discount_value, $start_date, $end_date, $is_active]);
                $message = "Thêm chương trình khuyến mãi thành công.";
                $action = 'list';
            } elseif ($action === 'edit_submit' && $id > 0) {
                $stmt = $pdo->prepare("UPDATE promotions SET title=?, slug=?, description=?, discount_type=?, discount_value=?, start_date=?, end_date=?, is_active=? WHERE id=?");
                $stmt->execute([$title, $slug, $description, $discount_type, $discount_value, $start_date, $end_date, $is_active, $id]);
                $message = "Cập nhật chương trình khuyến mãi thành công.";
                $action = 'list';
            }
        } catch (Exception $e) {
            $error = "Lỗi: " . $e->getMessage();
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 fw-bold text-gray-800"><i class="fas fa-tags me-2"></i>Quản Lý Chương Trình Khuyến Mãi</h2>
    <?php if ($action === 'list'): ?>
        <a href="?action=add" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Thêm Khuyến Mãi</a>
    <?php else: ?>
        <a href="promotions.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại danh sách</a>
    <?php endif; ?>
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

<?php if ($action === 'list'): ?>
    <?php
    $promotions = $pdo->query("SELECT * FROM promotions ORDER BY id DESC")->fetchAll();
    ?>
    <div class="card shadow border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Tên chương trình</th>
                            <th>Mức giảm</th>
                            <th>Bắt đầu</th>
                            <th>Kết thúc</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($promotions)): foreach ($promotions as $pr): ?>
                        <tr>
                            <td><?= $pr['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($pr['title']) ?></strong>
                                <br><small class="text-muted"><?= htmlspecialchars(truncate_text($pr['description'], 60)) ?></small>
                            </td>
                            <td>
                                <span class="badge bg-danger fs-6">
                                    <?= $pr['discount_type'] === 'percent' ? ($pr['discount_value'] . '%') : format_price($pr['discount_value']) ?>
                                </span>
                            </td>
                            <td><?= $pr['start_date'] ? date('d/m/Y', strtotime($pr['start_date'])) : '-' ?></td>
                            <td><?= $pr['end_date'] ? date('d/m/Y', strtotime($pr['end_date'])) : 'Không thời hạn' ?></td>
                            <td>
                                <span class="badge bg-<?= $pr['is_active'] ? 'success' : 'secondary' ?>">
                                    <?= $pr['is_active'] ? 'Đang chạy' : 'Tắt' ?>
                                </span>
                            </td>
                            <td>
                                <a href="?action=edit&id=<?= $pr['id'] ?>" class="btn btn-sm btn-info text-white" title="Sửa"><i class="fas fa-edit"></i></a>
                                <a href="?action=delete&id=<?= $pr['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa khuyến mãi này?');" title="Xóa"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Chưa có chương trình khuyến mãi nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($action === 'add' || $action === 'edit'): 
    $promo = [];
    if ($action === 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM promotions WHERE id = ?");
        $stmt->execute([(int)$_GET['id']]);
        $promo = $stmt->fetch();
    }
?>
    <div class="card shadow border-0">
        <div class="card-body p-4">
            <form action="?action=<?= $action ?>_submit" method="POST">
                <?php if ($action === 'edit'): ?>
                    <input type="hidden" name="id" value="<?= $promo['id'] ?>">
                <?php endif; ?>
                
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Tên chương trình khuyến mãi <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($promo['title'] ?? '') ?>" placeholder="VD: Giảm giá mùa hè 2026">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Slug (đường dẫn)</label>
                        <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($promo['slug'] ?? '') ?>" placeholder="Tự động tạo">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Loại giảm giá</label>
                        <select name="discount_type" class="form-select">
                            <option value="percent" <?= (isset($promo['discount_type']) && $promo['discount_type'] === 'percent') ? 'selected' : '' ?>>Theo phần trăm (%)</option>
                            <option value="fixed" <?= (isset($promo['discount_type']) && $promo['discount_type'] === 'fixed') ? 'selected' : '' ?>>Số tiền cố định (VNĐ)</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Giá trị giảm</label>
                        <input type="number" name="discount_value" class="form-control" value="<?= $promo['discount_value'] ?? 0 ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Ngày bắt đầu</label>
                        <input type="date" name="start_date" class="form-control" value="<?= $promo['start_date'] ?? date('Y-m-d') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Ngày kết thúc</label>
                        <input type="date" name="end_date" class="form-control" value="<?= $promo['end_date'] ?? '' ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Mô tả chương trình</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($promo['description'] ?? '') ?></textarea>
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" <?= (!isset($promo['is_active']) || $promo['is_active'] == 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isActive">Kích hoạt chương trình</label>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary px-5 fw-bold">
                            <i class="fas fa-save me-1"></i><?= $action === 'add' ? 'Thêm Khuyến Mãi' : 'Lưu Thay Đổi' ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php require_once 'includes/admin-footer.php'; ?>
