<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/seo.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header("Location: " . SITE_URL . "/pages/news.php");
    exit;
}

$news = get_news_by_slug($slug);
if (!$news) {
    header("HTTP/1.0 404 Not Found");
    $page_title = 'Không tìm thấy bài viết - PUC';
    require_once __DIR__ . '/../includes/header.php';
    echo '<div class="container py-5 text-center"><h2>Bài viết không tồn tại.</h2><a href="' . SITE_URL . '/pages/news.php" class="btn btn-primary mt-3">Về trang tin tức</a></div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Tăng lượt xem
increment_view('news', $news['id']);

$page_title = $news['title'] . ' - Tin Tức PUC';
$page_description = $news['meta_description'] ?: truncate_text(strip_tags($news['excerpt'] ?: $news['content']), 160);
$page_keywords = $news['meta_title'] ?: ($news['title'] . ', tin tức photocopy, PUC');
$canonical_url = SITE_URL . '/pages/news-detail.php?slug=' . $news['slug'];
$page_image = get_image_url($news['image']);

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= SITE_URL ?>">Trang Chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/pages/news.php">Tin Tức</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars(truncate_text($news['title'], 40)) ?></li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <article class="bg-white p-4 p-md-5 rounded shadow-sm border mb-5">
                <header class="mb-4 pb-3 border-bottom">
                    <h1 class="h2 fw-bold text-dark mb-3"><?= htmlspecialchars($news['title']) ?></h1>
                    <div class="d-flex align-items-center text-muted small gap-3">
                        <span><i class="far fa-calendar-alt me-1"></i>Đăng ngày: <?= date('d/m/Y H:i', strtotime($news['created_at'])) ?></span>
                        <span><i class="far fa-eye me-1"></i><?= number_format($news['view_count']) ?> lượt xem</span>
                    </div>
                </header>

                <?php if (!empty($news['image'])): ?>
                <div class="text-center mb-4">
                    <img src="<?= get_image_url($news['image']) ?>" class="img-fluid rounded shadow-sm" alt="<?= htmlspecialchars($news['title']) ?>" style="max-height: 450px; width: 100%; object-fit: cover;">
                </div>
                <?php endif; ?>

                <?php if (!empty($news['excerpt'])): ?>
                <div class="lead text-secondary fw-semibold mb-4 p-3 bg-light rounded border-start border-4 border-primary">
                    <?= nl2br(htmlspecialchars($news['excerpt'])) ?>
                </div>
                <?php endif; ?>

                <div class="article-content" style="line-height: 1.8; font-size: 1.05rem;">
                    <?= $news['content'] ?>
                </div>

                <!-- Call to action block -->
                <div class="bg-primary-subtle p-4 rounded mt-5 border border-primary-subtle d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h5 class="fw-bold text-primary mb-1">Cần tư vấn mua hoặc thuê máy photocopy?</h5>
                        <p class="mb-0 text-muted small">Liên hệ chuyên gia PUC để được khảo sát và báo giá tốt nhất.</p>
                    </div>
                    <a href="tel:<?= HOTLINE ?>" class="btn btn-danger fw-bold px-4 py-2">
                        <i class="fas fa-phone-alt me-2"></i><?= HOTLINE ?>
                    </a>
                </div>

                <!-- Share -->
                <div class="mt-4 pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span class="fw-bold text-muted"><i class="fas fa-share-alt me-2"></i>Chia sẻ bài viết:</span>
                    <div class="d-flex gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($canonical_url) ?>" target="_blank" class="btn btn-primary btn-sm">
                            <i class="fab fa-facebook-f me-1"></i>Facebook
                        </a>
                        <a href="https://zalo.me/share?v=3&app=1&url=<?= urlencode($canonical_url) ?>" target="_blank" class="btn btn-info btn-sm text-white">
                            <i class="fas fa-comment-dots me-1"></i>Zalo
                        </a>
                    </div>
                </div>
            </article>

            <!-- Bài viết liên quan -->
            <?php
            $stmt = $pdo->prepare("SELECT * FROM news WHERE id != ? AND is_active = 1 ORDER BY created_at DESC LIMIT 3");
            $stmt->execute([$news['id']]);
            $related = $stmt->fetchAll();
            if (!empty($related)):
            ?>
            <div class="related-news mb-5">
                <h4 class="fw-bold mb-4 text-primary"><i class="fas fa-newspaper me-2"></i>BÀI VIẾT LIÊN QUAN</h4>
                <div class="row row-cols-1 row-cols-md-3 g-3">
                    <?php foreach ($related as $rel): ?>
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm">
                            <a href="<?= SITE_URL ?>/pages/news-detail.php?slug=<?= $rel['slug'] ?>">
                                <img src="<?= get_image_url($rel['image'], 'assets/images/news-default.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($rel['title']) ?>" style="height: 140px; object-fit: cover;">
                            </a>
                            <div class="card-body p-3">
                                <small class="text-muted d-block mb-1"><i class="far fa-calendar-alt me-1"></i><?= date('d/m/Y', strtotime($rel['created_at'])) ?></small>
                                <h6 class="card-title mb-0">
                                    <a href="<?= SITE_URL ?>/pages/news-detail.php?slug=<?= $rel['slug'] ?>" class="text-decoration-none text-dark fw-semibold">
                                        <?= htmlspecialchars(truncate_text($rel['title'], 60)) ?>
                                    </a>
                                </h6>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
