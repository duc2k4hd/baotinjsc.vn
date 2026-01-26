@extends('clients.layouts.master')

@section('title', 'Giới thiệu - ' . ($settings->site_name ?? 'Bảo Tín JSC'))

@section('head')
    <link rel="stylesheet" href="{{ asset('clients/assets/css/introduction.css?v='.time()) }}">
    
    @php
        $pageTitle = 'Giới thiệu - ' . ($settings->site_name ?? 'Bảo Tín JSC');
        $pageDescription = 'Giới thiệu về ' . ($settings->site_name ?? 'Bảo Tín JSC') . ' - ' . ($settings->site_description ?? 'Nhà cung cấp thiết bị tự động hóa công nghiệp hàng đầu Việt Nam');
        $canonicalUrl = route('client.introduction.index');
    @endphp

    <!-- Meta Tags -->
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="keywords" content="giới thiệu, về chúng tôi, {{ $settings->site_name ?? 'Bảo Tín JSC' }}">
    <meta name="robots" content="index, follow, max-snippet:-1, max-video-preview:-1, max-image-preview:large" />
    
    <!-- 📅 Date -->
    <meta http-equiv="date" content="{{ now()->format('d/m/Y') }}" />
    
    <!-- 🌐 Open Graph -->
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $settings->site_name ?? $settings->subname ?? 'Bảo Tín JSC' }}">
    <meta property="og:locale" content="vi_VN">

    <!-- 🐦 Twitter -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">

    <!-- 🔗 Canonical & hreflang -->
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="alternate" hreflang="vi" href="{{ $canonicalUrl }}">
    <link rel="alternate" hreflang="x-default" href="{{ $canonicalUrl }}">
@endsection

@section('schema')
@php
    // BreadcrumbList Schema
    $breadcrumbItems = [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Trang chủ',
            'item' => url('/')
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => 'Giới thiệu',
            'item' => $canonicalUrl
        ]
    ];
    
    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $breadcrumbItems
    ];
    
    // AboutPage Schema
    $aboutPageSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'AboutPage',
        'name' => $pageTitle,
        'description' => $pageDescription,
        'url' => $canonicalUrl,
        'inLanguage' => 'vi-VN',
        'isPartOf' => [
            '@type' => 'WebSite',
            'name' => $settings->site_name ?? 'Bảo Tín JSC',
            'url' => url('/')
        ],
        'breadcrumb' => $breadcrumbSchema
    ];
    
    // Organization Schema
    $organizationSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $settings->site_name ?? 'Bảo Tín JSC',
        'url' => url('/'),
        'logo' => asset('clients/assets/img/business/' . ($settings->site_logo ?? 'logo.webp')),
        'description' => $settings->site_description ?? 'Nhà cung cấp thiết bị tự động hóa công nghiệp hàng đầu Việt Nam',
        'contactPoint' => [
            '@type' => 'ContactPoint',
            'telephone' => $settings->contact_phone ?? '',
            'email' => $settings->contact_email ?? '',
            'contactType' => 'customer service',
            'areaServed' => 'VN',
            'availableLanguage' => ['Vietnamese']
        ]
    ];
    
    if ($settings->contact_address ?? null) {
        $organizationSchema['address'] = [
            '@type' => 'PostalAddress',
            'streetAddress' => $settings->contact_address,
            'addressCountry' => 'VN'
        ];
    }
    
    if ($settings->site_tax_code ?? null) {
        $organizationSchema['taxID'] = $settings->site_tax_code;
    }
@endphp

<script type="application/ld+json">
{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>

<script type="application/ld+json">
{!! json_encode($aboutPageSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>

<script type="application/ld+json">
{!! json_encode($organizationSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endsection

@section('content')
<!-- Breadcrumb -->
<section class="intro-breadcrumb">
    <div class="container">
        <div class="breadcrumb-wrapper">
            <a href="{{ route('client.home.index') }}">Trang chủ</a>
            <span class="separator">/</span>
            <span class="current">Giới thiệu</span>
        </div>
    </div>
</section>

<!-- Hero Section -->
<section class="intro-hero">
    <div class="container">
        <div class="intro-hero-content">
            <h1 class="intro-hero-title">{{ $settings->site_name ?? 'Bảo Tín JSC' }}</h1>
            <p class="intro-hero-subtitle">{{ $settings->site_description ?? 'Nhà cung cấp thiết bị tự động hóa công nghiệp hàng đầu Việt Nam' }}</p>
            @if($settings->site_logo ?? null)
                <div class="intro-hero-logo">
                    <img src="{{ asset('clients/assets/img/business/' . $settings->site_logo) }}" alt="{{ $settings->site_name ?? 'Bảo Tín JSC' }}">
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Introduction Section -->
<main class="intro-page">
    <div class="container">
        <!-- About Company -->
        <section class="intro-section intro-about">
            <div class="intro-section-header">
                <h2 class="intro-section-title">Về Chúng Tôi</h2>
                <div class="intro-section-divider"></div>
            </div>
            <div class="intro-about-content">
                <div class="intro-about-text">
                    <p class="intro-lead">
                        {{ $settings->site_name ?? 'Bảo Tín JSC' }} là đơn vị chuyên cung cấp và phân phối các thiết bị tự động hóa công nghiệp hàng đầu tại Việt Nam. Với nhiều năm kinh nghiệm trong lĩnh vực tự động hóa, chúng tôi tự hào mang đến những giải pháp công nghệ tiên tiến và dịch vụ chuyên nghiệp cho các doanh nghiệp trên toàn quốc.
                    </p>
                    <p>
                        Chúng tôi chuyên cung cấp đa dạng các sản phẩm tự động hóa công nghiệp bao gồm: cảm biến công nghiệp, PLC, HMI, biến tần, servo, encoder, rơ le, bộ nguồn và nhiều thiết bị khác. Tất cả sản phẩm đều được nhập khẩu chính hãng từ các thương hiệu uy tín trên thế giới như Autonics, Omron, Schneider, Siemens, ABB và nhiều thương hiệu khác.
                    </p>
                    <p>
                        Với đội ngũ kỹ sư giàu kinh nghiệm và tận tâm, chúng tôi không chỉ cung cấp sản phẩm chất lượng cao mà còn đem đến những giải pháp tối ưu nhất cho từng nhu cầu cụ thể của khách hàng. Chúng tôi cam kết mang đến sự hài lòng tuyệt đối thông qua dịch vụ chuyên nghiệp và sản phẩm chính hãng.
                    </p>
                </div>
                @if($settings->site_logo ?? null)
                    <div class="intro-about-image">
                        <img src="{{ asset('clients/assets/img/business/' . $settings->site_logo) }}" alt="{{ $settings->site_name ?? 'Bảo Tín JSC' }}">
                    </div>
                @endif
            </div>
        </section>

        <!-- Mission & Vision -->
        <section class="intro-section intro-mission-vision">
            <div class="intro-mission-vision-grid">
                <div class="intro-mission-card">
                    <div class="intro-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="50" height="50">
                            <path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/>
                        </svg>
                    </div>
                    <h3 class="intro-card-title">Sứ Mệnh</h3>
                    <p class="intro-card-text">
                        Cung cấp các giải pháp tự động hóa công nghiệp chất lượng cao, đáp ứng nhu cầu của khách hàng và góp phần nâng cao hiệu quả sản xuất cho các doanh nghiệp Việt Nam.
                    </p>
                </div>
                <div class="intro-vision-card">
                    <div class="intro-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="50" height="50">
                            <path fill="currentColor" d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.2-1.8-11.1 .3-14.5 5.1c-3.2 4.5-3.5 10.7-1.1 15.5c11.2 22.6 34.1 38.7 60.3 38.7c35.3 0 64-28.7 64-64s-28.7-64-64-64c-26.2 0-49.1 16.1-60.3 38.7c-2.4 4.8-2.1 11 .1 15.5c3.4 4.8 9.3 6.9 14.5 5.1c6.4-2.1 13.2-3.3 20.3-3.3c35.3 0 64 28.7 64 64z"/>
                        </svg>
                    </div>
                    <h3 class="intro-card-title">Tầm Nhìn</h3>
                    <p class="intro-card-text">
                        Trở thành đơn vị hàng đầu trong lĩnh vực tự động hóa công nghiệp tại Việt Nam, được khách hàng tin tưởng và lựa chọn cho các dự án tự động hóa của họ.
                    </p>
                </div>
            </div>
        </section>

        <!-- Core Values -->
        <section class="intro-section intro-values">
            <div class="intro-section-header">
                <h2 class="intro-section-title">Giá Trị Cốt Lõi</h2>
                <div class="intro-section-divider"></div>
            </div>
            <div class="intro-values-grid">
                <div class="intro-value-item">
                    <div class="intro-value-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="40" height="40">
                            <path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/>
                        </svg>
                    </div>
                    <h4 class="intro-value-title">Chất Lượng</h4>
                    <p class="intro-value-text">Sản phẩm chính hãng 100%, đảm bảo chất lượng và độ bền cao</p>
                </div>
                <div class="intro-value-item">
                    <div class="intro-value-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="40" height="40">
                            <path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/>
                        </svg>
                    </div>
                    <h4 class="intro-value-title">Uy Tín</h4>
                    <p class="intro-value-text">Cam kết đúng hẹn, minh bạch trong giao dịch và dịch vụ</p>
                </div>
                <div class="intro-value-item">
                    <div class="intro-value-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="40" height="40">
                            <path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/>
                        </svg>
                    </div>
                    <h4 class="intro-value-title">Chuyên Nghiệp</h4>
                    <p class="intro-value-text">Đội ngũ kỹ sư giàu kinh nghiệm, tư vấn tận tâm</p>
                </div>
                <div class="intro-value-item">
                    <div class="intro-value-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="40" height="40">
                            <path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/>
                        </svg>
                    </div>
                    <h4 class="intro-value-title">Đổi Mới</h4>
                    <p class="intro-value-text">Luôn cập nhật công nghệ mới nhất, giải pháp tiên tiến</p>
                </div>
            </div>
        </section>

        <!-- Why Choose Us -->
        <section class="intro-section intro-why">
            <div class="intro-section-header">
                <h2 class="intro-section-title">Tại Sao Chọn Chúng Tôi</h2>
                <div class="intro-section-divider"></div>
            </div>
            <div class="intro-why-grid">
                <div class="intro-why-item">
                    <div class="intro-why-number">01</div>
                    <h4 class="intro-why-title">Sản Phẩm Chính Hãng</h4>
                    <p class="intro-why-text">Nhập khẩu trực tiếp từ nhà sản xuất, có đầy đủ giấy tờ chứng nhận và bảo hành chính hãng</p>
                </div>
                <div class="intro-why-item">
                    <div class="intro-why-number">02</div>
                    <h4 class="intro-why-title">Giá Cả Cạnh Tranh</h4>
                    <p class="intro-why-text">Cam kết giá tốt nhất thị trường với chính sách chiết khấu hấp dẫn cho khách hàng thân thiết</p>
                </div>
                <div class="intro-why-item">
                    <div class="intro-why-number">03</div>
                    <h4 class="intro-why-title">Tư Vấn Chuyên Nghiệp</h4>
                    <p class="intro-why-text">Đội ngũ kỹ sư giàu kinh nghiệm hỗ trợ 24/7, tư vấn giải pháp phù hợp nhất</p>
                </div>
                <div class="intro-why-item">
                    <div class="intro-why-number">04</div>
                    <h4 class="intro-why-title">Bảo Hành Uy Tín</h4>
                    <p class="intro-why-text">Chính sách bảo hành rõ ràng, hỗ trợ kỹ thuật tận tình và nhanh chóng</p>
                </div>
                <div class="intro-why-item">
                    <div class="intro-why-number">05</div>
                    <h4 class="intro-why-title">Giao Hàng Nhanh</h4>
                    <p class="intro-why-text">Hệ thống kho hàng rộng khắp, giao hàng nhanh chóng và đúng hẹn</p>
                </div>
                <div class="intro-why-item">
                    <div class="intro-why-number">06</div>
                    <h4 class="intro-why-title">Dịch Vụ Hậu Mãi</h4>
                    <p class="intro-why-text">Chăm sóc khách hàng chu đáo, hỗ trợ kỹ thuật lâu dài sau khi bán hàng</p>
                </div>
            </div>
        </section>

        <!-- Statistics -->
        <section class="intro-section intro-stats">
            <div class="intro-stats-grid">
                <div class="intro-stat-item">
                    <div class="intro-stat-number" data-target="500">0</div>
                    <div class="intro-stat-label">+</div>
                    <p class="intro-stat-text">Sản phẩm</p>
                </div>
                <div class="intro-stat-item">
                    <div class="intro-stat-number" data-target="1000">0</div>
                    <div class="intro-stat-label">+</div>
                    <p class="intro-stat-text">Khách hàng</p>
                </div>
                <div class="intro-stat-item">
                    <div class="intro-stat-number" data-target="5000">0</div>
                    <div class="intro-stat-label">+</div>
                    <p class="intro-stat-text">Dự án</p>
                </div>
                <div class="intro-stat-item">
                    <div class="intro-stat-number" data-target="10">0</div>
                    <div class="intro-stat-label">+</div>
                    <p class="intro-stat-text">Năm kinh nghiệm</p>
                </div>
            </div>
        </section>

        <!-- Contact CTA -->
        <section class="intro-section intro-cta">
            <div class="intro-cta-content">
                <h2 class="intro-cta-title">Bạn Cần Tư Vấn?</h2>
                <p class="intro-cta-text">Đội ngũ chuyên gia của chúng tôi sẵn sàng hỗ trợ bạn 24/7</p>
                <div class="intro-cta-buttons">
                    <a href="{{ route('client.contact.index') }}" class="intro-cta-btn intro-cta-btn-primary">
                        Liên hệ ngay
                    </a>
                    <a href="tel:{{ preg_replace('/[^0-9]/', '', $settings->contact_phone ?? '0827786198') }}" class="intro-cta-btn intro-cta-btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20">
                            <path fill="currentColor" d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.8 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.6 18.4-30.5 11.6-46.8l-40-96z"/>
                        </svg>
                        {{ $settings->contact_phone ?? '0827786198' }}
                    </a>
                </div>
            </div>
        </section>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate statistics counter
    const statNumbers = document.querySelectorAll('.intro-stat-number');
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = parseInt(entry.target.getAttribute('data-target'));
                animateCounter(entry.target, target);
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    statNumbers.forEach(stat => {
        observer.observe(stat);
    });
    
    function animateCounter(element, target) {
        let current = 0;
        const increment = target / 50;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 30);
    }
});
</script>
@endsection
