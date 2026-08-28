<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (is_admin_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Vui lòng nhập tài khoản và mật khẩu.';
    } else {
        if (admin_login($username, $password)) {
            header('Location: index.php');
            exit;
        } else {
            $error = 'Tài khoản hoặc mật khẩu không chính xác.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập quản trị - PUC Photocopy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background-color: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .login-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            padding: 35px 30px;
        }
        .login-logo {
            max-height: 55px;
            object-fit: contain;
        }
        .btn-login {
            background-color: #2563eb;
            color: #fff;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .btn-login:hover {
            background-color: #1d4ed8;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <img src="../assets/images/logo.png" alt="PUC Logo" class="login-logo mb-2">
        <h4 class="fw-bold text-dark mb-1">HỆ THỐNG QUẢN TRỊ PUC</h4>
        <small class="text-muted">Đăng nhập để quản lý sản phẩm & dịch vụ</small>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show small py-2">
            <i class="fas fa-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label fw-semibold small text-secondary">Tên đăng nhập</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                <input type="text" name="username" class="form-control border-start-0" placeholder="admin" required autofocus>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold small text-secondary">Mật khẩu</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
            </div>
            <small class="text-muted d-block mt-1">Mặc định: <code>admin</code> / <code>admin123</code></small>
        </div>

        <button type="submit" class="btn btn-login w-100 mb-3 shadow-sm">
            <i class="fas fa-sign-in-alt me-2"></i>ĐĂNG NHẬP
        </button>

        <div class="text-center">
            <a href="../" class="text-decoration-none small text-muted"><i class="fas fa-arrow-left me-1"></i>Quay lại trang chủ website</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
