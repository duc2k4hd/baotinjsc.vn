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
    <link rel="stylesheet" href="{{ asset('clients/assets/css/home-layout.css') }}?v=1.5">

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



    {{-- CATEGORY SECTIONS + BLOG: layout 2 cột (sidebar trái + nội dung phải) --}}
    <div class="home-body-layout">

        {{-- ===== SIDEBAR DANH MỤC BÊN TRÁI ===== --}}
        <aside class="home-cat-sidebar" aria-label="Danh mục sản phẩm">
            <div class="home-cat-sidebar__inner">
                <div class="home-cat-sidebar__heading">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    <span>Danh mục sản phẩm</span>
                </div>
                <nav class="home-cat-sidebar__list" aria-label="Danh mục">
                    @forelse ($categorySections ?? [] as $index => $section)
                        @php
                            $cat = $section->category;
                            $targetId = 'misutech_home_catalog_' . ($index + 1);
                        @endphp
                        <a href="#{{ $targetId }}"
                           data-target="{{ $targetId }}"
                           class="home-cat-sidebar__item{{ $index === 0 ? ' active' : '' }}"
                           title="{{ $cat->name }}">
                            @if (!empty($cat->image) && file_exists(public_path('clients/assets/img/categories/' . $cat->image)))
                                <img src="{{ asset('clients/assets/img/categories/' . $cat->image) }}"
                                     alt="{{ $cat->name }}" width="22" height="22" loading="lazy">
                            @else
                                <i class="fas {{ getCategoryFaIcon($cat->slug ?? '') }} home-cat-sidebar__icon"></i>
                            @endif
                            <span>{{ $cat->name }}</span>
                        </a>
                    @empty
                        <p style="padding:10px 14px;color:#94a3b8;font-size:13px;">Chưa có danh mục</p>
                    @endforelse
                </nav>
            </div>
        </aside>

        {{-- ===== NỘI DUNG CHÍNH ===== --}}
        <div class="home-body-main">

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

    {{-- Floating Toast for interactions --}}
    <div class="misutech_home_toast" aria-hidden="true"></div>

        </div>{{-- /.home-body-main --}}
    </div>{{-- /.home-body-layout --}}
@endsection

@section('foot')
    <script src="{{ asset('clients/assets/js/home.js') }}?v={{ file_exists(public_path('clients/assets/js/home.js')) ? filemtime(public_path('clients/assets/js/home.js')) : time() }}"></script>
    <script>
    // Sidebar danh mục trang chủ: Toggle mobile + Smooth Scroll đến section + ScrollSpy
    (function () {
        var sb = document.querySelector('.home-cat-sidebar');
        if (!sb) return;
        var hd = sb.querySelector('.home-cat-sidebar__heading');
        var links = sb.querySelectorAll('.home-cat-sidebar__item[data-target]');

        if (hd) {
            hd.addEventListener('click', function () {
                if (window.innerWidth <= 767) sb.classList.toggle('is-open');
            });
        }

        function setActiveLink(targetId) {
            links.forEach(function (item) {
                if (item.getAttribute('data-target') === targetId) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            });
        }

        // Click vào danh mục -> scroll mượt đến đúng section bên phải
        links.forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                var targetId = this.getAttribute('data-target');
                var targetEl = document.getElementById(targetId);
                if (targetEl) {
                    setActiveLink(targetId);
                    if (window.innerWidth <= 767) {
                        sb.classList.remove('is-open');
                    }
                    targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Tự động highlight danh mục bên trái khi cuộn chuột
        var sections = [];
        links.forEach(function (link) {
            var id = link.getAttribute('data-target');
            var el = document.getElementById(id);
            if (el) sections.push(el);
        });

        if (sections.length > 0) {
            var ticking = false;
            window.addEventListener('scroll', function () {
                if (!ticking) {
                    window.requestAnimationFrame(function () {
                        var currentId = sections[0].id;
                        var offset = 150;
                        for (var i = 0; i < sections.length; i++) {
                            var rect = sections[i].getBoundingClientRect();
                            if (rect.top <= offset) {
                                currentId = sections[i].id;
                            }
                        }
                        setActiveLink(currentId);
                        ticking = false;
                    });
                    ticking = true;
                }
            }, { passive: true });
        }
    })();
    </script>
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
