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
            <div class="header-info">
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
        </div>
    </div>
</header>

<!-- Navigation -->
<nav class="main-nav">
    <div class="container">
        <ul class="nav-menu">
            <li><a href="{{ route('client.home.index') }}" class="{{ request()->routeIs('client.home.*') ? 'active' : '' }}">TRANG CHỦ</a></li>
            <li><a href="{{ route('client.introduction.index') }}" class="{{ request()->routeIs('client.introduction.*') ? 'active' : '' }}">GIỚI THIỆU</a></li>
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
            <li><a href="{{ route('client.technical-service.index') }}" class="{{ request()->routeIs('client.technical-service.*') ? 'active' : '' }}">DỊCH VỤ KỸ THUẬT</a></li>
            <li><a href="{{ route('client.blog.index') }}">TIN TỨC</a></li>
            <li><a href="{{ route('client.contact.index') }}">LIÊN HỆ</a></li>
        </ul>
        <div class="search-icon" id="search-icon">
            <i class="fas fa-search"></i>
        </div>
    </div>
</nav>

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
