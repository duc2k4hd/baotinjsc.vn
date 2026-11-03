(() => {
    // 1. Countdown Timer (To midnight)
    function updateCountdown() {
        const now = new Date();
        const endOfDay = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59, 999);
        const diff = Math.max(0, Math.floor((endOfDay - now) / 1000));

        const hours = Math.floor(diff / 3600);
        const minutes = Math.floor((diff % 3600) / 60);
        const seconds = diff % 60;

        const hoursEl = document.querySelector('[data-countdown="hours"]');
        const minutesEl = document.querySelector('[data-countdown="minutes"]');
        const secondsEl = document.querySelector('[data-countdown="seconds"]');

        if (hoursEl) hoursEl.textContent = String(hours).padStart(2, "0");
        if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, "0");
        if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, "0");

        if (diff <= 0) {
            setTimeout(() => location.reload(), 1000);
        }
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);

    // 2. Toast Notification Helper
    function showHomeToast(message) {
        const toast = document.querySelector(".misutech_home_toast");
        if (toast) {
            toast.textContent = message;
            toast.setAttribute("aria-hidden", "false");
            toast.style.display = "block";
            setTimeout(() => {
                toast.setAttribute("aria-hidden", "true");
                toast.style.display = "none";
            }, 2000);
        } else if (typeof window.showToast === 'function') {
            window.showToast(message);
        }
    }

    // 3. Hero Slider
    const slides = document.querySelectorAll(".misutech_home_hero_slide");
    const dots = document.querySelectorAll(".misutech_home_hero_dot");
    let currentSlide = 0;
    let slideInterval;

    function showSlide(index) {
        if (slides.length === 0) return;

        slides.forEach((slide, i) => {
            if (i === index) {
                slide.style.opacity = '1';
                slide.style.transform = 'scale(1)';
                slide.style.pointerEvents = 'auto';
                slide.style.zIndex = '1';
                if (dots[i]) dots[i].setAttribute("aria-current", "true");
            } else {
                slide.style.opacity = '0';
                slide.style.transform = 'scale(1.05)';
                slide.style.pointerEvents = 'none';
                slide.style.zIndex = '0';
                if (dots[i]) dots[i].setAttribute("aria-current", "false");
            }
        });
        currentSlide = index;
    }

    function nextSlide() {
        if (slides.length <= 1) return;
        showSlide((currentSlide + 1) % slides.length);
    }

    function startSlider() {
        if (slides.length > 1) {
            slideInterval = setInterval(nextSlide, 5000);
        }
    }

    function resetSlider() {
        clearInterval(slideInterval);
        startSlider();
    }

    dots.forEach((dot, index) => {
        dot.addEventListener("click", () => {
            showSlide(index);
            resetSlider();
        });
    });

    startSlider();

    // 4. Featured Products Tabs Filter / Sort
    const featuredTabs = document.querySelectorAll("#misutech_home_catalog_0 .misutech_home_section_tab");
    const featuredGrid = document.querySelector("#misutech_home_catalog_0 .misutech_home_catalog_grid");

    if (featuredTabs.length && featuredGrid) {
        const originalCards = Array.from(featuredGrid.children);

        featuredTabs.forEach((tab) => {
            tab.addEventListener("click", () => {
                featuredTabs.forEach((item) => item.setAttribute("aria-selected", "false"));
                tab.setAttribute("aria-selected", "true");

                const tabType = tab.dataset.tab;
                let sortedCards = [...originalCards];

                if (tabType === 'best-price') {
                    sortedCards.sort((a, b) => {
                        const priceA = parseFloat(a.querySelector('.misutech_home_product_price')?.textContent.replace(/[^0-9]/g, '') || '0');
                        const priceB = parseFloat(b.querySelector('.misutech_home_product_price')?.textContent.replace(/[^0-9]/g, '') || '0');
                        return (priceA || Infinity) - (priceB || Infinity);
                    });
                } else if (tabType === 'newest') {
                    sortedCards.reverse();
                }

                // Smooth reorder
                featuredGrid.innerHTML = '';
                sortedCards.forEach(card => featuredGrid.appendChild(card));
                showHomeToast("Đang hiển thị: " + tab.textContent.trim());
            });
        });
    }

    // 5. Add to Cart via AJAX with Visual Flying / Pulse Feedback
    let lastCartClickTime = 0;
    document.addEventListener("click", async (event) => {
        const cartBtn = event.target.closest(".misutech_home_add_cart");
        if (!cartBtn) return;

        event.preventDefault();

        const now = Date.now();
        if (now - lastCartClickTime < 800) {
            return;
        }
        lastCartClickTime = now;

        const productId = cartBtn.dataset.productId;
        const productName = cartBtn.dataset.product || "Sản phẩm";
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        // Feedback on button
        const originalText = cartBtn.innerHTML;
        cartBtn.innerHTML = "✓";
        cartBtn.style.background = "#10B981";
        cartBtn.style.color = "#FFFFFF";

        try {
            const response = await fetch('/gio-hang', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1
                })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                showHomeToast("Đã thêm " + productName + " vào giỏ hàng!");

                // Update cart badges in header if present
                const cartCountBadges = document.querySelectorAll('.cart-count, #cart-count, .cart-counter, .header-cart-count');
                cartCountBadges.forEach(badge => {
                    if (data.cart_total_items) {
                        badge.textContent = data.cart_total_items;
                    }
                });
            } else {
                showHomeToast(data.message || "Không thể thêm vào giỏ hàng");
            }
        } catch (err) {
            console.error(err);
            showHomeToast("Đã thêm " + productName + " vào giỏ hàng!");
        } finally {
            setTimeout(() => {
                cartBtn.innerHTML = originalText;
                cartBtn.style.background = "";
                cartBtn.style.color = "";
            }, 1200);
        }
    });

    // 6. Favorite / Wishlist Toggle
    document.addEventListener("click", async (event) => {
        const favBtn = event.target.closest(".misutech_home_product_favorite");
        if (!favBtn) return;

        event.preventDefault();
        const productId = favBtn.dataset.productId;
        const isFavorited = favBtn.getAttribute("aria-pressed") === "true";
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        if (isFavorited) {
            favBtn.setAttribute("aria-pressed", "false");
            favBtn.textContent = "♡";
            favBtn.style.color = "";
            showHomeToast("Đã bỏ yêu thích");
        } else {
            favBtn.setAttribute("aria-pressed", "true");
            favBtn.textContent = "♥";
            favBtn.style.color = "#DC2626";
            showHomeToast("Đã thêm vào danh sách yêu thích");
        }

        if (productId && csrfToken) {
            try {
                await fetch('/san-pham/yeu-thich', {
                    method: isFavorited ? 'DELETE' : 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ product_id: productId })
                });
            } catch (e) {
                // Ignore API errors for silent UX
            }
        }
    });
})();
