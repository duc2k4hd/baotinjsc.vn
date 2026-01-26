// script.js

// Slider functionality cho Latest Posts
const latestPostsSlider = {
    container: null,
    currentIndex: 0,
    itemsToShow: 4,
    
    init() {
        this.container = document.getElementById('latestPosts');
        const prevBtn = document.querySelector('.blog_arrow_prev');
        const nextBtn = document.querySelector('.blog_arrow_next');
        
        if (prevBtn && nextBtn) {
            prevBtn.addEventListener('click', () => this.prev());
            nextBtn.addEventListener('click', () => this.next());
        }
        
        this.updateView();
    },
    
    next() {
        const totalItems = this.container.children.length;
        if (this.currentIndex < totalItems - this.itemsToShow) {
            this.currentIndex++;
            this.updateView();
        }
    },
    
    prev() {
        if (this.currentIndex > 0) {
            this.currentIndex--;
            this.updateView();
        }
    },
    
    updateView() {
        const itemWidth = this.container.children[0].offsetWidth;
        const gap = 30;
        const offset = -(this.currentIndex * (itemWidth + gap));
        this.container.style.transform = `translateX(${offset}px)`;
        this.container.style.transition = 'transform 0.3s ease';
    }
};

// Filter tabs functionality
const filterTabs = {
    init() {
        const tabs = document.querySelectorAll('.blog_tab_btn');
        tabs.forEach(tab => {
            tab.addEventListener('click', (e) => this.handleTabClick(e));
        });
    },
    
    handleTabClick(e) {
        const tabs = document.querySelectorAll('.blog_tab_btn');
        tabs.forEach(tab => tab.classList.remove('blog_tab_btn_active'));
        e.target.classList.add('blog_tab_btn_active');
        
        // Ở đây bạn có thể thêm logic để filter posts theo category
        console.log('Filter by:', e.target.textContent);
    }
};

// Hero slider/pagination
const heroPagination = {
    currentSlide: 0,
    totalSlides: 3,
    
    init() {
        const lines = document.querySelectorAll('.blog_pagination_line');
        lines.forEach((line, index) => {
            line.addEventListener('click', () => this.goToSlide(index));
        });
        
        // Auto slide every 5 seconds
        setInterval(() => this.autoSlide(), 5000);
    },
    
    goToSlide(index) {
        this.currentSlide = index;
        this.updatePagination();
    },
    
    autoSlide() {
        this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
        this.updatePagination();
    },
    
    updatePagination() {
        const lines = document.querySelectorAll('.blog_pagination_line');
        lines.forEach((line, index) => {
            if (index === this.currentSlide) {
                line.classList.add('blog_pagination_line_active');
            } else {
                line.classList.remove('blog_pagination_line_active');
            }
        });
    }
};

// Smooth scroll for links
const smoothScroll = {
    init() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }
};

// Responsive handling
const responsiveHandler = {
    init() {
        this.handleResize();
        window.addEventListener('resize', () => this.handleResize());
    },
    
    handleResize() {
        const width = window.innerWidth;
        
        if (width <= 768) {
            latestPostsSlider.itemsToShow = 1;
        } else if (width <= 992) {
            latestPostsSlider.itemsToShow = 2;
        } else if (width <= 1200) {
            latestPostsSlider.itemsToShow = 3;
        } else {
            latestPostsSlider.itemsToShow = 4;
        }
        
        latestPostsSlider.updateView();
    }
};

// Initialize all functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    latestPostsSlider.init();
    filterTabs.init();
    heroPagination.init();
    smoothScroll.init();
    responsiveHandler.init();
});

// Handle scroll effects
let lastScroll = 0;
window.addEventListener('scroll', () => {
    const header = document.querySelector('.blog_header');
    const currentScroll = window.pageYOffset;
    
    if (currentScroll > lastScroll && currentScroll > 100) {
        header.style.transform = 'translateY(-100%)';
    } else {
        header.style.transform = 'translateY(0)';
    }
    
    lastScroll = currentScroll;
});