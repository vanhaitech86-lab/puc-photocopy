<?php
require_once __DIR__ . '/includes/admin-header.php';

$action = $_GET['action'] ?? 'list';
$message = '';

// Handle Delete
if ($action == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $pdo->prepare("DELETE FROM banners WHERE id = ?")->execute([$id]);
    $message = "Đã xóa banner.";
    $action = 'list';
}

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? '';
    $link = $_POST['link'] ?? '';
    $position = $_POST['position'] ?? 'home_slider';
    $sort_order = $_POST['sort_order'] ?? 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Handle Image
    $image = $_POST['current_image'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../uploads/banners/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image = 'uploads/banners/' . $file_name;
        }
    }

    if ($action == 'add_submit') {
        $stmt = $pdo->prepare("INSERT INTO banners (title, image, link, position, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $image, $link, $position, $sort_order, $is_active]);
        $message = "Thêm banner thành công.";
        $action = 'list';
    } elseif ($action == 'edit_submit' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("UPDATE banners SET title=?, image=?, link=?, position=?, sort_order=?, is_active=? WHERE id=?");
        $stmt->execute([$title, $image, $link, $position, $sort_order, $is_active, $id]);
        $message = "Cập nhật banner thành công.";
        $action = 'list';
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Quản lý Banner</h2>
    <?php if ($action == 'list'): ?>
        <a href="?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm mới</a>
    <?php else: ?>
        <a href="banners.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
    <?php endif; ?>
</div>

<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($action == 'list'): ?>
    <?php
    $banners = $pdo->query("SELECT * FROM banners ORDER BY position ASC, sort_order ASC")->fetchAll();
    ?>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Ảnh</th>
                            <th>Tiêu đề</th>
                            <th>Vị trí</th>
                            <th>Sắp xếp</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($banners as $b): ?>
                        <tr>
                            <td>
                                <?php if ($b['image']): ?>
                                    <img src="../<?= htmlspecialchars($b['image']) ?>" alt="" height="50" style="object-fit: contain;">
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($b['title']) ?></td>
                            <td><?= htmlspecialchars($b['position']) ?></td>
                            <td><?= $b['sort_order'] ?></td>
                            <td>
                                <span class="badge bg-<?= $b['is_active'] ? 'success' : 'danger' ?>">
                                    <?= $b['is_active'] ? 'Hiển thị' : 'Ẩn' ?>
                                </span>
                            </td>
                            <td>
                                <a href="?action=edit&id=<?= $b['id'] ?>" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                                <a href="?action=delete&id=<?= $b['id'] ?>" class="btn btn-sm btn-danger delete-confirm"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($action == 'add' || $action == 'edit'): 
    $banner = [];
    if ($action == 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM banners WHERE id = ?");
        $stmt->execute([(int)$_GET['id']]);
        $banner = $stmt->fetch();
    }
?>
    <div class="card">
        <div class="card-body">
            <form action="?action=<?= $action ?>_submit" method="POST" enctype="multipart/form-data">
                <?php if ($action == 'edit'): ?>
                    <input type="hidden" name="id" value="<?= $banner['id'] ?>">
                    <input type="hidden" name="current_image" value="<?= htmlspecialchars($banner['image'] ?? '') ?>">
                <?php endif; ?>
                
                <div class="mb-3">
                    <label class="form-label">Tiêu đề *</label>
                    <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($banner['title'] ?? '') ?>">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Hình ảnh</label>
                    <?php if (!empty($banner['image'])): ?>
                        <div class="mb-2">
                            <img src="../<?= htmlspecialchars($banner['image']) ?>" alt="" height="100">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" class="form-control" accept="image/*" <?= $action=='add' ? 'required' : '' ?>>
                </div>

                <div class="mb-3">
                    <label class="form-label">Link liên kết (URL)</label>
                    <input type="text" name="link" class="form-control" value="<?= htmlspecialchars($banner['link'] ?? '') ?>">
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vị trí</label>
                        <select name="position" class="form-select">
                            <option value="home_slider" <?= (isset($banner['position']) && $banner['position'] == 'home_slider') ? 'selected' : '' ?>>Trang chủ - Slider chính</option>
                            <option value="home_side" <?= (isset($banner['position']) && $banner['position'] == 'home_side') ? 'selected' : '' ?>>Trang chủ - Bên cạnh slider</option>
                            <option value="category" <?= (isset($banner['position']) && $banner['position'] == 'category') ? 'selected' : '' ?>>Trang danh mục</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Thứ tự sắp xếp</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= $banner['sort_order'] ?? 0 ?>">
                    </div>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" id="isActive" <?= (!isset($banner['is_active']) || $banner['is_active'] == 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="isActive">Kích hoạt (Hiển thị)</label>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?= $action == 'add' ? 'Thêm banner' : 'Cập nhật' ?>
                </button>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
