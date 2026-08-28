<?php
// includes/footer.php
require_once __DIR__ . '/../config.php';
$site_url = defined('SITE_URL') ? SITE_URL : '';
?>
<!-- Footer -->
<footer class="bg-dark text-light pt-5 mt-5">
    <div class="container pb-4">
        <div class="row gy-4">
            <!-- About -->
            <div class="col-lg-4 col-md-6">
                <a href="<?= $site_url ?>/" class="d-inline-flex align-items-center text-decoration-none mb-3 bg-white px-2 py-1 rounded">
                    <img src="<?= $site_url ?>/assets/images/logo.png" alt="PUC Logo" style="height: 40px;">
                    <span class="ms-2 fs-4 fw-bold text-primary">PUC</span>
                </a>
                <p class="text-secondary mb-4">PUC là đơn vị chuyên cung cấp, cho thuê và bảo trì máy photocopy, máy in chính hãng uy tín hàng đầu tại Hà Nội. Cam kết chất lượng cao, giá thành tốt nhất và hỗ trợ kỹ thuật tận tâm 24/7.</p>
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                        <i class="fas fa-headset text-white fs-5"></i>
                    </div>
                    <div>
                        <small class="d-block text-secondary">Hotline Tư Vấn 24/7</small>
                        <a href="tel:<?= HOTLINE ?>" class="text-warning text-decoration-none fs-4 fw-bold"><?= HOTLINE ?></a>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h5 class="text-white fw-bold mb-4 position-relative pb-2 border-bottom border-secondary">LIÊN KẾT NHANH</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?= $site_url ?>/" class="text-secondary text-decoration-none hover-white"><i class="fas fa-angle-right me-2"></i>Trang chủ</a></li>
                    <li class="mb-2"><a href="<?= $site_url ?>/pages/products.php" class="text-secondary text-decoration-none hover-white"><i class="fas fa-angle-right me-2"></i>Máy photocopy</a></li>
                    <li class="mb-2"><a href="<?= $site_url ?>/pages/rental.php" class="text-secondary text-decoration-none hover-white"><i class="fas fa-angle-right me-2"></i>Dịch vụ cho thuê</a></li>
                    <li class="mb-2"><a href="<?= $site_url ?>/pages/supplies.php" class="text-secondary text-decoration-none hover-white"><i class="fas fa-angle-right me-2"></i>Vật tư & Linh kiện</a></li>
                    <li class="mb-2"><a href="<?= $site_url ?>/pages/news.php" class="text-secondary text-decoration-none hover-white"><i class="fas fa-angle-right me-2"></i>Tin tức & Cẩm nang</a></li>
                    <li class="mb-2"><a href="<?= $site_url ?>/pages/contact.php" class="text-secondary text-decoration-none hover-white"><i class="fas fa-angle-right me-2"></i>Liên hệ</a></li>
                </ul>
            </div>

            <!-- Categories -->
            <div class="col-lg-3 col-md-6">
                <h5 class="text-white fw-bold mb-4 position-relative pb-2 border-bottom border-secondary">DÒNG MÁY PHỔ BIẾN</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?= $site_url ?>/pages/products.php?category=may-photocopy-trang-den" class="text-secondary text-decoration-none hover-white"><i class="fas fa-angle-right me-2"></i>Máy Photocopy Trắng Đen</a></li>
                    <li class="mb-2"><a href="<?= $site_url ?>/pages/products.php?category=may-photocopy-mau" class="text-secondary text-decoration-none hover-white"><i class="fas fa-angle-right me-2"></i>Máy Photocopy Màu</a></li>
                    <li class="mb-2"><a href="<?= $site_url ?>/pages/products.php?condition=new" class="text-secondary text-decoration-none hover-white"><i class="fas fa-angle-right me-2"></i>Máy Photocopy Mới 100%</a></li>
                    <li class="mb-2"><a href="<?= $site_url ?>/pages/products.php?category=may-kho-lon-a0" class="text-secondary text-decoration-none hover-white"><i class="fas fa-angle-right me-2"></i>Máy In / Photo Khổ A0</a></li>
                    <li class="mb-2"><a href="<?= $site_url ?>/pages/supplies.php?category=muc-may-photocopy" class="text-secondary text-decoration-none hover-white"><i class="fas fa-angle-right me-2"></i>Mực Máy Photocopy</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="col-lg-3 col-md-6">
                <h5 class="text-white fw-bold mb-4 position-relative pb-2 border-bottom border-secondary">THÔNG TIN LIÊN HỆ</h5>
                <ul class="list-unstyled text-secondary">
                    <li class="d-flex mb-3">
                        <i class="fas fa-map-marker-alt mt-1 me-3 text-primary"></i>
                        <span><?= ADDRESS ?></span>
                    </li>
                    <li class="d-flex mb-3">
                        <i class="fas fa-phone-alt mt-1 me-3 text-danger"></i>
                        <a href="tel:<?= HOTLINE ?>" class="text-white text-decoration-none fw-bold"><?= HOTLINE ?></a>
                    </li>
                    <li class="d-flex mb-3">
                        <i class="fas fa-envelope mt-1 me-3 text-info"></i>
                        <a href="mailto:<?= EMAIL ?>" class="text-secondary text-decoration-none"><?= EMAIL ?></a>
                    </li>
                    <li class="d-flex mb-3">
                        <i class="far fa-clock mt-1 me-3 text-warning"></i>
                        <span>Thứ 2 - Thứ 7: 8h00 - 18h00</span>
                    </li>
                </ul>
                <div class="social-links mt-3">
                    <a href="https://facebook.com" target="_blank" class="btn btn-outline-secondary btn-sm rounded-circle me-2"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://zalo.me/<?= HOTLINE ?>" target="_blank" class="btn btn-outline-secondary btn-sm rounded-circle me-2"><i class="fas fa-comment-dots"></i></a>
                    <a href="https://youtube.com" target="_blank" class="btn btn-outline-secondary btn-sm rounded-circle"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Copyright -->
    <div class="copyright bg-black py-3">
        <div class="container text-center">
            <p class="mb-0 text-secondary small">&copy; <?= date('Y') ?> <span class="text-white fw-bold">PUC</span>. Tất cả các quyền được bảo lưu. Thiết kế & phát triển chuẩn SEO.</p>
        </div>
    </div>
</footer>

<!-- Floating Action Buttons -->
<div class="floating-buttons position-fixed bottom-0 end-0 p-3" style="z-index: 1000;">
    <a href="https://zalo.me/<?= HOTLINE ?>" target="_blank" class="btn btn-primary rounded-circle shadow d-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;" title="Chat Zalo">
        <b class="text-white" style="font-size: 13px;">Zalo</b>
    </a>
    <a href="tel:<?= HOTLINE ?>" class="btn btn-danger rounded-circle shadow d-flex align-items-center justify-content-center mb-2 pulse-btn" style="width: 50px; height: 50px;" title="Gọi Hotline: <?= HOTLINE ?>">
        <i class="fas fa-phone-alt text-white fs-5"></i>
    </a>
    <button id="backToTop" class="btn btn-secondary rounded-circle shadow d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; display: none;" title="Lên đầu trang">
        <i class="fas fa-chevron-up text-white"></i>
    </button>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="<?= $site_url ?>/assets/js/main.js"></script>
</body>
</html>
