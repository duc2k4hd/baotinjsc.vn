<!-- Header -->
<header class="header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="{{ route('client.home.index') }}">
                    <img src="{{ asset('clients/assets/img/business/' . ($settings->site_logo ?? 'logo.webp')) }}" 
                         alt="{{ $settings->site_name ?? 'Bảo Tín JSC' }}">
                </a>
            </div>
            <div class="info-item">
                <i class="fas fa-headset"></i>
                <div>
                    <strong>HỖ TRỢ 24/7</strong>
                    <p>Hotline: {{ $settings->contact_phone ?? '0985214595' }}</p>
                </div>
            </div>
            <div class="info-item">
                <a href="{{ auth()->check() ? route('client.profile.index') : route('client.auth.login') }}" class="info-item-link">
                    <i class="fas fa-user"></i>
                    <div>
                        <strong>NGƯỜI DÙNG</strong>
                        <p>
                            @auth
                                {{ auth()->user()->name ?? 'Tài khoản' }}
                                    @else
                                Đăng nhập / Đăng ký
                            @endauth
                        </p>
                    </div>
                </a>
            </div>
            <div class="info-item wishlist-item">
                <a href="{{ route('client.wishlist.index') }}" class="info-item-link">
                    <i class="fas fa-heart"></i>
                    @if(isset($wishlistCount) && $wishlistCount > 0)
                        <span class="badge">{{ $wishlistCount }}</span>
                    @endif
                    <div>
                        <strong>YÊU THÍCH</strong>
                        <p>Sản phẩm yêu thích</p>
                </div>
                </a>
            </div>
            <div class="info-item comparison-item">
                <a href="{{ route('client.comparison.index') }}" class="info-item-link">
                    <i class="fas fa-balance-scale"></i>
                    @if(isset($comparisonCount) && $comparisonCount > 0)
                        <span class="badge">{{ $comparisonCount }}</span>
                    @endif
                    <div>
                        <strong>SO SÁNH</strong>
                        <p>Sản phẩm so sánh</p>
                    </div>
                </a>
            </div>
            <div class="cart-icon">
                <a href="{{ $cartLink ?? route('client.cart.index') }}">
                    <i class="fas fa-shopping-cart"></i>
                    @if(isset($cartCount) && $cartCount > 0)
                        <span class="badge">{{ $cartCount }}</span>
                    @endif
                    <p>GIỎ HÀNG</p>
                </a>
            </div>
        </div>

        {{-- Mobile quick bar (chỉ hiển thị trên mobile bằng CSS) --}}
        <div class="mobile-header-bar" aria-label="Mobile quick actions">
            <div class="mobile-actions">
                <a class="mobile-action" href="tel:{{ preg_replace('/[^0-9]/', '', $settings->contact_phone ?? '0985214595') }}" aria-label="Gọi hotline">
                    <i class="fas fa-headset"></i>
                </a>
                <a class="mobile-action" href="{{ auth()->check() ? route('client.profile.index') : route('client.auth.login') }}" aria-label="Tài khoản">
                    <i class="fas fa-user"></i>
                </a>
                <a class="mobile-action" href="{{ route('client.cart.index') }}" aria-label="Giỏ hàng">
                    <i class="fas fa-shopping-cart"></i>
                    @if(isset($cartCount) && $cartCount > 0)
                        <span class="badge">{{ $cartCount }}</span>
                    @endif
                </a>
                <button type="button" class="mobile-action" id="mobile-search-btn" aria-label="Tìm kiếm">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <button type="button" class="mobile-menu-btn" id="mobile-menu-btn" aria-label="Mở menu">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="24px" height="24px" viewBox="0 0 600 600" preserveAspectRatio="xMidYMid meet">

                    <g transform="translate(0,600) scale(0.1,-0.1)" stroke="none" fill="#3bb77e">
    
                        <path d="M672 5989 c-165 -28 -308 -103 -433 -228 -128 -129 -200 -267 -228 -441 -15 -91 -15 -4549 0 -4640 28 -174 100 -312 228 -441 129 -128 267 -200 441 -228 91 -15 4549 -15 4640 0 174 28 312 100 441 228 128 129 200 267 228 441 15 91 15 4549 0 4640 -28 174 -100 312 -228 441 -129 128 -267 200 -441 228 -81 13 -4569 13 -4648 0z m1960 -1372 c68 -33 111 -82 135 -152 16 -45 18 -99 18 -565 0 -590 0 -586 -82 -666 -81 -80 -77 -79 -663 -79 -466 0 -520 2 -565 18 -70 24 -119 67 -152 135 l-28 57 -3 504 c-2 335 1 521 8 556 22 104 96 183 198 211 36 10 168 13 562 11 l515 -2 57 -28z m1895 4 c71 -32 127 -95 150 -168 16 -53 18 -105 18 -553 0 -441 -2 -501 -17 -551 -23 -72 -81 -139 -150 -170 l-53 -24 -530 0 -530 0 -52 24 c-71 32 -127 95 -150 168 -16 53 -18 105 -18 553 0 447 2 501 18 553 19 63 70 128 121 154 79 41 92 42 626 40 l515 -2 52 -24z m-1922 -1852 c66 -25 119 -68 145 -118 38 -75 41 -137 38 -646 l-3 -490 -26 -55 c-33 -71 -79 -114 -148 -142 -55 -22 -67 -23 -531 -26 -323 -2 -495 0 -540 8 -130 23 -205 89 -235 209 -14 51 -15 136 -13 563 l3 503 24 53 c13 30 42 69 67 91 80 70 78 70 651 70 497 1 514 0 568 -20z m1490 -35 c83 -21 209 -84 277 -139 121 -96 222 -265 254 -425 30 -151 5 -331 -66 -469 l-19 -38 160 -159 c165 -164 189 -194 189 -239 0 -35 -50 -85 -85 -85 -43 0 -63 16 -227 178 l-156 155 -40 -34 c-62 -53 -160 -107 -247 -136 -71 -24 -95 -27 -220 -27 -128 0 -147 2 -220 28 -307 107 -499 372 -499 686 1 130 21 217 76 330 34 68 60 103 133 176 97 98 172 146 285 183 132 43 275 48 405 15z"></path>
                        <path d="M3823 2569 c-370 -62 -563 -489 -368 -814 20 -33 51 -75 68 -94 40 -43 148 -112 207 -134 311 -113 647 76 716 403 61 286 -130 574 -421 635 -77 16 -124 17 -202 4z"></path>
                    </g>
                </svg>
            </button>
        </div>
    </div>
</header>

<!-- Navigation -->
<nav class="main-nav">
    <div class="container">
        {{-- Mobile drawer overlay --}}
        <div class="mobile-nav-overlay" id="mobile-nav-overlay" aria-hidden="true"></div>
        <ul class="nav-menu">
            {{-- 1. TRANG CHỦ --}}
            <li><a href="{{ route('client.home.index') }}" class="{{ request()->routeIs('client.home.*') ? 'active' : '' }}">TRANG CHỦ</a></li>
            {{-- 2. SẢN PHẨM --}}
            <li class="has-mega-menu">
                <a href="{{ route('client.shop.index') }}">
                    SẢN PHẨM <i class="fas fa-chevron-down"></i>
                </a>
                <!-- Mega Menu -->
                <div class="mega-menu">
                    <div class="mega-menu-content">
                        <!-- Left Column: Category List -->
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
                        <!-- Right Column: Subcategories Content -->
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
            {{-- 3. LIÊN HỆ --}}
            <li><a href="{{ route('client.contact.index') }}">LIÊN HỆ</a></li>
            {{-- 4. DỊCH VỤ KỸ THUẬT --}}
            <li><a href="{{ route('client.technical-service.index') }}" class="{{ request()->routeIs('client.technical-service.*') ? 'active' : '' }}">DỊCH VỤ KỸ THUẬT</a></li>
            {{-- 5. TIN TỨC --}}
            <li><a href="{{ route('client.blog.index') }}">TIN TỨC</a></li>
            {{-- 6. GIỚI THIỆU --}}
            <li><a href="{{ route('client.introduction.index') }}" class="{{ request()->routeIs('client.introduction.*') ? 'active' : '' }}">GIỚI THIỆU</a></li>
            {{-- Mobile-only quick links --}}
            <li class="mobile-only"><a href="{{ route('client.wishlist.index') }}">YÊU THÍCH</a></li>
            <li class="mobile-only"><a href="{{ route('client.comparison.index') }}">SO SÁNH</a></li>
        </ul>
        <div class="search-icon" id="search-icon">
            <i class="fas fa-search"></i>
        </div>
    </div>
</nav>

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

        // Click vào toggle button → mở/đóng subcategories
        if (toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                item.classList.toggle('active');
            });
        }

        // Click vào box → mở/đóng subcategories (trừ khi click vào link)
        box.addEventListener('click', function(e) {
            // Nếu click vào link → navigate, không toggle
            if (link && (e.target === link || link.contains(e.target))) {
                return;
            }
            // Nếu click vào toggle → đã xử lý ở trên
            if (toggle && (e.target === toggle || toggle.contains(e.target))) {
                return;
            }
            // Click vào phần còn lại của box → toggle subcategories
            e.preventDefault();
            item.classList.toggle('active');
        });
    });
});
</script>

<!-- Mobile Category Menu (overlay giống layout Danh Mục Sản Phẩm) -->
<div id="mobile-category-menu" class="mobile-category-menu">
    <div class="mobile-category-header">
        <button type="button" class="mobile-category-back" id="mobile-category-back" aria-label="Đóng danh mục">
            <i class="fas fa-arrow-left"></i>
        </button>
        <h2 class="mobile-category-title">Danh Mục Sản Phẩm</h2>
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
