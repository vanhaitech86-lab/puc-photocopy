<?php
require_once __DIR__ . '/includes/admin-header.php';

// Fetch stats
$stats = [
    'products' => $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
    'contacts' => $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn(),
    'news' => $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn(),
    'categories' => $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
];

// Fetch recent contacts
$recent_contacts = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Fetch recent products
$recent_products = $pdo->query("SELECT p.*, c.name as category_name 
                               FROM products p 
                               LEFT JOIN categories c ON p.category_id = c.id 
                               ORDER BY p.created_at DESC LIMIT 5")->fetchAll();
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

<div class="row">
    <!-- Products Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng sản phẩm</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['products'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-box fa-2x text-gray-300" style="color: #dddfeb;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contacts Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Liên hệ</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['contacts'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-envelope fa-2x text-gray-300" style="color: #dddfeb;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- News Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Bài viết</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['news'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-newspaper fa-2x text-gray-300" style="color: #dddfeb;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Danh mục</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['categories'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-list fa-2x text-gray-300" style="color: #dddfeb;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Contacts -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Liên hệ mới nhất</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tên</th>
                                <th>SĐT</th>
                                <th>Ngày</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recent_contacts as $contact): ?>
                            <tr>
                                <td><?= htmlspecialchars($contact['name']) ?></td>
                                <td><?= htmlspecialchars($contact['phone']) ?></td>
                                <td><?= date('d/m/Y', strtotime($contact['created_at'])) ?></td>
                                <td>
                                    <?php if($contact['is_read']): ?>
                                        <span class="badge bg-secondary">Đã đọc</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Mới</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Products -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Sản phẩm mới nhất</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Ảnh</th>
                                <th>Tên SP</th>
                                <th>Danh mục</th>
                                <th>Giá</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recent_products as $product): ?>
                            <tr>
                                <td>
                                    <?php if($product['image']): ?>
                                        <img src="../<?= htmlspecialchars($product['image']) ?>" alt="thumb" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                    <?php else: ?>
                                        <div style="width: 40px; height: 40px; background: #eee; border-radius: 4px;"></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($product['name']) ?>">
                                        <?= htmlspecialchars($product['name']) ?>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($product['category_name']) ?></td>
                                <td><?= number_format($product['price'], 0, ',', '.') ?> đ</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
