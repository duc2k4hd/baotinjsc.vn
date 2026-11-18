@extends('clients.layouts.master')

@php
    $companyName = $settings->site_name ?? ($settings->subname ?? 'Bảo Tín JSC');
    $homeTitle = !empty($settings->site_title)
        ? (str_contains($settings->site_title, $companyName) ? $settings->site_title : ($settings->site_title . ' - ' . $companyName))
        : ('Nhà Phân Phối Thiết Bị Tự Động Hóa Chính Hãng - ' . $companyName);

    if (!function_exists('getCategoryFaIcon')) {
        function getCategoryFaIcon($slug) {
            if (str_contains($slug, 'cam-bien')) return 'fa-satellite-dish';
            if (str_contains($slug, 'bien-tan')) return 'fa-gauge-high';
            if (str_contains($slug, 'plc')) return 'fa-microchip';
            if (str_contains($slug, 'hmi')) return 'fa-desktop';
            if (str_contains($slug, 'ro-le')) return 'fa-wave-square';
            if (str_contains($slug, 'aptomat')) return 'fa-toggle-on';
            if (str_contains($slug, 'bien-dong')) return 'fa-bolt';
            if (str_contains($slug, 'bo-dem')) return 'fa-stopwatch';
            if (str_contains($slug, 'bo-dieu-khien')) return 'fa-sliders';
            if (str_contains($slug, 'bo-khuech-dai')) return 'fa-volume-high';
            if (str_contains($slug, 'bo-loc')) return 'fa-filter';
            if (str_contains($slug, 'bo-nguon')) return 'fa-plug';
            if (str_contains($slug, 'khoi-dong-mem')) return 'fa-forward-step';
            if (str_contains($slug, 'contactor')) return 'fa-magnet';
            if (str_contains($slug, 'encoder')) return 'fa-compass';
            if (str_contains($slug, 'day-dien')) return 'fa-bezier-curve';
            return 'fa-shapes';
        }
    }
@endphp

@section('title', $homeTitle)

@section('head')
    <link rel="stylesheet" href="{{ asset('clients/assets/css/home.css') }}?v={{ file_exists(public_path('clients/assets/css/home.css')) ? filemtime(public_path('clients/assets/css/home.css')) : time() }}">

    @php
        $siteTitle = $homeTitle;
        $siteDesc = $settings->site_description ?? 'Bảo Tín JSC – Đơn vị cung cấp giải pháp và thiết bị tự động hóa công nghiệp hàng đầu Việt Nam: Biến tần, PLC, Cảm biến, Màn hình HMI, Khởi động mềm, Servo chính hãng.';
        $siteKeywords = $settings->seo_keywords ?? 'Bảo Tín JSC, thiết bị tự động hóa, biến tần Mitsubishi, biến tần Schneider, PLC Panasonic, PLC Mitsubishi, cảm biến Omron, màn hình HMI, khởi động mềm';
        $siteUrl = url('/');
        $ogImageUrl = !empty($settings->site_logo)
            ? asset('clients/assets/img/business/' . $settings->site_logo)
            : asset('clients/assets/img/business/logo.webp');
        $hotline = $settings->contact_hotline ?? ($settings->contact_phone ?? '0985214595');
        $email = $settings->contact_email ?? 'info@baotinjsc.vn';
        $address = $settings->contact_address ?? 'Hà Nội, Việt Nam';

        $firstHeroBanner = $banners->get('1', collect())->first();
        $firstHeroBannerUrl = null;
        if ($firstHeroBanner && $firstHeroBanner->image_desktop) {
            $firstHeroBannerUrl = Str::startsWith($firstHeroBanner->image_desktop, ['http://', 'https://'])
                ? $firstHeroBanner->image_desktop
                : asset('clients/assets/img/banners/' . $firstHeroBanner->image_desktop);
        }
    @endphp

    {{-- LCP Image Preload for Instant First Paint --}}
    @if($firstHeroBannerUrl)
        <link rel="preload" as="image" href="{{ $firstHeroBannerUrl }}" fetchpriority="high">
    @endif

    {{-- Canonical & Language Alternates --}}
    <link rel="canonical" href="{{ $siteUrl }}">
    <link rel="alternate" hreflang="vi" href="{{ $siteUrl }}">
    <link rel="alternate" hreflang="x-default" href="{{ $siteUrl }}">

    {{-- SEO Meta Tags --}}
    <meta name="robots" content="index, follow, max-snippet:-1, max-video-preview:-1, max-image-preview:large">
    <meta name="description" content="{{ $siteDesc }}">
    <meta name="keywords" content="{{ $siteKeywords }}">

    {{-- OpenGraph Tags --}}
    <meta property="og:title" content="{{ $siteTitle }}">
    <meta property="og:description" content="{{ $siteDesc }}">
    <meta property="og:url" content="{{ $siteUrl }}">
    <meta property="og:image" content="{{ $ogImageUrl }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $companyName }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $companyName }}">
    <meta property="og:locale" content="vi_VN">

    {{-- Twitter Card Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $siteTitle }}">
    <meta name="twitter:description" content="{{ $siteDesc }}">
    <meta name="twitter:image" content="{{ $ogImageUrl }}">
@endsection

@section('schema')
    @php
        $schemaHome = [
            "@context" => "https://schema.org",
            "@graph" => [
                [
                    "@type" => "Organization",
                    "@id" => $siteUrl . "#organization",
                    "name" => $companyName,
                    "url" => $siteUrl,
                    "logo" => $ogImageUrl,
                    "email" => $email,
                    "address" => [
                        "@type" => "PostalAddress",
                        "streetAddress" => $address,
                        "addressLocality" => "Hà Nội",
                        "addressRegion" => "Hà Nội",
                        "addressCountry" => "VN"
                    ],
                    "contactPoint" => [
                        [
                            "@type" => "ContactPoint",
                            "telephone" => $hotline,
                            "contactType" => "customer service"
                        ]
                    ]
                ],
                [
                    "@type" => "WebSite",
                    "@id" => $siteUrl . "#website",
                    "url" => $siteUrl,
                    "name" => $companyName,
                    "publisher" => [
                        "@id" => $siteUrl . "#organization"
                    ],
                    "potentialAction" => [
                        "@type" => "SearchAction",
                        "target" => route('client.shop.index') . "?q={search_term_string}",
                        "query-input" => "required name=search_term_string"
                    ]
                ],
                [
                    "@type" => "WebPage",
                    "@id" => $siteUrl . "#webpage",
                    "url" => $siteUrl,
                    "name" => $siteTitle,
                    "description" => $siteDesc,
                    "inLanguage" => "vi",
                    "isPartOf" => [
                        "@id" => $siteUrl . "#website"
                    ]
                ]
            ]
        ];
    @endphp
    <script type="application/ld+json">
        {!! json_encode($schemaHome, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('content')
    {{-- HERO SLIDER: Full-width 16:9, quản lý qua Admin > Banner (position = 1) --}}
    @php
        $heroBanners      = $banners->get('1', collect());
        $heroCount        = $heroBanners->count();
        $heroShowCaption  = !empty($settings->hero_show_caption); // mặc định false = ảnh thuần
    @endphp
    @if ($heroCount > 0)
    <section class="btjsc-hero" aria-label="Banner quảng cáo chính">
        <div class="btjsc-hero__track" id="btjscHeroTrack" role="list" aria-live="polite">
            @foreach ($heroBanners as $idx => $banner)
                @php
                    $bImg = $banner->image_desktop;
                    $bUrl = Str::startsWith($bImg, ['http://', 'https://']) ? $bImg : asset('clients/assets/img/banners/' . $bImg);
                    $bMob = $banner->image_mobile;
                    $bMobUrl = $bMob ? (Str::startsWith($bMob, ['http://', 'https://']) ? $bMob : asset('clients/assets/img/banners/' . $bMob)) : $bUrl;
                @endphp
                <div class="btjsc-hero__slide{{ $idx === 0 ? ' btjsc-hero__slide--active' : '' }}"
                     role="listitem"
                     aria-label="Slide {{ $idx + 1 }} / {{ $heroCount }}"
                     aria-hidden="{{ $idx === 0 ? 'false' : 'true' }}">
                    @if ($banner->link)
                        <a href="{{ $banner->link }}" target="{{ $banner->target ?? '_self' }}" tabindex="{{ $idx === 0 ? '0' : '-1' }}" aria-label="{{ $banner->title }}">
                    @endif
                        <picture>
                            <source media="(max-width: 639px)" srcset="{{ $bMobUrl }}">
                            <img src="{{ $bUrl }}"
                                 alt="{{ $banner->title }}"
                                 width="1920" height="1080"
                                 @if ($idx === 0) fetchpriority="high" loading="eager" @else loading="lazy" @endif
                                 decoding="async"
                                 class="btjsc-hero__img">
                        </picture>
                        @if (!empty($banner->title) || !empty($banner->description))
                            @if ($heroShowCaption)
                            <div class="btjsc-hero__caption">
                                @if (!empty($banner->title))
                                    <h2 class="btjsc-hero__title">{{ $banner->title }}</h2>
                                @endif
                                @if (!empty($banner->description))
                                    <p class="btjsc-hero__desc">{{ $banner->description }}</p>
                                @endif
                                @if ($banner->link)
                                    <span class="btjsc-hero__btn">Xem ngay</span>
                                @endif
                            </div>
                            @endif
                        @endif
                    @if ($banner->link)
                        </a>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Prev / Next --}}
        @if ($heroCount > 1)
            <button class="btjsc-hero__nav btjsc-hero__nav--prev" id="btjscHeroPrev" aria-label="Slide trước" type="button">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <button class="btjsc-hero__nav btjsc-hero__nav--next" id="btjscHeroNext" aria-label="Slide tiếp" type="button">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
            </button>

            {{-- Dots --}}
            <div class="btjsc-hero__dots" role="tablist" aria-label="Chọn slide">
                @foreach ($heroBanners as $idx => $banner)
                    <button class="btjsc-hero__dot{{ $idx === 0 ? ' btjsc-hero__dot--active' : '' }}"
                            role="tab"
                            type="button"
                            aria-selected="{{ $idx === 0 ? 'true' : 'false' }}"
                            aria-label="Slide {{ $idx + 1 }}"
                            data-btjsc-slide="{{ $idx }}"></button>
                @endforeach
            </div>
        @endif
    </section>
    @endif

    {{-- TRUST BAR --}}
    <div class="btjsc-trust" aria-label="Cam kết của chúng tôi">
        <div class="misutech_home_container btjsc-trust__grid">
            <div class="btjsc-trust__item">
                <span class="btjsc-trust__icon" style="background:#EFF6FF;color:#2563EB"><i class="fa-solid fa-truck-fast"></i></span>
                <div><strong>Giao hàng nhanh</strong><span>Vận chuyển toàn quốc</span></div>
            </div>
            <div class="btjsc-trust__item">
                <span class="btjsc-trust__icon" style="background:#ECFDF5;color:#059669"><i class="fa-solid fa-shield-halved"></i></span>
                <div><strong>100% Chính hãng</strong><span>CO / CQ đầy đủ</span></div>
            </div>
            <div class="btjsc-trust__item">
                <span class="btjsc-trust__icon" style="background:#FFFBEB;color:#D97706"><i class="fa-solid fa-headset"></i></span>
                <div><strong>Kỹ sư tư vấn 24/7</strong><span>Hotline: 0985 214 595</span></div>
            </div>
            <div class="btjsc-trust__item">
                <span class="btjsc-trust__icon" style="background:#F5F3FF;color:#7C3AED"><i class="fa-solid fa-rotate-left"></i></span>
                <div><strong>Đổi trả dễ dàng</strong><span>Hỗ trợ trong 7 ngày</span></div>
            </div>
        </div>
    </div>

    {{-- QUICK CATEGORY BAR --}}
    <section class="misutech_home_quick" aria-label="Danh mục nhanh">
        <div class="misutech_home_container misutech_home_quick_grid">
            @foreach ($mainCategories->take(9) as $category)
                <a class="misutech_home_quick_item" href="{{ route('client.product.detail', $category->slug) }}"
                    style="text-decoration: none; color: inherit;">
                    <span class="misutech_home_quick_icon">
                        @if (!empty($category->image) && file_exists(public_path('storage/clients/imgs/categories/' . $category->image)))
                            <img src="{{ asset('storage/clients/imgs/categories/' . $category->image) }}" alt="{{ $category->name }}" width="36" height="36" loading="lazy" decoding="async">
                        @else
                            <i class="fa-solid {{ getCategoryFaIcon($category->slug) }}" style="font-size: 24px; color: var(--misutech_home_primary, #2563EB);"></i>
                        @endif
                    </span>
                    {{ $category->name }}
                </a>
            @endforeach
            <a class="misutech_home_quick_item" href="{{ route('client.shop.index') }}"
                style="text-decoration: none; color: inherit;">
                <span class="misutech_home_quick_icon"><i class="fa-solid fa-plus" style="font-size: 20px;"></i></span> Xem thêm
            </a>
        </div>
    </section>

    {{-- FLASH SALE SECTION --}}
    <section class="misutech_home_section" id="misutech_home_flash">
        <div class="misutech_home_container misutech_home_flash_shell">
            <div class="misutech_home_flash_header">
                <h2 class="misutech_home_flash_title">
                    <span class="misutech_home_flash_bolt">ϟ</span> ƯU ĐÃI HÔM NAY
                </h2>
                @php
                    $diffSecs = max(0, (int) now()->diffInSeconds(now()->endOfDay()));
                    $initH = (int) floor($diffSecs / 3600);
                    $initM = (int) floor(($diffSecs % 3600) / 60);
                    $initS = (int) ($diffSecs % 60);
                @endphp
                <div class="misutech_home_countdown">
                    <span>Kết thúc sau</span>
                    <span class="misutech_home_countdown_box" data-countdown="hours">{{ sprintf('%02d', $initH) }}</span>:
                    <span class="misutech_home_countdown_box" data-countdown="minutes">{{ sprintf('%02d', $initM) }}</span>:
                    <span class="misutech_home_countdown_box" data-countdown="seconds">{{ sprintf('%02d', $initS) }}</span>
                </div>
            </div>
            <div class="misutech_home_product_grid">
                @foreach ($flashSaleProducts as $product)
                    @php
                        $pImg = ($product->primaryImage && $product->primaryImage->url)
                            ? asset('clients/assets/img/clothes/' . $product->primaryImage->url)
                            : asset('clients/assets/img/clothes/no-image.webp');
                        $hasSale = ($product->sale_price && $product->price > 0 && $product->sale_price < $product->price);
                        $discountPct = $hasSale ? round((($product->price - $product->sale_price) / $product->price) * 100) : 0;
                    @endphp
                    <article class="misutech_home_product_card" data-product-name="{{ strtolower($product->name) }}" data-id="{{ $product->id }}">
                        <button class="misutech_home_product_favorite" type="button"
                            data-product-id="{{ $product->id }}"
                            aria-label="Thêm {{ $product->name }} vào yêu thích">
                            ♡
                        </button>
                        @if ($hasSale)
                            <span class="misutech_home_product_badge">-{{ $discountPct }}%</span>
                        @endif
                        <div class="misutech_home_product_image">
                            <a href="{{ route('client.product.detail', $product->slug) }}">
                                <img src="{{ $pImg }}"
                                    alt="{{ $product->name }}"
                                    width="220"
                                    height="220"
                                    loading="lazy"
                                    decoding="async" />
                            </a>
                        </div>
                        <h3 class="misutech_home_product_name">
                            <a href="{{ route('client.product.detail', $product->slug) }}"
                                style="color:inherit;text-decoration:none;">{{ $product->name }}</a>
                        </h3>
                        <div class="misutech_home_product_rating"
                            aria-label="Đánh giá {{ $product->display_rating_value ?? 5 }} trên 5">
                            @php $stars = round($product->display_rating_value ?? 5); @endphp
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $stars) ★ @else ☆ @endif
                            @endfor
                            <span>({{ $product->display_review_count ?? 0 }})</span>
                        </div>
                        <div class="misutech_home_product_price_row">
                            <div>
                                @if (($product->sale_price ?? 0) > 1000 || ($product->price ?? 0) > 1000)
                                    <strong class="misutech_home_product_price">{{ number_format($product->sale_price ?: $product->price, 0, ',', '.') }} đ</strong>
                                    @if ($hasSale)
                                        <del class="misutech_home_product_old_price">{{ number_format($product->price, 0, ',', '.') }} đ</del>
                                    @endif
                                @else
                                    <strong class="misutech_home_product_price" style="color: var(--misutech_home_primary, #2563EB); font-size: 13px;">Liên hệ</strong>
                                @endif
                            </div>
                            <button class="misutech_home_add_cart" type="button" data-cart
                                data-product-id="{{ $product->id }}"
                                data-product="{{ $product->name }}" aria-label="Thêm {{ $product->name }} vào giỏ hàng">
                                +
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="misutech_home_empty" aria-hidden="true">
                Không tìm thấy sản phẩm phù hợp.
            </div>
        </div>
    </section>

    {{-- FEATURED PRODUCTS SECTION --}}
    <section class="misutech_home_catalog_section" id="misutech_home_catalog_0">
        <div class="misutech_home_container">
            <header class="misutech_home_section_header">
                <h2 class="misutech_home_section_title">
                    <span class="misutech_home_section_title_mark"></span>Sản phẩm nổi bật
                </h2>
                <div class="misutech_home_section_tabs" role="tablist" aria-label="Bộ lọc Sản phẩm nổi bật">
                    <button class="misutech_home_section_tab" type="button" role="tab" aria-selected="true" data-tab="best-seller">
                        Bán chạy
                    </button>
                    <button class="misutech_home_section_tab" type="button" role="tab" aria-selected="false" data-tab="newest">
                        Mới nhất
                    </button>
                    <button class="misutech_home_section_tab" type="button" role="tab" aria-selected="false" data-tab="best-price">
                        Giá tốt
                    </button>
                    <a class="misutech_home_view_all" href="{{ route('client.shop.index') }}">Xem tất cả ›</a>
                </div>
            </header>

            <div class="misutech_home_catalog_grid">
                @foreach ($featuredProducts as $index => $product)
                    @php
                        $pImg = ($product->primaryImage && $product->primaryImage->url)
                            ? asset('clients/assets/img/clothes/' . $product->primaryImage->url)
                            : asset('clients/assets/img/clothes/no-image.webp');
                        $hasSale = ($product->sale_price && $product->price > 0 && $product->sale_price < $product->price);
                        $discountPct = $hasSale ? round((($product->price - $product->sale_price) / $product->price) * 100) : 0;
                    @endphp
                    <article class="misutech_home_product_card" data-product-name="{{ strtolower($product->name) }}" data-id="{{ $product->id }}">
                        <button class="misutech_home_product_favorite" type="button"
                            data-product-id="{{ $product->id }}"
                            aria-label="Thêm {{ $product->name }} vào yêu thích">
                            ♡
                        </button>
                        @if ($hasSale)
                            <span class="misutech_home_product_badge">-{{ $discountPct }}%</span>
                        @endif
                        <div class="misutech_home_product_image">
                            <a href="{{ route('client.product.detail', $product->slug) }}">
                                <img src="{{ $pImg }}"
                                    alt="{{ $product->name }}"
                                    width="220"
                                    height="220"
                                    loading="lazy"
                                    decoding="async" />
                            </a>
                        </div>
                        <h3 class="misutech_home_product_name">
                            <a href="{{ route('client.product.detail', $product->slug) }}"
                                style="color:inherit;text-decoration:none;">{{ $product->name }}</a>
                        </h3>
                        <div class="misutech_home_product_rating"
                            aria-label="Đánh giá {{ $product->display_rating_value ?? 5 }} trên 5">
                            @php $stars = round($product->display_rating_value ?? 5); @endphp
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $stars) ★ @else ☆ @endif
                            @endfor
                            <span>({{ $product->display_review_count ?? 0 }})</span>
                        </div>
                        @if ($index === 0)
                            @php
                                $rawDesc = strip_tags($product->short_description ?: $product->description ?: '');
                                $cleanDesc = trim(preg_replace('/\s+/', ' ', preg_replace('/\[.*?\]/', '', $rawDesc)));
                                if (empty($cleanDesc) || strlen($cleanDesc) < 10) {
                                    $cleanDesc = 'Thiết bị tự động hóa chính hãng chất lượng cao, độ bền vượt trội, CO/CQ đầy đủ.';
                                }
                            @endphp
                            <div class="misutech_home_product_short_desc"
                                style="font-size:13px; color:#666; margin:5px 0; line-height: 1.4;">
                                {{ Str::limit($cleanDesc, 110) }}
                            </div>
                        @endif
                        <div class="misutech_home_product_price_row">
                            <div>
                                @if (($product->sale_price ?? 0) > 1000 || ($product->price ?? 0) > 1000)
                                    <strong class="misutech_home_product_price">{{ number_format($product->sale_price ?: $product->price, 0, ',', '.') }} đ</strong>
                                    @if ($hasSale)
                                        <del class="misutech_home_product_old_price">{{ number_format($product->price, 0, ',', '.') }} đ</del>
                                    @endif
                                @else
                                    <strong class="misutech_home_product_price" style="color: var(--misutech_home_primary, #2563EB); font-size: 13px;">Liên hệ</strong>
                                @endif
                            </div>
                            <button class="misutech_home_add_cart" type="button" data-cart
                                data-product-id="{{ $product->id }}"
                                data-product="{{ $product->name }}" aria-label="Thêm {{ $product->name }} vào giỏ hàng">
                                +
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CATEGORY SECTIONS (Biến tần, Cảm biến, PLC, Rơ le, Bộ lọc...) --}}
    @foreach ($categorySections as $index => $section)
        <section class="misutech_home_catalog_section" id="misutech_home_catalog_{{ $index + 1 }}">
            <div class="misutech_home_container">
                <header class="misutech_home_section_header">
                    <h2 class="misutech_home_section_title">
                        <span class="misutech_home_section_title_mark"></span>{{ $section->category->name }}
                    </h2>
                    <div class="misutech_home_section_tabs" role="tablist"
                        aria-label="Danh mục {{ $section->category->name }}">
                        <a class="misutech_home_view_all"
                            href="{{ route('client.product.detail', $section->category->slug ?? $section->category->id) }}">Xem tất cả ›</a>
                    </div>
                </header>
                <div class="misutech_home_catalog_grid">
                    @foreach ($section->products as $product)
                        @php
                            $pImg = ($product->primaryImage && $product->primaryImage->url)
                                ? asset('clients/assets/img/clothes/' . $product->primaryImage->url)
                                : asset('clients/assets/img/clothes/no-image.webp');
                            $hasSale = ($product->sale_price && $product->price > 0 && $product->sale_price < $product->price);
                            $discountPct = $hasSale ? round((($product->price - $product->sale_price) / $product->price) * 100) : 0;
                        @endphp
                        <article class="misutech_home_product_card" data-product-name="{{ strtolower($product->name) }}" data-id="{{ $product->id }}">
                            <button class="misutech_home_product_favorite" type="button"
                                data-product-id="{{ $product->id }}"
                                aria-label="Thêm {{ $product->name }} vào yêu thích">
                                ♡
                            </button>
                            @if ($hasSale)
                                <span class="misutech_home_product_badge">-{{ $discountPct }}%</span>
                            @endif
                            <div class="misutech_home_product_image">
                                <a href="{{ route('client.product.detail', $product->slug) }}">
                                    <img src="{{ $pImg }}"
                                        alt="{{ $product->name }}"
                                        width="220"
                                        height="220"
                                        loading="lazy"
                                        decoding="async" />
                                </a>
                            </div>
                            <h3 class="misutech_home_product_name">
                                <a href="{{ route('client.product.detail', $product->slug) }}"
                                    style="color:inherit;text-decoration:none;">{{ $product->name }}</a>
                            </h3>
                            <div class="misutech_home_product_rating"
                                aria-label="Đánh giá {{ $product->display_rating_value ?? 5 }} trên 5">
                                @php $stars = round($product->display_rating_value ?? 5); @endphp
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $stars) ★ @else ☆ @endif
                                @endfor
                                <span>({{ $product->display_review_count ?? 0 }})</span>
                            </div>
                            <div class="misutech_home_product_price_row">
                                <div>
                                    @if (($product->sale_price ?? 0) > 1000 || ($product->price ?? 0) > 1000)
                                        <strong class="misutech_home_product_price">{{ number_format($product->sale_price ?: $product->price, 0, ',', '.') }} đ</strong>
                                        @if ($hasSale)
                                            <del class="misutech_home_product_old_price">{{ number_format($product->price, 0, ',', '.') }} đ</del>
                                        @endif
                                    @else
                                        <strong class="misutech_home_product_price" style="color: var(--misutech_home_primary, #2563EB); font-size: 13px;">Liên hệ</strong>
                                    @endif
                                </div>
                                <button class="misutech_home_add_cart" type="button" data-cart
                                    data-product-id="{{ $product->id }}"
                                    data-product="{{ $product->name }}" aria-label="Thêm {{ $product->name }} vào giỏ hàng">
                                    +
                                </button>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endforeach

    {{-- SHOP BY CATEGORY SECTION --}}
    <section class="misutech_home_shop_categories" aria-labelledby="misutech_home_shop_categories_title">
        <div class="misutech_home_container">
            <header class="misutech_home_section_header">
                <h2 class="misutech_home_section_title" id="misutech_home_shop_categories_title">
                    <span class="misutech_home_section_title_mark"></span>Mua sắm theo danh mục
                </h2>
                <a class="misutech_home_view_all" href="{{ route('client.shop.index') }}">Xem tất cả ›</a>
            </header>
            <div class="misutech_home_shop_category_grid"
                style="display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 15px; padding: 20px 0;">
                @foreach ($allCategories->take(14) as $category)
                    <a href="{{ route('client.product.detail', $category->slug) }}" class="misutech_home_shop_category_item">
                        <span class="misutech_home_shop_category_icon_wrapper">
                            @if (!empty($category->image) && file_exists(public_path('storage/clients/imgs/categories/' . $category->image)))
                                <img src="{{ asset('storage/clients/imgs/categories/' . $category->image) }}"
                                    alt="{{ $category->name }}"
                                    width="35"
                                    height="35"
                                    loading="lazy"
                                    decoding="async"
                                    style="max-width: 35px; max-height: 35px; object-fit: contain;">
                            @else
                                <i class="fa-solid {{ getCategoryFaIcon($category->slug) }}" style="font-size: 22px; color: var(--misutech_home_primary, #2563EB);"></i>
                            @endif
                        </span>
                        <span style="font-size: 13px; font-weight: 500; line-height: 1.4;">{{ $category->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- BLOG & TECHNICAL NEWS SECTION --}}
    @if(isset($blogSections) && $blogSections->isNotEmpty())
        <section class="misutech_home_content_area" aria-label="Tin tức và cẩm nang kỹ thuật">
            <div class="misutech_home_container misutech_home_content_grid">
                @foreach($blogSections as $blogCat)
                    <div class="misutech_home_content_column">
                        <h2 class="misutech_home_content_heading">
                            <span>{{ $blogCat->name }}</span>
                            <a class="misutech_home_content_more" href="{{ route('client.blog.index') }}">Xem thêm ›</a>
                        </h2>
                        <div class="misutech_home_article_list">
                            @foreach($blogCat->posts as $post)
                                @php
                                    $pThumb = $post->thumbnail
                                        ? asset('clients/assets/img/blogs/' . $post->thumbnail)
                                        : (($post->primaryImage && $post->primaryImage->url)
                                            ? asset('clients/assets/img/clothes/' . $post->primaryImage->url)
                                            : asset('clients/assets/img/clothes/no-image.png'));
                                @endphp
                                <article class="misutech_home_article">
                                    <a href="{{ route('client.blog.show', $post->slug) }}" class="misutech_home_article_image" title="{{ $post->title }}">
                                        <img src="{{ $pThumb }}" alt="{{ $post->title }}" width="120" height="80" loading="lazy" decoding="async" style="width: 100%; height: 100%; object-fit: cover; aspect-ratio: 3/2; border-radius: 4px;">
                                    </a>
                                    <div class="misutech_home_article_body">
                                        <h3 class="misutech_home_article_title">
                                            <a href="{{ route('client.blog.show', $post->slug) }}" title="{{ $post->title }}">
                                                {{ $post->title }}
                                            </a>
                                        </h3>
                                        @if($post->excerpt)
                                            <p class="misutech_home_article_excerpt">
                                                {{ Str::limit($post->excerpt, 85) }}
                                            </p>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- 4 COMMITMENT SERVICES BAR --}}
    <section class="misutech_home_service_bar" aria-label="Cam kết dịch vụ">
        <div class="misutech_home_container misutech_home_service_grid">
            <div class="misutech_home_service">
                <span class="misutech_home_service_icon"><i class="fa-solid fa-truck-fast"></i></span>
                <span class="misutech_home_service_copy">
                    <strong>Giao hàng toàn quốc</strong>
                    <span>Đóng gói cẩn thận, theo dõi dễ dàng</span>
                </span>
            </div>
            <div class="misutech_home_service">
                <span class="misutech_home_service_icon"><i class="fa-solid fa-rotate-left"></i></span>
                <span class="misutech_home_service_copy">
                    <strong>Đổi trả linh hoạt</strong>
                    <span>Hỗ trợ nhanh trong thời hạn quy định</span>
                </span>
            </div>
            <div class="misutech_home_service">
                <span class="misutech_home_service_icon"><i class="fa-solid fa-circle-check"></i></span>
                <span class="misutech_home_service_copy">
                    <strong>Sản phẩm chính hãng</strong>
                    <span>Nguồn gốc rõ ràng, bảo hành an tâm</span>
                </span>
            </div>
            <div class="misutech_home_service">
                <span class="misutech_home_service_icon"><i class="fa-solid fa-headset"></i></span>
                <span class="misutech_home_service_copy">
                    <strong>Hỗ trợ tận tâm</strong>
                    <span>Tư vấn kỹ thuật và lựa chọn phù hợp</span>
                </span>
            </div>
        </div>
    </section>

    {{-- Floating Toast for interactions --}}
    <div class="misutech_home_toast" aria-hidden="true"></div>
@endsection

@section('foot')
    <script src="{{ asset('clients/assets/js/home.js') }}?v={{ file_exists(public_path('clients/assets/js/home.js')) ? filemtime(public_path('clients/assets/js/home.js')) : time() }}"></script>
    @if ($heroCount > 1)
    <script>
    (function () {
        'use strict';
        var track   = document.getElementById('btjscHeroTrack');
        if (!track) return;
        var slides  = track.querySelectorAll('.btjsc-hero__slide');
        var dots    = document.querySelectorAll('.btjsc-hero__dot');
        var btnPrev = document.getElementById('btjscHeroPrev');
        var btnNext = document.getElementById('btjscHeroNext');
        var total   = slides.length;
        var current = 0;
        var timer   = null;
        var DELAY   = 5000;

        function goTo(n) {
            slides[current].classList.remove('btjsc-hero__slide--active');
            slides[current].setAttribute('aria-hidden', 'true');
            if (dots[current]) {
                dots[current].classList.remove('btjsc-hero__dot--active');
                dots[current].setAttribute('aria-selected', 'false');
            }
            current = (n + total) % total;
            slides[current].classList.add('btjsc-hero__slide--active');
            slides[current].setAttribute('aria-hidden', 'false');
            if (dots[current]) {
                dots[current].classList.add('btjsc-hero__dot--active');
                dots[current].setAttribute('aria-selected', 'true');
            }
        }

        function next() { goTo(current + 1); }
        function prev() { goTo(current - 1); }

        function startAuto() {
            clearInterval(timer);
            timer = setInterval(next, DELAY);
        }

        function resetAuto() { startAuto(); }

        if (btnNext) btnNext.addEventListener('click', function () { next(); resetAuto(); });
        if (btnPrev) btnPrev.addEventListener('click', function () { prev(); resetAuto(); });

        dots.forEach(function (dot, i) {
            dot.addEventListener('click', function () { goTo(i); resetAuto(); });
        });

        /* Swipe hỗ trợ touch */
        var touchStartX = 0;
        track.addEventListener('touchstart', function (e) { touchStartX = e.changedTouches[0].clientX; }, { passive: true });
        track.addEventListener('touchend', function (e) {
            var diff = touchStartX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 50) { diff > 0 ? next() : prev(); resetAuto(); }
        }, { passive: true });

        /* Dừng auto khi tab ẩn */
        document.addEventListener('visibilitychange', function () {
            document.hidden ? clearInterval(timer) : startAuto();
        });

        startAuto();
    })();
    </script>
    @endif
@endsection
