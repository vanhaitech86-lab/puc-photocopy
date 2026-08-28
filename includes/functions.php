<?php
// includes/functions.php
require_once __DIR__ . '/../config.php';

/**
 * Định dạng giá tiền VNĐ
 */
function format_price($price) {
    if (!$price || $price <= 0) return 'Liên hệ';
    return number_format($price, 0, ',', '.') . ' ₫';
}

/**
 * Lấy URL ảnh hợp lệ (hoặc ảnh mặc định)
 */
function get_image_url($image_path, $default = 'assets/images/no-image.jpg') {
    if (empty($image_path)) {
        return SITE_URL . '/' . ltrim($default, '/');
    }
    if (strpos($image_path, 'http://') === 0 || strpos($image_path, 'https://') === 0) {
        return $image_path;
    }
    if (strpos($image_path, 'assets/') === 0) {
        return SITE_URL . '/' . $image_path;
    }
    if (strpos($image_path, 'uploads/') === 0) {
        return SITE_URL . '/assets/' . $image_path;
    }
    return SITE_URL . '/assets/uploads/' . ltrim($image_path, '/');
}

/**
 * Lấy cài đặt từ database
 */
function get_setting($key, $default = '') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        return $val !== false ? $val : $default;
    } catch (Exception $e) {
        return $default;
    }
}

/**
 * Tạo URL slug từ tiếng Việt
 */
function create_slug($string) {
    $search = array(
        '#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#',
        '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#',
        '#(ì|í|ị|ỉ|ĩ)#',
        '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#',
        '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#',
        '#(ỳ|ý|ỵ|ỷ|ỹ)#',
        '#(đ)#',
        '#(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)#',
        '#(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)#',
        '#(Ì|Í|Ị|Ỉ|Ĩ)#',
        '#(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)#',
        '#(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)#',
        '#(Ỳ|Ý|Ỵ|Ỷ|Ỹ)#',
        '#(Đ)#',
        '/[^a-zA-Z0-9\-\_]/'
    );
    $replace = array(
        'a', 'e', 'i', 'o', 'u', 'y', 'd',
        'A', 'E', 'I', 'O', 'U', 'Y', 'D',
        '-'
    );
    $string = preg_replace($search, $replace, $string);
    $string = preg_replace('/(-)+/', '-', $string);
    $string = strtolower($string);
    return trim($string, '-');
}

/**
 * Cắt ngắn văn bản
 */
function truncate_text($text, $length = 120) {
    $text = strip_tags($text);
    if (mb_strlen($text, 'UTF-8') > $length) {
        $text = mb_substr($text, 0, $length, 'UTF-8') . '...';
    }
    return $text;
}

/**
 * Lấy danh mục sản phẩm
 */
function get_categories($parent_id = null) {
    global $pdo;
    try {
        if ($parent_id === null) {
            $stmt = $pdo->prepare("SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
            $stmt->execute();
        } else {
            $stmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ? AND is_active = 1 ORDER BY sort_order ASC, id ASC");
            $stmt->execute([$parent_id]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Lấy chi tiết danh mục theo slug
 */
function get_category_by_slug($slug) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ? AND is_active = 1");
        $stmt->execute([$slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Lấy chi tiết sản phẩm theo slug
 */
function get_product_by_slug($slug) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT p.*, c.name as category_name, c.slug as category_slug, b.name as brand_name, b.slug as brand_slug
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            WHERE p.slug = ? AND p.is_active = 1
        ");
        $stmt->execute([$slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Lấy danh sách sản phẩm theo bộ lọc
 */
function get_products($options = []) {
    global $pdo;
    $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug, b.name as brand_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN brands b ON p.brand_id = b.id 
            WHERE p.is_active = 1";
    $params = [];
    
    if (!empty($options['category_id'])) {
        $sql .= " AND (p.category_id = ? OR c.parent_id = ?)";
        $params[] = $options['category_id'];
        $params[] = $options['category_id'];
    }
    if (!empty($options['category_slug'])) {
        $sql .= " AND (c.slug = ? OR c.parent_id IN (SELECT id FROM categories WHERE slug = ?))";
        $params[] = $options['category_slug'];
        $params[] = $options['category_slug'];
    }
    if (!empty($options['brand_id'])) {
        $sql .= " AND p.brand_id = ?";
        $params[] = $options['brand_id'];
    }
    if (!empty($options['brand_slug'])) {
        $sql .= " AND b.slug = ?";
        $params[] = $options['brand_slug'];
    }
    if (!empty($options['condition_type'])) {
        $sql .= " AND p.condition_type = ?";
        $params[] = $options['condition_type'];
    }
    if (isset($options['is_featured'])) {
        $sql .= " AND p.is_featured = ?";
        $params[] = $options['is_featured'];
    }
    if (isset($options['is_hot'])) {
        $sql .= " AND p.is_hot = ?";
        $params[] = $options['is_hot'];
    }
    if (!empty($options['search'])) {
        $sql .= " AND (p.name LIKE ? OR p.short_description LIKE ? OR p.sku LIKE ?)";
        $searchParam = '%' . $options['search'] . '%';
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    if (!empty($options['order_by'])) {
        $sql .= " ORDER BY " . $options['order_by'];
    } else {
        $sql .= " ORDER BY p.created_at DESC";
    }
    
    if (isset($options['limit'])) {
        $sql .= " LIMIT " . (int)$options['limit'];
        if (isset($options['offset'])) {
            $sql .= " OFFSET " . (int)$options['offset'];
        }
    }
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Đếm tổng số sản phẩm theo bộ lọc (dùng cho phân trang)
 */
function count_products($options = []) {
    global $pdo;
    $sql = "SELECT COUNT(*) 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN brands b ON p.brand_id = b.id 
            WHERE p.is_active = 1";
    $params = [];
    
    if (!empty($options['category_id'])) {
        $sql .= " AND (p.category_id = ? OR c.parent_id = ?)";
        $params[] = $options['category_id'];
        $params[] = $options['category_id'];
    }
    if (!empty($options['category_slug'])) {
        $sql .= " AND (c.slug = ? OR c.parent_id IN (SELECT id FROM categories WHERE slug = ?))";
        $params[] = $options['category_slug'];
        $params[] = $options['category_slug'];
    }
    if (!empty($options['brand_id'])) {
        $sql .= " AND p.brand_id = ?";
        $params[] = $options['brand_id'];
    }
    if (!empty($options['brand_slug'])) {
        $sql .= " AND b.slug = ?";
        $params[] = $options['brand_slug'];
    }
    if (!empty($options['condition_type'])) {
        $sql .= " AND p.condition_type = ?";
        $params[] = $options['condition_type'];
    }
    if (isset($options['is_featured'])) {
        $sql .= " AND p.is_featured = ?";
        $params[] = $options['is_featured'];
    }
    if (isset($options['is_hot'])) {
        $sql .= " AND p.is_hot = ?";
        $params[] = $options['is_hot'];
    }
    if (!empty($options['search'])) {
        $sql .= " AND (p.name LIKE ? OR p.short_description LIKE ? OR p.sku LIKE ?)";
        $searchParam = '%' . $options['search'] . '%';
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

function get_featured_products($limit = 8) {
    return get_products(['is_featured' => 1, 'limit' => $limit]);
}

function get_hot_products($limit = 8) {
    return get_products(['is_hot' => 1, 'limit' => $limit]);
}

function get_latest_products($limit = 8) {
    return get_products(['limit' => $limit]);
}

function get_related_products($product_id, $category_id, $limit = 4) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM products 
            WHERE category_id = ? AND id != ? AND is_active = 1 
            ORDER BY RAND() LIMIT ?
        ");
        $stmt->execute([$category_id, $product_id, (int)$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function get_brands() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM brands WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function get_banners($position = 'home_slider') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM banners WHERE position = ? AND is_active = 1 ORDER BY sort_order ASC, id ASC");
        $stmt->execute([$position]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function get_news($limit = 9, $offset = 0, $category = null) {
    global $pdo;
    $sql = "SELECT * FROM news WHERE is_active = 1";
    $params = [];
    if ($category) {
        $sql .= " AND category = ?";
        $params[] = $category;
    }
    $sql .= " ORDER BY created_at DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function get_news_by_slug($slug) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM news WHERE slug = ? AND is_active = 1");
        $stmt->execute([$slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return null;
    }
}

function get_rental_packages($color_type = null) {
    global $pdo;
    $sql = "SELECT * FROM rental_packages WHERE is_active = 1";
    $params = [];
    if ($color_type) {
        $sql .= " AND color_type = ?";
        $params[] = $color_type;
    }
    $sql .= " ORDER BY sort_order ASC, monthly_price ASC";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function get_promotions() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM promotions WHERE is_active = 1 AND (end_date IS NULL OR end_date >= CURDATE()) ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function save_contact($data) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO contacts (name, phone, email, subject, message, product_id, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        return $stmt->execute([
            $data['name'], 
            $data['phone'], 
            $data['email'] ?? '', 
            $data['subject'] ?? '', 
            $data['message'] ?? '',
            $data['product_id'] ?? null
        ]);
    } catch (Exception $e) {
        return false;
    }
}

function increment_view($table, $id) {
    global $pdo;
    $allowed = ['products', 'news'];
    if (!in_array($table, $allowed)) return false;
    try {
        $stmt = $pdo->prepare("UPDATE `{$table}` SET view_count = view_count + 1 WHERE id = ?");
        return $stmt->execute([(int)$id]);
    } catch (Exception $e) {
        return false;
    }
}

function get_breadcrumb_trail($category_id) {
    global $pdo;
    $trail = [];
    $current_id = $category_id;
    while ($current_id) {
        try {
            $stmt = $pdo->prepare("SELECT id, name, slug, parent_id FROM categories WHERE id = ?");
            $stmt->execute([$current_id]);
            $cat = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($cat) {
                array_unshift($trail, $cat);
                $current_id = $cat['parent_id'];
            } else {
                break;
            }
        } catch (Exception $e) {
            break;
        }
    }
    return $trail;
}

function is_admin_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function admin_login($username, $password) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user_id'] = $user['id'];
            $_SESSION['admin_fullname'] = $user['fullname'] ?? $user['name'] ?? $user['username'];
            $_SESSION['admin_username'] = $user['username'];
            return true;
        }
    } catch (Exception $e) {
        return false;
    }
    return false;
}

function admin_logout() {
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_user_id']);
    unset($_SESSION['admin_fullname']);
    unset($_SESSION['admin_username']);
}

function upload_image($file, $folder = 'products') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return false;
    
    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_exts)) return false;
    
    $filename = time() . '_' . uniqid() . '.' . $ext;
    $target_dir = UPLOAD_PATH . trim($folder, '/') . '/';
    
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    $target_path = $target_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return 'uploads/' . trim($folder, '/') . '/' . $filename;
    }
    return false;
}

function clean_input($data) {
    $data = trim((string)$data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function generate_pagination($total, $per_page, $current_page, $base_url) {
    $total_pages = ceil($total / $per_page);
    if ($total_pages <= 1) return '';
    
    $delim = (strpos($base_url, '?') !== false) ? '&' : '?';
    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
    
    if ($current_page > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="'.$base_url.$delim.'page='.($current_page - 1).'">« Trước</a></li>';
    }
    
    for ($i = 1; $i <= $total_pages; $i++) {
        $active = $i == $current_page ? ' active' : '';
        $html .= '<li class="page-item'.$active.'"><a class="page-link" href="'.$base_url.$delim.'page='.$i.'">'.$i.'</a></li>';
    }
    
    if ($current_page < $total_pages) {
        $html .= '<li class="page-item"><a class="page-link" href="'.$base_url.$delim.'page='.($current_page + 1).'">Sau »</a></li>';
    }
    
    $html .= '</ul></nav>';
    return $html;
}
