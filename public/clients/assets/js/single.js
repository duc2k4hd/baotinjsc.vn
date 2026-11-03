/**
 * SINGLE PRODUCT PAGE SCRIPT - BẢO TÍN JSC
 * Xử lý: Gallery slider, Chọn biến thể, Giỏ hàng AJAX, Mua ngay, Yêu thích,
 * Tabs chi tiết, Rating picker, Copy link chia sẻ, Tự động wrap table & ẩn ảnh lỗi.
 */

// 1. Điều khiển số lượng đặt mua
function increaseQty() {
    const qtyInput = document.getElementById('quantity');
    const cartQtyInput = document.getElementById('cart-quantity');
    if (qtyInput) {
        let val = parseInt(qtyInput.value) || 1;
        qtyInput.value = val + 1;
        if (cartQtyInput) {
            cartQtyInput.value = qtyInput.value;
        }
    }
}

function decreaseQty() {
    const qtyInput = document.getElementById('quantity');
    const cartQtyInput = document.getElementById('cart-quantity');
    if (qtyInput) {
        let val = parseInt(qtyInput.value) || 1;
        if (val > 1) {
            qtyInput.value = val - 1;
            if (cartQtyInput) {
                cartQtyInput.value = qtyInput.value;
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Helper: Định dạng giá tiền VNĐ
    function formatPrice(number) {
        return new Intl.NumberFormat('vi-VN').format(Math.round(number));
    }

    // Helper: Toast thông báo an toàn
    function notify(message, type = 'success') {
        if (typeof showCustomToast === 'function') {
            showCustomToast(message, type);
        } else {
            console.log(`[${type.toUpperCase()}] ${message}`);
        }
    }

    // =========================================================================
    // 2. GALLERY ẢNH & THUMBNAILS CAROUSEL
    // =========================================================================
    const mainImage = document.getElementById('mainImage');
    const thumbnailsTrack = document.getElementById('thumbnailsTrack');
    const thumbImages = document.querySelectorAll('.thumb-img');
    const thumbPrev = document.getElementById('thumbPrev');
    const thumbNext = document.getElementById('thumbNext');

    if (thumbImages.length > 0 && mainImage) {
        thumbImages.forEach(thumb => {
            thumb.addEventListener('click', function () {
                thumbImages.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                mainImage.src = this.src;
            });
        });
    }

    if (thumbPrev && thumbnailsTrack) {
        thumbPrev.addEventListener('click', function () {
            thumbnailsTrack.scrollBy({ left: -160, behavior: 'smooth' });
        });
    }

    if (thumbNext && thumbnailsTrack) {
        thumbNext.addEventListener('click', function () {
            thumbnailsTrack.scrollBy({ left: 160, behavior: 'smooth' });
        });
    }

    // Phóng to ảnh chính (Zoom Modal)
    const zoomBtn = document.querySelector('.zoom-btn');
    if (zoomBtn && mainImage) {
        zoomBtn.addEventListener('click', function () {
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background-color: rgba(15, 23, 42, 0.92);
                z-index: 99999;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: zoom-out;
                padding: 20px;
                box-sizing: border-box;
            `;

            const img = document.createElement('img');
            img.src = mainImage.src;
            img.style.cssText = `
                max-width: 95%;
                max-height: 92%;
                object-fit: contain;
                border-radius: 8px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            `;

            modal.appendChild(img);
            document.body.appendChild(modal);

            modal.addEventListener('click', function () {
                if (document.body.contains(modal)) {
                    document.body.removeChild(modal);
                }
            });
        });
    }

    // =========================================================================
    // 3. SAO CHÉP LIÊN KẾT CHIA SẺ
    // =========================================================================
    const shareCopyBtns = [document.getElementById('shareCopyLinkBtn'), document.getElementById('shareCopyLinkBtn2')];
    shareCopyBtns.forEach(btn => {
        if (btn) {
            btn.addEventListener('click', function () {
                const url = window.location.href;
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(() => {
                        notify('Đã sao chép liên kết sản phẩm vào bộ nhớ tạm!', 'success');
                    }).catch(() => {
                        copyFallback(url);
                    });
                } else {
                    copyFallback(url);
                }
            });
        }
    });

    function copyFallback(text) {
        const dummy = document.createElement('input');
        document.body.appendChild(dummy);
        dummy.value = text;
        dummy.select();
        document.execCommand('copy');
        document.body.removeChild(dummy);
        notify('Đã sao chép liên kết sản phẩm vào bộ nhớ tạm!', 'success');
    }

    // =========================================================================
    // 4. CHỌN BIẾN THỂ SẢN PHẨM (VARIANTS)
    // =========================================================================
    const variantOptions = document.querySelectorAll('.variant-option');
    const variantIdInput = document.getElementById('product-variant-id');
    const displayPrice = document.getElementById('display-price');
    const displayOldPrice = document.getElementById('display-old-price');
    const displayBadge = document.getElementById('display-badge');
    const addToCartBtn = document.getElementById('btn-add-cart');
    const addToCartText = document.getElementById('btn-add-cart-text');
    const hasVariants = variantOptions.length > 0;

    function applyVariantState(variantEl) {
        if (!variantEl) return;

        const variantId = variantEl.getAttribute('data-variant-id');
        const price = parseFloat(variantEl.getAttribute('data-variant-price')) || 0;
        const salePrice = parseFloat(variantEl.getAttribute('data-variant-sale-price')) || 0;
        const image = variantEl.getAttribute('data-variant-image');
        const inStock = variantEl.getAttribute('data-variant-in-stock') === '1';

        if (variantIdInput) {
            variantIdInput.value = variantId;
        }

        if (displayPrice) {
            if (price <= 0 && salePrice <= 0) {
                displayPrice.textContent = 'Liên hệ';
                displayPrice.className = 'product-price-contact';
                if (displayOldPrice) displayOldPrice.style.display = 'none';
                if (displayBadge) displayBadge.style.display = 'none';
            } else if (salePrice > 0 && salePrice < price) {
                displayPrice.textContent = formatPrice(salePrice) + ' đ';
                displayPrice.className = 'product-price-sale';
                if (displayOldPrice) {
                    displayOldPrice.textContent = formatPrice(price) + ' đ';
                    displayOldPrice.style.display = 'inline-block';
                }
                if (displayBadge) {
                    const discount = Math.round((1 - salePrice / price) * 100);
                    displayBadge.textContent = '-' + discount + '%';
                    displayBadge.style.display = 'inline-block';
                }
            } else {
                displayPrice.textContent = formatPrice(price) + ' đ';
                displayPrice.className = 'product-price-sale';
                if (displayOldPrice) displayOldPrice.style.display = 'none';
                if (displayBadge) displayBadge.style.display = 'none';
            }
        }

        if (image && mainImage) {
            mainImage.src = image;
            thumbImages.forEach(thumb => {
                if (thumb.src === image) {
                    thumbImages.forEach(t => t.classList.remove('active'));
                    thumb.classList.add('active');
                }
            });
        }

        if (addToCartBtn) {
            if (!inStock) {
                addToCartBtn.disabled = true;
                if (addToCartText) addToCartText.textContent = 'Hết hàng';
            } else {
                addToCartBtn.disabled = false;
                if (addToCartText) addToCartText.textContent = 'Thêm vào giỏ hàng';
            }
        }
    }

    if (hasVariants) {
        const activeVariant = Array.from(variantOptions).find(v => v.classList.contains('active')) || variantOptions[0];
        if (activeVariant) {
            variantOptions.forEach(v => v.classList.remove('active'));
            activeVariant.classList.add('active');
            applyVariantState(activeVariant);
        }

        variantOptions.forEach(option => {
            option.addEventListener('click', function () {
                variantOptions.forEach(v => v.classList.remove('active'));
                this.classList.add('active');
                applyVariantState(this);
            });
        });
    }

    // =========================================================================
    // 5. ĐỒNG BỘ INPUT SỐ LƯỢNG
    // =========================================================================
    const qtyInput = document.getElementById('quantity');
    const cartQtyInput = document.getElementById('cart-quantity');
    if (qtyInput) {
        qtyInput.addEventListener('input', function () {
            let val = parseInt(this.value);
            if (isNaN(val) || val < 1) {
                this.value = 1;
            }
            if (cartQtyInput) {
                cartQtyInput.value = this.value;
            }
        });
    }

    // =========================================================================
    // 6. FORM THÊM VÀO GIỎ HÀNG (AJAX)
    // =========================================================================
    const addToCartForm = document.getElementById('addToCartForm');
    if (addToCartForm) {
        addToCartForm.addEventListener('submit', function (e) {
            e.preventDefault();

            if (hasVariants) {
                const selectedVariantId = variantIdInput ? variantIdInput.value : '';
                if (!selectedVariantId) {
                    notify('Vui lòng chọn phiên bản / quy cách sản phẩm', 'error');
                    return;
                }
            }

            const formData = new FormData(this);
            const currentQty = qtyInput ? qtyInput.value : 1;
            formData.set('quantity', currentQty);

            const submitBtn = document.getElementById('btn-add-cart');
            if (submitBtn) submitBtn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                }
            })
            .then(res => {
                if (res.redirected || res.status === 302) {
                    return { success: true, message: 'Đã thêm sản phẩm vào giỏ hàng!' };
                }
                return res.json().catch(() => ({ success: true, message: 'Đã thêm sản phẩm vào giỏ hàng!' }));
            })
            .then(data => {
                if (data.success !== false) {
                    notify(data.message || 'Đã thêm sản phẩm vào giỏ hàng thành công!', 'success');
                    if (typeof window.updateCartCount === 'function') {
                        window.updateCartCount();
                    }
                } else {
                    notify(data.message || 'Có lỗi xảy ra, vui lòng thử lại.', 'error');
                }
            })
            .catch(() => {
                notify('Đã thêm sản phẩm vào giỏ hàng!', 'success');
                if (typeof window.updateCartCount === 'function') {
                    window.updateCartCount();
                }
            })
            .finally(() => {
                if (submitBtn) submitBtn.disabled = false;
            });
        });
    }

    // =========================================================================
    // 7. NÚT MUA NGAY (BUY NOW)
    // =========================================================================
    const btnBuyNow = document.getElementById('btnBuyNow');
    if (btnBuyNow && addToCartForm) {
        btnBuyNow.addEventListener('click', function () {
            if (hasVariants) {
                const selectedVariantId = variantIdInput ? variantIdInput.value : '';
                if (!selectedVariantId) {
                    notify('Vui lòng chọn phiên bản / quy cách sản phẩm', 'error');
                    return;
                }
            }

            const formData = new FormData(addToCartForm);
            const currentQty = qtyInput ? qtyInput.value : 1;
            formData.set('quantity', currentQty);

            btnBuyNow.disabled = true;
            btnBuyNow.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Đang xử lý đặt hàng...</span>';

            fetch(addToCartForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                }
            })
            .then(() => {
                window.location.href = '/gio-hang';
            })
            .catch(() => {
                window.location.href = '/gio-hang';
            });
        });
    }

    // =========================================================================
    // 8. NÚT YÊU THÍCH (WISHLIST)
    // =========================================================================
    const favoriteBtn = document.querySelector('.btn-favorite');
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', function () {
            const productId = this.getAttribute('data-product-id');
            const addUrl = this.getAttribute('data-wishlist-add');
            const removeUrl = this.getAttribute('data-wishlist-remove');
            const isActive = this.classList.contains('active');
            const url = isActive ? removeUrl : addUrl;
            const method = isActive ? 'DELETE' : 'POST';

            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');

            this.classList.toggle('active');
            notify(isActive ? 'Đã xóa khỏi danh sách yêu thích' : 'Đã thêm vào danh sách yêu thích!', 'success');

            fetch(url, {
                method: method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                }
            }).catch(e => console.error('Wishlist error:', e));
        });
    }

    // =========================================================================
    // 9. CHUYỂN TABS CHI TIẾT SẢN PHẨM
    // =========================================================================
    const tabButtons = document.querySelectorAll('.product-tabs-header .tab-btn');
    const tabPanels = document.querySelectorAll('.product-tabs-main .tab-content');

    tabButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const targetId = this.getAttribute('data-tab');

            tabButtons.forEach(b => {
                b.classList.remove('active');
                b.setAttribute('aria-selected', 'false');
            });
            tabPanels.forEach(p => p.classList.remove('active'));

            this.classList.add('active');
            this.setAttribute('aria-selected', 'true');

            const targetPanel = document.getElementById(targetId);
            if (targetPanel) {
                targetPanel.classList.add('active');
            }
        });
    });

    // =========================================================================
    // 10. CHỌN SAO ĐÁNH GIÁ (INTERACTIVE STAR RATING PICKER)
    // =========================================================================
    const starPicker = document.getElementById('starPicker');
    const ratingInput = document.getElementById('review-rating');
    const starRatingLabel = document.getElementById('starRatingLabel');

    if (starPicker && ratingInput) {
        const starBtns = starPicker.querySelectorAll('.star-btn');
        const starLabels = {
            1: 'Rất tệ (1 sao)',
            2: 'Chưa hài lòng (2 sao)',
            3: 'Bình thường (3 sao)',
            4: 'Hài lòng (4 sao)',
            5: 'Rất tốt (5 sao)'
        };

        function setStars(score) {
            starBtns.forEach(btn => {
                const s = parseInt(btn.getAttribute('data-score'));
                if (s <= score) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
            if (starRatingLabel && starLabels[score]) {
                starRatingLabel.textContent = starLabels[score];
            }
        }

        starBtns.forEach(btn => {
            btn.addEventListener('mouseenter', function () {
                const hoverScore = parseInt(this.getAttribute('data-score'));
                setStars(hoverScore);
            });

            btn.addEventListener('click', function () {
                const clickedScore = parseInt(this.getAttribute('data-score'));
                ratingInput.value = clickedScore;
                setStars(clickedScore);
            });
        });

        starPicker.addEventListener('mouseleave', function () {
            const currentScore = parseInt(ratingInput.value) || 5;
            setStars(currentScore);
        });
    }

    // =========================================================================
    // 11. GỬI FORM ĐÁNH GIÁ (REVIEW FORM AJAX)
    // =========================================================================
    const reviewForm = document.getElementById('review-form');
    if (reviewForm) {
        reviewForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const submitBtn = reviewForm.querySelector('.btn-submit-review');
            const originalHtml = submitBtn ? submitBtn.innerHTML : 'Gửi đánh giá';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
            }

            const formData = new FormData(reviewForm);

            try {
                const response = await fetch('/api/comments', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json().catch(() => ({}));

                if (response.ok) {
                    notify('Cảm ơn bạn đã gửi đánh giá! Nhận xét sẽ được kiểm duyệt và hiển thị sớm nhất.', 'success');
                    reviewForm.reset();
                    if (ratingInput) ratingInput.value = 5;
                    if (starPicker) {
                        const starBtns = starPicker.querySelectorAll('.star-btn');
                        starBtns.forEach(b => b.classList.add('active'));
                    }
                    if (starRatingLabel) starRatingLabel.textContent = 'Rất tốt (5 sao)';
                } else {
                    const msg = data.message || data.error || 'Có lỗi xảy ra khi gửi nhận xét. Vui lòng thử lại.';
                    notify(msg, 'error');
                }
            } catch (err) {
                notify('Có lỗi kết nối máy chủ. Vui lòng thử lại sau!', 'error');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            }
        });
    }

    // =========================================================================
    // 12. TỰ ĐỘNG WRAP BẢNG & XỬ LÝ ẢNH TRONG .description-text
    // =========================================================================
    const descTables = document.querySelectorAll('.description-text table');
    descTables.forEach(table => {
        if (!table.parentElement.classList.contains('product-table-wrapper')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'product-table-wrapper';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
    });

    // Ẩn ảnh bị lỗi nếu có để không để lại khoảng trống trắng khổng lồ
    const descImages = document.querySelectorAll('.description-text img');
    descImages.forEach(img => {
        img.addEventListener('error', function () {
            this.style.display = 'none';
            const fig = this.closest('figure');
            if (fig) fig.style.display = 'none';
        });
    });
});
