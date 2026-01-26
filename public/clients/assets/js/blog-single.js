// Single Blog Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll for TOC links
    const tocLinks = document.querySelectorAll('.single_blog_toc_list a');
    
    tocLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Copy link functionality
    const copyBtn = document.querySelector('.single_blog_share_copy');
    if (copyBtn) {
        copyBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('data-url') || window.location.href;
            
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(function() {
                    const originalText = copyBtn.innerHTML;
                    copyBtn.innerHTML = '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M7 10l2 2 4-4"/></svg> Đã sao chép!';
                    copyBtn.style.background = '#28a745';
                    copyBtn.style.color = 'white';
                    
                    setTimeout(function() {
                        copyBtn.innerHTML = originalText;
                        copyBtn.style.background = '';
                        copyBtn.style.color = '';
                    }, 2000);
                }).catch(function(err) {
                    console.error('Failed to copy:', err);
                });
            } else {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = url;
                textArea.style.position = 'fixed';
                textArea.style.opacity = '0';
                document.body.appendChild(textArea);
                textArea.select();
                try {
                    document.execCommand('copy');
                    const originalText = copyBtn.innerHTML;
                    copyBtn.innerHTML = '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M7 10l2 2 4-4"/></svg> Đã sao chép!';
                    copyBtn.style.background = '#28a745';
                    copyBtn.style.color = 'white';
                    
                    setTimeout(function() {
                        copyBtn.innerHTML = originalText;
                        copyBtn.style.background = '';
                        copyBtn.style.color = '';
                    }, 2000);
                } catch (err) {
                    console.error('Failed to copy:', err);
                }
                document.body.removeChild(textArea);
            }
        });
    }
    
    // Reading progress bar
    const article = document.querySelector('.single_blog_article');
    if (article) {
        const progressBar = document.createElement('div');
        progressBar.className = 'single_blog_progress_bar';
        progressBar.style.cssText = 'position: fixed; top: 0; left: 0; width: 0; height: 3px; background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); z-index: 9999; transition: width 0.2s;';
        document.body.appendChild(progressBar);
        
        window.addEventListener('scroll', function() {
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight - windowHeight;
            const scrolled = window.scrollY;
            const progress = Math.min(100, (scrolled / documentHeight) * 100);
            progressBar.style.width = progress + '%';
        });
    }
    
    // Lazy load images
    const images = document.querySelectorAll('img[data-src]');
    if (images.length > 0) {
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
    }
    
    // Add active class to TOC links on scroll
    const sections = document.querySelectorAll('.single_blog_content section[id]');
    const navLinks = document.querySelectorAll('.single_blog_toc_list a');
    
    if (sections.length > 0 && navLinks.length > 0) {
        window.addEventListener('scroll', function() {
            let current = '';
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (window.pageYOffset >= (sectionTop - 100)) {
                    current = section.getAttribute('id');
                }
            });
            
            navLinks.forEach(link => {
                link.style.fontWeight = '';
                link.style.color = '';
                if (link.getAttribute('href') === '#' + current) {
                    link.style.fontWeight = '600';
                    link.style.color = 'var(--single-blog-secondary-color)';
                }
            });
        });
    }
    
    // Share functionality (already handled by links, but can add analytics here)
    const shareButtons = document.querySelectorAll('.single_blog_share_btn');
    shareButtons.forEach(btn => {
        if (!btn.classList.contains('single_blog_share_copy')) {
            btn.addEventListener('click', function(e) {
                // Links already have href, just track if needed
                // Can add analytics tracking here
            });
        }
    });
    
    // Hover effects for related posts
    const relatedItems = document.querySelectorAll('.single_blog_related_item');
    relatedItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
