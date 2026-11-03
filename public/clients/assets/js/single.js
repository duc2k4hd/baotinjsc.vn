// JavaScript for Single Product Page - Tích hợp từ Themes/Single/script.js

// Quantity Controls - Global functions for onclick handlers
function increaseQty() {
    const qtyInput = document.getElementById('quantity');
    const cartQtyInput = document.getElementById('cart-quantity');
    if (qtyInput) {
        let currentValue = parseInt(qtyInput.value) || 1;
        qtyInput.value = currentValue + 1;
        // Update cart quantity if exists
        if (cartQtyInput) {
            cartQtyInput.value = qtyInput.value;
        }
    }
}

function decreaseQty() {
    const qtyInput = document.getElementById('quantity');
    const cartQtyInput = document.getElementById('cart-quantity');
    if (qtyInput) {
        let currentValue = parseInt(qtyInput.value) || 1;
        if (currentValue > 1) {
            qtyInput.value = currentValue - 1;
            // Update cart quantity if exists
            if (cartQtyInput) {
                cartQtyInput.value = qtyInput.value;
            }
        }
    }
}

// Thumbnail Image Click
document.addEventListener('DOMContentLoaded', function() {
    const thumbnails = document.querySelectorAll('.thumb-img');
    const mainImage = document.getElementById('mainImage');
    
    if (thumbnails.length > 0 && mainImage) {
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function() {
                // Remove active class from all thumbnails
                thumbnails.forEach(t => t.classList.remove('active'));
                
                // Add active class to clicked thumbnail
                this.classList.add('active');
                
                // Change main image source
                mainImage.src = this.src;
            });
        });
    }

    // Variant Selection
    const variantOptions = document.querySelectorAll('.variant-option');
    let productPriceEl = document.getElementById('product-price');
    // Nếu không có #product-price, tìm .contact-price hoặc tạo mới
    if (!productPriceEl) {
        productPriceEl = document.querySelector('.contact-price');
        if (!productPriceEl) {
            // Tạo element mới nếu không có
            const priceContainer = document.querySelector('.product-info-details');
            if (priceContainer) {
                productPriceEl = document.createElement('div');
                productPriceEl.className = 'product-price';
                productPriceEl.id = 'product-price';
                // Tìm vị trí chèn (sau variant selector hoặc trước action buttons)
                const variantsSection = priceContainer.querySelector('.product-variants');
                if (variantsSection) {
                    variantsSection.insertAdjacentElement('afterend', productPriceEl);
        } else {
                    priceContainer.insertBefore(productPriceEl, priceContainer.querySelector('.action-buttons-row'));
                }
            }
        }
    }
    const variantIdInput = document.getElementById('product-variant-id');
    const addToCartBtn = document.getElementById('btn-add-cart');
    const addToCartText = document.getElementById('btn-add-cart-text');
    const hasVariants = variantOptions.length > 0;
    
    // Function to update add to cart button state
    function updateAddToCartButton(variantEl = null) {
        if (!addToCartBtn) return;
        
        let inStock = false;
        let variantId = '';
        
        if (hasVariants) {
            // Nếu có variants, phải chọn variant
            if (variantEl) {
                variantId = variantEl.getAttribute('data-variant-id') || '';
                const inStockValue = variantEl.getAttribute('data-variant-in-stock');
                inStock = inStockValue === '1';
        } else {
                // Chưa chọn variant
                inStock = false;
            }
                } else {
            // Không có variants, check product stock từ data attribute của button
            const stockValue = addToCartBtn.getAttribute('data-in-stock');
            inStock = stockValue === '1';
        }
        
        if (hasVariants && !variantId) {
            addToCartBtn.disabled = true;
            if (addToCartText) {
                addToCartText.textContent = 'Vui lòng chọn biến thể';
            }
        } else if (!inStock) {
            addToCartBtn.disabled = true;
            if (addToCartText) {
                addToCartText.textContent = 'Hết hàng';
                }
            } else {
            addToCartBtn.disabled = false;
            if (addToCartText) {
                addToCartText.textContent = 'Thêm vào giỏ hàng';
            }
        }
    }
    
    if (variantOptions.length > 0) {
        // Tìm variant đã có class active (từ server-side) hoặc variant đầu tiên
        let activeVariant = Array.from(variantOptions).find(v => v.classList.contains('active'));
        if (!activeVariant) {
            // Nếu không có variant nào active, lấy variant đầu tiên
            activeVariant = variantOptions[0];
            if (activeVariant) {
                // Đảm bảo variant đầu tiên có class active
                variantOptions.forEach(v => v.classList.remove('active'));
                activeVariant.classList.add('active');
            }
        }
        
        // Đảm bảo chỉ có 1 variant active
        variantOptions.forEach(v => {
            if (v !== activeVariant) {
                v.classList.remove('active');
            }
        });
        
        // Hàm để cập nhật variant active
        function updateActiveVariant(variant) {
            if (!variant) return;
            
            // Remove active từ tất cả variants
            variantOptions.forEach(v => v.classList.remove('active'));
            
            // Add active cho variant được chọn
            variant.classList.add('active');
            
            // Cập nhật variant ID, giá, ảnh, button
            if (variantIdInput) {
                variantIdInput.value = variant.getAttribute('data-variant-id') || '';
            }
            
            if (productPriceEl) {
                updatePriceFromVariant(variant, productPriceEl);
            }
            
            if (mainImage) {
                updateImageFromVariant(variant, mainImage, thumbnails);
            }
            
            updateAddToCartButton(variant);
        }
        
        // Cập nhật variant active ngay lập tức và sau khi DOM ready
        if (activeVariant) {
            updateActiveVariant(activeVariant);
        } else {
            updateAddToCartButton();
        }
        
        // Đảm bảo cập nhật lại sau khi tất cả đã load (fallback)
    setTimeout(() => {
            const currentActive = Array.from(variantOptions).find(v => v.classList.contains('active'));
            if (currentActive) {
                updateActiveVariant(currentActive);
            }
        }, 200);

        variantOptions.forEach(option => {
            option.addEventListener('click', function() {
                updateActiveVariant(this);
            });
        });
    } else {
        // Không có variants, check product stock
        updateAddToCartButton();
    }

    // Function to update price from variant
    function updatePriceFromVariant(variantEl, priceContainer) {
        if (!variantEl || !priceContainer) return;
        
        const price = variantEl.getAttribute('data-variant-price');
        const salePrice = variantEl.getAttribute('data-variant-sale-price');
        
        if (price !== null && price !== undefined) {
            const priceNum = parseFloat(price) || 0;
            const salePriceNum = salePrice ? parseFloat(salePrice) : null;
            
            // Kiểm tra giá hợp lệ (> 0)
            const hasValidPrice = priceNum > 0 || (salePriceNum && salePriceNum > 0 && salePriceNum < priceNum);
            
            if (!hasValidPrice) {
                // Giá = 0: Hiển thị "Liên hệ"
                priceContainer.innerHTML = `<strong style="color: var(--primary-color);">Liên hệ</strong>`;
            } else if (salePriceNum && salePriceNum < priceNum && salePriceNum > 0) {
                // Có giá sale
                priceContainer.innerHTML = `
                    <span class="old-price">${formatPrice(priceNum)} đ</span>
                    <span class="sale-price">${formatPrice(salePriceNum)} đ</span>
                `;
            } else {
                // Giá thường
                priceContainer.innerHTML = `<strong>${formatPrice(priceNum)} đ</strong>`;
            }
        }
    }

    // Function to update image from variant
    function updateImageFromVariant(variantEl, mainImg, thumbnails) {
        if (!variantEl || !mainImg) return;
        
        const variantImage = variantEl.getAttribute('data-variant-image');
        if (variantImage) {
            mainImg.src = variantImage;
            
            // Update active thumbnail if exists
            if (thumbnails && thumbnails.length > 0) {
                thumbnails.forEach(thumb => {
                    if (thumb.src === variantImage || thumb.getAttribute('data-src') === variantImage) {
                        thumbnails.forEach(t => t.classList.remove('active'));
                        thumb.classList.add('active');
                    }
                });
            }
        }
    }

    // Helper function to format price
    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN').format(Math.round(price));
    }

    // Zoom Button
    const zoomBtn = document.querySelector('.zoom-btn');
    if (zoomBtn && mainImage) {
        zoomBtn.addEventListener('click', function() {
            // Create modal for image zoom
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.9);
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: zoom-out;
            `;
            
            const img = document.createElement('img');
            img.src = mainImage.src;
            img.style.cssText = `
                max-width: 90%;
                max-height: 90%;
                object-fit: contain;
            `;
            
            modal.appendChild(img);
            document.body.appendChild(modal);
            
            // Close modal on click
            modal.addEventListener('click', function() {
                document.body.removeChild(modal);
            });
        });
    }
    
    // Update cart quantity when quantity input changes
    const quantityInput = document.getElementById('quantity');
    const cartQuantityInput = document.getElementById('cart-quantity');
    if (quantityInput && cartQuantityInput) {
        quantityInput.addEventListener('input', function() {
            cartQuantityInput.value = this.value;
        });
        quantityInput.addEventListener('change', function() {
            cartQuantityInput.value = this.value;
        });
    }

    // Add to cart form
    const addToCartForm = document.querySelector('.add-to-cart-form');
    if (addToCartForm) {
        addToCartForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate: Nếu có variants, phải chọn variant
            if (hasVariants) {
                const selectedVariantId = variantIdInput?.value || '';
                if (!selectedVariantId) {
                    if (typeof showCustomToast === 'function') {
                        showCustomToast('Vui lòng chọn biến thể sản phẩm', 'error');
                    }
                    return;
                }
                
                // Check stock của variant đã chọn
                const selectedVariant = Array.from(variantOptions).find(v => 
                    v.getAttribute('data-variant-id') === selectedVariantId
                );
                if (selectedVariant) {
                    const inStock = selectedVariant.getAttribute('data-variant-in-stock') === '1';
                    if (!inStock) {
                        if (typeof showCustomToast === 'function') {
                            showCustomToast('Biến thể này đã hết hàng', 'error');
                        }
                        return;
                    }
                }
            } else {
                // Check stock của product
                const inStockAttr = addToCartBtn ? addToCartBtn.getAttribute('data-in-stock') : '0';
                const stockStatus = document.querySelector('.info-value.in-stock') || (inStockAttr === '1');
                if (!stockStatus) {
                    if (typeof showCustomToast === 'function') {
                        showCustomToast('Sản phẩm đã hết hàng', 'error');
                    }
                    return;
                }
            }
            
            const formData = new FormData(this);
            const quantity = document.getElementById('quantity')?.value || 1;
            formData.set('quantity', quantity);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                // Check if response is redirect (Laravel redirect back)
                if (response.redirected || response.status === 302) {
                    return { success: true, message: 'Đã thêm sản phẩm vào giỏ hàng!' };
                }
                // Try to parse JSON, if fails, assume success
                return response.json().catch(() => ({ success: true, message: 'Đã thêm sản phẩm vào giỏ hàng!' }));
            })
            .then(data => {
                if (data.success !== false) {
                    if (typeof showCustomToast === 'function') {
                        showCustomToast(data.message || 'Đã thêm sản phẩm vào giỏ hàng!', 'success');
                    }
                    // Reload cart count if needed
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
                // Even on error, show success nếu backend redirect
                if (typeof showCustomToast === 'function') {
                    showCustomToast('Đã thêm sản phẩm vào giỏ hàng!', 'success');
                }
                if (window.updateCartCount) {
                    window.updateCartCount();
                }
            });
    });
}

    // Favorite button
    const favoriteBtn = document.querySelector('.btn-favorite');
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const addUrl = this.getAttribute('data-wishlist-add');
            const removeUrl = this.getAttribute('data-wishlist-remove');
            const isActive = this.classList.contains('active');
            const url = isActive ? removeUrl : addUrl;
            const method = isActive ? 'DELETE' : 'POST';
            
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
                // Laravel redirect back means success
                if (response.redirected || response.status === 302) {
                    return { success: true };
                }
                return response.json().catch(() => ({ success: true }));
            })
            .then(data => {
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
            .catch(error => {
                // Even on error, toggle UI and show message (optimistic update)
                this.classList.toggle('active');
                if (typeof showCustomToast === 'function') {
                    const message = isActive 
                        ? 'Đã xóa khỏi danh sách yêu thích'
                        : 'Đã thêm vào danh sách yêu thích';
                    showCustomToast(message, 'success');
                }
        });
    });
}


    // Quote Button
    const quoteBtn = document.querySelector('.quote-btn');
    if (quoteBtn) {
        quoteBtn.addEventListener('click', function() {
            const quantityInput = document.getElementById('quantity');
            const productNameEl = document.querySelector('.product-info h1');
            
            if (quantityInput && productNameEl) {
                const quantity = quantityInput.value;
                const productName = productNameEl.textContent;
                
                // Create quote request
                const message = `Yêu cầu báo giá:\nSản phẩm: ${productName}\nSố lượng: ${quantity}\n\nVui lòng liên hệ: ${window.phoneNumber || '0903 223 663'}`;
                
                // You can implement actual quote request logic here
                if (typeof showCustomToast === 'function') {
                    showCustomToast(message, 'info', 8000);
                }
            }
        });
    }
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
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

    // Product card click - Navigate to product detail
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        card.addEventListener('click', function(e) {
            const link = this.querySelector('a');
            if (link && link.href) {
                window.location.href = link.href;
            }
            });
        });

    // Quantity input validation (update existing quantityInput)
        if (quantityInput) {
        quantityInput.addEventListener('input', function(e) {
            let value = parseInt(e.target.value);
            if (isNaN(value) || value < 1) {
                e.target.value = 1;
            }
            // Update cart quantity if exists
            if (cartQuantityInput) {
                cartQuantityInput.value = e.target.value;
            }
        });
    }

    // Review Form Submission
    const reviewForm = document.getElementById('review-form');
    if (reviewForm) {
        reviewForm.addEventListener('submit', async function(e) {
                e.preventDefault();
            
            const submitBtn = reviewForm.querySelector('.btn-submit-review');
            const originalText = submitBtn.textContent;

                // Disable button
            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang gửi...';
            
            const formData = new FormData(reviewForm);
            
            try {
                const response = await fetch('/api/comments', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    if (typeof showCustomToast === 'function') {
                        showCustomToast('Cảm ơn bạn đã đánh giá! Đánh giá của bạn đang được xem xét.', 'success', 5000);
                    }
                    // Reset form
                    reviewForm.reset();
                    // Reload page after 2 seconds to show new review
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
        } else {
                    const errorMessage = data.message || data.error || 'Có lỗi xảy ra khi gửi đánh giá. Vui lòng thử lại.';
                    if (typeof showCustomToast === 'function') {
                        showCustomToast(errorMessage, 'error', 5000);
                    }
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            } catch (error) {
                if (typeof showCustomToast === 'function') {
                    showCustomToast('Có lỗi xảy ra khi gửi đánh giá. Vui lòng thử lại.', 'error', 5000);
                }
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }

    // Tab switching
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button and corresponding content
                this.classList.add('active');
            const targetContent = document.getElementById(targetTab);
            if (targetContent) {
                targetContent.classList.add('active');
            }
                    });
            });
    
});
