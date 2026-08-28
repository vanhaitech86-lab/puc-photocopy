<?php
require_once __DIR__ . '/includes/admin-header.php';

$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// Handle Delete
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
        $message = "Đã xóa sản phẩm thành công.";
    } catch (Exception $e) {
        $error = "Không thể xóa sản phẩm: " . $e->getMessage();
    }
    $action = 'list';
}

// Handle Form Submit (Add / Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = clean_input($_POST['name'] ?? '');
    $slug = !empty($_POST['slug']) ? create_slug($_POST['slug']) : create_slug($name);
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $brand_id = !empty($_POST['brand_id']) ? (int)$_POST['brand_id'] : null;
    $sku = clean_input($_POST['sku'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $sale_price = !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
    $short_desc = $_POST['short_description'] ?? '';
    $description = $_POST['description'] ?? '';
    $condition_type = $_POST['condition_type'] ?? 'renew';
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_hot = isset($_POST['is_hot']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $meta_title = clean_input($_POST['meta_title'] ?? '');
    $meta_description = clean_input($_POST['meta_description'] ?? '');

    // Process Specifications JSON
    $specs = [];
    if (isset($_POST['spec_keys']) && isset($_POST['spec_values'])) {
        foreach ($_POST['spec_keys'] as $k => $key) {
            $key = trim($key);
            if (!empty($key)) {
                $specs[$key] = trim($_POST['spec_values'][$k] ?? '');
            }
        }
    }
    $specifications = !empty($specs) ? json_encode($specs, JSON_UNESCAPED_UNICODE) : null;

    // Handle Image Upload
    $image = $_POST['current_image'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploaded = upload_image($_FILES['image'], 'products');
        if ($uploaded) {
            $image = $uploaded;
        }
    }

    if (empty($name)) {
        $error = "Vui lòng nhập tên sản phẩm.";
    } else {
        try {
            if ($action === 'add_submit') {
                $stmt = $pdo->prepare("
                    INSERT INTO products (name, slug, category_id, brand_id, sku, price, sale_price, short_description, description, specifications, image, condition_type, is_featured, is_hot, is_active, meta_title, meta_description, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $name, $slug, $category_id, $brand_id, $sku, $price, $sale_price,
                    $short_desc, $description, $specifications, $image, $condition_type,
                    $is_featured, $is_hot, $is_active, $meta_title, $meta_description
                ]);
                $message = "Thêm sản phẩm thành công.";
                $action = 'list';
            } elseif ($action === 'edit_submit' && $id > 0) {
                $stmt = $pdo->prepare("
                    UPDATE products SET 
                        name = ?, slug = ?, category_id = ?, brand_id = ?, sku = ?, 
                        price = ?, sale_price = ?, short_description = ?, description = ?, 
                        specifications = ?, image = ?, condition_type = ?, is_featured = ?, 
                        is_hot = ?, is_active = ?, meta_title = ?, meta_description = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([
                    $name, $slug, $category_id, $brand_id, $sku, $price, $sale_price,
                    $short_desc, $description, $specifications, $image, $condition_type,
                    $is_featured, $is_hot, $is_active, $meta_title, $meta_description, $id
                ]);
                $message = "Cập nhật sản phẩm thành công.";
                $action = 'list';
            }
        } catch (Exception $e) {
            $error = "Lỗi: " . $e->getMessage();
        }
    }
}

// Fetch categories & brands for dropdown
$categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order ASC, name ASC")->fetchAll();
$brands = $pdo->query("SELECT * FROM brands ORDER BY sort_order ASC, name ASC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 fw-bold text-gray-800"><i class="fas fa-boxes me-2"></i>Quản Lý Sản Phẩm</h2>
    <?php if ($action === 'list'): ?>
        <a href="?action=add" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Thêm Sản Phẩm</a>
    <?php else: ?>
        <a href="products.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại danh sách</a>
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
    $products = $pdo->query("
        SELECT p.*, c.name as category_name, b.name as brand_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN brands b ON p.brand_id = b.id 
        ORDER BY p.id DESC
    ")->fetchAll();
    ?>
    <div class="card shadow border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 50px;">ID</th>
                            <th style="width: 70px;">Ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Hãng</th>
                            <th>Giá bán</th>
                            <th>Trạng thái</th>
                            <th>Nổi bật</th>
                            <th style="width: 120px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($products)): foreach ($products as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td>
                                <img src="<?= get_image_url($p['image']) ?>" alt="" width="50" height="50" style="object-fit: contain; border-radius: 4px; background: #fff; border: 1px solid #ddd;">
                            </td>
                            <td>
                                <strong class="d-block"><?= htmlspecialchars($p['name']) ?></strong>
                                <small class="text-muted">Slug: <?= htmlspecialchars($p['slug']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($p['category_name'] ?? 'Chưa phân loại') ?></td>
                            <td><?= htmlspecialchars($p['brand_name'] ?? '-') ?></td>
                            <td>
                                <span class="fw-bold text-danger"><?= format_price($p['sale_price'] ?? $p['price']) ?></span>
                                <?php if ($p['sale_price']): ?>
                                    <br><small class="text-muted text-decoration-line-through"><?= format_price($p['price']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= $p['is_active'] ? 'success' : 'secondary' ?>">
                                    <?= $p['is_active'] ? 'Hiển thị' : 'Ẩn' ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($p['is_featured']): ?><span class="badge bg-primary me-1">Hot</span><?php endif; ?>
                                <?php if ($p['is_hot']): ?><span class="badge bg-danger">Bán chạy</span><?php endif; ?>
                            </td>
                            <td>
                                <a href="?action=edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-info text-white" title="Sửa"><i class="fas fa-edit"></i></a>
                                <a href="?action=delete&id=<?= $p['id'] ?>" class="btn btn-sm btn-danger delete-confirm" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');" title="Xóa"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted">Chưa có sản phẩm nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($action === 'add' || $action === 'edit'): 
    $product = [];
    $specs = [];
    if ($action === 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([(int)$_GET['id']]);
        $product = $stmt->fetch();
        if ($product && !empty($product['specifications'])) {
            $decoded = json_decode($product['specifications'], true);
            if (is_array($decoded)) $specs = $decoded;
        }
    }
?>
    <div class="card shadow border-0">
        <div class="card-body p-4">
            <form action="?action=<?= $action ?>_submit" method="POST" enctype="multipart/form-data">
                <?php if ($action === 'edit'): ?>
                    <input type="hidden" name="id" value="<?= $product['id'] ?>">
                    <input type="hidden" name="current_image" value="<?= htmlspecialchars($product['image'] ?? '') ?>">
                <?php endif; ?>
                
                <div class="row g-4">
                    <!-- Cột trái: Thông tin chính -->
                    <div class="col-lg-8">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-lg" required value="<?= htmlspecialchars($product['name'] ?? '') ?>" placeholder="Ví dụ: Máy Photocopy Ricoh MP 5055">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Đường dẫn tĩnh (Slug)</label>
                            <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($product['slug'] ?? '') ?>" placeholder="Tự động tạo nếu để trống">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Mã SKU</label>
                                <input type="text" name="sku" class="form-control" value="<?= htmlspecialchars($product['sku'] ?? '') ?>" placeholder="VD: RICOH-5055">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Giá gốc (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control" required value="<?= $product['price'] ?? 0 ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Giá khuyến mãi (VNĐ)</label>
                                <input type="number" name="sale_price" class="form-control" value="<?= $product['sale_price'] ?? '' ?>" placeholder="Bỏ trống nếu không giảm">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả ngắn</label>
                            <textarea name="short_description" class="form-control" rows="3" placeholder="Tóm tắt tính năng chính, thông số nổi bật..."><?= htmlspecialchars($product['short_description'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả chi tiết (HTML)</label>
                            <textarea name="description" class="form-control" rows="8" placeholder="Nội dung giới thiệu chi tiết sản phẩm..."><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                        </div>
                        
                        <!-- Dynamic Specifications -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Thông số kỹ thuật</label>
                            <div id="specs-container">
                                <?php if (!empty($specs)): foreach ($specs as $k => $v): ?>
                                <div class="row mb-2 spec-row">
                                    <div class="col-5"><input type="text" name="spec_keys[]" class="form-control" value="<?= htmlspecialchars($k) ?>" placeholder="Tên thông số"></div>
                                    <div class="col-6"><input type="text" name="spec_values[]" class="form-control" value="<?= htmlspecialchars($v) ?>" placeholder="Giá trị"></div>
                                    <div class="col-1"><button type="button" class="btn btn-danger btn-sm remove-spec"><i class="fas fa-times"></i></button></div>
                                </div>
                                <?php endforeach; else: ?>
                                <div class="row mb-2 spec-row">
                                    <div class="col-5"><input type="text" name="spec_keys[]" class="form-control" placeholder="Tên thông số (VD: Tốc độ sao chụp)"></div>
                                    <div class="col-6"><input type="text" name="spec_values[]" class="form-control" placeholder="Giá trị (VD: 50 bản/phút)"></div>
                                    <div class="col-1"><button type="button" class="btn btn-danger btn-sm remove-spec"><i class="fas fa-times"></i></button></div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <button type="button" id="add-spec" class="btn btn-sm btn-outline-primary mt-2"><i class="fas fa-plus me-1"></i>Thêm thông số kỹ thuật</button>
                        </div>

                        <!-- SEO Meta -->
                        <div class="card bg-light border p-3">
                            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-search me-1"></i>Cấu hình SEO</h6>
                            <div class="mb-2">
                                <label class="form-label small">SEO Meta Title</label>
                                <input type="text" name="meta_title" class="form-control" value="<?= htmlspecialchars($product['meta_title'] ?? '') ?>">
                            </div>
                            <div>
                                <label class="form-label small">SEO Meta Description</label>
                                <textarea name="meta_description" class="form-control" rows="2"><?= htmlspecialchars($product['meta_description'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Cột phải: Thuộc tính & Ảnh -->
                    <div class="col-lg-4">
                        <div class="card mb-3 border">
                            <div class="card-header bg-primary text-white fw-bold">Phân loại & Hãng</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Danh mục sản phẩm</label>
                                    <select name="category_id" class="form-select">
                                        <option value="">-- Chọn danh mục --</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>" <?= (isset($product['category_id']) && $product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Thương hiệu / Hãng</label>
                                    <select name="brand_id" class="form-select">
                                        <option value="">-- Chọn thương hiệu --</option>
                                        <?php foreach ($brands as $b): ?>
                                            <option value="<?= $b['id'] ?>" <?= (isset($product['brand_id']) && $product['brand_id'] == $b['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($b['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Tình trạng máy</label>
                                    <select name="condition_type" class="form-select">
                                        <option value="new" <?= (isset($product['condition_type']) && $product['condition_type'] === 'new') ? 'selected' : '' ?>>Mới 100% Chính hãng</option>
                                        <option value="renew" <?= (!isset($product['condition_type']) || $product['condition_type'] === 'renew') ? 'selected' : '' ?>>Máy Renew 99%</option>
                                        <option value="used" <?= (isset($product['condition_type']) && $product['condition_type'] === 'used') ? 'selected' : '' ?>>Nhập khẩu đã qua sử dụng</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mb-3 border">
                            <div class="card-header bg-primary text-white fw-bold">Hình ảnh đại diện</div>
                            <div class="card-body text-center">
                                <?php if (!empty($product['image'])): ?>
                                    <div class="mb-3">
                                        <img src="<?= get_image_url($product['image']) ?>" alt="" class="img-thumbnail" style="max-height: 160px; object-fit: contain;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <small class="text-muted d-block mt-1">Hỗ trợ JPG, PNG, WebP (Dung lượng < 5MB)</small>
                            </div>
                        </div>
                        
                        <div class="card mb-4 border">
                            <div class="card-header bg-primary text-white fw-bold">Tùy chọn hiển thị</div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" <?= (!isset($product['is_active']) || $product['is_active'] == 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="isActive">Kích hoạt (Hiển thị website)</label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured" <?= (!empty($product['is_featured'])) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="isFeatured">Sản phẩm nổi bật</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_hot" id="isHot" <?= (!empty($product['is_hot'])) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="isHot">Sản phẩm bán chạy</label>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 btn-lg fw-bold shadow">
                            <i class="fas fa-save me-1"></i> <?= $action === 'add' ? 'Lưu Sản Phẩm' : 'Cập Nhật Sản Phẩm' ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('add-spec')?.addEventListener('click', function() {
            const container = document.getElementById('specs-container');
            const row = document.createElement('div');
            row.className = 'row mb-2 spec-row';
            row.innerHTML = `
                <div class="col-5"><input type="text" name="spec_keys[]" class="form-control" placeholder="Tên thông số"></div>
                <div class="col-6"><input type="text" name="spec_values[]" class="form-control" placeholder="Giá trị"></div>
                <div class="col-1"><button type="button" class="btn btn-danger btn-sm remove-spec"><i class="fas fa-times"></i></button></div>
            `;
            container.appendChild(row);
        });
        
        document.getElementById('specs-container')?.addEventListener('click', function(e) {
            if (e.target.closest('.remove-spec')) {
                e.target.closest('.spec-row').remove();
            }
        });
    });
    </script>
<?php endif; ?>

<?php require_once 'includes/admin-footer.php'; ?>
