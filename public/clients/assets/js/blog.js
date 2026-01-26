// Blog Page JavaScript

// Slider functionality cho Latest Posts
const latestPostsSlider = {
    container: null,
    currentIndex: 0,
    itemsToShow: 4,
    
    init() {
        this.container = document.getElementById('latestPosts');
        if (!this.container) return;
        
        const prevBtn = document.querySelector('.blog_arrow_prev');
        const nextBtn = document.querySelector('.blog_arrow_next');
        
        if (prevBtn && nextBtn) {
            prevBtn.addEventListener('click', () => this.prev());
            nextBtn.addEventListener('click', () => this.next());
        }
        
        this.updateView();
    },
    
    next() {
        if (!this.container) return;
        const totalItems = this.container.children.length;
        if (this.currentIndex < totalItems - this.itemsToShow) {
            this.currentIndex++;
            this.updateView();
        }
    },
    
    prev() {
        if (!this.container) return;
        if (this.currentIndex > 0) {
            this.currentIndex--;
            this.updateView();
        }
    },
    
    updateView() {
        if (!this.container) return;
        const items = Array.from(this.container.children);
        if (items.length === 0) return;
        
        const itemWidth = items[0].offsetWidth;
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
            tab.addEventListener('click', (e) => {
                // Links sẽ tự động navigate, không cần preventDefault
                // Chỉ cần update active state nếu cần
            });
        });
    }
};

// Hero slider/pagination
const heroPagination = {
    currentSlide: 0,
    totalSlides: 0,
    featuredPosts: [],
    
    init() {
        const lines = document.querySelectorAll('.blog_pagination_line');
        if (lines.length === 0) return;
        
        this.totalSlides = lines.length;
        
        // Collect featured posts data
        lines.forEach((line, index) => {
            const postId = line.getAttribute('data-post-id');
            if (postId) {
                this.featuredPosts.push({
                    id: postId,
                    element: line
                });
            }
            
            line.addEventListener('click', () => this.goToSlide(index));
        });
        
        // Auto slide every 5 seconds
        if (this.totalSlides > 1) {
            setInterval(() => this.autoSlide(), 5000);
        }
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
    responsiveHandler.init();
});
