// JavaScript for SANDI Website

// Wait for DOM to load
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

    // Search functionality
    const searchIcon = document.querySelector('.search-icon');
    if (searchIcon) {
        searchIcon.addEventListener('click', function() {
            const searchTerm = prompt('Nhập từ khóa tìm kiếm:');
            if (searchTerm) {
                console.log('Tìm kiếm: ' + searchTerm);
                // Add your search logic here
                alert('Tính năng tìm kiếm đang được phát triển');
            }
        });
    }

    // Cart functionality
    const cartIcon = document.querySelector('.cart-icon');
    if (cartIcon) {
        cartIcon.addEventListener('click', function() {
            alert('Giỏ hàng của bạn đang trống');
            // Add your cart logic here
        });
    }

    // Quick view functionality
    const quickViewButtons = document.querySelectorAll('.quick-view');
    quickViewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const productCard = this.closest('.product-card');
            const productName = productCard.querySelector('h4').textContent;
            alert('Xem nhanh: ' + productName);
            // Add your quick view logic here
        });
    });

    // Product card click
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        card.addEventListener('click', function() {
            const productName = this.querySelector('h4').textContent;
            console.log('Sản phẩm được chọn: ' + productName);
            // Add your product detail navigation here
        });
    });

    // News card click
    const newsCards = document.querySelectorAll('.news-card');
    newsCards.forEach(card => {
        card.addEventListener('click', function() {
            const newsTitle = this.querySelector('h4').textContent;
            console.log('Tin tức được chọn: ' + newsTitle);
            // Add your news detail navigation here
        });
    });

    // Newsletter form
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value;
            
            if (email) {
                alert('Cảm ơn bạn đã đăng ký nhận tin!\nEmail: ' + email);
                emailInput.value = '';
                // Add your newsletter subscription logic here
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
            console.log('Bộ lọc được chọn: ' + filterText);
            // Add your filter logic here
        });
    });

    // Sidebar menu items
    const sidebarItems = document.querySelectorAll('.sidebar-box ul li');
    sidebarItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active state from all items
            sidebarItems.forEach(i => i.style.fontWeight = 'normal');
            // Make clicked item bold
            this.style.fontWeight = 'bold';
            
            const categoryText = this.textContent;
            console.log('Danh mục được chọn: ' + categoryText);
            // Add your category navigation logic here
        });
    });

    // Motor type cards
    const motorItems = document.querySelectorAll('.motor-item');
    motorItems.forEach(item => {
        item.addEventListener('click', function() {
            const motorType = this.querySelector('h3').textContent;
            console.log('Loại động cơ được chọn: ' + motorType);
            // Add your motor type filter logic here
        });
    });

    // Banner items
    const bannerItems = document.querySelectorAll('.banner-item');
    bannerItems.forEach(item => {
        item.addEventListener('click', function() {
            const label = this.querySelector('.banner-label');
            if (label) {
                const categoryText = label.textContent;
                console.log('Banner được chọn: ' + categoryText);
                // Add your banner navigation logic here
            }
        });
    });

    // Scroll to top on page load
    window.scrollTo(0, 0);

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

    // WhatsApp and Messenger float buttons animation
    const floatButtons = document.querySelectorAll('.whatsapp-float, .messenger-float');
    floatButtons.forEach(button => {
        button.style.animation = 'pulse 2s infinite';
    });

    // Add pulse animation keyframes dynamically
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
    `;
    document.head.appendChild(style);

    // Handle navigation dropdown (if needed in future)
    const navDropdowns = document.querySelectorAll('.nav-menu li a i');
    navDropdowns.forEach(icon => {
        const parentLink = icon.parentElement;
        parentLink.addEventListener('click', function(e) {
            if (this.querySelector('i')) {
                e.preventDefault();
                console.log('Dropdown menu: ' + this.textContent);
                // Add dropdown menu logic here
            }
        });
    });

    // Mobile menu toggle (for future mobile navigation)
    function createMobileMenu() {
        if (window.innerWidth <= 768) {
            console.log('Mobile view detected');
            // Add mobile menu logic here
        }
    }

    // Check on load and resize
    createMobileMenu();
    window.addEventListener('resize', createMobileMenu);

    // Lazy loading images
    const images = document.querySelectorAll('img');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.classList.add('loaded');
                    observer.unobserve(img);
                }
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));

    // Add to cart functionality (placeholder)
    function addToCart(productName, price) {
        console.log('Thêm vào giỏ hàng: ' + productName + ' - ' + price);
        // Update cart badge
        const cartBadge = document.querySelector('.cart-icon .badge');
        if (cartBadge) {
            let currentCount = parseInt(cartBadge.textContent);
            cartBadge.textContent = currentCount + 1;
        }
    }

    // Social media links
    const socialLinks = document.querySelectorAll('.social-icons a');
    socialLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const platform = this.querySelector('i').classList[1].replace('fa-', '');
            alert('Đang chuyển đến ' + platform);
            // Add actual social media links here
        });
    });

    // View all links
    const viewAllLinks = document.querySelectorAll('.view-all');
    viewAllLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const categoryName = this.textContent.trim();
            console.log('Xem tất cả: ' + categoryName);
            // Add navigation logic here
        });
    });

    // Brand logos click
    const brandLogos = document.querySelectorAll('.brand-logos img');
    brandLogos.forEach(logo => {
        logo.addEventListener('click', function() {
            const brandName = this.alt;
            console.log('Thương hiệu được chọn: ' + brandName);
            // Add brand filter logic here
        });
    });

    // Installation preview click
    const installationPreview = document.querySelector('.installation-preview');
    if (installationPreview) {
        installationPreview.addEventListener('click', function() {
            console.log('Xem dự án lắp đặt');
            // Add installation gallery or detail page logic here
        });
    }

    console.log('SANDI Website initialized successfully!');
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
