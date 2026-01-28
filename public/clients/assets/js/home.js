// JavaScript for Home Page - Tích hợp từ Themes/Home/script.js

document.addEventListener('DOMContentLoaded', function() {
    
    // Smooth scroll for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // Quick view functionality - Redirect to product page
    const quickViewButtons = document.querySelectorAll('.quick-view');
    quickViewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const productCard = this.closest('.product-card');
            if (productCard) {
                const productLink = productCard.querySelector('h4 a, a[href*="/"]');
                if (productLink && productLink.href) {
                    window.location.href = productLink.href;
                }
            }
        });
    });

    // Product card click - Navigate to product detail
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Không navigate nếu click vào quick-view button
            if (e.target.closest('.quick-view')) {
                return;
            }
            const productLink = this.querySelector('h4 a, a[href*="/"]');
            if (productLink && productLink.href) {
                window.location.href = productLink.href;
            }
        });
    });

    // News card click - Navigate to news detail
    const newsCards = document.querySelectorAll('.news-card');
    newsCards.forEach(card => {
        card.addEventListener('click', function(e) {
            const newsLink = this.querySelector('h4 a, a[href*="/"]');
            if (newsLink && newsLink.href) {
                window.location.href = newsLink.href;
            }
        });
    });

    // Newsletter form - Tích hợp với Laravel
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value;
            
            if (email && validateEmail(email)) {
                // Gửi request đến Laravel API
                fetch('/api/newsletter/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({ email: email })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (typeof showCustomToast === 'function') {
                            showCustomToast('Cảm ơn bạn đã đăng ký nhận tin! Email: ' + email, 'success');
                        }
                        emailInput.value = '';
                    } else {
                        if (typeof showCustomToast === 'function') {
                            showCustomToast(data.message || 'Có lỗi xảy ra, vui lòng thử lại.', 'error');
                        }
                    }
                })
                .catch(() => {
                    if (typeof showCustomToast === 'function') {
                        showCustomToast('Có lỗi xảy ra, vui lòng thử lại.', 'error');
                    }
                });
            } else {
                if (typeof showCustomToast === 'function') {
                    showCustomToast('Vui lòng nhập email hợp lệ.', 'warning');
                }
            }
        });
    }

    // Category filter buttons
    const filterButtons = document.querySelectorAll('.category-filter button');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            const filterText = this.textContent;
            // Có thể thêm logic filter sản phẩm ở đây nếu cần
        });
    });

    // Category dropdown functionality
    document.querySelectorAll('.home_category-item.has-children').forEach(item => {
        const row = item.querySelector('.home_category-row');
        const toggle = item.querySelector('.home_category-toggle');
        const link = item.querySelector('.home_category-link');
        
        // Click vào toggle icon → mở/đóng
        if (toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                item.classList.toggle('active');
            });
        }
        
        // Click vào row (box) → mở/đóng (trừ khi click vào link)
        if (row) {
            row.addEventListener('click', function(e) {
                // Nếu click vào link → navigate, không toggle
                if (link && (e.target === link || link.contains(e.target))) {
                    return;
                }
                // Nếu click vào toggle → đã xử lý ở trên
                if (toggle && (e.target === toggle || toggle.contains(e.target))) {
                    return;
                }
                // Click vào phần còn lại → toggle
                e.preventDefault();
                item.classList.toggle('active');
            });
        }
    });

    // Motor type cards - Navigate to category
    const motorItems = document.querySelectorAll('.motor-item');
    motorItems.forEach(item => {
        item.addEventListener('click', function() {
            const link = this.querySelector('a');
            if (link && link.href) {
                window.location.href = link.href;
            }
        });
    });

    // Banner items - Already handled by links in HTML
    const bannerItems = document.querySelectorAll('.banner-item');
    bannerItems.forEach(item => {
        item.addEventListener('click', function() {
            const link = this.querySelector('a');
            if (link && link.href) {
                window.location.href = link.href;
            }
        });
    });

    // Add animation on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe product cards and news cards
    const animatedElements = document.querySelectorAll('.product-card, .news-card, .motor-item');
    animatedElements.forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
        element.style.transition = 'opacity 0.5s, transform 0.5s';
        observer.observe(element);
    });

    // Brand logos - auto horizontal scroll + drag + click to filter
    const brandsContainer = document.querySelector('.brand-logos');
    if (brandsContainer) {
        // Click để filter theo brand
        brandsContainer.querySelectorAll('.brand-logo-item img').forEach(logo => {
            logo.addEventListener('click', function() {
                const brandName = this.alt;
                if (!brandName || !window.shopIndexRoute) return;
                window.location.href = window.shopIndexRoute + '?brand=' + encodeURIComponent(brandName);
            });
        });
    }

    // Variant Modal Functions
    const variantModal = document.getElementById('variantModal');
    let currentProductData = null;
    let selectedVariantId = null;

    function openVariantModal(productCard) {
        const hasVariants = productCard.getAttribute('data-has-variants') === '1';
        let variantsData = productCard.getAttribute('data-variants');
        const productId = productCard.getAttribute('data-product-id');
        const productName = productCard.getAttribute('data-product-name');
        const productImage = productCard.querySelector('img')?.src || '';

        if (!hasVariants || !variantsData || variantsData === '[]') {
            // No variants, add directly to cart
            addToCartDirect(productId, null);
            return;
        }

        try {
            // Decode HTML entities if any
            if (variantsData.includes('&quot;') || variantsData.includes('&amp;')) {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = variantsData;
                variantsData = tempDiv.textContent || tempDiv.innerText || variantsData;
            }
            
            const variants = JSON.parse(variantsData);
            
            if (!Array.isArray(variants) || variants.length === 0) {
                // No valid variants, add directly to cart
                addToCartDirect(productId, null);
                return;
            }
            currentProductData = {
                productId: productId,
                productName: productName,
                productImage: productImage,
                variants: variants
            };

            // Populate modal
            document.getElementById('variantModalProductImage').src = productImage;
            document.getElementById('variantModalProductName').textContent = productName;

            const variantsList = document.getElementById('variantModalVariantsList');
            variantsList.innerHTML = '';

            variants.forEach((variant, index) => {
                const variantOption = document.createElement('button');
                variantOption.type = 'button';
                variantOption.className = `variant-option-modal ${!variant.in_stock ? 'out-of-stock' : ''}`;
                variantOption.setAttribute('data-variant-id', variant.id);
                variantOption.setAttribute('data-variant-in-stock', variant.in_stock ? '1' : '0');

                let priceHtml = '';
                if (variant.price) {
                    if (variant.sale_price && variant.sale_price < variant.price) {
                        priceHtml = `
                            <span class="variant-price">
                                <span class="sale-price">${formatNumber(variant.sale_price)} đ</span>
                                <span class="old-price">${formatNumber(variant.price)} đ</span>
                            </span>
                        `;
                    } else {
                        priceHtml = `<span class="variant-price">${formatNumber(variant.price)} đ</span>`;
                    }
                } else {
                    priceHtml = '<span class="variant-price">Liên hệ</span>';
                }

                variantOption.innerHTML = `
                    <span class="variant-name">${variant.name}</span>
                    ${priceHtml}
                `;

                if (variant.in_stock) {
                    variantOption.addEventListener('click', function() {
                        // Remove active from all
                        variantsList.querySelectorAll('.variant-option-modal').forEach(opt => {
                            opt.classList.remove('active');
                        });
                        // Add active to clicked
                        this.classList.add('active');
                        selectedVariantId = variant.id;
                        
                        // Enable add button
                        const addBtn = document.querySelector('.variant-modal-btn-add');
                        addBtn.disabled = false;
                    });
                }

                variantsList.appendChild(variantOption);
            });

            // Reset selected variant
            selectedVariantId = null;
            document.querySelector('.variant-modal-btn-add').disabled = true;

            // Show modal
            variantModal.classList.add('active');
        } catch (error) {
            // Fallback: add directly to cart
            addToCartDirect(productId, null);
        }
    }

    function closeVariantModal() {
        variantModal.classList.remove('active');
        currentProductData = null;
        selectedVariantId = null;
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('vi-VN').format(num);
    }

    function setButtonLoading(btn, isLoading, options = {}) {
        if (!btn) return;
        const loadingText = options.loadingText || 'Đang thêm...';
        const loadingIcon = options.loadingIcon || '<i class="fas fa-spinner fa-spin"></i>';

        if (isLoading) {
            if (!btn.dataset.originalHtml) {
                btn.dataset.originalHtml = btn.innerHTML;
            }
            btn.disabled = true;
            btn.classList.add('is-loading');
            // Với nút icon-only (vd: .btn-add-cart ở home) thì chỉ hiển thị spinner để không làm vỡ layout.
            if (btn.classList.contains('btn-add-cart') || btn.classList.contains('btn-compare-home') || btn.classList.contains('btn-favorite-home')) {
                btn.innerHTML = loadingIcon;
            } else {
                btn.innerHTML = `${loadingIcon} <span class="btn-text">${loadingText}</span>`;
            }
            return;
        }

        btn.disabled = false;
        btn.classList.remove('is-loading');
        if (btn.dataset.originalHtml) {
            btn.innerHTML = btn.dataset.originalHtml;
            delete btn.dataset.originalHtml;
        }
    }

    function addToCartDirect(productId, variantId, options = {}) {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', 1);
        if (variantId) {
            formData.append('product_variant_id', variantId);
        }
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');

        const triggerBtn = options.button || null;
        setButtonLoading(triggerBtn, true, { loadingText: 'Đang thêm...' });

        return fetch(window.cartStoreRoute || '/gio-hang', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.redirected || response.status === 302) {
                return { success: true, message: 'Đã thêm sản phẩm vào giỏ hàng!' };
            }
            return response.json().catch(() => ({ success: true, message: 'Đã thêm sản phẩm vào giỏ hàng!' }));
        })
        .then(data => {
            if (data.success !== false) {
                if (typeof showCustomToast === 'function') {
                    showCustomToast(data.message || 'Đã thêm sản phẩm vào giỏ hàng!', 'success');
                }
                if (window.updateCartCount) {
                    window.updateCartCount();
                }
            } else {
                if (typeof showCustomToast === 'function') {
                    showCustomToast(data.message || 'Có lỗi xảy ra, vui lòng thử lại.', 'error');
                }
            }
        })
        .catch(() => {
            if (typeof showCustomToast === 'function') {
                showCustomToast('Đã thêm sản phẩm vào giỏ hàng!', 'success');
            }
            if (window.updateCartCount) {
                window.updateCartCount();
            }
        })
        .finally(() => {
            setButtonLoading(triggerBtn, false);
        });
    }

    // Modal close handlers
    document.querySelector('.variant-modal-close')?.addEventListener('click', closeVariantModal);
    document.querySelector('.variant-modal-btn-cancel')?.addEventListener('click', closeVariantModal);
    document.querySelector('.variant-modal-overlay')?.addEventListener('click', closeVariantModal);

    // Add to cart from modal
    document.querySelector('.variant-modal-btn-add')?.addEventListener('click', function() {
        if (!currentProductData || !selectedVariantId) {
            if (typeof showCustomToast === 'function') {
                showCustomToast('Vui lòng chọn biến thể sản phẩm', 'error');
            }
            return;
        }

        const btn = this;
        setButtonLoading(btn, true, { loadingText: 'Đang thêm...' });

        // Không đóng modal ngay; đợi request xong để user thấy trạng thái đang xử lý.
        addToCartDirect(currentProductData.productId, selectedVariantId)
            .then(() => {
                closeVariantModal();
            })
            .catch(() => {
                // Giữ modal mở nếu có lỗi bất ngờ (tránh cảm giác “bấm là tắt”)
            })
            .finally(() => {
                setButtonLoading(btn, false);
            });
    });

    // Product Card Actions - Add to Cart & Favorite
    // Add to Cart button
    document.querySelectorAll('.btn-add-cart').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productCard = this.closest('.product-card');
            if (!productCard) {
                if (typeof showCustomToast === 'function') {
                    showCustomToast('Không tìm thấy sản phẩm', 'error');
                }
                return;
            }

            // Check if product has variants
            const hasVariants = productCard.getAttribute('data-has-variants') === '1';
            
            if (hasVariants) {
                // Open variant selection modal
                openVariantModal(productCard);
            } else {
                // Add directly to cart
                const productId = productCard.getAttribute('data-product-id');
                addToCartDirect(productId, null, { button: this });
            }
        });
    });
    
    // Favorite button
    document.querySelectorAll('.btn-favorite-home').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = this.getAttribute('data-product-id');
            const addUrl = this.getAttribute('data-wishlist-add');
            const removeUrl = this.getAttribute('data-wishlist-remove');
            const isActive = this.classList.contains('active');
            const url = isActive ? removeUrl : addUrl;
            const method = isActive ? 'DELETE' : 'POST';
            
            if (!productId || !url) {
                if (typeof showCustomToast === 'function') {
                    showCustomToast('Có lỗi xảy ra, vui lòng thử lại.', 'error');
                }
                return;
            }
            
            // Disable button during request
            this.disabled = true;
            const originalHTML = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');
            
            fetch(url, {
                method: method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.redirected || response.status === 302) {
                    return { success: true };
                }
                return response.json().catch(() => ({ success: true }));
            })
            .then(data => {
                this.disabled = false;
                this.innerHTML = originalHTML;
                
                if (data.success !== false) {
                    this.classList.toggle('active');
                    if (typeof showCustomToast === 'function') {
                        const message = isActive 
                            ? 'Đã xóa khỏi danh sách yêu thích'
                            : 'Đã thêm vào danh sách yêu thích';
                        showCustomToast(message, 'success');
                    }
                } else {
                    if (typeof showCustomToast === 'function') {
                        showCustomToast(data.message || 'Có lỗi xảy ra, vui lòng thử lại.', 'error');
                    }
                }
            })
            .catch(() => {
                this.disabled = false;
                this.innerHTML = originalHTML;
                // Optimistic update
                this.classList.toggle('active');
                if (typeof showCustomToast === 'function') {
                    const message = isActive 
                        ? 'Đã xóa khỏi danh sách yêu thích'
                        : 'Đã thêm vào danh sách yêu thích';
                    showCustomToast(message, 'success');
                }
            });
        });
    });

    // Compare button
    document.querySelectorAll('.btn-compare-home').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = this.getAttribute('data-product-id');
            
            if (!productId) {
                if (typeof showCustomToast === 'function') {
                    showCustomToast('Có lỗi xảy ra, vui lòng thử lại.', 'error');
                }
                return;
            }
            
            // Disable button during request
            this.disabled = true;
            const originalHTML = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            fetch(`/so-sanh/${productId}/add`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                this.disabled = false;
                this.innerHTML = originalHTML;
                
                if (data.success) {
                    this.classList.add('active');
                    // Update comparison count in header
                    updateComparisonCount(data.count || 0);
                    if (typeof showCustomToast === 'function') {
                        showCustomToast(data.message || 'Đã thêm vào danh sách so sánh', 'success');
                    }
                } else {
                    if (typeof showCustomToast === 'function') {
                        showCustomToast(data.message || 'Có lỗi xảy ra, vui lòng thử lại.', 'error');
                    }
                }
            })
            .catch(() => {
                this.disabled = false;
                this.innerHTML = originalHTML;
                if (typeof showCustomToast === 'function') {
                    showCustomToast('Có lỗi xảy ra, vui lòng thử lại.', 'error');
                }
            });
        });
    });
    
    // Prevent product card link from navigating when clicking on action buttons
    document.querySelectorAll('.product-card').forEach(card => {
        const actions = card.querySelector('.product-card-actions');
        if (actions) {
            actions.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    });

});

// Utility function to format price
function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price);
}

// Utility function to validate email
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Utility function for smooth page transitions
function navigateTo(url) {
    document.body.style.opacity = '0';
    setTimeout(() => {
        window.location.href = url;
    }, 300);
}

// Update comparison count in header (shared function)
window.updateComparisonCount = function(count) {
    const comparisonBadge = document.querySelector('.comparison-item .badge');
    const comparisonItem = document.querySelector('.comparison-item');
    const comparisonLink = comparisonItem?.querySelector('.info-item-link');
    
    if (comparisonBadge) {
        if (count > 0) {
            comparisonBadge.textContent = count;
            comparisonBadge.style.display = 'flex';
        } else {
            comparisonBadge.style.display = 'none';
        }
    } else if (count > 0 && comparisonLink) {
        // Create badge if it doesn't exist
        const badge = document.createElement('span');
        badge.className = 'badge';
        badge.textContent = count;
        comparisonLink.appendChild(badge);
    }
};
