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

            {{-- Cụm icon liên hệ nhanh bên phải (Zalo, Phone, Mail lấy dữ liệu thật từ settings DB) --}}
            <div class="bt-header-socials">
                @if(!empty($settings->contact_zalo) || !empty($settings->contact_phone))
                    <a href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', $settings->contact_zalo ?? $settings->contact_phone) }}" target="_blank" rel="noopener noreferrer" class="bt-social-circle bt-social-zalo" title="Chat Zalo: {{ $settings->contact_zalo ?? $settings->contact_phone }}" aria-label="Chat Zalo">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 50 50" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M22.782 0.166016H27.199C33.2653 0.166016 36.8103 1.05701 39.9572 2.74421C43.1041 4.4314 45.5875 6.89585 47.2557 10.0428C48.9429 13.1897 49.8339 16.7347 49.8339 22.801V27.1991C49.8339 33.2654 48.9429 36.8104 47.2557 39.9573C45.5685 43.1042 43.1041 45.5877 39.9572 47.2559C36.8103 48.9431 33.2653 49.8341 27.199 49.8341H22.8009C16.7346 49.8341 13.1896 48.9431 10.0427 47.2559C6.89583 45.5687 4.41243 43.1042 2.7442 39.9573C1.057 36.8104 0.166016 33.2654 0.166016 27.1991V22.801C0.166016 16.7347 1.057 13.1897 2.7442 10.0428C4.43139 6.89585 6.89583 4.41245 10.0427 2.74421C13.1707 1.05701 16.7346 0.166016 22.782 0.166016Z" fill="#0068FF"></path>
                            <path opacity="0.12" fill-rule="evenodd" clip-rule="evenodd" d="M49.8336 26.4736V27.1994C49.8336 33.2657 48.9427 36.8107 47.2555 39.9576C45.5683 43.1045 43.1038 45.5879 39.9569 47.2562C36.81 48.9434 33.265 49.8344 27.1987 49.8344H22.8007C17.8369 49.8344 14.5612 49.2378 11.8104 48.0966L7.27539 43.4267L49.8336 26.4736Z" fill="#001A33"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M7.779 43.5892C10.1019 43.846 13.0061 43.1836 15.0682 42.1825C24.0225 47.1318 38.0197 46.8954 46.4923 41.4732C46.8209 40.9803 47.1279 40.4677 47.4128 39.9363C49.1062 36.7779 50.0004 33.22 50.0004 27.1316V22.7175C50.0004 16.629 49.1062 13.0711 47.4128 9.91273C45.7385 6.75436 43.2461 4.28093 40.0877 2.58758C36.9293 0.894239 33.3714 0 27.283 0H22.8499C17.6644 0 14.2982 0.652754 11.4699 1.89893C11.3153 2.03737 11.1636 2.17818 11.0151 2.32135C2.71734 10.3203 2.08658 27.6593 9.12279 37.0782C9.13064 37.0921 9.13933 37.1061 9.14889 37.1203C10.2334 38.7185 9.18694 41.5154 7.55068 43.1516C7.28431 43.399 7.37944 43.5512 7.779 43.5892Z" fill="white"></path>
                            <path d="M20.5632 17H10.8382V19.0853H17.5869L10.9329 27.3317C10.7244 27.635 10.5728 27.9194 10.5728 28.5639V29.0947H19.748C20.203 29.0947 20.5822 28.7156 20.5822 28.2606V27.1421H13.4922L19.748 19.2938C19.8428 19.1801 20.0134 18.9716 20.0893 18.8768L20.1272 18.8199C20.4874 18.2891 20.5632 17.8341 20.5632 17.2844V17Z" fill="#0068FF"></path>
                            <path d="M32.9416 29.0947H34.3255V17H32.2402V28.3933C32.2402 28.7725 32.5435 29.0947 32.9416 29.0947Z" fill="#0068FF"></path>
                            <path d="M25.814 19.6924C23.1979 19.6924 21.0747 21.8156 21.0747 24.4317C21.0747 27.0478 23.1979 29.171 25.814 29.171C28.4301 29.171 30.5533 27.0478 30.5533 24.4317C30.5723 21.8156 28.4491 19.6924 25.814 19.6924ZM25.814 27.2184C24.2785 27.2184 23.0273 25.9672 23.0273 24.4317C23.0273 22.8962 24.2785 21.645 25.814 21.645C27.3495 21.645 28.6007 22.8962 28.6007 24.4317C28.6007 25.9672 27.3685 27.2184 25.814 27.2184Z" fill="#0068FF"></path>
                            <path d="M40.4867 19.6162C37.8516 19.6162 35.7095 21.7584 35.7095 24.3934C35.7095 27.0285 37.8516 29.1707 40.4867 29.1707C43.1217 29.1707 45.2639 27.0285 45.2639 24.3934C45.2639 21.7584 43.1217 19.6162 40.4867 19.6162ZM40.4867 27.2181C38.9322 27.2181 37.681 25.9669 37.681 24.4124C37.681 22.8579 38.9322 21.6067 40.4867 21.6067C42.0412 21.6067 43.2924 22.8579 43.2924 24.4124C43.2924 25.9669 42.0412 27.2181 40.4867 27.2181Z" fill="#0068FF"></path>
                            <path d="M29.4562 29.0944H30.5747V19.957H28.6221V28.2793C28.6221 28.7153 29.0012 29.0944 29.4562 29.0944Z" fill="#0068FF"></path>
                        </svg>
                    </a>
                @endif
                @if(!empty($settings->contact_phone))
                    <a href="tel:{{ preg_replace('/[^0-9]/', '', $settings->contact_phone) }}" class="bt-social-circle bt-social-phone" title="Hotline: {{ $settings->contact_phone }}" aria-label="Gọi điện {{ $settings->contact_phone }}">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                    </a>
                @endif
                @if(!empty($settings->contact_email))
                    <a href="mailto:{{ $settings->contact_email }}" class="bt-social-circle bt-social-mail" title="Email: {{ $settings->contact_email }}" aria-label="Email {{ $settings->contact_email }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 119.45 122.88" fill="currentColor"><title>email-address</title><path d="M0,58.68V53.56L39.82,88.38.52,119.18A6.14,6.14,0,0,1,0,116.71v-58Zm82-6a24,24,0,0,1-1,6.19,15.32,15.32,0,0,1-2.48,4.85,10.89,10.89,0,0,1-3.83,3.18A11.09,11.09,0,0,1,69.83,68a8.69,8.69,0,0,1-2.13-.25A7.6,7.6,0,0,1,65.79,67a6.7,6.7,0,0,1-1.58-1.21,6.8,6.8,0,0,1-.69-.82,8.54,8.54,0,0,1-6.69,2.92,8,8,0,0,1-2.52-.39,7.53,7.53,0,0,1-2.19-1.19l0,0a7.89,7.89,0,0,1-1.71-1.88,10.11,10.11,0,0,1-1.19-2.52v0a15.7,15.7,0,0,1-.55-6.27v0a23.36,23.36,0,0,1,1.46-5.95,16.59,16.59,0,0,1,2.83-4.71,12.06,12.06,0,0,1,4-3.09,11.11,11.11,0,0,1,4.79-1,14.3,14.3,0,0,1,3.67.45,11.15,11.15,0,0,1,3.17,1.39l1.63,1.09a.65.65,0,0,1,.28.59L69.11,59.36a3.29,3.29,0,0,0,.29,2.22,1.75,1.75,0,0,0,1.54.61,2.63,2.63,0,0,0,1.61-.56A5.63,5.63,0,0,0,74,59.74a12,12,0,0,0,1.09-3,20.78,20.78,0,0,0,.47-3.83,24.13,24.13,0,0,0-.65-7.4,13.59,13.59,0,0,0-2.76-5.39,11.76,11.76,0,0,0-4.67-3.32A17.51,17.51,0,0,0,61,35.68a16.06,16.06,0,0,0-4.45.6,14.57,14.57,0,0,0-3.91,1.79A15.31,15.31,0,0,0,49.32,41a18.56,18.56,0,0,0-2.54,3.93,24,24,0,0,0-1.69,4.76,29.77,29.77,0,0,0-.74,5.42h0A25.78,25.78,0,0,0,45,62.81a14,14,0,0,0,2.77,5.57,11.75,11.75,0,0,0,4.76,3.38,18.42,18.42,0,0,0,6.77,1.15,19.82,19.82,0,0,0,2.16-.12c.74-.08,1.51-.21,2.28-.37s1.48-.33,2.12-.52a15.05,15.05,0,0,0,1.7-.61.64.64,0,0,1,.84.34.55.55,0,0,1,0,.17L69.41,76a.67.67,0,0,1-.28.68,10.53,10.53,0,0,1-2,1,17.2,17.2,0,0,1-2.47.76,23.7,23.7,0,0,1-2.73.49,23.18,23.18,0,0,1-2.75.16,24.74,24.74,0,0,1-12.17-2.77,16.89,16.89,0,0,1-4.39-3.45,18.16,18.16,0,0,1-3.09-4.79l0,0a24,24,0,0,1-1.73-5.95A35.46,35.46,0,0,1,37.41,55a32.6,32.6,0,0,1,1-6.86,28,28,0,0,1,2.36-6.12A23.13,23.13,0,0,1,44.37,37a21.6,21.6,0,0,1,4.74-3.82,22.27,22.27,0,0,1,5.65-2.37A24.52,24.52,0,0,1,61.07,30a22.8,22.8,0,0,1,11.5,2.77,17.53,17.53,0,0,1,4.28,3.4,18.32,18.32,0,0,1,3,4.59,22.32,22.32,0,0,1,1.76,5.6A28.62,28.62,0,0,1,82,52.71Zm-25.7,3a15.3,15.3,0,0,0,0,2.79,5.85,5.85,0,0,0,.48,1.9,2.42,2.42,0,0,0,.83,1,2.13,2.13,0,0,0,1.19.32,2.1,2.1,0,0,0,.89-.2,3.08,3.08,0,0,0,.87-.67,5.64,5.64,0,0,0,.83-1.17,10.13,10.13,0,0,0,.69-1.58l1-11-.17,0a5.62,5.62,0,0,0-.74,0,5,5,0,0,0-2.25.49A4.51,4.51,0,0,0,58.27,49a9.45,9.45,0,0,0-1.2,2.7,23.81,23.81,0,0,0-.73,4ZM.15,43.47C1,40.14,5.72,38,8.34,36.14V21.85a6,6,0,0,1,6-6H36.81L58.26.57A3.24,3.24,0,0,1,62,.59L83.08,15.85h22a6,6,0,0,1,6,6V36.1c2.82,2,8.34,4.72,8.34,8.49v.15L105.11,56.83v-35H14.34v34L.15,43.47Zm119.3,11.31v61.93a6.12,6.12,0,0,1-1.42,3.93L78.34,89.43l41.11-34.65Zm-11,68.1H8.26L45.71,93.53l11.15,9.75a3.83,3.83,0,0,0,5.15-.09L72.33,94.5l36.09,28.38Z"/></svg>
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
