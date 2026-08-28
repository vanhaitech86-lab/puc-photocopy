<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/seo.php';

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

$cat_names = [
    'service' => 'Dịch Vụ',
    'guide' => 'Hướng Dẫn & Tài Liệu',
    'driver' => 'Driver Máy Photocopy',
    'news' => 'Tin Tức Chung'
];

$page_title = ($category && isset($cat_names[$category])) ? ($cat_names[$category] . ' - PUC') : 'Tin Tức & Kiến Thức Máy Photocopy - PUC';
$page_description = 'Cập nhật tin tức, cẩm nang sử dụng máy photocopy, link tải driver máy Ricoh, Toshiba, Canon và mẹo tiết kiệm chi phí in ấn.';
$page_keywords = 'tin tức máy photocopy, hướng dẫn sử dụng máy photocopy, driver máy photocopy, PUC';
$canonical_url = SITE_URL . '/pages/news.php' . ($category ? '?category=' . urlencode($category) : '');

require_once __DIR__ . '/../includes/header.php';

$limit = NEWS_PER_PAGE;
$offset = ($page - 1) * $limit;

$where = ["is_active = 1"];
$params = [];
if ($category) {
    $where[] = "category = ?";
    $params[] = $category;
}
$where_sql = implode(' AND ', $where);

$total_stmt = $pdo->prepare("SELECT COUNT(*) FROM news WHERE $where_sql");
$total_stmt->execute($params);
$total_articles = (int)$total_stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT * FROM news WHERE $where_sql ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
$stmt->execute($params);
$news_list = $stmt->fetchAll();
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= SITE_URL ?>">Trang Chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tin Tức</li>
        </ol>
    </nav>

    <!-- Header Section -->
    <div class="text-center mb-5">
        <h1 class="h2 fw-bold text-primary mb-2">TIN TỨC & KIẾN THỨC PHOTOCOPY</h1>
        <p class="text-muted">Chia sẻ cẩm nang, tài liệu kỹ thuật, hướng dẫn cài đặt và tin tức ngành in ấn</p>
        
        <!-- Category Tabs -->
        <div class="d-flex justify-content-center gap-2 flex-wrap mt-3">
            <a href="<?= SITE_URL ?>/pages/news.php" class="btn btn-sm <?= empty($category) ? 'btn-primary' : 'btn-outline-primary' ?>">Tất Cả</a>
            <a href="<?= SITE_URL ?>/pages/news.php?category=service" class="btn btn-sm <?= $category === 'service' ? 'btn-primary' : 'btn-outline-primary' ?>">Dịch Vụ</a>
            <a href="<?= SITE_URL ?>/pages/news.php?category=guide" class="btn btn-sm <?= $category === 'guide' ? 'btn-primary' : 'btn-outline-primary' ?>">Hướng Dẫn & Tài Liệu</a>
            <a href="<?= SITE_URL ?>/pages/news.php?category=driver" class="btn btn-sm <?= $category === 'driver' ? 'btn-primary' : 'btn-outline-primary' ?>">Driver</a>
            <a href="<?= SITE_URL ?>/pages/news.php?category=news" class="btn btn-sm <?= $category === 'news' ? 'btn-primary' : 'btn-outline-primary' ?>">Tin Tức</a>
        </div>
    </div>

    <!-- News Grid -->
    <?php if (!empty($news_list)): ?>
    <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
        <?php foreach ($news_list as $news): ?>
        <div class="col">
            <div class="card h-100 border-0 shadow-sm news-card overflow-hidden">
                <a href="<?= SITE_URL ?>/pages/news-detail.php?slug=<?= $news['slug'] ?>">
                    <img src="<?= get_image_url($news['image'], 'assets/images/news-default.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($news['title']) ?>" style="height: 220px; object-fit: cover;">
                </a>
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2 small text-muted">
                        <span><i class="far fa-calendar-alt me-1"></i><?= date('d/m/Y', strtotime($news['created_at'])) ?></span>
                        <span><i class="far fa-eye me-1"></i><?= number_format($news['view_count']) ?> xem</span>
                    </div>
                    <h5 class="card-title mb-2">
                        <a href="<?= SITE_URL ?>/pages/news-detail.php?slug=<?= $news['slug'] ?>" class="text-decoration-none text-dark fw-bold">
                            <?= htmlspecialchars($news['title']) ?>
                        </a>
                    </h5>
                    <p class="card-text text-secondary mb-3 flex-grow-1">
                        <?= truncate_text(strip_tags($news['excerpt'] ?: $news['content']), 120) ?>
                    </p>
                    <a href="<?= SITE_URL ?>/pages/news-detail.php?slug=<?= $news['slug'] ?>" class="btn btn-link p-0 text-primary fw-bold text-decoration-none mt-auto">
                        Đọc tiếp <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Phân trang -->
    <?php 
    $pagination_base = SITE_URL . '/pages/news.php?' . http_build_query(array_filter(['category' => $category]));
    echo generate_pagination($total_articles, NEWS_PER_PAGE, $page, $pagination_base);
    ?>

    <?php else: ?>
    <div class="alert alert-info text-center py-5 bg-white shadow-sm border">
        <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
        <h5>Chưa có bài viết trong chuyên mục này</h5>
        <a href="<?= SITE_URL ?>/pages/news.php" class="btn btn-primary mt-2">Xem tất cả tin tức</a>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
