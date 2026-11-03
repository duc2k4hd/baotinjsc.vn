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
    {{-- HERO SECTION: 3 Columns --}}
    <section class="misutech_home_hero">
        <div class="misutech_home_container misutech_home_hero_grid">
            {{-- Cột 1: Menu danh mục đa cấp --}}
            <ul class="misutech_home_category_menu">
                @foreach ($mainCategories as $category)
                    <li class="misutech_home_category_item">
                        <a href="{{ route('client.product.detail', $category->slug) }}" class="misutech_home_category_label">
                            <span class="misutech_home_category_name_text">{{ $category->name }}</span>
                            @if ($category->children && $category->children->count() > 0)
                                <span class="misutech_home_category_arrow_icon">›</span>
                            @endif
                        </a>

                        @if ($category->children && $category->children->count() > 0)
                            <div class="misutech_home_mega_menu">
                                <div class="misutech_home_mega_grid">
                                    @foreach ($category->children as $child)
                                        <div class="misutech_home_mega_column {{ ($child->children && $child->children->count() > 0) ? 'has-subitems' : 'no-subitems' }}">
                                            <h4 class="misutech_home_mega_title">
                                                <a href="{{ route('client.product.detail', $child->slug) }}">
                                                    <span class="misutech_mega_title_text">{{ $child->name }}</span>
                                                    <span class="misutech_mega_title_arrow">›</span>
                                                </a>
                                            </h4>
                                            @if ($child->children && $child->children->count() > 0)
                                                <ul class="misutech_home_mega_list">
                                                    @foreach ($child->children as $grandchild)
                                                        <li><a href="{{ route('client.product.detail', $grandchild->slug) }}">{{ $grandchild->name }}</a></li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </li>
                @endforeach

                <li class="misutech_home_category_item">
                    <a href="{{ route('client.shop.index') }}" class="misutech_home_category_label misutech_home_category_all_link">
                        <span class="misutech_home_category_name_text">+ Xem tất cả danh mục</span>
                        <span class="misutech_home_category_arrow_icon">›</span>
                    </a>

                    <div class="misutech_home_mega_menu">
                        <div class="misutech_home_mega_grid">
                            @foreach ($allCategories as $allCat)
                                <div class="misutech_home_mega_column {{ ($allCat->children && $allCat->children->count() > 0) ? 'has-subitems' : 'no-subitems' }}">
                                    <h4 class="misutech_home_mega_title">
                                        <a href="{{ route('client.product.detail', $allCat->slug) }}">
                                            <span class="misutech_mega_title_text">{{ $allCat->name }}</span>
                                            <span class="misutech_mega_title_arrow">›</span>
                                        </a>
                                    </h4>
                                    @if ($allCat->children && $allCat->children->count() > 0)
                                        <ul class="misutech_home_mega_list">
                                            @foreach ($allCat->children as $child)
                                                <li><a href="{{ route('client.product.detail', $child->slug) }}">{{ $child->name }}</a></li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </li>
            </ul>

            {{-- Cột 2: Slider Banner chính & Dải ưu đãi --}}
            <div class="misutech_home_hero_center">
                <div class="misutech_home_hero_main" style="position: relative; overflow: hidden;">
                    @php $heroBanners = $banners->get('1', collect()); @endphp
                    @foreach ($heroBanners as $index => $banner)
                        @php
                            $bImg = $banner->image_desktop;
                            $bUrl = Str::startsWith($bImg, ['http://', 'https://']) ? $bImg : asset('clients/assets/img/banners/' . $bImg);
                        @endphp
                        <div class="misutech_home_hero_slide"
                            style="opacity: {{ $index === 0 ? '1' : '0' }}; transition: opacity 0.6s ease-in-out, transform 0.6s ease-in-out; transform: {{ $index === 0 ? 'scale(1)' : 'scale(1.05)' }}; pointer-events: {{ $index === 0 ? 'auto' : 'none' }}; z-index: {{ $index === 0 ? '1' : '0' }}; width: 100%; height: 100%; position: absolute; top: 0; left: 0;">
                            <img src="{{ $bUrl }}"
                                alt="{{ $banner->title }}"
                                width="790"
                                height="380"
                                @if($index === 0) fetchpriority="high" loading="eager" decoding="async" @else loading="lazy" decoding="async" @endif
                                style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;" />
                            <div class="misutech_home_hero_overlay">
                                <div class="misutech_home_hero_copy">
                                    @if(!empty($banner->description))
                                        <p class="misutech_home_hero_eyebrow">
                                            ƯU ĐÃI ĐẶC BIỆT
                                        </p>
                                    @endif
                                    <h1 class="misutech_home_hero_title">
                                        {!! nl2br(e($banner->title)) !!}
                                    </h1>
                                    @if(!empty($banner->description))
                                        <p class="misutech_home_hero_desc">
                                            {{ $banner->description }}
                                        </p>
                                    @endif
                                    <a class="misutech_home_primary_button"
                                        href="{{ $banner->link ?: route('client.shop.index') }}">MUA NGAY</a>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="misutech_home_hero_dots" aria-label="Chọn banner"
                        style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; z-index: 10;">
                        @foreach ($heroBanners as $index => $banner)
                            <button class="misutech_home_hero_dot" type="button"
                                aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                                aria-label="Banner {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                </div>

                {{-- Dải chương trình ưu đãi nhanh (Khớp chính xác chiều cao 105px của hàng thứ 2) --}}
                <div class="misutech_home_hero_strip" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; height: 100%; border-radius: 4px; overflow: hidden;">
                    <div style="background: #ffffff; border: 1px solid #E2E8F0; border-radius: 4px; padding: 12px 14px; display: flex; align-items: center; gap: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.03);">
                        <div style="width: 44px; height: 44px; border-radius: 8px; background: #EFF6FF; display: grid; place-items: center; color: #2563EB; font-size: 20px; flex-shrink: 0;">
                            <i class="fa-solid fa-truck-fast"></i>
                        </div>
                        <div>
                            <strong style="display: block; font-size: 12px; color: #1E293B; text-transform: uppercase; line-height: 1.3;">Giao hàng nhanh</strong>
                            <span style="font-size: 11px; color: #64748B;">Vận chuyển toàn quốc</span>
                        </div>
                    </div>
                    <div style="background: #ffffff; border: 1px solid #E2E8F0; border-radius: 4px; padding: 12px 14px; display: flex; align-items: center; gap: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.03);">
                        <div style="width: 44px; height: 44px; border-radius: 8px; background: #ECFDF5; display: grid; place-items: center; color: #059669; font-size: 20px; flex-shrink: 0;">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <strong style="display: block; font-size: 12px; color: #1E293B; text-transform: uppercase; line-height: 1.3;">100% Chính hãng</strong>
                            <span style="font-size: 11px; color: #64748B;">CO / CQ đầy đủ</span>
                        </div>
                    </div>
                    <div style="background: #ffffff; border: 1px solid #E2E8F0; border-radius: 4px; padding: 12px 14px; display: flex; align-items: center; gap: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.03);">
                        <div style="width: 44px; height: 44px; border-radius: 8px; background: #FFFBEB; display: grid; place-items: center; color: #D97706; font-size: 20px; flex-shrink: 0;">
                            <i class="fa-solid fa-headset"></i>
                        </div>
                        <div>
                            <strong style="display: block; font-size: 12px; color: #1E293B; text-transform: uppercase; line-height: 1.3;">Kỹ sư tư vấn 24/7</strong>
                            <span style="font-size: 11px; color: #64748B;">Hotline: 0985 214 595</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cột 3: 2 Banner phụ khuyến mại bên phải --}}
            <aside class="misutech_home_hero_side" aria-label="Khuyến mại nổi bật">
                @php
                    $sideBanner1 = $banners->get('2', collect())->first();
                    $sideBanner2 = $banners->get('3', collect())->first();
                @endphp
                @if ($sideBanner1)
                    @php
                        $s1Img = $sideBanner1->image_desktop;
                        $s1Url = Str::startsWith($s1Img, ['http://', 'https://']) ? $s1Img : asset('clients/assets/img/banners/' . $s1Img);
                    @endphp
                    <a class="misutech_home_side_banner" href="{{ $sideBanner1->link ?: route('client.shop.index') }}"
                        style="position: relative; border-radius: 4px; overflow: hidden; display: block; height: 100%;">
                        <img src="{{ $s1Url }}"
                            alt="{{ $sideBanner1->title }}"
                            width="290"
                            height="185"
                            loading="lazy"
                            decoding="async"
                            style="width: 100%; height: 100%; object-fit: cover;" />
                        <span class="misutech_home_side_banner_label">{{ $sideBanner1->title }}</span>
                    </a>
                @endif

                @if ($sideBanner2)
                    @php
                        $s2Img = $sideBanner2->image_desktop;
                        $s2Url = Str::startsWith($s2Img, ['http://', 'https://']) ? $s2Img : asset('clients/assets/img/banners/' . $s2Img);
                    @endphp
                    <a class="misutech_home_side_banner" href="{{ $sideBanner2->link ?: route('client.shop.index') }}"
                        style="position: relative; border-radius: 4px; overflow: hidden; display: block; height: 100%;">
                        <img src="{{ $s2Url }}"
                            alt="{{ $sideBanner2->title }}"
                            width="290"
                            height="185"
                            loading="lazy"
                            decoding="async"
                            style="width: 100%; height: 100%; object-fit: cover;" />
                        <span class="misutech_home_side_banner_label">{{ $sideBanner2->title }}</span>
                    </a>
                @endif
            </aside>
        </div>
    </section>

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
@endsection
