@extends('clients.layouts.master')

@section('title', $settings->site_title ?? 'Thiết bị công nghiệp và tự động hóa')

@section('head')
    <link rel="stylesheet" href="{{ asset('clients/assets/css/home.css') }}">

    @php
        $pageTitle = $settings->site_title ?? 'Thiết bị công nghiệp và tự động hóa';
        $pageDescription =
            $settings->site_description ?? 'Nhà cung cấp thiết bị tự động hóa công nghiệp hàng đầu Việt Nam';
        $pageKeywords = $settings->seo_keywords ?? 'thiết bị tự động hóa, công nghiệp, cảm biến, Bảo Tín JSC';
        $canonicalUrl = url('/');
        $pageImage =
            $settings->site_logo ?? null
                ? asset('clients/assets/img/business/' . $settings->site_logo)
                : asset('clients/assets/img/business/logo.webp');
    @endphp

    <!-- Meta Tags -->
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="keywords" content="{{ $pageKeywords }}">
    <meta name="robots" content="index, follow, max-snippet:-1, max-video-preview:-1, max-image-preview:large" />

    <!-- 📅 Date -->
    <meta http-equiv="date" content="{{ now()->format('d/m/Y') }}" />

    <!-- 🌐 Open Graph -->
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $pageImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $pageTitle }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $settings->site_name ?? ($settings->subname ?? 'Bảo Tín JSC') }}">
    <meta property="og:locale" content="vi_VN">

    <!-- 🐦 Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $pageImage }}">
    <meta name="twitter:creator" content="{{ $settings->site_name ?? ($settings->subname ?? 'Bảo Tín JSC') }}">

    <!-- 🔗 Canonical & hreflang -->
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="alternate" hreflang="vi" href="{{ $canonicalUrl }}">
    <link rel="alternate" hreflang="x-default" href="{{ $canonicalUrl }}">
@endsection

@section('schema')
    @php
        // WebSite Schema
        $websiteSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $settings->site_name ?? 'Bảo Tín JSC',
            'url' => url('/'),
            'description' =>
                $settings->site_description ?? 'Nhà cung cấp thiết bị tự động hóa công nghiệp hàng đầu Việt Nam',
            'inLanguage' => 'vi-VN',
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => url('/cua-hang?keyword={search_term_string}'),
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];

        if ($settings->site_logo ?? null) {
            $websiteSchema['logo'] = asset('clients/assets/img/business/' . $settings->site_logo);
        }

        // WebPage Schema
        $webPageSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $pageTitle,
            'description' => $pageDescription,
            'url' => $canonicalUrl,
            'inLanguage' => 'vi-VN',
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => $settings->site_name ?? 'Bảo Tín JSC',
                'url' => url('/'),
            ],
            'breadcrumb' => [
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => 'Trang chủ',
                        'item' => url('/'),
                    ],
                ],
            ],
        ];

        if ($pageImage) {
            $webPageSchema['image'] = $pageImage;
        }

        if ($settings->site_name ?? null) {
            $webPageSchema['publisher'] = [
                '@type' => 'Organization',
                'name' => $settings->site_name ?? 'Bảo Tín JSC',
                'url' => url('/'),
            ];
        }

        // Organization Schema
        $organizationSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $settings->site_name ?? 'Bảo Tín JSC',
            'url' => url('/'),
            'logo' =>
                $settings->site_logo ?? null ? asset('clients/assets/img/business/' . $settings->site_logo) : null,
            'description' =>
                $settings->site_description ?? 'Nhà cung cấp thiết bị tự động hóa công nghiệp hàng đầu Việt Nam',
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => 'VN',
                'addressLocality' => $settings->contact_address ?? null,
            ],
            'contactPoint' => [],
        ];

        // Thêm contact phone nếu có
        if ($settings->contact_phone ?? null) {
            $organizationSchema['contactPoint'][] = [
                '@type' => 'ContactPoint',
                'telephone' => $settings->contact_phone ?? '',
                'contactType' => 'customer service',
                'areaServed' => 'VN',
                'availableLanguage' => 'Vietnamese',
            ];
        }

        // Thêm email nếu có
        if ($settings->contact_email ?? null) {
            $organizationSchema['email'] = $settings->contact_email ?? '';
        }

        // Thêm social media nếu có
        $sameAs = [];
        if ($settings->facebook_link ?? null) {
            $sameAs[] = $settings->facebook_link ?? '';
        }
        if ($settings->twitter_link ?? null) {
            $sameAs[] = $settings->twitter_link ?? '';
        }
        if ($settings->instagram_link ?? null) {
            $sameAs[] = $settings->instagram_link ?? '';
        }
        if (!empty($sameAs)) {
            $organizationSchema['sameAs'] = array_filter($sameAs);
        }

        // Xóa các field null
        $organizationSchema = array_filter($organizationSchema, function ($value) {
            return $value !== null && $value !== '';
        });

        // Xử lý address nếu có field null
        if (isset($organizationSchema['address'])) {
            $organizationSchema['address'] = array_filter($organizationSchema['address'], function ($value) {
                return $value !== null && $value !== '';
            });
            if (empty($organizationSchema['address'])) {
                unset($organizationSchema['address']);
            }
        }

        // Xử lý contactPoint nếu rỗng
        if (empty($organizationSchema['contactPoint'])) {
            unset($organizationSchema['contactPoint']);
        }
            @endphp
    <script type="application/ld+json">
        {!! json_encode($websiteSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    <script type="application/ld+json">
        {!! json_encode($webPageSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    <script type="application/ld+json">
        {!! json_encode($organizationSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
            </script>
@endsection

@section('content')
    <!-- Banner Slider -->
    <section class="banner-section">
        <div class="container">
            <div class="banner-grid">
                @if ($banners_home_parent->isNotEmpty())
                    @foreach ($banners_home_parent->take(1) as $banner)
                        <div class="banner-item large">
                            <a href="{{ $banner->link ?? '#' }}">
                                <img src="{{ asset('clients/assets/img/banners/' . ($banner->image_desktop ?? 'no-image.webp')) }}"
                                    alt="{{ $banner->title ?? 'Banner' }}">
                                @if ($banner->title)
                                    <div class="banner-label red">{{ $banner->title }}</div>
                                @endif
                    </a>
                </div>
                    @endforeach
                @endif
                @if ($banners_home_children->isNotEmpty())
                    @foreach ($banners_home_children->take(2) as $banner)
                        <div class="banner-item">
                            <a href="{{ $banner->link ?? '#' }}">
                                <img src="{{ asset('clients/assets/img/banners/' . ($banner->image_desktop ?? 'no-image.webp')) }}"
                                    alt="{{ $banner->title ?? 'Banner' }}">
                                @if ($banner->title)
                                    <div class="banner-label">{{ $banner->title }}</div>
                                @endif
                            </a>
                                        </div>
                    @endforeach
                @endif
                                            </div>
                                            </div>
    </section>

    <!-- Products Section -->
    <section class="products-section">
        <div class="container">
            <!-- Motor Types -->
            @if ($categoriesForTabs->isNotEmpty())
                <div class="motor-types">
                    @foreach ($categoriesForTabs->take(3) as $category)
                        <div class="motor-item">
                            <a href="/{{ $category->slug ?? '#' }}">
                                @if ($category->image)
                                    <img src="{{ asset('clients/assets/img/categories/' . $category->image) }}"
                                        alt="{{ $category->name }}">
                                            @else
                                    <img src="https://placehold.co/600x400" alt="{{ $category->name }}">
                                            @endif
                                <h3>{{ $category->name }}</h3>
                            </a>
                                        </div>
                        @endforeach
                    </div>
        @endif

            <!-- Sidebar and Products Grid -->
            <div class="content-grid">
                <aside class="sidebar">
                    <div class="sidebar-box categories">
                        <h3><i class="fas fa-bars"></i> DANH MỤC SẢN PHẨM</h3>
                        <ul class="home_category-list">
                            <li class="home_category-item">
                                <a href="{{ route('client.shop.index') }}" class="home_category-link">
                                    Tất Cả Sản Phẩm
                                </a>
                        </li>
                            @if (isset($categories) && $categories->isNotEmpty())
                                @foreach ($categories->take(7) as $cat)
                                    <li
                                        class="home_category-item {{ $cat->children->isNotEmpty() ? 'has-children' : '' }}">
                                        @if ($cat->children->isNotEmpty())
                                            <div class="home_category-row">
                                                <i class="fas fa-chevron-right home_category-toggle"></i>
                                                <a href="/{{ $cat->slug ?? '#' }}"
                                                    class="home_category-link">{{ $cat->name }}</a>
                    </div>
                                            <ul class="home_category-children">
                                                @foreach ($cat->children as $child)
                                                    <li>
                                                        <a href="/{{ $child->slug ?? '#' }}" class="home_category-link">
                                                            <i class="fas fa-chevron-right"></i> {{ $child->name }}
                                                        </a>
                                                    </li>
                        @endforeach
                                            </ul>
                                        @else
                                            <a href="/{{ $cat->slug ?? '#' }}"
                                                class="home_category-link">{{ $cat->name }}</a>
                                        @endif
                                    </li>
                    @endforeach
                            @endif
                        </ul>
                </div>

                    @if ($vouchers->isNotEmpty())
                        <div class="sidebar-box green">
                            <h3><i class="fas fa-tag"></i> KHUYẾN MÃI</h3>
                            </div>
        @endif
                </aside>

                <main class="main-content">
                    @if ($productsFeatured->isNotEmpty())
                        <!-- Featured Products Section -->
                        <div class="product-category">
                            <div class="category-header">
                                <h2><i class="fas fa-list"></i> SẢN PHẨM NỔI BẬT</h2>
                                <a href="{{ route('client.shop.index') }}" class="view-all">Xem Tất Cả <i
                                        class="fas fa-chevron-right"></i></a>
                    </div>
                            <div class="product-grid">
                                @foreach ($productsFeatured->take(4) as $product)
                                    @php
                                        $hasVariants = $product->hasVariants() && $product->variants->count() > 0;
                                        $variantsData = $hasVariants
                                            ? $product->variants
                                                ->map(function ($variant) {
                                                    $variantStock = $variant->stock_quantity;
                                                    $variantInStock = $variantStock === null || $variantStock > 0;
                                                    return [
                                                        'id' => $variant->id,
                                                        'name' => $variant->name,
                                                        'price' => $variant->price,
                                                        'sale_price' => $variant->sale_price ?? null,
                                                        'stock_quantity' => $variantStock,
                                                        'in_stock' => $variantInStock,
                                                        'image' => $variant->primaryVariantImage
                                                            ? asset(
                                                                'clients/assets/img/clothes/' .
                                                                    $variant->primaryVariantImage->url,
                                                            )
                                                            : ($variant->product->primaryImage
                                                                ? asset(
                                                                    'clients/assets/img/clothes/' .
                                                                        $variant->product->primaryImage->url,
                                                                )
                                                                : ''),
                                                    ];
                                                })
                                                ->toJson()
                                            : '[]';
                                    @endphp
                                    <div class="product-card" data-product-id="{{ $product->id }}"
                                                    data-product-name="{{ $product->name }}"
                                        data-product-slug="{{ $product->slug ?? '#' }}"
                                        data-has-variants="{{ $hasVariants ? '1' : '0' }}"
                                        data-variants='{{ $variantsData }}'>
                                        <a href="/{{ $product->slug ?? '#' }}">
                                            @if ($product->primaryImage && $product->primaryImage->url)
                                                <img src="{{ asset('clients/assets/img/clothes/' . $product->primaryImage->url) }}"
                                                    alt="{{ $product->name }}">
                                            @else
                                                <img src="{{ asset('clients/assets/img/clothes/no-image.webp') }}"
                                                    alt="{{ $product->name }}">
                                            @endif
                                            <h4>{{ $product->name }}</h4>
                                            <p class="price">
                                                @if ($product->price)
                                                    {{ number_format($product->price, 0, ',', '.') }} đ
                                                @else
                                                    Liên hệ {{ $settings->contact_phone ?? '0902 223 663' }}
                                                @endif
                                            </p>
                                            <a href="/{{ $product->slug ?? '#' }}" class="quick-view">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </a>
                                        <div class="product-card-actions">
                                            <button class="btn-add-cart" data-product-id="{{ $product->id }}"
                                                data-product-slug="{{ $product->slug ?? '#' }}"
                                                title="Thêm vào giỏ hàng">
                                                <i class="fas fa-shopping-cart"></i>
                                            </button>
                                            <button class="btn-favorite-home" data-product-id="{{ $product->id }}"
                                                data-wishlist-add="{{ route('client.product.wishlist.add') }}"
                                                data-wishlist-remove="{{ route('client.product.wishlist.remove') }}"
                                                title="Yêu thích">
                                                <i class="fas fa-heart"></i>
                                            </button>
                                            <button class="btn-compare-home" data-product-id="{{ $product->id }}"
                                                title="Thêm vào so sánh">
                                                <i class="fas fa-balance-scale"></i>
                                            </button>
                                </div>
                            </div>
                        @endforeach
                </div>
            </div>
                    @endif

                    @if ($categoriesForTabs->isNotEmpty() && isset($categoryProducts))
                        @foreach ($categoriesForTabs->skip(1)->take(2) as $category)
                            @if (isset($categoryProducts[$category->id]) && $categoryProducts[$category->id]->isNotEmpty())
                                <div class="product-category">
                                    <div class="category-header green-bg">
                                        <h2><i class="fas fa-cog"></i> {{ strtoupper($category->name) }}</h2>
                                        <a href="/{{ $category->slug ?? '' }}" class="view-all white">Xem Tất Cả <i
                                                class="fas fa-chevron-right"></i></a>
                </div>
                                    <div class="product-grid">
                                        @foreach ($categoryProducts[$category->id]->take(4) as $product)
                                            @php
                                                $hasVariants =
                                                    $product->hasVariants() && $product->variants->count() > 0;
                                                $variantsData = $hasVariants
                                                    ? $product->variants
                                                        ->map(function ($variant) {
                                                            $variantStock = $variant->stock_quantity;
                                                            $variantInStock =
                                                                $variantStock === null || $variantStock > 0;
                                                            return [
                                                                'id' => $variant->id,
                                                                'name' => $variant->name,
                                                                'price' => $variant->price,
                                                                'sale_price' => $variant->sale_price ?? null,
                                                                'stock_quantity' => $variantStock,
                                                                'in_stock' => $variantInStock,
                                                                'image' => $variant->primaryVariantImage
                                                                    ? asset(
                                                                        'clients/assets/img/clothes/' .
                                                                            $variant->primaryVariantImage->url,
                                                                    )
                                                                    : ($variant->product->primaryImage
                                                                        ? asset(
                                                                            'clients/assets/img/clothes/' .
                                                                                $variant->product->primaryImage->url,
                                                                        )
                                                                        : ''),
                                                            ];
                                                        })
                                                        ->toJson()
                                                    : '[]';
                                        @endphp
                                            <div class="product-card" data-product-id="{{ $product->id }}"
                                                            data-product-name="{{ $product->name }}"
                                                data-product-slug="{{ $product->slug ?? '#' }}"
                                                data-has-variants="{{ $hasVariants ? '1' : '0' }}"
                                                data-variants="{{ htmlspecialchars($variantsData, ENT_QUOTES, 'UTF-8') }}">
                                                <a href="/{{ $product->slug ?? '#' }}">
                                                    @if ($product->primaryImage && $product->primaryImage->url)
                                                        <img src="{{ asset('clients/assets/img/clothes/' . $product->primaryImage->url) }}"
                                                            alt="{{ $product->name }}">
                                                    @else
                                                        <img src="{{ asset('clients/assets/img/clothes/no-image.webp') }}"
                                                            alt="{{ $product->name }}">
                                                    @endif
                                                    <h4>{{ $product->name }}</h4>
                                                    <p class="price">
                                                        @if ($product->price)
                                                            {{ number_format($product->price, 0, ',', '.') }} đ
                                @else
                                                            Liên hệ {{ $settings->contact_phone ?? '0902 223 663' }}
                                @endif
                                                    </p>
                                                    <a href="/{{ $product->slug ?? '#' }}" class="quick-view">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </a>
                                                <div class="product-card-actions">
                                                    <button class="btn-add-cart" data-product-id="{{ $product->id }}"
                                                        data-product-slug="{{ $product->slug ?? '#' }}"
                                                        title="Thêm vào giỏ hàng">
                                                        <i class="fas fa-shopping-cart"></i>
                                                    </button>
                                                    <button class="btn-favorite-home"
                                                        data-product-id="{{ $product->id }}"
                                                        data-wishlist-add="{{ route('client.product.wishlist.add') }}"
                                                        data-wishlist-remove="{{ route('client.product.wishlist.remove') }}"
                                                        title="Yêu thích">
                                                        <i class="fas fa-heart"></i>
                                                    </button>
                                                    <button class="btn-compare-home" data-product-id="{{ $product->id }}"
                                                        title="Thêm vào so sánh">
                                                        <i class="fas fa-balance-scale"></i>
                                                    </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
                        @endforeach
                                        @endif

                    <!-- Brands Section -->
                    @if ($partners->isNotEmpty())
                        <div class="brands-section">
                            <div class="category-header green-bg">
                                <h2><i class="fas fa-award"></i> ĐỐI TÁC CỦA
                                    {{ strtoupper($settings->site_name ?? 'BẢO TÍN JSC') }}</h2>
                                        </div>
                            <div class="brands-grid">
                                <div class="brand-logos">
                                    @foreach ($partners->take(8) as $partner)
                                        <img src="{{ asset($partner->image ?? 'no-image.webp') }}"
                                            alt="{{ $partner->name }}">
                            @endforeach
                    </div>
                </div>
            </div>
                    @endif

                    <!-- Installation Section -->
                    <div class="installation-section">
                        <div class="category-header blue-bg">
                            <h2><i class="fas fa-tools"></i> DỊCH VỤ KHẢO SÁT VÀ TƯ VẤN CHUYÊN NGHIỆP</h2>
                    </div>
                        <div class="installation-preview">
                            @if ($banners_home_parent->isNotEmpty() && $banners_home_parent->first()->image_desktop)
                                <img src="{{ asset('clients/assets/img/banners/' . $banners_home_parent->first()->image_desktop) }}"
                                    alt="Dịch vụ khảo sát">
                            @else
                                <img src="https://via.placeholder.com/500x300/3498db/ffffff?text=DỊCH+VỤ+KHẢO+SÁT"
                                    alt="Dịch vụ khảo sát">
                            @endif
                            <div class="installation-overlay">
                                <p>DỊCH VỤ KHẢO SÁT VÀ TƯ VẤN CHUYÊN NGHIỆP</p>
                    </div>
                    </div>
                    </div>

                    <!-- News Section -->
                    @php
                        $recentPosts = \App\Models\Post::published()->orderBy('created_at', 'desc')->take(3)->get();
                    @endphp
                    @if ($recentPosts->isNotEmpty())
                        <div class="news-section">
                            <div class="category-header">
                                <h2><i class="fas fa-newspaper"></i> TIN TỨC</h2>
                                <a href="{{ route('client.blog.index') }}" class="view-all">Xem thêm <i
                                        class="fas fa-chevron-right"></i></a>
                    </div>
                            <div class="news-grid">
                                @foreach ($recentPosts as $post)
                                    <div class="news-card">
                                        <a href="/tu-dong-hoa/{{ $post->slug ?? '' }}">
                                            <img src="{{ asset('clients/assets/img/posts/' . ($post->image ?? 'no-image.webp')) }}"
                                                alt="{{ $post->title ?? 'Tin tức' }}">
                                            <div class="news-content">
                                                <h4>{{ Str::limit($post->title ?? 'Tin tức', 50) }}</h4>
                                                <div class="news-meta">
                                                    <span><i class="far fa-calendar"></i>
                                                        {{ $post->created_at->format('d/m/Y') ?? 'Đọc bí' }}</span>
                </div>
            </div>
                                        </a>
                                </div>
                            @endforeach
                                </div>
                        </div>
                    @endif
                </main>
                    </div>
                </div>
            </section>


    <!-- Variant Selection Modal -->
    <div id="variantModal" class="variant-modal">
        <div class="variant-modal-overlay"></div>
        <div class="variant-modal-content">
            <button class="variant-modal-close">&times;</button>
            <h3 class="variant-modal-title">Chọn biến thể sản phẩm</h3>
            <div class="variant-modal-product-info">
                <img id="variantModalProductImage" src="" alt="">
                <div>
                    <h4 id="variantModalProductName"></h4>
                    </div>
                    </div>
            <div class="variant-modal-variants">
                <p class="variant-modal-label">Vui lòng chọn biến thể:</p>
                <div id="variantModalVariantsList" class="variant-list"></div>
                </div>
            <div class="variant-modal-actions">
                <button class="variant-modal-btn-cancel">Hủy</button>
                <button class="variant-modal-btn-add" disabled>Thêm vào giỏ hàng</button>
                </div>
                    </div>
                </div>
@endsection

    @section('foot')
    <script src="{{ asset('clients/assets/js/home.js') }}"></script>
    <script>
        // Pass routes to JavaScript
        window.cartStoreRoute = '{{ route('client.cart.store') }}';
    </script>
    @endsection
