@extends('clients.layouts.master')

@section('title', 'Liên hệ - ' . ($settings->site_name ?? 'Bảo Tín JSC'))

@section('head')
    <link rel="stylesheet" href="{{ asset('clients/assets/css/contact.css?v='.time()) }}">
    
    @php
        $pageTitle = 'Liên hệ - ' . ($settings->site_name ?? 'Bảo Tín JSC');
        $pageDescription = 'Liên hệ với ' . ($settings->site_name ?? 'Bảo Tín JSC') . ' - ' . ($settings->site_description ?? 'Nhà cung cấp thiết bị tự động hóa công nghiệp hàng đầu Việt Nam');
        $canonicalUrl = route('client.contact.index');
    @endphp

    <!-- Meta Tags -->
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="keywords" content="liên hệ, hỗ trợ, tư vấn, {{ $settings->site_name ?? 'Bảo Tín JSC' }}">
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
            'name' => 'Liên hệ',
            'item' => $canonicalUrl
        ]
    ];
    
    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $breadcrumbItems
    ];
    
    // ContactPage Schema
    $contactPageSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'ContactPage',
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
{!! json_encode($contactPageSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>

<script type="application/ld+json">
{!! json_encode($organizationSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endsection

@section('content')
<!-- Breadcrumb -->
<section class="contact-breadcrumb">
    <div class="container">
        <div class="breadcrumb-wrapper">
            <a href="{{ route('client.home.index') }}">Trang chủ</a>
            <span class="separator">/</span>
            <span class="current">Liên hệ</span>
        </div>
    </div>
</section>

<!-- Contact Section -->
<main class="contact-page">
    <div class="container">
        <div class="contact-header">
            <h1 class="contact-title">Liên hệ với chúng tôi</h1>
            <p class="contact-subtitle">Chúng tôi luôn sẵn sàng hỗ trợ và tư vấn cho bạn</p>
        </div>

        <div class="contact-content">
            <!-- Contact Info Cards -->
            <div class="contact-info-section">
                <div class="contact-info-grid">
                    <div class="contact-info-card">
                        <div class="contact-info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="40" height="40">
                                <path fill="currentColor" d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.8 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.6 18.4-30.5 11.6-46.8l-40-96z"/>
                            </svg>
                        </div>
                        <h3 class="contact-info-title">Điện thoại</h3>
                        <p class="contact-info-text">
                            @if($settings->contact_phone ?? null)
                                <a href="tel:{{ preg_replace('/[^0-9]/', '', $settings->contact_phone) }}">{{ $settings->contact_phone }}</a>
                            @else
                                <span>Đang cập nhật</span>
                            @endif
                        </p>
                    </div>

                    <div class="contact-info-card">
                        <div class="contact-info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="40" height="40">
                                <path fill="currentColor" d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/>
                            </svg>
                        </div>
                        <h3 class="contact-info-title">Email</h3>
                        <p class="contact-info-text">
                            @if($settings->contact_email ?? null)
                                <a href="mailto:{{ $settings->contact_email }}">{{ $settings->contact_email }}</a>
                            @else
                                <span>Đang cập nhật</span>
                            @endif
                        </p>
                    </div>

                    <div class="contact-info-card">
                        <div class="contact-info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="40" height="40">
                                <path fill="currentColor" d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z"/>
                            </svg>
                        </div>
                        <h3 class="contact-info-title">Địa chỉ</h3>
                        <p class="contact-info-text">
                            {{ $settings->contact_address ?? 'Đang cập nhật' }}
                        </p>
                    </div>

                    <div class="contact-info-card">
                        <div class="contact-info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="40" height="40">
                                <path fill="currentColor" d="M57.7 193l9.4 16.4c8.3 14.5 21.9 25.2 37.8 29.8l57.4 16c17.6 4.9 28.2 22.9 23.3 40.5l-11.4 41.1c-2.5 9.1-4.1 18.4-4.9 27.8l-2.1 19.2c-1.1 10.1 6.2 19.2 16.3 20.3l46.5 5.1c18.1 2 35.2-7.6 41.8-24.6l18.5-40.5c5.1-11.1 14.2-19.7 25.6-24.1l41.1-16.4c17.3-6.9 26.1-26.1 19.2-43.4l-18.5-40.5c-5.1-11.1-14.2-19.7-25.6-24.1l-41.1-16.4c-17.3-6.9-36.5 1.9-43.4 19.2l-9.3 20.3c-3.1 6.8-9.4 11.7-16.7 13.4l-28.6 6.8c-10.5 2.5-21.5-1.9-28.2-10.8L57.7 193zM464 256c0-141.4-114.6-256-256-256S-48 114.6-48 256s114.6 256 256 256 256-114.6 256-256zM256 0C114.6 0 0 114.6 0 256S114.6 512 256 512s256-114.6 256-256S397.4 0 256 0z"/>
                            </svg>
                        </div>
                        <h3 class="contact-info-title">Giờ làm việc</h3>
                        <p class="contact-info-text">
                            Thứ 2 - Thứ 6: 8:00 - 17:30<br>
                            Thứ 7: 8:00 - 12:00<br>
                            Chủ nhật: Nghỉ
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contact Form & Map -->
            <div class="contact-form-map-section">
                <!-- Contact Form -->
                <div class="contact-form-wrapper">
                    <h2 class="contact-form-title">Gửi tin nhắn cho chúng tôi</h2>
                    <p class="contact-form-subtitle">Điền thông tin bên dưới, chúng tôi sẽ liên hệ lại trong 24 giờ</p>
                    
                    @if(session('success'))
                        <div class="contact-alert contact-alert-success">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20">
                                <path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/>
                            </svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="contact-alert contact-alert-error">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20">
                                <path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM175 175c9.4-9.4 24.6-9.4 33.9 0l47 47 47-47c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-47 47 47 47c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-47-47-47 47c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l47-47-47-47c-9.4-9.4-9.4-24.6 0-33.9z"/>
                            </svg>
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('client.contact.store') }}" method="POST" class="contact-form" enctype="multipart/form-data" id="contact-form">
                        @csrf
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Họ và tên <span class="required">*</span></label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="Nhập họ và tên của bạn"
                                       required>
                                @error('name')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email">Email <span class="required">*</span></label>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="Nhập email của bạn"
                                       required>
                                @error('email')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Số điện thoại <span class="required">*</span></label>
                                <input type="tel" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone') }}" 
                                       placeholder="Nhập số điện thoại"
                                       required>
                                @error('phone')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="subject">Phân khúc quan tâm <span class="required">*</span></label>
                                <select id="subject" name="subject" required>
                                    <option value="">Chọn phân khúc quan tâm</option>
                                    <option value="Tư vấn sản phẩm" {{ old('subject') == 'Tư vấn sản phẩm' ? 'selected' : '' }}>Tư vấn sản phẩm</option>
                                    <option value="Báo giá" {{ old('subject') == 'Báo giá' ? 'selected' : '' }}>Báo giá</option>
                                    <option value="Hỗ trợ kỹ thuật" {{ old('subject') == 'Hỗ trợ kỹ thuật' ? 'selected' : '' }}>Hỗ trợ kỹ thuật</option>
                                    <option value="Đặt hàng" {{ old('subject') == 'Đặt hàng' ? 'selected' : '' }}>Đặt hàng</option>
                                    <option value="Hợp tác" {{ old('subject') == 'Hợp tác' ? 'selected' : '' }}>Hợp tác</option>
                                    <option value="Khác" {{ old('subject') == 'Khác' ? 'selected' : '' }}>Khác</option>
                                </select>
                                @error('subject')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="message">Nội dung tin nhắn <span class="required">*</span></label>
                            <textarea id="message" 
                                      name="message" 
                                      rows="6" 
                                      placeholder="Mô tả chi tiết nhu cầu của bạn (tối thiểu 20 ký tự)"
                                      required>{{ old('message') }}</textarea>
                            <div class="char-count">
                                <span id="char-count">0</span> / 5000 ký tự
                            </div>
                            @error('message')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="attachment">Tệp đính kèm (tùy chọn)</label>
                            <input type="file" 
                                   id="attachment" 
                                   name="attachment" 
                                   accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            <small class="form-hint">Định dạng: JPG, PNG, PDF, DOC, DOCX. Tối đa 4MB</small>
                            @error('attachment')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="contact-submit-btn" id="contact-submit-btn">
                            <span class="btn-text">Gửi tin nhắn</span>
                            <span class="btn-loading" style="display: none;">
                                <svg class="spinner" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.25"/>
                                    <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                </svg>
                                Đang gửi...
                            </span>
                        </button>
                    </form>
                </div>

                <!-- Map Section -->
                <div class="contact-map-wrapper">
                    <h2 class="contact-map-title">Vị trí của chúng tôi</h2>
                    @if($settings->source_map ?? null)
                        <div class="contact-map">
                            <iframe 
                                src="{{ $settings->source_map }}" 
                                width="100%" 
                                height="450" 
                                style="border:0; border-radius: 8px;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    @else
                        <div class="contact-map-placeholder">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="60" height="60">
                                <path fill="currentColor" d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z"/>
                            </svg>
                            <p>Bản đồ đang được cập nhật</p>
                        </div>
                    @endif
                    
                    @if($settings->contact_address ?? null)
                        <div class="contact-address-info">
                            <h3>Địa chỉ</h3>
                            <p>{{ $settings->contact_address }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for message textarea
    const messageTextarea = document.getElementById('message');
    const charCount = document.getElementById('char-count');
    
    if (messageTextarea && charCount) {
        messageTextarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
        
        // Set initial count
        charCount.textContent = messageTextarea.value.length;
    }
    
    // Form submission handler
    const contactForm = document.getElementById('contact-form');
    const submitBtn = document.getElementById('contact-submit-btn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            // Validate form
            if (!this.checkValidity()) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            if (submitBtn && btnText && btnLoading) {
                submitBtn.disabled = true;
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline-flex';
                btnLoading.style.alignItems = 'center';
                btnLoading.style.gap = '8px';
            }
        });
    }
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.contact-alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s';
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
});
</script>
@endsection
