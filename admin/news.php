<?php
require_once __DIR__ . '/includes/admin-header.php';

$action = $_GET['action'] ?? 'list';
$message = '';

// Handle Delete
if ($action == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $pdo->prepare("DELETE FROM news WHERE id = ?")->execute([$id]);
    $message = "Đã xóa bài viết.";
    $action = 'list';
}

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? '';
    $slug = create_slug($title);
    $excerpt = $_POST['excerpt'] ?? '';
    $content = $_POST['content'] ?? '';
    $category = $_POST['category'] ?? 'news';
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Handle Image
    $image = $_POST['current_image'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../uploads/news/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image = 'uploads/news/' . $file_name;
        }
    }

    if ($action == 'add_submit') {
        $stmt = $pdo->prepare("INSERT INTO news (title, slug, excerpt, content, image, category, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$title, $slug, $excerpt, $content, $image, $category, $is_active]);
        $message = "Thêm bài viết thành công.";
        $action = 'list';
    } elseif ($action == 'edit_submit' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("UPDATE news SET title=?, slug=?, excerpt=?, content=?, image=?, category=?, is_active=?, updated_at=NOW() WHERE id=?");
        $stmt->execute([$title, $slug, $excerpt, $content, $image, $category, $is_active, $id]);
        $message = "Cập nhật bài viết thành công.";
        $action = 'list';
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Quản lý Bài viết</h2>
    <?php if ($action == 'list'): ?>
        <a href="?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm mới</a>
    <?php else: ?>
        <a href="news.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
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
    $news = $pdo->query("SELECT * FROM news ORDER BY id DESC")->fetchAll();
    ?>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Ảnh</th>
                            <th>Tiêu đề</th>
                            <th>Chuyên mục</th>
                            <th>Ngày đăng</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($news as $n): ?>
                        <tr>
                            <td>
                                <?php if ($n['image']): ?>
                                    <img src="../<?= htmlspecialchars($n['image']) ?>" alt="" width="60" height="40" style="object-fit: cover;">
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 300px;" title="<?= htmlspecialchars($n['title']) ?>">
                                    <?= htmlspecialchars($n['title']) ?>
                                </div>
                            </td>
                            <td><?= ucfirst($n['category']) ?></td>
                            <td><?= date('d/m/Y', strtotime($n['created_at'])) ?></td>
                            <td>
                                <span class="badge bg-<?= $n['is_active'] ? 'success' : 'danger' ?>">
                                    <?= $n['is_active'] ? 'Hiển thị' : 'Ẩn' ?>
                                </span>
                            </td>
                            <td>
                                <a href="?action=edit&id=<?= $n['id'] ?>" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                                <a href="?action=delete&id=<?= $n['id'] ?>" class="btn btn-sm btn-danger delete-confirm"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($action == 'add' || $action == 'edit'): 
    $article = [];
    if ($action == 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
        $stmt->execute([(int)$_GET['id']]);
        $article = $stmt->fetch();
    }
?>
    <div class="card">
        <div class="card-body">
            <form action="?action=<?= $action ?>_submit" method="POST" enctype="multipart/form-data">
                <?php if ($action == 'edit'): ?>
                    <input type="hidden" name="id" value="<?= $article['id'] ?>">
                    <input type="hidden" name="current_image" value="<?= htmlspecialchars($article['image'] ?? '') ?>">
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-9">
                        <div class="mb-3">
                            <label class="form-label">Tiêu đề bài viết *</label>
                            <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($article['title'] ?? '') ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Mô tả ngắn</label>
                            <textarea name="excerpt" class="form-control" rows="3"><?= htmlspecialchars($article['excerpt'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nội dung chi tiết</label>
                            <textarea name="content" class="form-control" rows="15"><?= htmlspecialchars($article['content'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mb-3">
                            <div class="card-header">Tùy chọn</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Chuyên mục</label>
                                    <select name="category" class="form-select">
                                        <option value="news" <?= (isset($article['category']) && $article['category'] == 'news') ? 'selected' : '' ?>>Tin tức</option>
                                        <option value="service" <?= (isset($article['category']) && $article['category'] == 'service') ? 'selected' : '' ?>>Dịch vụ</option>
                                        <option value="guide" <?= (isset($article['category']) && $article['category'] == 'guide') ? 'selected' : '' ?>>Hướng dẫn</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ảnh đại diện</label>
                                    <?php if (!empty($article['image'])): ?>
                                        <div class="mb-2">
                                            <img src="../<?= htmlspecialchars($article['image']) ?>" alt="" class="img-thumbnail" style="max-width: 100%;">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" name="is_active" class="form-check-input" id="isActive" <?= (!isset($article['is_active']) || $article['is_active'] == 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="isActive">Kích hoạt (Hiển thị)</label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> <?= $action == 'add' ? 'Đăng bài viết' : 'Cập nhật' ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
