<?php
/**
 * AJAX API Endpoints
 * Handles search suggestions, contact form, cart operations
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/json; charset=UTF-8');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'search':
        handle_search();
        break;
    case 'contact':
        handle_contact_form();
        break;
    case 'newsletter':
        handle_newsletter();
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
}

/**
 * Tìm kiếm sản phẩm (gợi ý Ajax)
 */
function handle_search() {
    global $pdo;
    $q = trim($_GET['q'] ?? '');
    
    if (strlen($q) < 2) {
        echo json_encode(['results' => []]);
        return;
    }
    
    $stmt = $pdo->prepare("
        SELECT p.name, p.slug, p.image, p.price, p.sale_price, b.name as brand_name
        FROM products p
        LEFT JOIN brands b ON p.brand_id = b.id
        WHERE p.is_active = 1 AND (p.name LIKE :q OR p.sku LIKE :q2)
        ORDER BY p.is_featured DESC, p.name ASC
        LIMIT 8
    ");
    $stmt->execute(['q' => "%{$q}%", 'q2' => "%{$q}%"]);
    $products = $stmt->fetchAll();
    
    $results = [];
    foreach ($products as $p) {
        $results[] = [
            'name' => $p['name'],
            'slug' => $p['slug'],
            'url' => SITE_URL . '/' . $p['slug'] . '.html',
            'image' => $p['image'] ? SITE_URL . '/assets/' . $p['image'] : SITE_URL . '/assets/images/no-image.jpg',
            'price' => format_price($p['sale_price'] ?? $p['price']),
            'original_price' => $p['sale_price'] ? format_price($p['price']) : null,
            'brand' => $p['brand_name']
        ];
    }
    
    echo json_encode(['results' => $results]);
}

/**
 * Gửi form liên hệ
 */
function handle_contact_form() {
    global $pdo;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }
    
    $name = clean_input($_POST['name'] ?? '');
    $phone = clean_input($_POST['phone'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $subject = clean_input($_POST['subject'] ?? '');
    $message = clean_input($_POST['message'] ?? '');
    $product_id = !empty($_POST['product_id']) ? (int)$_POST['product_id'] : null;
    
    // Validation
    $errors = [];
    if (empty($name)) $errors[] = 'Vui lòng nhập họ tên';
    if (empty($phone)) $errors[] = 'Vui lòng nhập số điện thoại';
    if (!preg_match('/^[0-9]{9,11}$/', $phone)) $errors[] = 'Số điện thoại không hợp lệ';
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ';
    
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO contacts (name, phone, email, subject, message, product_id, created_at)
            VALUES (:name, :phone, :email, :subject, :message, :product_id, NOW())
        ");
        $stmt->execute([
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'product_id' => $product_id
        ]);
        
        // Gửi email thông báo (optional)
        $to = EMAIL;
        $mail_subject = "Liên hệ mới từ website PUC: " . $subject;
        $mail_body = "Tên: {$name}\nĐiện thoại: {$phone}\nEmail: {$email}\nNội dung: {$message}";
        @mail($to, $mail_subject, $mail_body);
        
        echo json_encode(['success' => true, 'message' => 'Gửi thông tin thành công! Chúng tôi sẽ liên hệ lại sớm nhất.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại sau.']);
    }
}

/**
 * Đăng ký nhận tin
 */
function handle_newsletter() {
    $email = clean_input($_POST['email'] ?? '');
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Email không hợp lệ']);
        return;
    }
    
    echo json_encode(['success' => true, 'message' => 'Đăng ký nhận tin thành công!']);
}
