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

    // =========================================================================
    // 2.1 ADVANCED FULL-FEATURED IMAGE LIGHTBOX POPUP (BẢO TÍN JSC)
    // =========================================================================
    const lightboxModal = document.getElementById('product_image_lightbox');
    const lbMainImg = document.getElementById('baotinLightboxMainImg');
    const lbCounter = document.getElementById('baotinLightboxCounter');
    const lbCloseBtn = document.getElementById('baotinLightboxClose');
    const lbBackdrop = document.getElementById('baotinLightboxBackdrop');
    const lbPrevBtn = document.getElementById('baotinLbPrev');
    const lbNextBtn = document.getElementById('baotinLbNext');
    const lbZoomInBtn = document.getElementById('baotinLbZoomIn');
    const lbZoomOutBtn = document.getElementById('baotinLbZoomOut');
    const lbRotateBtn = document.getElementById('baotinLbRotate');
    const lbResetBtn = document.getElementById('baotinLbReset');
    const lbDownloadBtn = document.getElementById('baotinLbDownload');
    const lbThumbItems = document.querySelectorAll('.baotin-lb-thumb-item');
    const zoomBtn = document.querySelector('.zoom-btn');

    if (lightboxModal && lbMainImg) {
        let lbImages = [];
        let lbCurrentIndex = 0;
        let lbScale = 1;
        let lbRotation = 0;
        let lbTranslateX = 0;
        let lbTranslateY = 0;
        let lbIsDragging = false;
        let lbStartX = 0;
        let lbStartY = 0;

        // Swipe touch variables
        let touchStartX = 0;
        let touchStartY = 0;
        let touchEndX = 0;
        let touchEndY = 0;
        let isTouchMoving = false;

        // 1. Khởi tạo danh sách ảnh chuẩn
        function collectLightboxImages() {
            lbImages = [];
            if (lbThumbItems && lbThumbItems.length > 0) {
                lbThumbItems.forEach((btn, idx) => {
                    const src = btn.getAttribute('data-src') || btn.querySelector('img')?.src;
                    if (src) {
                        lbImages.push({
                            src: src,
                            alt: btn.querySelector('img')?.alt || '',
                            index: idx
                        });
                    }
                });
            } else if (thumbImages && thumbImages.length > 0) {
                thumbImages.forEach((img, idx) => {
                    lbImages.push({
                        src: img.src,
                        alt: img.alt || '',
                        index: idx
                    });
                });
            } else if (mainImage) {
                lbImages.push({
                    src: mainImage.src,
                    alt: mainImage.alt || '',
                    index: 0
                });
            }
        }

        collectLightboxImages();

        // 2. Áp dụng biến đổi (Scale, Rotate, Translate)
        function applyImageTransform(animate = true) {
            if (!lbMainImg) return;
            lbMainImg.style.transition = animate ? 'transform 0.22s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.2s ease' : 'none';
            lbMainImg.style.transform = `translate(${lbTranslateX}px, ${lbTranslateY}px) scale(${lbScale}) rotate(${lbRotation}deg)`;
            lbMainImg.classList.toggle('is-zoomed', lbScale > 1);
        }

        // 3. Đặt lại trạng thái phóng to / xoay
        function resetImageTransform() {
            lbScale = 1;
            lbRotation = 0;
            lbTranslateX = 0;
            lbTranslateY = 0;
            applyImageTransform(true);
        }

        // 4. Cập nhật ảnh hiển thị
        function updateLightboxImage(index) {
            if (lbImages.length === 0) return;
            if (index < 0) index = lbImages.length - 1;
            if (index >= lbImages.length) index = 0;

            lbCurrentIndex = index;
            const targetData = lbImages[lbCurrentIndex];

            // Hiệu ứng fade nhẹ khi chuyển ảnh
            lbMainImg.style.opacity = '0.35';
            lbMainImg.style.transform = `translate(0px, 0px) scale(0.96) rotate(${lbRotation}deg)`;

            const temp = new Image();
            temp.onload = function () {
                lbMainImg.src = targetData.src;
                lbMainImg.alt = targetData.alt || '';
                resetImageTransform();
                lbMainImg.style.opacity = '1';

                // Cập nhật link tải xuống
                if (lbDownloadBtn) {
                    lbDownloadBtn.href = targetData.src;
                    const fileName = targetData.src.split('/').pop().split('?')[0] || 'bao-tin-jsc-product.jpg';
                    lbDownloadBtn.setAttribute('download', fileName);
                }
            };
            temp.onerror = function () {
                lbMainImg.src = targetData.src;
                resetImageTransform();
                lbMainImg.style.opacity = '1';
            };
            temp.src = targetData.src;

            // Cập nhật bộ đếm
            if (lbCounter) {
                lbCounter.textContent = `${lbCurrentIndex + 1} / ${lbImages.length}`;
            }

            // Cập nhật thumbnail active trong popup & cuộn mượt vào giữa
            if (lbThumbItems && lbThumbItems.length > 0) {
                lbThumbItems.forEach((btn, idx) => {
                    const isActive = (idx === lbCurrentIndex);
                    btn.classList.toggle('active', isActive);
                    if (isActive) {
                        btn.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
                    }
                });
            }

            // Đồng bộ thumbnail & ảnh chính ngoài trang
            if (thumbImages && thumbImages[lbCurrentIndex]) {
                thumbImages.forEach(t => t.classList.remove('active'));
                thumbImages[lbCurrentIndex].classList.add('active');
                if (mainImage) {
                    mainImage.src = thumbImages[lbCurrentIndex].src;
                }
            }
        }

        // 5. Mở Lightbox
        function openLightbox(startIndex = 0) {
            collectLightboxImages();
            if (lbImages.length === 0) return;

            lightboxModal.style.display = 'block';
            // Buộc reflow để kích hoạt CSS transition
            void lightboxModal.offsetHeight;
            lightboxModal.classList.add('is-open');
            document.body.style.overflow = 'hidden';

            updateLightboxImage(startIndex);
        }

        // 6. Đóng Lightbox
        function closeLightbox() {
            lightboxModal.classList.remove('is-open');
            setTimeout(() => {
                if (!lightboxModal.classList.contains('is-open')) {
                    lightboxModal.style.display = 'none';
                    document.body.style.overflow = '';
                    resetImageTransform();
                }
            }, 280);
        }

        // Mở popup khi click nút Phóng to
        if (zoomBtn) {
            zoomBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                // Tìm vị trí ảnh đang active
                let activeIdx = 0;
                if (thumbImages && thumbImages.length > 0) {
                    thumbImages.forEach((t, i) => {
                        if (t.classList.contains('active')) activeIdx = i;
                    });
                }
                openLightbox(activeIdx);
            });
        }

        // Mở popup khi click trực tiếp vào ảnh chính ngoài trang
        if (mainImage) {
            mainImage.addEventListener('click', function (e) {
                e.preventDefault();
                let activeIdx = 0;
                if (thumbImages && thumbImages.length > 0) {
                    thumbImages.forEach((t, i) => {
                        if (t.classList.contains('active')) activeIdx = i;
                    });
                }
                openLightbox(activeIdx);
            });
        }

        // Đóng popup
        if (lbCloseBtn) lbCloseBtn.addEventListener('click', closeLightbox);
        if (lbBackdrop) lbBackdrop.addEventListener('click', closeLightbox);

        // Nút chuyển ảnh Trước / Sau
        if (lbPrevBtn) {
            lbPrevBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                updateLightboxImage(lbCurrentIndex - 1);
            });
        }

        if (lbNextBtn) {
            lbNextBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                updateLightboxImage(lbCurrentIndex + 1);
            });
        }

        // Click vào các thumbnail trong popup
        if (lbThumbItems && lbThumbItems.length > 0) {
            lbThumbItems.forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const idx = parseInt(this.getAttribute('data-index'), 10) || 0;
                    updateLightboxImage(idx);
                });
            });
        }

        // Công cụ: Phóng to
        if (lbZoomInBtn) {
            lbZoomInBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                if (lbScale < 3.5) {
                    lbScale = Math.min(3.5, lbScale + 0.5);
                    applyImageTransform(true);
                }
            });
        }

        // Công cụ: Thu nhỏ
        if (lbZoomOutBtn) {
            lbZoomOutBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                if (lbScale > 1) {
                    lbScale = Math.max(1, lbScale - 0.5);
                    if (lbScale === 1) {
                        lbTranslateX = 0;
                        lbTranslateY = 0;
                    }
                    applyImageTransform(true);
                }
            });
        }

        // Công cụ: Xoay ảnh 90 độ
        if (lbRotateBtn) {
            lbRotateBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                lbRotation = (lbRotation + 90) % 360;
                applyImageTransform(true);
            });
        }

        // Công cụ: Đặt lại ban đầu
        if (lbResetBtn) {
            lbResetBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                resetImageTransform();
            });
        }

        // Click hoặc double click vào ảnh để zoom nhanh
        lbMainImg.addEventListener('dblclick', function (e) {
            e.stopPropagation();
            if (lbScale === 1) {
                lbScale = 2;
            } else {
                lbScale = 1;
                lbTranslateX = 0;
                lbTranslateY = 0;
            }
            applyImageTransform(true);
        });

        // Kéo rê chuột khi phóng to (Pan/Drag)
        const viewport = document.getElementById('baotinLbViewport');
        if (viewport) {
            viewport.addEventListener('mousedown', function (e) {
                if (lbScale > 1 && e.button === 0) {
                    lbIsDragging = true;
                    lbStartX = e.clientX - lbTranslateX;
                    lbStartY = e.clientY - lbTranslateY;
                    lbMainImg.classList.add('is-dragging');
                    e.preventDefault();
                }
            });

            window.addEventListener('mousemove', function (e) {
                if (lbIsDragging && lbScale > 1) {
                    lbTranslateX = e.clientX - lbStartX;
                    lbTranslateY = e.clientY - lbStartY;
                    applyImageTransform(false);
                }
            });

            window.addEventListener('mouseup', function () {
                if (lbIsDragging) {
                    lbIsDragging = false;
                    lbMainImg.classList.remove('is-dragging');
                }
            });

            // Touch Drag / Swipe trên điện thoại
            viewport.addEventListener('touchstart', function (e) {
                if (e.touches.length === 1) {
                    touchStartX = e.touches[0].clientX;
                    touchStartY = e.touches[0].clientY;
                    touchEndX = touchStartX;
                    touchEndY = touchStartY;
                    isTouchMoving = true;

                    if (lbScale > 1) {
                        lbIsDragging = true;
                        lbStartX = touchStartX - lbTranslateX;
                        lbStartY = touchStartY - lbTranslateY;
                        lbMainImg.classList.add('is-dragging');
                    }
                }
            }, { passive: true });

            viewport.addEventListener('touchmove', function (e) {
                if (!isTouchMoving || e.touches.length !== 1) return;

                touchEndX = e.touches[0].clientX;
                touchEndY = e.touches[0].clientY;

                if (lbScale > 1 && lbIsDragging) {
                    lbTranslateX = touchEndX - lbStartX;
                    lbTranslateY = touchEndY - lbStartY;
                    applyImageTransform(false);
                    e.preventDefault();
                }
            }, { passive: false });

            viewport.addEventListener('touchend', function () {
                if (!isTouchMoving) return;
                isTouchMoving = false;

                if (lbScale > 1 && lbIsDragging) {
                    lbIsDragging = false;
                    lbMainImg.classList.remove('is-dragging');
                    return;
                }

                // Nếu ảnh đang ở tỉ lệ 1:1, kiểm tra cử chỉ vuốt chuyển ảnh (Swipe)
                if (lbScale === 1) {
                    const diffX = touchEndX - touchStartX;
                    const diffY = touchEndY - touchStartY;

                    // Chỉ kích hoạt swipe nếu vuốt ngang nhiều hơn vuốt dọc và cự ly > 45px
                    if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 45) {
                        if (diffX < 0) {
                            // Vuốt sang trái -> Ảnh tiếp theo
                            updateLightboxImage(lbCurrentIndex + 1);
                        } else {
                            // Vuốt sang phải -> Ảnh trước
                            updateLightboxImage(lbCurrentIndex - 1);
                        }
                    }
                }
            }, { passive: true });
        }

        // Hỗ trợ phím tắt bàn phím
        document.addEventListener('keydown', function (e) {
            if (!lightboxModal.classList.contains('is-open')) return;

            switch (e.key) {
                case 'Escape':
                    closeLightbox();
                    break;
                case 'ArrowLeft':
                    updateLightboxImage(lbCurrentIndex - 1);
                    break;
                case 'ArrowRight':
                    updateLightboxImage(lbCurrentIndex + 1);
                    break;
                case '+':
                case '=':
                    if (lbScale < 3.5) {
                        lbScale = Math.min(3.5, lbScale + 0.5);
                        applyImageTransform(true);
                    }
                    break;
                case '-':
                    if (lbScale > 1) {
                        lbScale = Math.max(1, lbScale - 0.5);
                        if (lbScale === 1) {
                            lbTranslateX = 0;
                            lbTranslateY = 0;
                        }
                        applyImageTransform(true);
                    }
                    break;
                case '0':
                    resetImageTransform();
                    break;
                case 'r':
                case 'R':
                    lbRotation = (lbRotation + 90) % 360;
                    applyImageTransform(true);
                    break;
            }
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
