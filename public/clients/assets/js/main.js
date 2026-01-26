// Search Box Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchIcon = document.getElementById('search-icon');
    const searchBox = document.getElementById('search-box');
    const closeSearchBox = document.getElementById('close-search-box');
    const searchInput = document.getElementById('search-input');
    const searchForm = document.getElementById('search-form');
    const searchSuggestions = document.getElementById('search-suggestions');
    
    let searchTimeout = null;
    
    // Open search box
    if (searchIcon && searchBox) {
        searchIcon.addEventListener('click', function() {
            searchBox.classList.add('show');
            if (searchInput) {
                setTimeout(() => searchInput.focus(), 100);
            }
        });
    }
    
    // Close search box
    if (closeSearchBox && searchBox) {
        closeSearchBox.addEventListener('click', function() {
            searchBox.classList.remove('show');
            if (searchInput) {
                searchInput.value = '';
            }
            if (searchSuggestions) {
                searchSuggestions.classList.remove('show');
                searchSuggestions.innerHTML = '';
            }
        });
    }
    
    // Close search box when clicking outside
    if (searchBox) {
        searchBox.addEventListener('click', function(e) {
            if (e.target === searchBox) {
                searchBox.classList.remove('show');
                if (searchInput) {
                    searchInput.value = '';
                }
                if (searchSuggestions) {
                    searchSuggestions.classList.remove('show');
                    searchSuggestions.innerHTML = '';
                }
            }
        });
    }
    
    // Close search box with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && searchBox && searchBox.classList.contains('show')) {
            searchBox.classList.remove('show');
            if (searchInput) {
                searchInput.value = '';
            }
            if (searchSuggestions) {
                searchSuggestions.classList.remove('show');
                searchSuggestions.innerHTML = '';
            }
        }
    });
    
    // Search suggestions (autocomplete)
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const keyword = this.value.trim();
            
            // Clear previous timeout
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }
            
            if (keyword.length < 2) {
                if (searchSuggestions) {
                    searchSuggestions.classList.remove('show');
                    searchSuggestions.innerHTML = '';
                }
                return;
            }
            
            // Debounce search
            searchTimeout = setTimeout(function() {
                // Use the search API endpoint
                fetch(`/api/shop/search?keyword=${encodeURIComponent(keyword)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Search failed');
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data || !Array.isArray(data)) {
                        if (searchSuggestions) {
                            searchSuggestions.classList.remove('show');
                            searchSuggestions.innerHTML = '';
                        }
                        return;
                    }
                    
                    if (searchSuggestions && data.length > 0) {
                        searchSuggestions.innerHTML = '';
                        data.forEach(product => {
                            const item = document.createElement('div');
                            item.className = 'search-suggestion-item';
                            item.innerHTML = `
                                <span class="suggestion-name">${product.name || ''}</span>
                                ${product.sale_price ? 
                                    `<span class="suggestion-price">${new Intl.NumberFormat('vi-VN').format(product.sale_price)} đ</span>` :
                                    product.price ? 
                                        `<span class="suggestion-price">${new Intl.NumberFormat('vi-VN').format(product.price)} đ</span>` :
                                        ''
                                }
                            `;
                            item.addEventListener('click', function() {
                                window.location.href = `/${product.slug || '#'}`;
                            });
                            searchSuggestions.appendChild(item);
                        });
                        searchSuggestions.classList.add('show');
                    } else {
                        if (searchSuggestions) {
                            searchSuggestions.classList.remove('show');
                            searchSuggestions.innerHTML = '';
                        }
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    if (searchSuggestions) {
                        searchSuggestions.classList.remove('show');
                        searchSuggestions.innerHTML = '';
                    }
                });
            }, 300);
        });
    }

    // Back to Top Button
    const backToTopBtn = document.getElementById('back-to-top');
    if (backToTopBtn) {
        // Show/hide button based on scroll position
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        });
        
        // Scroll to top when clicked
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Chat Button - Open Contact Box
    const chatButton = document.getElementById('chat-button');
    const contactInfoBox = document.getElementById('contact-info-box');
    const closeContactBox = document.getElementById('close-contact-box');
    
    if (chatButton && contactInfoBox) {
        chatButton.addEventListener('click', function() {
            contactInfoBox.classList.toggle('show');
        });
    }
    
    if (closeContactBox && contactInfoBox) {
        closeContactBox.addEventListener('click', function() {
            contactInfoBox.classList.remove('show');
        });
    }
    
    // Close contact box when clicking outside
    if (contactInfoBox && chatButton) {
        document.addEventListener('click', function(e) {
            if (contactInfoBox.classList.contains('show') && 
                !contactInfoBox.contains(e.target) && 
                !chatButton.contains(e.target)) {
                contactInfoBox.classList.remove('show');
            }
        });
    }
});

/**
 * Hiển thị toast notification
 * @param {string} message - Nội dung thông báo
 * @param {string} type - Loại thông báo: success, error, warning, info
 * @param {number} duration - Thời gian hiển thị (ms)
 */
function showCustomToast(
    message = "Thông báo!",
    type = "info",
    duration = 5000
) {
    const container = document.getElementById("custom-toast-container");
    if (!container) {
        console.warn('Toast container not found');
        return;
    }
    
    const toast = document.createElement("div");
    const icon = document.createElement("span");

    toast.className = `custom-toast ${type}`;
    icon.className = "custom-toast-icon";

    // Gán biểu tượng theo loại
    const icons = {
        success: "✅",
        error: "❌",
        warning: "⚠️",
        info: "💬",
    };
    icon.textContent = icons[type] || "🔔";

    toast.appendChild(icon);
    toast.appendChild(document.createTextNode(message));
    container.appendChild(toast);

    // Kích hoạt animation
    setTimeout(() => {
        if (toast && toast.classList) {
            toast.classList.add("show");
        }
    }, 100);

    toast.addEventListener("click", () => {
        if (toast && toast.classList) {
            toast.classList.remove("show");
        }
        setTimeout(() => {
            if (container && toast && container.contains(toast)) {
                container.removeChild(toast);
            }
        }, 300);
        return;
    });

    // Gỡ thông báo sau duration
    setTimeout(() => {
        if (toast && toast.classList) {
            toast.classList.remove("show");
        }
        setTimeout(() => {
            if (container && toast && container.contains(toast)) {
                container.removeChild(toast);
            }
        }, 300);
        return;
    }, duration);
}

/**
 * Khởi tạo và hiển thị session messages từ backend
 * @param {Object} messages - Object chứa các session messages
 * @param {string} messages.success - Success message
 * @param {string} messages.error - Error message
 * @param {string} messages.warning - Warning message
 * @param {string} messages.info - Info message
 * @param {Array} messages.errors - Array các validation errors
 */
function initSessionMessages(messages = {}) {
    if (!messages || typeof messages !== 'object') {
        return;
    }

    // Success message
    if (messages.success) {
        showCustomToast(messages.success, 'success', 5000);
    }

    // Error message
    if (messages.error) {
        showCustomToast(messages.error, 'error', 5000);
    }

    // Warning message
    if (messages.warning) {
        showCustomToast(messages.warning, 'warning', 5000);
    }

    // Info message
    if (messages.info) {
        showCustomToast(messages.info, 'info', 5000);
    }

    // Validation errors
    if (messages.errors && Array.isArray(messages.errors)) {
        messages.errors.forEach(error => {
            if (error) {
                showCustomToast(error, 'error', 5000);
            }
        });
    }
}

// Mega Menu Functionality
document.addEventListener('DOMContentLoaded', function() {
    const categoryItems = document.querySelectorAll('.category-item');
    const categoryContents = document.querySelectorAll('.mega-menu-category-content');
    const megaMenuDefault = document.querySelector('.mega-menu-default');
    const megaMenu = document.querySelector('.mega-menu');
    const hasMegaMenu = document.querySelector('.has-mega-menu');
    
    // Function to show category content
    function showCategoryContent(categoryId) {
        // Remove active class from all items
        categoryItems.forEach(ci => ci.classList.remove('active'));
        
        // Hide all category contents
        categoryContents.forEach(content => {
            content.style.display = 'none';
        });
        
        // Hide default content
        if (megaMenuDefault) {
            megaMenuDefault.style.display = 'none';
        }
        
        // Show target category content
        const targetContent = document.querySelector(`[data-category-content="${categoryId}"]`);
        if (targetContent) {
            targetContent.style.display = 'block';
        }
    }
    
    // Function to activate category item
    function activateCategoryItem(item) {
        categoryItems.forEach(ci => ci.classList.remove('active'));
        item.classList.add('active');
    }
    
    // When hover into "SẢN PHẨM" menu item, show first category by default
    if (hasMegaMenu && megaMenu) {
        hasMegaMenu.addEventListener('mouseenter', function() {
            // Get first category item
            const firstCategoryItem = categoryItems[0];
            if (firstCategoryItem) {
                const firstCategoryId = firstCategoryItem.getAttribute('data-category-id');
                activateCategoryItem(firstCategoryItem);
                showCategoryContent(firstCategoryId);
            }
        });
    }
    
    categoryItems.forEach(item => {
        const categoryId = item.getAttribute('data-category-id');
        
        // Mouse enter - show subcategories
        item.addEventListener('mouseenter', function() {
            activateCategoryItem(item);
            showCategoryContent(categoryId);
        });
    });
    
    // When mouse leaves mega menu, reset to first category
    if (megaMenu) {
        megaMenu.addEventListener('mouseleave', function() {
            // Reset to first category
            const firstCategoryItem = categoryItems[0];
            if (firstCategoryItem) {
                const firstCategoryId = firstCategoryItem.getAttribute('data-category-id');
                activateCategoryItem(firstCategoryItem);
                showCategoryContent(firstCategoryId);
            } else {
                // If no categories, show default
                categoryItems.forEach(ci => ci.classList.remove('active'));
                categoryContents.forEach(content => {
                    content.style.display = 'none';
                });
                if (megaMenuDefault) {
                    megaMenuDefault.style.display = 'block';
                }
            }
        });
    }
    
    // Newsletter form in footer
    const footerNewsletterForm = document.getElementById('footer-newsletter-form');
    const footerNewsletterMessage = document.getElementById('footer-newsletter-message');
    
    function showNewsletterMessage(message, type) {
        if (!footerNewsletterMessage) return;
        
        footerNewsletterMessage.textContent = message;
        footerNewsletterMessage.className = type === 'success' ? 'success' : 'error';
        footerNewsletterMessage.style.display = 'block';
        footerNewsletterMessage.style.padding = '10px';
        footerNewsletterMessage.style.borderRadius = '4px';
        footerNewsletterMessage.style.marginTop = '10px';
        
        if (type === 'success') {
            footerNewsletterMessage.style.backgroundColor = '#d4edda';
            footerNewsletterMessage.style.color = '#155724';
            footerNewsletterMessage.style.border = '1px solid #c3e6cb';
        } else {
            footerNewsletterMessage.style.backgroundColor = '#f8d7da';
            footerNewsletterMessage.style.color = '#721c24';
            footerNewsletterMessage.style.border = '1px solid #f5c6cb';
        }
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            footerNewsletterMessage.style.display = 'none';
        }, 5000);
    }
    
    if (footerNewsletterForm) {
        footerNewsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const emailInput = document.getElementById('footer-newsletter-email');
            const email = emailInput.value.trim();
            const submitBtn = footerNewsletterForm.querySelector('button[type="submit"]');
            
            if (!email) {
                showNewsletterMessage('Vui lòng nhập email', 'error');
                return;
            }
            
            // Validate email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showNewsletterMessage('Email không hợp lệ', 'error');
                return;
            }
            
            // Disable button
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Đang gửi...';
            }
            
            // Get CSRF token
            const csrfToken = document.querySelector('input[name="_token"]')?.value;
            
            // Submit form via fetch
            fetch(footerNewsletterForm.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json'
                },
                body: new URLSearchParams({
                    _token: csrfToken || '',
                    autosensor_main_newsletter_email: email
                })
            })
            .then(response => {
                if (response.redirected) {
                    // Laravel redirect - reload page to show flash message
                    window.location.reload();
                    return;
                }
                return response.json().catch(() => {
                    // If not JSON, assume success and reload
                    window.location.reload();
                });
            })
            .then(data => {
                if (data) {
                    if (data.message) {
                        showNewsletterMessage(data.message, data.success ? 'success' : 'error');
                        if (data.success) {
                            emailInput.value = '';
                        }
                    } else if (data.errors) {
                        const firstError = Object.values(data.errors)[0];
                        showNewsletterMessage(Array.isArray(firstError) ? firstError[0] : firstError, 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Newsletter error:', error);
                showNewsletterMessage('Đã có lỗi xảy ra. Vui lòng thử lại sau.', 'error');
            })
            .finally(() => {
                // Re-enable button
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Gửi đi';
                }
            });
        });
    }
});