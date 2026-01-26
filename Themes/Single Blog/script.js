// script.js

// Smooth scroll for TOC links
document.addEventListener('DOMContentLoaded', function() {
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
            const url = window.location.href;
            
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
            });
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
            const progress = (scrolled / documentHeight) * 100;
            progressBar.style.width = progress + '%';
        });
    }
    
    // Lazy load images
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
    
    // Add active class to TOC links on scroll
    const sections = document.querySelectorAll('.single_blog_content section[id]');
    const navLinks = document.querySelectorAll('.single_blog_toc_list a');
    
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
                link.style.color = 'var(--secondary-color)';
            }
        });
    });
    
    // Share functionality
    const shareButtons = document.querySelectorAll('.single_blog_share_btn');
    shareButtons.forEach(btn => {
        if (!btn.classList.contains('single_blog_share_copy')) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const url = encodeURIComponent(window.location.href);
                const title = encodeURIComponent(document.title);
                let shareUrl = '';
                
                if (this.classList.contains('single_blog_share_facebook')) {
                    shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
                } else if (this.classList.contains('single_blog_share_twitter')) {
                    shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
                } else if (this.classList.contains('single_blog_share_linkedin')) {
                    shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
                }
                
                if (shareUrl) {
                    window.open(shareUrl, '_blank', 'width=600,height=400');
                }
            });
        }
    });
    
    // Table responsive wrapper
    const tables = document.querySelectorAll('.single_blog_table');
    tables.forEach(table => {
        if (!table.parentElement.classList.contains('single_blog_table_wrapper')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'single_blog_table_wrapper';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
    });
    
    // Print functionality
    const printBtn = document.createElement('button');
    printBtn.className = 'single_blog_print_btn';
    printBtn.innerHTML = '🖨️ In bài viết';
    printBtn.style.cssText = 'position: fixed; bottom: 20px; right: 20px; background: var(--primary-color); color: white; padding: 12px 20px; border: none; border-radius: 6px; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.15); font-weight: 600; z-index: 999;';
    document.body.appendChild(printBtn);
    
    printBtn.addEventListener('click', function() {
        window.print();
    });
    
    // Back to top button
    const backToTop = document.createElement('button');
    backToTop.className = 'single_blog_back_to_top';
    backToTop.innerHTML = '↑';
    backToTop.style.cssText = 'position: fixed; bottom: 80px; right: 20px; background: var(--text-dark); color: white; width: 45px; height: 45px; border: none; border-radius: 50%; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.15); font-size: 20px; display: none; z-index: 999;';
    document.body.appendChild(backToTop);
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTop.style.display = 'block';
        } else {
            backToTop.style.display = 'none';
        }
    });
    
    backToTop.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});