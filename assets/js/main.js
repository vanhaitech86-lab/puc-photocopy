/**
 * Main JavaScript for PUC Photocopy Website
 * Clean, fast, responsive vanilla JS
 */

document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Sticky Header
    const header = document.querySelector('.main-header');
    if (header) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 120) {
                header.classList.add('shadow');
            } else {
                header.classList.remove('shadow');
            }
        });
    }

    // 2. Ajax Live Search with Suggestions
    const searchInput = document.getElementById('searchInput');
    const searchSuggestions = document.getElementById('searchSuggestions');
    
    if (searchInput && searchSuggestions) {
        let debounceTimer;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(debounceTimer);
            const query = e.target.value.trim();
            
            if (query.length < 2) {
                searchSuggestions.innerHTML = '';
                searchSuggestions.classList.add('d-none');
                return;
            }

            debounceTimer = setTimeout(() => {
                const apiBase = window.SITE_URL || '';
                fetch(`${apiBase}/api.php?action=search&q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.results && data.results.length > 0) {
                            let html = '<div class="list-group list-group-flush">';
                            data.results.forEach(item => {
                                html += `
                                    <a href="${item.url}" class="list-group-item list-group-item-action d-flex align-items-center py-2 text-decoration-none">
                                        <img src="${item.image}" alt="${item.name}" class="rounded me-3" style="width: 45px; height: 45px; object-fit: contain;">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 text-dark small fw-bold">${item.name}</h6>
                                            <small class="text-danger fw-bold">${item.price}</small>
                                            ${item.brand ? `<span class="badge bg-light text-secondary ms-2">${item.brand}</span>` : ''}
                                        </div>
                                    </a>
                                `;
                            });
                            html += `
                                <a href="${apiBase}/pages/search.php?q=${encodeURIComponent(query)}" class="list-group-item list-group-item-action text-center text-primary py-2 small fw-bold bg-light">
                                    Xem tất cả kết quả cho "${query}" <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            `;
                            html += '</div>';
                            searchSuggestions.innerHTML = html;
                            searchSuggestions.classList.remove('d-none');
                        } else {
                            searchSuggestions.innerHTML = '<div class="p-3 text-muted small text-center">Không tìm thấy sản phẩm nào.</div>';
                            searchSuggestions.classList.remove('d-none');
                        }
                    })
                    .catch(() => {
                        searchSuggestions.classList.add('d-none');
                    });
            }, 300);
        });

        // Close suggestions when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-form') && !e.target.closest('#searchSuggestions')) {
                searchSuggestions.classList.add('d-none');
            }
        });
    }

    // 3. Back to Top Button
    const backToTop = document.getElementById('backToTop');
    if (backToTop) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 350) {
                backToTop.style.display = 'flex';
            } else {
                backToTop.style.display = 'none';
            }
        });

        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // 4. Initialize Swiper Slider (Trang chủ)
    if (typeof Swiper !== 'undefined') {
        const swiperContainer = document.querySelector('.heroSwiper');
        if (swiperContainer) {
            new Swiper('.heroSwiper', {
                loop: true,
                speed: 800,
                autoplay: {
                    delay: 4500,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
        }
    }

    // 5. Toast Notification Helper
    window.showToast = (message, type = 'success') => {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container position-fixed bottom-0 start-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `alert alert-${type} shadow-lg d-flex align-items-center mb-2`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-info-circle'} fs-5 me-2"></i>
            <div>${message}</div>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('fade');
            setTimeout(() => toast.remove(), 400);
        }, 3500);
    };
});
