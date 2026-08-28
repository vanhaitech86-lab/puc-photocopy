<?php
require_once 'includes/admin-header.php';

$action = $_GET['action'] ?? 'list';
$message = '';

// Handle Delete
if ($action == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $pdo->prepare("DELETE FROM contacts WHERE id = ?")->execute([$id]);
    $message = "Đã xóa liên hệ.";
    $action = 'list';
}

// Handle Mark as Read
if ($action == 'read' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $pdo->prepare("UPDATE contacts SET is_read = 1 WHERE id = ?")->execute([$id]);
    $message = "Đã đánh dấu là đã đọc.";
    $action = 'list';
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Quản lý Liên hệ</h2>
</div>

<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($action == 'list'): ?>
    <?php
    $contacts = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC")->fetchAll();
    ?>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Ngày gửi</th>
                            <th>Người gửi</th>
                            <th>SĐT</th>
                            <th>Email</th>
                            <th>Tiêu đề</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $c): ?>
                        <tr class="<?= $c['is_read'] ? '' : 'fw-bold' ?>">
                            <td><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></td>
                            <td><?= htmlspecialchars($c['name']) ?></td>
                            <td><?= htmlspecialchars($c['phone']) ?></td>
                            <td><?= htmlspecialchars($c['email']) ?></td>
                            <td><?= htmlspecialchars($c['subject']) ?></td>
                            <td>
                                <?php if($c['is_read']): ?>
                                    <span class="badge bg-secondary">Đã đọc</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Mới</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?action=view&id=<?= $c['id'] ?>" class="btn btn-sm btn-info text-white" title="Xem chi tiết"><i class="fas fa-eye"></i></a>
                                <?php if(!$c['is_read']): ?>
                                    <a href="?action=read&id=<?= $c['id'] ?>" class="btn btn-sm btn-success" title="Đánh dấu đã đọc"><i class="fas fa-check"></i></a>
                                <?php endif; ?>
                                <a href="?action=delete&id=<?= $c['id'] ?>" class="btn btn-sm btn-danger delete-confirm"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($action == 'view' && isset($_GET['id'])): 
    $id = (int)$_GET['id'];
    // Mark as read when viewing
    $pdo->prepare("UPDATE contacts SET is_read = 1 WHERE id = ?")->execute([$id]);
    
    $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
    $stmt->execute([$id]);
    $contact = $stmt->fetch();
    
    if (!$contact):
?>
    <div class="alert alert-danger">Không tìm thấy liên hệ.</div>
<?php else: ?>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Chi tiết liên hệ</h5>
            <a href="contacts.php" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-sm-3 text-muted">Họ tên:</div>
                <div class="col-sm-9 fw-bold"><?= htmlspecialchars($contact['name']) ?></div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 text-muted">Điện thoại:</div>
                <div class="col-sm-9 fw-bold"><?= htmlspecialchars($contact['phone']) ?></div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 text-muted">Email:</div>
                <div class="col-sm-9 fw-bold"><?= htmlspecialchars($contact['email']) ?></div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 text-muted">Ngày gửi:</div>
                <div class="col-sm-9"><?= date('d/m/Y H:i:s', strtotime($contact['created_at'])) ?></div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 text-muted">Tiêu đề:</div>
                <div class="col-sm-9 fw-bold"><?= htmlspecialchars($contact['subject']) ?></div>
            </div>
            <hr>
            <div class="mb-3">
                <div class="text-muted mb-2">Nội dung tin nhắn:</div>
                <div class="p-3 bg-light rounded border">
                    <?= nl2br(htmlspecialchars($contact['message'])) ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; endif; ?>

<?php require_once 'includes/admin-footer.php'; ?>
