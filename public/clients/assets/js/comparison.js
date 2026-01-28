// Comparison Page JavaScript
(function() {
    'use strict';

    // Remove product from comparison
    window.removeProduct = function(productId) {
        if (!confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi danh sách so sánh?')) {
            return;
        }

        fetch(`/so-sanh/${productId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update comparison count in header if function exists
                if (typeof updateComparisonCount === 'function') {
                    updateComparisonCount(data.count || 0);
                }
                location.reload();
            } else {
                alert(data.message || 'Có lỗi xảy ra');
            }
        })
        .catch(() => {
            alert('Có lỗi xảy ra khi xóa sản phẩm');
        });
    };

    // Clear all products
    window.clearComparison = function() {
        if (!confirm('Bạn có chắc chắn muốn xóa tất cả sản phẩm khỏi danh sách so sánh?')) {
            return;
        }

        fetch('/so-sanh/clear', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update comparison count in header if function exists
                if (typeof updateComparisonCount === 'function') {
                    updateComparisonCount(0);
                }
                location.reload();
            } else {
                alert(data.message || 'Có lỗi xảy ra');
            }
        })
        .catch(() => {
            alert('Có lỗi xảy ra khi xóa danh sách so sánh');
        });
    };

    // Add to cart
    window.addToCart = function(productId) {
        fetch('/gio-hang', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update cart count if exists
                const cartBadge = document.querySelector('.cart-icon .badge');
                if (cartBadge) {
                    cartBadge.textContent = data.cart_count || 0;
                }
                alert('Đã thêm sản phẩm vào giỏ hàng');
            } else {
                alert(data.message || 'Có lỗi xảy ra');
            }
        })
        .catch(() => {
            alert('Có lỗi xảy ra khi thêm vào giỏ hàng');
        });
    };

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
})();
