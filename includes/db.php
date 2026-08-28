<?php
/**
 * Database Connection - PDO with MySQL & SQLite Fallback
 */
require_once __DIR__ . '/../config.php';

$pdo = null;

// 1. Thử kết nối MySQL nếu có cấu hình
if (DB_HOST && DB_NAME) {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT            => 2, // Timeout nhanh nếu không kết nối được
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        $pdo = null;
    }
}

// 2. Nếu MySQL không khả dụng (Vercel Serverless / chưa setup DB), dùng SQLite Fallback
if (!$pdo) {
    try {
        $sqlite_file = __DIR__ . '/../database/puc.sqlite';
        $is_new = !file_exists($sqlite_file);
        
        $pdo = new PDO("sqlite:" . $sqlite_file);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Tạo bảng & dữ liệu mẫu nếu file SQLite mới tạo
        if ($is_new || filesize($sqlite_file) < 100) {
            init_sqlite_db($pdo);
        }
    } catch (Exception $e) {
        // Mock fallback nếu môi trường readonly
        die("Không thể kết nối cơ sở dữ liệu. Vui lòng kiểm tra cấu hình trong config.php hoặc biến môi trường.");
    }
}

/**
 * Khởi tạo bảng và dữ liệu mẫu cho SQLite
 */
function init_sqlite_db($pdo) {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            fullname TEXT NOT NULL,
            email TEXT,
            role TEXT DEFAULT 'admin',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT UNIQUE NOT NULL,
            parent_id INTEGER DEFAULT NULL,
            description TEXT,
            image TEXT,
            icon TEXT,
            sort_order INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            meta_title TEXT,
            meta_description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS brands (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT UNIQUE NOT NULL,
            logo TEXT,
            sort_order INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1
        );

        CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT UNIQUE NOT NULL,
            category_id INTEGER,
            brand_id INTEGER,
            sku TEXT,
            price REAL DEFAULT 0,
            sale_price REAL DEFAULT NULL,
            short_description TEXT,
            description TEXT,
            specifications TEXT,
            image TEXT,
            gallery TEXT,
            condition_type TEXT DEFAULT 'renew',
            is_featured INTEGER DEFAULT 0,
            is_hot INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            view_count INTEGER DEFAULT 0,
            meta_title TEXT,
            meta_description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS promotions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            slug TEXT UNIQUE NOT NULL,
            description TEXT,
            discount_type TEXT DEFAULT 'percent',
            discount_value REAL DEFAULT 0,
            image TEXT,
            start_date DATE,
            end_date DATE,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS banners (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT,
            image TEXT NOT NULL,
            link TEXT,
            position TEXT DEFAULT 'home_slider',
            sort_order INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS news (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            slug TEXT UNIQUE NOT NULL,
            excerpt TEXT,
            content TEXT,
            image TEXT,
            category TEXT DEFAULT 'news',
            view_count INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            meta_title TEXT,
            meta_description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS contacts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            phone TEXT NOT NULL,
            email TEXT,
            subject TEXT,
            message TEXT,
            product_id INTEGER DEFAULT NULL,
            is_read INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS settings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            setting_key TEXT UNIQUE NOT NULL,
            setting_value TEXT,
            setting_group TEXT DEFAULT 'general'
        );

        CREATE TABLE IF NOT EXISTS rental_packages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            machine_type TEXT,
            speed TEXT,
            color_type TEXT DEFAULT 'bw',
            monthly_price REAL DEFAULT 0,
            included_pages INTEGER DEFAULT 0,
            extra_page_price REAL DEFAULT 0,
            deposit REAL DEFAULT 0,
            min_contract_months INTEGER DEFAULT 12,
            features TEXT,
            is_active INTEGER DEFAULT 1,
            sort_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Thêm dữ liệu mặc định
    $pdo->exec("
        INSERT OR IGNORE INTO admin_users (username, password, fullname, email) VALUES
        ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Quản trị viên', 'phuong86.annguyen@gmail.com');

        INSERT OR IGNORE INTO categories (id, name, slug, parent_id, description, icon, sort_order) VALUES
        (1, 'Máy Photocopy', 'may-photocopy', NULL, 'Máy photocopy nhập khẩu chính hãng', 'fa-print', 1),
        (2, 'Máy Photocopy Trắng Đen', 'may-photocopy-trang-den', 1, 'Máy photocopy trắng đen các hãng', 'fa-print', 1),
        (3, 'Máy Photocopy Màu', 'may-photocopy-mau', 1, 'Máy photocopy màu các hãng', 'fa-palette', 2),
        (4, 'Máy Photocopy Mới 100%', 'may-photocopy-moi', 1, 'Máy photocopy mới 100% chính hãng', 'fa-star', 3),
        (5, 'Máy Khổ Lớn A0', 'may-kho-lon-a0', 1, 'Máy photocopy khổ lớn A0', 'fa-expand', 4),
        (6, 'Cho Thuê Máy Photocopy', 'cho-thue-may-photocopy', NULL, 'Dịch vụ cho thuê máy photocopy', 'fa-handshake', 2),
        (7, 'Thuê Máy Trắng Đen', 'thue-may-trang-den', 6, 'Cho thuê máy photocopy trắng đen', 'fa-print', 1),
        (8, 'Thuê Máy Màu', 'thue-may-mau', 6, 'Cho thuê máy photocopy màu', 'fa-palette', 2),
        (9, 'Thuê Máy Khổ A0', 'thue-may-kho-a0', 6, 'Cho thuê máy khổ lớn A0', 'fa-expand', 3),
        (10, 'Máy In', 'may-in', NULL, 'Máy in các loại', 'fa-print', 3),
        (11, 'Vật Tư & Linh Kiện', 'vat-tu-linh-kien', NULL, 'Mực in, linh kiện máy photocopy', 'fa-cogs', 4),
        (12, 'Mực Máy Photocopy', 'muc-may-photocopy', 11, 'Mực máy photocopy các hãng', 'fa-tint', 1),
        (13, 'Linh Kiện Ricoh', 'linh-kien-ricoh', 11, 'Linh kiện máy Ricoh', 'fa-wrench', 2),
        (14, 'Linh Kiện Toshiba', 'linh-kien-toshiba', 11, 'Linh kiện máy Toshiba', 'fa-wrench', 3),
        (15, 'Linh Kiện Konica', 'linh-kien-konica', 11, 'Linh kiện máy Konica', 'fa-wrench', 4),
        (16, 'Máy Ép - Bàn Cắt', 'may-ep-ban-cat', NULL, 'Máy ép plastic, bàn cắt giấy', 'fa-cut', 5);

        INSERT OR IGNORE INTO brands (name, slug, sort_order) VALUES
        ('Ricoh', 'ricoh', 1),
        ('Toshiba', 'toshiba', 2),
        ('Canon', 'canon', 3),
        ('Konica Minolta', 'konica-minolta', 4),
        ('Fujifilm', 'fujifilm', 5),
        ('Pantum', 'pantum', 6),
        ('HP', 'hp', 7);

        INSERT OR IGNORE INTO products (name, slug, category_id, brand_id, price, sale_price, short_description, description, specifications, image, condition_type, is_featured, is_hot) VALUES
        ('Ricoh MP 5055 Nhập Khẩu', 'ricoh-mp-5055-nhap-khau', 2, 1, 35000000, 29000000, 'Tốc độ sao chụp/in: 50 bản/phút. Khổ giấy: A3-A5. Bộ nhớ: 4GB RAM.', '<h3>Máy Photocopy Ricoh MP 5055 Nhập Khẩu</h3><p>Ricoh MP 5055 là dòng máy photocopy đa chức năng trắng đen cao cấp, phù hợp cho văn phòng lớn và trung tâm in ấn.</p><h4>Tính năng nổi bật:</h4><ul><li>Tốc độ in/copy: 50 trang/phút</li><li>Độ phân giải: 1200x1200 dpi</li><li>Khổ giấy: A3 đến A5</li><li>Bộ nhớ: 4GB RAM + 320GB HDD</li><li>Kết nối: USB, Ethernet, WiFi (optional)</li></ul>', '{\"speed\":\"50 bản/phút\",\"paper\":\"A3-A5\",\"memory\":\"4GB RAM + 320GB HDD\",\"resolution\":\"1200x1200 dpi\",\"duplex\":\"Tự động 2 mặt\",\"feeder\":\"ARDF 100 tờ\",\"tray\":\"2 khay x 550 tờ\"}', 'uploads/ricoh-mp-5055.jpg', 'used', 1, 1),
        ('Ricoh MP 6055 Renew 99%', 'ricoh-mp-6055-renew-99', 2, 1, 42000000, 38000000, 'Tốc độ sao chụp/in: 60 bản/phút. Máy Renew 99% như mới.', '<h3>Máy Photocopy Ricoh MP 6055 Renew 99%</h3><p>Máy được renew tại Nhật Bản, tình trạng 99% như mới. Phù hợp cho văn phòng có nhu cầu in ấn lớn.</p>', '{\"speed\":\"60 bản/phút\",\"paper\":\"A3-A5\",\"memory\":\"4GB RAM + 320GB HDD\",\"resolution\":\"1200x1200 dpi\",\"duplex\":\"Tự động 2 mặt\"}', 'uploads/ricoh-mp-6055.jpg', 'renew', 1, 0),
        ('Toshiba e-Studio 5018A Nhập Khẩu', 'toshiba-e-studio-5018a', 2, 2, 32000000, NULL, 'Tốc độ 50 bản/phút. Máy photocopy đa chức năng Toshiba.', '<h3>Máy Photocopy Toshiba e-Studio 5018A</h3><p>Dòng máy photocopy đa chức năng tiết kiệm năng lượng của Toshiba.</p>', '{\"speed\":\"50 bản/phút\",\"paper\":\"A3-A5\",\"memory\":\"4GB RAM\",\"resolution\":\"1200x1200 dpi\"}', 'uploads/toshiba-5018a.jpg', 'used', 1, 0),
        ('Ricoh IM C3000 Màu Nhập Khẩu', 'ricoh-im-c3000-mau-nhap-khau', 3, 1, 55000000, 48000000, 'Máy photocopy màu Ricoh IM C3000, tốc độ 30 bản/phút.', '<h3>Máy Photocopy Màu Ricoh IM C3000</h3><p>Máy photocopy màu đa chức năng thế hệ mới của Ricoh.</p>', '{\"speed\":\"30 bản/phút\",\"paper\":\"A3-A6\",\"memory\":\"4GB RAM + 320GB HDD\",\"resolution\":\"1200x1200 dpi\",\"color\":\"Có\"}', 'uploads/ricoh-im-c3000.jpg', 'used', 1, 1),
        ('Ricoh IM 3500 Mới 100%', 'ricoh-im-3500-moi-100', 4, 1, 65000000, NULL, 'Máy photocopy mới 100% chính hãng Ricoh IM 3500.', '<h3>Máy Photocopy Ricoh IM 3500 Mới 100%</h3><p>Máy photocopy trắng đen mới 100%, bảo hành chính hãng.</p>', '{\"speed\":\"35 bản/phút\",\"paper\":\"A3-A5\",\"memory\":\"2GB RAM + 320GB HDD\",\"resolution\":\"1200x1200 dpi\"}', 'uploads/ricoh-im-3500.jpg', 'new', 1, 0),
        ('Canon iR-ADV 4545i Mới 100%', 'canon-ir-adv-4545i-moi', 4, 3, 58000000, 52000000, 'Máy photocopy Canon imageRUNNER ADVANCE 4545i mới 100%.', '<h3>Máy Photocopy Canon iR-ADV 4545i</h3><p>Máy in đa chức năng Canon imageRUNNER ADVANCE.</p>', '{\"speed\":\"45 bản/phút\",\"paper\":\"A3-A5\",\"memory\":\"3.5GB RAM\",\"resolution\":\"1200x1200 dpi\"}', 'uploads/canon-4545i.jpg', 'new', 0, 1),
        ('Mực Ricoh MP 5055/6055', 'muc-ricoh-mp-5055-6055', 12, 1, 850000, 750000, 'Mực máy photocopy Ricoh dùng cho MP 5055, 6055, 4055.', '<h3>Mực Ricoh MP 5055/6055</h3><p>Mực chính hãng dùng cho dòng máy Ricoh MP 4055/5055/6055.</p>', '{\"weight\":\"900g\",\"yield\":\"37000 trang\",\"compatible\":\"MP 4055/5055/6055\"}', 'uploads/muc-ricoh.jpg', 'new', 0, 0),
        ('Konica Minolta Bizhub C368 Renew 99%', 'konica-bizhub-c368-renew', 3, 4, 45000000, 39000000, 'Máy photocopy màu Konica Minolta Bizhub C368.', '<h3>Konica Minolta Bizhub C368 Renew 99%</h3><p>Máy photocopy màu đa chức năng Konica Minolta.</p>', '{\"speed\":\"36 bản/phút\",\"paper\":\"A3-A5\",\"memory\":\"4GB RAM\",\"resolution\":\"1800x600 dpi\",\"color\":\"Có\"}', 'uploads/konica-c368.jpg', 'renew', 1, 1);

        INSERT OR IGNORE INTO banners (title, image, link, position, sort_order) VALUES
        ('Khuyến mãi mùa hè', 'uploads/banner1.jpg', '/pages/products.php', 'home_slider', 1),
        ('Cho thuê máy photocopy', 'uploads/banner2.jpg', '/pages/rental.php', 'home_slider', 2),
        ('Máy photocopy Ricoh', 'uploads/banner3.jpg', '/pages/products.php?category=may-photocopy-trang-den', 'home_slider', 3);

        INSERT OR IGNORE INTO promotions (title, slug, description, discount_type, discount_value, start_date, end_date, is_active) VALUES
        ('Giảm giá mùa hè 2026', 'giam-gia-mua-he-2026', 'Giảm giá lên đến 20% tất cả máy photocopy nhập khẩu', 'percent', 20, '2026-06-01', '2026-12-31', 1),
        ('Flash Sale cuối tháng', 'flash-sale-cuoi-thang', 'Giảm thêm 5 triệu cho đơn hàng trên 30 triệu', 'fixed', 5000000, '2026-08-01', '2026-12-31', 1);

        INSERT OR IGNORE INTO rental_packages (name, machine_type, speed, color_type, monthly_price, included_pages, extra_page_price, deposit, min_contract_months, features) VALUES
        ('Gói Cơ Bản', 'Ricoh MP 3055', '30 bản/phút', 'bw', 2500000, 5000, 200, 5000000, 12, 'Bảo trì miễn phí, Cung cấp mực, Hỗ trợ kỹ thuật 24/7'),
        ('Gói Tiêu Chuẩn', 'Ricoh MP 5055', '50 bản/phút', 'bw', 4000000, 10000, 150, 8000000, 12, 'Bảo trì miễn phí, Cung cấp mực, Hỗ trợ kỹ thuật 24/7, Thay máy backup'),
        ('Gói Cao Cấp', 'Ricoh MP 6055', '60 bản/phút', 'bw', 5500000, 20000, 120, 10000000, 6, 'Bảo trì miễn phí, Cung cấp mực, Hỗ trợ kỹ thuật 24/7, Thay máy backup, Giao hàng miễn phí'),
        ('Gói Màu Cơ Bản', 'Ricoh IM C3000', '30 bản/phút', 'color', 5000000, 3000, 500, 10000000, 12, 'Bảo trì miễn phí, Cung cấp mực màu, Hỗ trợ kỹ thuật 24/7'),
        ('Gói Màu Cao Cấp', 'Ricoh IM C6000', '60 bản/phút', 'color', 8000000, 5000, 400, 15000000, 6, 'Bảo trì miễn phí, Cung cấp mực màu, Hỗ trợ kỹ thuật 24/7, Thay máy backup');

        INSERT OR IGNORE INTO news (title, slug, excerpt, content, category) VALUES
        ('Hướng dẫn chọn mua máy photocopy phù hợp', 'huong-dan-chon-mua-may-photocopy', 'Bài viết hướng dẫn chi tiết cách chọn mua máy photocopy phù hợp nhu cầu sử dụng.', '<h2>Cách chọn mua máy photocopy phù hợp</h2><p>Khi chọn mua máy photocopy, bạn cần xem xét các yếu tố sau...</p>', 'guide'),
        ('Bảng giá cho thuê máy photocopy 2026', 'bang-gia-cho-thue-may-photocopy-2026', 'Cập nhật bảng giá cho thuê máy photocopy mới nhất năm 2026.', '<h2>Bảng giá cho thuê máy photocopy 2026</h2><p>PUC cung cấp dịch vụ cho thuê máy photocopy với giá tốt nhất...</p>', 'service'),
        ('So sánh máy photocopy Ricoh vs Toshiba', 'so-sanh-may-photocopy-ricoh-vs-toshiba', 'So sánh chi tiết giữa hai dòng máy photocopy phổ biến nhất.', '<h2>So sánh Ricoh vs Toshiba</h2><p>Ricoh và Toshiba là hai thương hiệu máy photocopy hàng đầu...</p>', 'news');

        INSERT OR IGNORE INTO settings (setting_key, setting_value, setting_group) VALUES
        ('site_name', 'PUC - Máy Photocopy', 'general'),
        ('site_description', 'PUC chuyên bán và cho thuê máy photocopy chính hãng, giá tốt nhất tại Hà Nội', 'general'),
        ('site_keywords', 'máy photocopy, bán máy photocopy, cho thuê máy photocopy, máy photocopy Hà Nội, PUC', 'general'),
        ('hotline', '0907586969', 'contact'),
        ('email', 'phuong86.annguyen@gmail.com', 'contact'),
        ('address', 'Số 21 ngõ 75 Cầu Đất - Cửa Nam - Hà Nội', 'contact'),
        ('facebook', 'https://facebook.com/puc.photocopy', 'social'),
        ('zalo', '0907586969', 'social'),
        ('google_maps', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.5!2d105.84!3d21.02!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjHCsDAxJzEyLjAiTiAxMDXCsDUwJzI0LjAiRQ!5e0!3m2!1svi!2s!4v1', 'contact'),
        ('working_hours', 'Thứ 2 - Thứ 7: 8:00 - 18:00', 'contact'),
        ('copyright', '© 2026 PUC. All rights reserved.', 'general');
    ");
}
