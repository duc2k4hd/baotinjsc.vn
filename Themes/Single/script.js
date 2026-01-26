// Quantity Controls
function increaseQty() {
    const qtyInput = document.getElementById('quantity');
    let currentValue = parseInt(qtyInput.value) || 1;
    qtyInput.value = currentValue + 1;
}

function decreaseQty() {
    const qtyInput = document.getElementById('quantity');
    let currentValue = parseInt(qtyInput.value) || 1;
    if (currentValue > 1) {
        qtyInput.value = currentValue - 1;
    }
}

// Thumbnail Image Click
document.addEventListener('DOMContentLoaded', function() {
    const thumbnails = document.querySelectorAll('.thumb-img');
    const mainImage = document.getElementById('mainImage');
    
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
    
    // Zoom Button
    const zoomBtn = document.querySelector('.zoom-btn');
    if (zoomBtn) {
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
    
    // Quote Button
    const quoteBtn = document.querySelector('.quote-btn');
    if (quoteBtn) {
        quoteBtn.addEventListener('click', function() {
            const quantity = document.getElementById('quantity').value;
            const productName = document.querySelector('.product-info h1').textContent;
            
            // Create quote request
            const message = `Yêu cầu báo giá:\nSản phẩm: ${productName}\nSố lượng: ${quantity}`;
            
            // You can implement actual quote request logic here
            alert(message + '\n\nVui lòng liên hệ: 0903 223 663');
        });
    }
    
    // Chat Widget
    const chatButton = document.querySelector('.chat-button');
    if (chatButton) {
        chatButton.addEventListener('click', function() {
            // Implement chat widget opening logic
            alert('Chức năng chat sẽ được kích hoạt. Vui lòng liên hệ: 0903 223 663');
        });
    }
    
    // Newsletter Form
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value;
            
            if (email) {
                alert('Cảm ơn bạn đã đăng ký nhận bản tin!\nEmail: ' + email);
                emailInput.value = '';
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
    
    // Mobile menu toggle (if needed)
    const mobileMenuToggle = () => {
        const navMenu = document.querySelector('.nav-menu');
        if (window.innerWidth <= 768) {
            navMenu.style.display = navMenu.style.display === 'none' ? 'flex' : 'none';
        }
    };
    
    // Sticky header on scroll
    let lastScroll = 0;
    const header = document.querySelector('.header');
    const nav = document.querySelector('.main-nav');
    
    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 100) {
            nav.style.position = 'sticky';
            nav.style.top = '0';
            nav.style.zIndex = '999';
            nav.style.boxShadow = '0 2px 5px rgba(0,0,0,0.1)';
        } else {
            nav.style.position = 'relative';
            nav.style.boxShadow = 'none';
        }
        
        lastScroll = currentScroll;
    });
    
    // Product card hover effect
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });
    
    // Lazy loading for images
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
    
    // Add to cart animation (placeholder)
    const addAnimation = (button) => {
        button.style.transform = 'scale(0.95)';
        setTimeout(() => {
            button.style.transform = 'scale(1)';
        }, 150);
    };
    
    // Search functionality
    const searchInput = document.querySelector('.search-box input');
    const searchButton = document.querySelector('.search-box button');
    
    if (searchButton) {
        searchButton.addEventListener('click', function() {
            const searchTerm = searchInput.value.trim();
            if (searchTerm) {
                // Implement search logic
                console.log('Searching for:', searchTerm);
                // window.location.href = '/search?q=' + encodeURIComponent(searchTerm);
            }
        });
    }
    
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchButton.click();
            }
        });
    }
    
    // Phone widget click tracking
    const phoneWidget = document.querySelector('.phone-widget');
    if (phoneWidget) {
        phoneWidget.addEventListener('click', function() {
            // Track phone call click
            console.log('Phone call initiated: 0903223663');
        });
    }
});

// Quantity input validation
document.addEventListener('input', function(e) {
    if (e.target.id === 'quantity') {
        let value = parseInt(e.target.value);
        if (isNaN(value) || value < 1) {
            e.target.value = 1;
        }
    }
});

// Window resize handler
let resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        // Handle resize events
        const nav = document.querySelector('.nav-menu');
        if (window.innerWidth > 768) {
            nav.style.display = 'flex';
        }
    }, 250);
});

// Print page function
function printPage() {
    window.print();
}

// Share function (placeholder)
function sharePage(platform) {
    const url = window.location.href;
    const title = document.title;
    
    switch(platform) {
        case 'facebook':
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
            break;
        case 'twitter':
            window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`, '_blank');
            break;
        case 'linkedin':
            window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`, '_blank');
            break;
        default:
            console.log('Share to:', platform);
    }
}

// Back to top button
const createBackToTop = () => {
    const backToTop = document.createElement('button');
    backToTop.innerHTML = '↑';
    backToTop.style.cssText = `
        position: fixed;
        bottom: 150px;
        right: 20px;
        width: 50px;
        height: 50px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 50%;
        font-size: 24px;
        cursor: pointer;
        display: none;
        z-index: 999;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        transition: all 0.3s;
    `;
    
    backToTop.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            backToTop.style.display = 'block';
        } else {
            backToTop.style.display = 'none';
        }
    });
    
    document.body.appendChild(backToTop);
};

// Initialize back to top button
createBackToTop();

// Local storage for viewed products
const saveViewedProduct = () => {
    const productName = document.querySelector('.product-info h1')?.textContent;
    if (productName) {
        let viewedProducts = JSON.parse(localStorage.getItem('viewedProducts') || '[]');
        if (!viewedProducts.includes(productName)) {
            viewedProducts.unshift(productName);
            viewedProducts = viewedProducts.slice(0, 10); // Keep only last 10
            localStorage.setItem('viewedProducts', JSON.stringify(viewedProducts));
        }
    }
};

// Save current product as viewed
saveViewedProduct();

// Console log for debugging
console.log('Sandi Product Page - JavaScript Loaded Successfully');
console.log('Contact: 0903 223 663');
