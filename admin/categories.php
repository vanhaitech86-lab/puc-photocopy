<?php
require_once __DIR__ . '/includes/admin-header.php';

$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// Handle Delete
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
        $message = "Đã xóa danh mục thành công.";
    } catch (Exception $e) {
        $error = "Lỗi khi xóa: " . $e->getMessage();
    }
    $action = 'list';
}

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = clean_input($_POST['name'] ?? '');
    $slug = !empty($_POST['slug']) ? create_slug($_POST['slug']) : create_slug($name);
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
    $description = $_POST['description'] ?? '';
    $icon = clean_input($_POST['icon'] ?? 'fa-print');
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if (empty($name)) {
        $error = "Vui lòng nhập tên danh mục.";
    } else {
        try {
            if ($action === 'add_submit') {
                $stmt = $pdo->prepare("INSERT INTO categories (name, slug, parent_id, description, icon, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $slug, $parent_id, $description, $icon, $sort_order, $is_active]);
                $message = "Thêm danh mục thành công.";
                $action = 'list';
            } elseif ($action === 'edit_submit' && $id > 0) {
                $stmt = $pdo->prepare("UPDATE categories SET name=?, slug=?, parent_id=?, description=?, icon=?, sort_order=?, is_active=? WHERE id=?");
                $stmt->execute([$name, $slug, $parent_id, $description, $icon, $sort_order, $is_active, $id]);
                $message = "Cập nhật danh mục thành công.";
                $action = 'list';
            }
        } catch (Exception $e) {
            $error = "Lỗi: " . $e->getMessage();
        }
    }
}

$parent_categories = $pdo->query("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY sort_order ASC, name ASC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 fw-bold text-gray-800"><i class="fas fa-list me-2"></i>Quản Lý Danh Mục</h2>
    <?php if ($action === 'list'): ?>
        <a href="?action=add" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Thêm Danh Mục</a>
    <?php else: ?>
        <a href="categories.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại danh sách</a>
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
    $categories = $pdo->query("
        SELECT c.*, p.name as parent_name, (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count
        FROM categories c 
        LEFT JOIN categories p ON c.parent_id = p.id 
        ORDER BY c.sort_order ASC, c.id ASC
    ")->fetchAll();
    ?>
    <div class="card shadow border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Icon</th>
                            <th>Tên danh mục</th>
                            <th>Slug</th>
                            <th>Danh mục cha</th>
                            <th>Số SP</th>
                            <th>Thứ tự</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($categories)): foreach ($categories as $cat): ?>
                        <tr>
                            <td><?= $cat['id'] ?></td>
                            <td><i class="fas <?= htmlspecialchars($cat['icon'] ?: 'fa-folder') ?> text-primary"></i></td>
                            <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                            <td><code><?= htmlspecialchars($cat['slug']) ?></code></td>
                            <td><?= htmlspecialchars($cat['parent_name'] ?? '— (Danh mục gốc)') ?></td>
                            <td><span class="badge bg-info text-dark"><?= $cat['product_count'] ?></span></td>
                            <td><?= $cat['sort_order'] ?></td>
                            <td>
                                <span class="badge bg-<?= $cat['is_active'] ? 'success' : 'secondary' ?>">
                                    <?= $cat['is_active'] ? 'Hiển thị' : 'Ẩn' ?>
                                </span>
                            </td>
                            <td>
                                <a href="?action=edit&id=<?= $cat['id'] ?>" class="btn btn-sm btn-info text-white" title="Sửa"><i class="fas fa-edit"></i></a>
                                <a href="?action=delete&id=<?= $cat['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');" title="Xóa"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted">Chưa có danh mục nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($action === 'add' || $action === 'edit'): 
    $cat = [];
    if ($action === 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([(int)$_GET['id']]);
        $cat = $stmt->fetch();
    }
?>
    <div class="card shadow border-0">
        <div class="card-body p-4">
            <form action="?action=<?= $action ?>_submit" method="POST">
                <?php if ($action === 'edit'): ?>
                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                <?php endif; ?>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($cat['name'] ?? '') ?>" placeholder="VD: Máy Photocopy Trắng Đen">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Slug (đường dẫn)</label>
                        <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($cat['slug'] ?? '') ?>" placeholder="Tự động tạo">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Danh mục cha</label>
                        <select name="parent_id" class="form-select">
                            <option value="">-- Là danh mục gốc --</option>
                            <?php foreach ($parent_categories as $pcat): ?>
                                <?php if (!isset($cat['id']) || $cat['id'] != $pcat['id']): ?>
                                    <option value="<?= $pcat['id'] ?>" <?= (isset($cat['parent_id']) && $cat['parent_id'] == $pcat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($pcat['name']) ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">FontAwesome Icon</label>
                        <input type="text" name="icon" class="form-control" value="<?= htmlspecialchars($cat['icon'] ?? 'fa-print') ?>" placeholder="VD: fa-print, fa-palette">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Thứ tự sắp xếp</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= $cat['sort_order'] ?? 0 ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Mô tả danh mục</label>
                        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($cat['description'] ?? '') ?></textarea>
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" <?= (!isset($cat['is_active']) || $cat['is_active'] == 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isActive">Kích hoạt hiển thị danh mục</label>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary px-5 fw-bold">
                            <i class="fas fa-save me-1"></i><?= $action === 'add' ? 'Lưu Danh Mục' : 'Cập Nhật Danh Mục' ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php require_once 'includes/admin-footer.php'; ?>
