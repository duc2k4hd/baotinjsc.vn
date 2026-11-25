<!-- Header -->
<header class="bt-header">
    <div class="container">
        {{-- TẦNG 1: Logo (Trái) & Tìm kiếm (Phải) --}}
        <div class="bt-header-top">
            <div class="bt-header-brand">
                <a href="{{ route('client.home.index') }}" class="bt-header-logo">
                    <img src="{{ asset('clients/assets/img/business/' . ($settings->site_logo ?? 'logo.webp')) }}" 
                         alt="{{ $settings->site_name ?? 'Bảo Tín JSC' }}">
                </a>
            </div>

            <div class="bt-header-right-top">
                {{-- Thanh tìm kiếm bo nhẹ màu xám --}}
                <form action="{{ route('client.shop.index') }}" method="GET" class="bt-header-search">
                    <input type="text" 
                           name="keyword" 
                           placeholder="Từ khóa tìm kiếm..." 
                           value="{{ request('keyword', '') }}"
                           autocomplete="off"
                           aria-label="Tìm kiếm sản phẩm">
                    <button type="submit" aria-label="Tìm kiếm">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            {{-- Nút hành động nhanh trên Mobile (ẩn trên desktop) --}}
            <div class="bt-mobile-actions">
                @if(!empty($settings->contact_phone))
                    <a href="tel:{{ preg_replace('/[^0-9]/', '', $settings->contact_phone) }}" class="bt-mob-btn" aria-label="Gọi hotline">
                        <i class="fas fa-phone-alt"></i>
                    </a>
                @endif
                <a href="{{ auth()->check() ? route('client.profile.index') : route('client.auth.login') }}" class="bt-mob-btn" aria-label="Tài khoản">
                    <i class="fas fa-user"></i>
                </a>
                <a href="{{ $cartLink ?? route('client.cart.index') }}" class="bt-mob-btn" aria-label="Giỏ hàng">
                    <i class="fas fa-shopping-cart"></i>
                    @if(isset($cartCount) && $cartCount > 0)
                        <span class="bt-mob-badge">{{ $cartCount }}</span>
                    @endif
                </a>
                <button type="button" class="bt-mob-btn bt-mob-menu-toggle" id="mobile-menu-btn" aria-label="Mở menu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>

        {{-- TẦNG 2: Menu ngang gạch đứng (Trái) & Các nút tròn mạng xã hội (Phải) --}}
        <div class="bt-header-nav-bar">
            <nav class="main-nav bt-main-nav" aria-label="Menu chính">
                <div class="mobile-nav-overlay" id="mobile-nav-overlay" aria-hidden="true"></div>
                <ul class="nav-menu bt-nav-list">
                    {{-- 1. TRANG CHỦ --}}
                    <li>
                        <a href="{{ route('client.home.index') }}" class="{{ request()->routeIs('client.home.*') ? 'active' : '' }}">TRANG CHỦ</a>
                    </li>
                    {{-- 2. SẢN PHẨM (có Mega Menu) --}}
                    <li class="has-mega-menu">
                        <a href="{{ route('client.shop.index') }}" class="{{ request()->routeIs('client.shop.*') ? 'active' : '' }}">
                            SẢN PHẨM <i class="fas fa-chevron-down bt-caret"></i>
                        </a>
                        <!-- Mega Menu -->
                        <div class="mega-menu">
                            <div class="mega-menu-content">
                                <div class="mega-menu-categories">
                                    <ul class="category-list">
                                        @foreach($categories as $category)
                                            <li class="category-item" data-category-id="{{ $category->id }}">
                                                <a href="{{ route('client.shop.index', ['category' => $category->slug]) }}" class="category-link">
                                                    <span class="category-arrow">▶</span>
                                                    <span class="category-name">{{ $category->name }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="mega-menu-content-area">
                                    <div class="mega-menu-default">
                                        <h3>Chọn danh mục để xem sản phẩm</h3>
                                    </div>
                                    @foreach($categories as $category)
                                        <div class="mega-menu-category-content" data-category-content="{{ $category->id }}" style="display: none;">
                                            <h3 class="category-title">{{ strtoupper($category->name) }}</h3>
                                            <div class="subcategories-grid">
                                                @if($category->children && $category->children->count() > 0)
                                                    @foreach($category->children as $child)
                                                        <a href="{{ route('client.shop.index', ['category' => $child->slug]) }}" class="subcategory-item">
                                                            {{ $child->name }}
                                                        </a>
                                                    @endforeach
                                                @else
                                                    <a href="{{ route('client.shop.index', ['category' => $category->slug]) }}" class="subcategory-item">
                                                        Xem tất cả {{ $category->name }}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </li>
                    {{-- 3. GIỚI THIỆU --}}
                    <li>
                        <a href="{{ route('client.introduction.index') }}" class="{{ request()->routeIs('client.introduction.*') ? 'active' : '' }}">GIỚI THIỆU</a>
                    </li>
                    {{-- 4. DỊCH VỤ --}}
                    <li>
                        <a href="{{ route('client.technical-service.index') }}" class="{{ request()->routeIs('client.technical-service.*') ? 'active' : '' }}">DỊCH VỤ</a>
                    </li>
                    {{-- 5. TIN TỨC --}}
                    <li>
                        <a href="{{ route('client.blog.index') }}" class="{{ request()->routeIs('client.blog.*') ? 'active' : '' }}">TIN TỨC</a>
                    </li>
                    {{-- 6. LIÊN HỆ --}}
                    <li>
                        <a href="{{ route('client.contact.index') }}" class="{{ request()->routeIs('client.contact.*') ? 'active' : '' }}">LIÊN HỆ</a>
                    </li>
                </ul>
            </nav>

            {{-- Hàng nút tròn bên phải (lấy link thật từ settings DB) --}}
            <div class="bt-header-socials">
                @if(!empty($settings->facebook_link))
                    <a href="{{ $settings->facebook_link }}" target="_blank" rel="noopener noreferrer" class="bt-social-circle" title="Facebook" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                @endif
                @if(!empty($settings->contact_zalo) || !empty($settings->contact_phone))
                    <a href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', $settings->contact_zalo ?? $settings->contact_phone) }}" target="_blank" rel="noopener noreferrer" class="bt-social-circle" title="Chat Zalo" aria-label="Zalo">
                        <span class="bt-zalo-text">Z</span>
                    </a>
                @endif
                @if(!empty($settings->twitter_link))
                    <a href="{{ $settings->twitter_link }}" target="_blank" rel="noopener noreferrer" class="bt-social-circle" title="Twitter / X" aria-label="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                @endif
                @if(!empty($settings->contact_email))
                    <a href="mailto:{{ $settings->contact_email }}" class="bt-social-circle" title="Email: {{ $settings->contact_email }}" aria-label="Email">
                        <i class="fas fa-envelope"></i>
                    </a>
                @endif
            </div>
        </div>

        {{-- Thanh tìm kiếm Mobile (chỉ hiện trên màn hình nhỏ < 768px) --}}
        <div class="bt-mobile-search-bar">
            <form action="{{ route('client.shop.index') }}" method="GET" class="bt-header-search">
                <input type="text" name="keyword" placeholder="Từ khóa tìm kiếm..." value="{{ request('keyword', '') }}" autocomplete="off" aria-label="Tìm kiếm trên mobile">
                <button type="submit" aria-label="Tìm kiếm">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('mobile-menu-btn');
    const mobileSearchBtn = document.getElementById('mobile-search-btn');
    const searchIcon = document.getElementById('search-icon');
    const mobileMenu = document.getElementById('mobile-category-menu');
    const mobileMenuClose = document.getElementById('mobile-category-close');
    const mobileMenuBack = document.getElementById('mobile-category-back');

    function openMobileMenu() {
        if (!mobileMenu) return;
        mobileMenu.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileMenu() {
        if (!mobileMenu) return;
        mobileMenu.classList.remove('open');
        document.body.style.overflow = '';
    }

    btn?.addEventListener('click', function () {
        openMobileMenu();
    });

    mobileMenuClose?.addEventListener('click', closeMobileMenu);
    mobileMenuBack?.addEventListener('click', closeMobileMenu);

    // Đóng bằng Esc
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && mobileMenu?.classList.contains('open')) {
            closeMobileMenu();
        }
    });

    // Nút search nhỏ trên header mobile -> reuse search icon sẵn có
    mobileSearchBtn?.addEventListener('click', function () {
        searchIcon?.click();
    });

    // Xử lý mobile category list với subcategories
    document.querySelectorAll('.mobile-category-item.has-children').forEach(item => {
        const box = item.querySelector('.mobile-category-item-box');
        const toggle = item.querySelector('.mobile-category-item-toggle');
        const link = item.querySelector('.mobile-category-item-link');

        if (!box) return;

        if (toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                item.classList.toggle('active');
            });
        }

        box.addEventListener('click', function(e) {
            if (link && (e.target === link || link.contains(e.target))) {
                return;
            }
            if (toggle && (e.target === toggle || toggle.contains(e.target))) {
                return;
            }
            e.preventDefault();
            item.classList.toggle('active');
        });
    });
});
</script>

<!-- Mobile Category Menu (overlay điều hướng & danh mục trên Mobile) -->
<div id="mobile-category-menu" class="mobile-category-menu">
    <div class="mobile-category-header">
        <button type="button" class="mobile-category-back" id="mobile-category-back" aria-label="Đóng menu">
            <i class="fas fa-arrow-left"></i>
        </button>
        <h2 class="mobile-category-title">Menu &amp; Danh Mục</h2>
        <button type="button" class="mobile-category-close" id="mobile-category-close" aria-label="Đóng">
            ×
        </button>
    </div>

    <div class="mobile-category-search">
        <form action="{{ route('client.shop.index') }}" method="GET" class="mobile-category-search-form">
            <div class="mobile-category-search-row">
                <div class="mobile-category-select">
                    <select name="category" aria-label="Danh mục">
                        <option value="">{{ $category?->name ?? 'Danh mục' }}</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mobile-category-input">
                    <input type="text" name="keyword" placeholder="Tìm kiếm sản phẩm..." value="{{ request('keyword','') }}">
                </div>
                <button type="submit" class="mobile-category-search-btn">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>

    {{-- Menu điều hướng nhanh các trang chính trên Mobile --}}
    <div class="bt-mobile-quick-nav">
        <a href="{{ route('client.home.index') }}" class="{{ request()->routeIs('client.home.*') ? 'active' : '' }}">Trang chủ</a>
        <a href="{{ route('client.shop.index') }}" class="{{ request()->routeIs('client.shop.*') ? 'active' : '' }}">Sản phẩm</a>
        <a href="{{ route('client.introduction.index') }}" class="{{ request()->routeIs('client.introduction.*') ? 'active' : '' }}">Giới thiệu</a>
        <a href="{{ route('client.technical-service.index') }}" class="{{ request()->routeIs('client.technical-service.*') ? 'active' : '' }}">Dịch vụ</a>
        <a href="{{ route('client.blog.index') }}" class="{{ request()->routeIs('client.blog.*') ? 'active' : '' }}">Tin tức</a>
        <a href="{{ route('client.contact.index') }}" class="{{ request()->routeIs('client.contact.*') ? 'active' : '' }}">Liên hệ</a>
    </div>

    <div class="mobile-category-actions">
        <a href="{{ route('client.comparison.index') }}" class="mobile-category-action">
            <span class="icon">🔄</span>
            <span class="label">So sánh</span>
        </a>
        <a href="{{ route('client.wishlist.index') }}" class="mobile-category-action">
            <span class="icon">💙</span>
            <span class="label">Yêu thích</span>
        </a>
        <a href="{{ route('client.cart.index') }}" class="mobile-category-action">
            <span class="icon">🛒</span>
            <span class="label">Giỏ hàng</span>
        </a>
        <a href="{{ auth()->check() ? route('client.profile.index') : route('client.auth.login') }}" class="mobile-category-action">
            <span class="icon">👤</span>
            <span class="label">{{ auth()->check() ? (auth()->user()->name ?? 'Người dùng') : 'Đăng nhập' }}</span>
        </a>
    </div>

    @auth
        <div class="mobile-category-logout">
            <form action="{{ route('client.auth.logout') }}" method="POST">
                @csrf
                <button type="submit">Đăng xuất</button>
            </form>
        </div>
    @endauth

    <div class="mobile-category-body">
        <ul class="mobile-category-list">
            @foreach($categories as $cat)
                <li class="mobile-category-item {{ ($cat->children && $cat->children->count() > 0) ? 'has-children' : '' }}">
                    <div class="mobile-category-item-box">
                        <a href="{{ route('client.shop.index', ['category' => $cat->slug]) }}" class="mobile-category-item-link">
                            <span class="name">{{ $cat->name }}</span>
                        </a>
                        @if($cat->children && $cat->children->count() > 0)
                            <button type="button" class="mobile-category-item-toggle" aria-label="Mở danh mục con">
                                <span class="arrow">›</span>
                            </button>
                        @else
                            <span class="arrow">›</span>
                        @endif
                    </div>
                    @if($cat->children && $cat->children->count() > 0)
                        <ul class="mobile-category-children">
                            @foreach($cat->children as $child)
                                <li class="mobile-category-child-item">
                                    <a href="{{ route('client.shop.index', ['category' => $child->slug]) }}">
                                        <span class="name">{{ $child->name }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>

<!-- Search Box -->
<div class="search-box" id="search-box">
    <div class="search-box-content">
        <div class="search-box-header">
            <h3>Tìm kiếm sản phẩm</h3>
            <button class="close-search-box" id="close-search-box">×</button>
        </div>
        <form action="{{ route('client.shop.index') }}" method="GET" class="search-form" id="search-form">
            <div class="search-input-wrapper">
                <input type="text" 
                       name="keyword" 
                       id="search-input" 
                       placeholder="Nhập tên sản phẩm hoặc SKU..." 
                       autocomplete="off"
                       value="{{ request('keyword', '') }}">
                <button type="submit" class="search-submit-btn">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <div class="search-suggestions" id="search-suggestions"></div>
        </form>
    </div>
</div>
