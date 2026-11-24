@extends('clients.layouts.master')

@section('title', ($product->meta_title ?? $product->name) . ' - ' . ($settings->site_name ?? 'Bảo Tín JSC'))

@section('head')
    <link rel="stylesheet" href="{{ asset('clients/assets/css/service-detail.css?v=' . (file_exists(public_path('clients/assets/css/service-detail.css')) ? filemtime(public_path('clients/assets/css/service-detail.css')) : '1.0')) }}">

    @php
        $pageTitle = $product->meta_title ?? ($product->name ?? 'Dịch vụ kỹ thuật');
        $pageDescription = $product->meta_description ?? ($product->short_description ? strip_tags($product->short_description) : ($product->name ?? ''));
        $pageKeywords = is_array($product->meta_keywords) ? implode(', ', $product->meta_keywords) : ($product->meta_keywords ?? '');
        $canonicalUrl = url('/' . ($product->slug ?? ''));
        
        $resolveImg = function($path) {
            if (empty($path)) return asset('clients/assets/img/clothes/no-image.webp');
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;
            if (str_starts_with($path, '/')) return asset(ltrim($path, '/'));
            if (str_starts_with($path, 'clients/')) return asset($path);
            return asset('clients/assets/img/clothes/' . $path);
        };

        $firstImgPath = ($product->primaryImage && $product->primaryImage->url) ? $product->primaryImage->url : null;
        if (!$firstImgPath && isset($galleryImages) && $galleryImages->isNotEmpty()) {
            $firstImgPath = $galleryImages->first()['url'] ?? null;
        }
        $pageImage = $resolveImg($firstImgPath);
    @endphp

    <!-- Meta Tags -->
    <meta name="description" content="{{ Str::limit($pageDescription, 160) }}">
    @if(!empty($pageKeywords))
        <meta name="keywords" content="{{ $pageKeywords }}">
    @endif
    <meta name="robots" content="index, follow, max-snippet:-1, max-video-preview:-1, max-image-preview:large" />
    <meta http-equiv="date" content="{{ $product->updated_at ? $product->updated_at->format('d/m/Y') : now()->format('d/m/Y') }}" />

    <!-- Open Graph -->
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ Str::limit($pageDescription, 200) }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $pageImage }}">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="{{ $settings->site_name ?? 'Bảo Tín JSC' }}">
    <meta property="og:locale" content="vi_VN">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ Str::limit($pageDescription, 200) }}">
    <meta name="twitter:image" content="{{ $pageImage }}">

    <!-- Canonical -->
    <link rel="canonical" href="{{ $canonicalUrl }}">
@endsection

@section('schema')
@php
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
            'name' => 'Dịch vụ kỹ thuật',
            'item' => route('client.technical-service.index')
        ],
        [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $product->name,
            'item' => $canonicalUrl
        ]
    ];

    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $breadcrumbItems
    ];

    $serviceSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => $product->name,
        'description' => Str::limit($pageDescription, 300),
        'url' => $canonicalUrl,
        'image' => $pageImage,
        'provider' => [
            '@type' => 'Organization',
            'name' => $settings->site_name ?? 'Bảo Tín JSC',
            'url' => url('/'),
            'telephone' => $settings->hotline ?? ($settings->phone ?? '0983214896'),
        ]
    ];
@endphp

<script type="application/ld+json">
{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>

<script type="application/ld+json">
{!! json_encode($serviceSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endsection

@section('content')
<div class="service-detail-wrapper">
    <div class="service-container">
        {{-- Breadcrumb --}}
        <nav class="service-breadcrumb" aria-label="breadcrumb">
            <a href="{{ url('/') }}"><i class="fa-solid fa-house"></i> Trang chủ</a>
            <span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
            <a href="{{ route('client.technical-service.index') }}">Dịch vụ kỹ thuật</a>
            @if($product->primaryCategory)
                <span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
                <a href="{{ url('/' . $product->primaryCategory->slug) }}">{{ $product->primaryCategory->name }}</a>
            @endif
            <span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
            <span class="current">{{ $product->name }}</span>
        </nav>

        {{-- Session Flash Notifications --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="background:#dcfce7;border:1px solid #86efac;color:#166534;border-radius:8px;padding:14px 18px;">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- 2-Column Layout --}}
        <div class="service-grid-layout">
            {{-- CỘT TRÁI: SIDEBAR --}}
            <aside class="service-sidebar">
                {{-- Box 1: Danh mục dịch vụ / sản phẩm --}}
                <div class="sidebar-box">
                    <div class="sidebar-box-header">
                        <i class="fa-solid fa-layer-group"></i>
                        <h3>Danh mục dịch vụ & giải pháp</h3>
                    </div>
                    <ul class="sidebar-cat-list">
                        @if(isset($sidebarCategories) && $sidebarCategories->isNotEmpty())
                            @foreach($sidebarCategories as $cat)
                                <li class="{{ ($product->primary_category_id === $cat->id) ? 'active' : '' }}">
                                    <a href="{{ url('/' . $cat->slug) }}">
                                        <span>{{ $cat->name }}</span>
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </a>
                                </li>
                            @endforeach
                        @else
                            {{-- Fallback navigation danh mục --}}
                            <li><a href="{{ route('client.technical-service.index') }}"><span>Dịch vụ lắp đặt băng tải</span><i class="fa-solid fa-chevron-right"></i></a></li>
                            <li><a href="{{ route('client.technical-service.index') }}"><span>Lập trình PLC & Tủ điện</span><i class="fa-solid fa-chevron-right"></i></a></li>
                            <li><a href="{{ route('client.technical-service.index') }}"><span>Bảo trì & Cài đặt biến tần</span><i class="fa-solid fa-chevron-right"></i></a></li>
                            <li><a href="{{ route('client.technical-service.index') }}"><span>Giải pháp IoT Nhà máy</span><i class="fa-solid fa-chevron-right"></i></a></li>
                            <li><a href="{{ route('client.shop.index') }}"><span>Thiết bị tự động hóa</span><i class="fa-solid fa-chevron-right"></i></a></li>
                        @endif
                    </ul>
                </div>

                {{-- Box 2: Hỗ trợ tư vấn 24/7 --}}
                <div class="sidebar-box">
                    <div class="sidebar-box-header accent">
                        <i class="fa-solid fa-headset"></i>
                        <h3>Tư vấn kỹ thuật 24/7</h3>
                    </div>
                    <div class="sidebar-contact-body">
                        <div class="sidebar-hotline-badge">
                            <span class="sidebar-hotline-label">Hotline tư vấn kỹ thuật</span>
                            <a href="tel:{{ preg_replace('/[^0-9]/', '', $settings->hotline ?? '0983214896') }}" class="sidebar-hotline-num">
                                <i class="fa-solid fa-phone"></i>
                                <span>{{ $settings->hotline ?? '0983.214.896' }}</span>
                            </a>
                        </div>

                        <a href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', $settings->hotline ?? '0983214896') }}" target="_blank" rel="noopener noreferrer" class="sidebar-action-btn zalo-btn">
                            <i class="fa-solid fa-comment-dots"></i> Chat Zalo tư vấn ngay
                        </a>

                        <ul class="sidebar-commit-list">
                            <li><i class="fa-solid fa-circle-check"></i> Khảo sát hiện trạng tận nơi</li>
                            <li><i class="fa-solid fa-circle-check"></i> Thiết kế bản vẽ 2D/3D tối ưu</li>
                            <li><i class="fa-solid fa-circle-check"></i> Báo giá nhanh chóng trong 30p</li>
                            <li><i class="fa-solid fa-circle-check"></i> Bảo hành kỹ thuật dài hạn</li>
                        </ul>
                    </div>
                </div>

                {{-- Box 3: Thông tin đơn vị Bảo Tín JSC --}}
                <div class="sidebar-box">
                    <div class="sidebar-box-header">
                        <i class="fa-solid fa-building"></i>
                        <h3>Bảo Tín JSC</h3>
                    </div>
                    <div class="sidebar-info-body">
                        <div class="sidebar-info-item">
                            <i class="fa-solid fa-location-dot"></i>
                            <div>
                                <strong>Văn phòng & Nhà xưởng:</strong><br>
                                {{ $settings->address ?? 'Hà Nội, Việt Nam' }}
                            </div>
                        </div>
                        <div class="sidebar-info-item">
                            <i class="fa-solid fa-envelope"></i>
                            <div>
                                <strong>Email báo giá:</strong><br>
                                {{ $settings->email ?? 'info@baotinjsc.vn' }}
                            </div>
                        </div>
                        <div class="sidebar-info-item">
                            <i class="fa-solid fa-globe"></i>
                            <div>
                                <strong>Website:</strong><br>
                                {{ url('/') }}
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- CỘT PHẢI: NỘI DUNG CHÍNH --}}
            <main class="service-main-content">
                {{-- Header: H1 Title --}}
                <div class="service-header">
                    <h1 class="service-title-h1">{{ $product->name }}</h1>
                    <div class="service-meta-bar">
                        <span class="service-badge-tech"><i class="fa-solid fa-screwdriver-wrench"></i> Dịch vụ & Giải pháp</span>
                        @if($product->updated_at)
                            <span class="service-meta-item"><i class="fa-regular fa-clock"></i> Cập nhật: {{ $product->updated_at->format('d/m/Y') }}</span>
                        @endif
                        <span class="service-meta-item"><i class="fa-solid fa-shield-halved"></i> Khảo sát & Thi công toàn quốc</span>
                    </div>
                </div>

                {{-- SHOWCASE GALLERY (NHIỀU ẢNH THEO ẢNH MẪU) --}}
                @php
                    $allServiceImages = collect();
                    if (isset($galleryImages) && $galleryImages->isNotEmpty()) {
                        $allServiceImages = $galleryImages;
                    } elseif ($product->images && $product->images->isNotEmpty()) {
                        foreach ($product->images as $img) {
                            $allServiceImages->push(['id' => $img->id, 'url' => $img->url]);
                        }
                    } elseif ($product->primaryImage && $product->primaryImage->url) {
                        $allServiceImages->push(['id' => $product->primaryImage->id, 'url' => $product->primaryImage->url]);
                    }
                    if ($allServiceImages->isEmpty()) {
                        $allServiceImages->push(['id' => 0, 'url' => 'no-image.webp']);
                    }
                    $mainImgUrl = $resolveImg($allServiceImages->first()['url'] ?? null);
                @endphp

                <div class="service-showcase-gallery">
                    {{-- Ảnh lớn chính --}}
                    <div class="service-main-image-wrap" id="serviceMainImageWrap" onclick="openServiceLightbox(0)">
                        <span class="service-gallery-badge">Hình ảnh thực tế</span>
                        <img id="serviceMainImage" src="{{ $mainImgUrl }}" alt="{{ $product->name }}">
                        <div class="service-zoom-hint">
                            <i class="fa-solid fa-magnifying-glass-plus"></i> Phóng to ảnh
                        </div>
                    </div>

                    {{-- Dải thumbnails nhiều ảnh --}}
                    <div class="service-thumbs-col" id="serviceThumbsCol">
                        @foreach($allServiceImages as $index => $itemImg)
                            @php
                                $thumbUrl = $resolveImg($itemImg['url'] ?? null);
                            @endphp
                            <div class="service-thumb-item {{ $loop->first ? 'active' : '' }}" 
                                 onclick="changeMainServiceImage('{{ $thumbUrl }}', {{ $index }}, this)"
                                 data-img-src="{{ $thumbUrl }}">
                                <img src="{{ $thumbUrl }}" alt="{{ $product->name }} - Ảnh {{ $index + 1 }}">
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- CTA ACTION BAR --}}
                <div class="service-cta-bar">
                    <div class="service-cta-text">
                        <strong>Cần tư vấn thiết kế & báo giá giải pháp này?</strong>
                        <div>Liên hệ ngay để nhận giải pháp tối ưu và mức giá cạnh tranh nhất.</div>
                    </div>
                    <div class="service-cta-buttons">
                        <a href="#consultation-box" class="btn-cta-quote">
                            <i class="fa-solid fa-paper-plane"></i> Nhận báo giá ngay
                        </a>
                        <a href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', $settings->hotline ?? '0983214896') }}" target="_blank" rel="noopener noreferrer" class="btn-cta-zalo">
                            <i class="fa-solid fa-comment-dots"></i> Chat Zalo
                        </a>
                        <a href="tel:{{ preg_replace('/[^0-9]/', '', $settings->hotline ?? '0983214896') }}" class="btn-cta-call">
                            <i class="fa-solid fa-phone"></i> {{ $settings->hotline ?? '0983.214.896' }}
                        </a>
                    </div>
                </div>

                {{-- MỤC LỤC BÀI VIẾT TỰ ĐỘNG (TABLE OF CONTENTS) --}}
                <div class="service-toc-box" id="serviceTocBox" style="display: none;">
                    <div class="service-toc-header" onclick="toggleServiceToc()">
                        <div class="service-toc-title">
                            <i class="fa-solid fa-list-ul text-primary"></i> Mục lục nội dung
                        </div>
                        <button type="button" class="service-toc-toggle" id="serviceTocToggle">
                            <span>[Thu gọn]</span>
                        </button>
                    </div>
                    <ol class="service-toc-list" id="serviceTocList"></ol>
                </div>

                {{-- MÔ TẢ NGẮN (LEAD SUMMARY) --}}
                @if(!empty($product->short_description))
                    <div class="service-lead-summary">
                        {!! $product->short_description !!}
                    </div>
                @endif

                {{-- MÔ TẢ CHI TIẾT (BODY CONTENT) --}}
                <article class="service-body-content" id="serviceBodyContent">
                    @if(!empty($product->description))
                        {!! $product->description !!}
                    @else
                        <p>Thông tin chi tiết về dịch vụ <strong>{{ $product->name }}</strong> đang được cập nhật. Quý khách vui lòng liên hệ hotline <strong>{{ $settings->hotline ?? '0983.214.896' }}</strong> để nhận tư vấn kỹ thuật trực tiếp.</p>
                    @endif

                    {{-- Video nếu có --}}
                    @if(!empty($product->video_url))
                        @php
                            $videoUrl = $product->video_url;
                            $embedUrl = null;
                            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $videoUrl, $matches)) {
                                $embedUrl = 'https://www.youtube.com/embed/' . $matches[1];
                            }
                        @endphp
                        @if($embedUrl)
                            <div class="my-4">
                                <h3 class="mb-3"><i class="fa-brands fa-youtube text-danger me-2"></i>Video thực tế</h3>
                                <div class="ratio ratio-16x9 service-video-embed">
                                    <iframe src="{{ $embedUrl }}" title="{{ $product->name }}" allowfullscreen loading="lazy"></iframe>
                                </div>
                            </div>
                        @endif
                    @endif
                </article>

                {{-- BANNER ĐĂNG KÝ TƯ VẤN NHANH Ở CHÂN BÀI --}}
                <div class="service-consultation-banner" id="consultation-box">
                    <div class="consult-banner-info">
                        <h3>Đăng ký nhận tư vấn & khảo sát miễn phí</h3>
                        <p>Để lại số điện thoại, kỹ sư Bảo Tín JSC sẽ liên hệ lại ngay trong 15 phút để tư vấn giải pháp tối ưu nhất cho nhà máy của bạn.</p>
                        <div class="consult-banner-perks">
                            <span><i class="fa-solid fa-circle-check"></i> Khảo sát tận nơi</span>
                            <span><i class="fa-solid fa-circle-check"></i> Tư vấn đúng nhu cầu</span>
                            <span><i class="fa-solid fa-circle-check"></i> Giá tốt nhất</span>
                        </div>
                    </div>
                    <div class="consult-banner-form">
                        <form action="{{ route('client.product.phone-request') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="tel" name="phone" required placeholder="Nhập số điện thoại của bạn..." class="consult-banner-input" pattern="[0-9]{10,11}">
                            <button type="submit" class="consult-banner-btn">
                                <i class="fa-solid fa-paper-plane me-1"></i> Gửi yêu cầu
                            </button>
                        </form>
                    </div>
                </div>

                {{-- DỊCH VỤ & GIẢI PHÁP LIÊN QUAN --}}
                @if(isset($relatedServices) && $relatedServices->isNotEmpty())
                    <section class="service-related-section">
                        <div class="service-related-heading">
                            <i class="fa-solid fa-network-wired"></i>
                            <span>Dịch vụ & Giải pháp liên quan</span>
                        </div>
                        <div class="service-related-grid">
                            @foreach($relatedServices as $rel)
                                @php
                                    $relImgPath = ($rel->primaryImage && $rel->primaryImage->url) ? $rel->primaryImage->url : null;
                                    $relImgUrl = $resolveImg($relImgPath);
                                @endphp
                                <div class="service-card-item">
                                    <a href="{{ url('/' . $rel->slug) }}" class="service-card-img-wrap">
                                        <img src="{{ $relImgUrl }}" alt="{{ $rel->name }}" loading="lazy">
                                    </a>
                                    <div class="service-card-body">
                                        <h4 class="service-card-title">
                                            <a href="{{ url('/' . $rel->slug) }}">{{ $rel->name }}</a>
                                        </h4>
                                        <a href="{{ url('/' . $rel->slug) }}" class="service-card-link">
                                            Xem chi tiết <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </main>
        </div>
    </div>
</div>

{{-- LIGHTBOX MODAL --}}
<div class="service-lightbox-modal" id="serviceLightboxModal" onclick="closeServiceLightbox(event)">
    <div class="lightbox-content-box" onclick="event.stopPropagation()">
        <button type="button" class="lightbox-close-btn" onclick="closeServiceLightbox(null)" aria-label="Đóng">&times;</button>
        <button type="button" class="lightbox-nav-btn prev" onclick="navigateServiceLightbox(-1)" aria-label="Ảnh trước">&#10094;</button>
        <img id="serviceLightboxImg" src="" alt="{{ $product->name }}">
        <button type="button" class="lightbox-nav-btn next" onclick="navigateServiceLightbox(1)" aria-label="Ảnh sau">&#10095;</button>
    </div>
</div>
@endsection

@section('foot')
<script>
    // Gallery state
    const serviceImages = @json($allServiceImages->map(fn($img) => $resolveImg($img['url'] ?? null))->values());
    let currentImgIndex = 0;

    function changeMainServiceImage(imgSrc, index, thumbEl) {
        currentImgIndex = index;
        const mainImg = document.getElementById('serviceMainImage');
        if (mainImg) {
            mainImg.style.opacity = '0.3';
            setTimeout(() => {
                mainImg.src = imgSrc;
                mainImg.style.opacity = '1';
            }, 120);
        }

        // Highlight active thumbnail
        const thumbs = document.querySelectorAll('.service-thumb-item');
        thumbs.forEach(t => t.classList.remove('active'));
        if (thumbEl) {
            thumbEl.classList.add('active');
        }
    }

    // Lightbox modal logic
    function openServiceLightbox(index) {
        if (!serviceImages || serviceImages.length === 0) return;
        currentImgIndex = (typeof index === 'number') ? index : currentImgIndex;
        const modal = document.getElementById('serviceLightboxModal');
        const img = document.getElementById('serviceLightboxImg');
        if (modal && img) {
            img.src = serviceImages[currentImgIndex];
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeServiceLightbox(event) {
        const modal = document.getElementById('serviceLightboxModal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    function navigateServiceLightbox(direction) {
        if (!serviceImages || serviceImages.length === 0) return;
        currentImgIndex = (currentImgIndex + direction + serviceImages.length) % serviceImages.length;
        const img = document.getElementById('serviceLightboxImg');
        if (img) {
            img.style.opacity = '0.5';
            setTimeout(() => {
                img.src = serviceImages[currentImgIndex];
                img.style.opacity = '1';
            }, 100);
        }
    }

    // Keyboard navigation for Lightbox
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('serviceLightboxModal');
        if (modal && modal.classList.contains('active')) {
            if (e.key === 'Escape') closeServiceLightbox(null);
            if (e.key === 'ArrowLeft') navigateServiceLightbox(-1);
            if (e.key === 'ArrowRight') navigateServiceLightbox(1);
        }
    });

    // Auto Table of Contents generator
    document.addEventListener('DOMContentLoaded', function() {
        const contentArea = document.getElementById('serviceBodyContent');
        const tocBox = document.getElementById('serviceTocBox');
        const tocList = document.getElementById('serviceTocList');

        if (contentArea && tocBox && tocList) {
            const headings = contentArea.querySelectorAll('h2, h3');
            if (headings.length >= 2) {
                headings.forEach((heading, idx) => {
                    const id = 'section-heading-' + idx;
                    heading.id = id;

                    const li = document.createElement('li');
                    if (heading.tagName.toLowerCase() === 'h3') {
                        li.style.marginLeft = '18px';
                        li.style.listStyleType = 'circle';
                    }
                    const a = document.createElement('a');
                    a.href = '#' + id;
                    a.textContent = heading.textContent.trim();
                    a.addEventListener('click', function(e) {
                        e.preventDefault();
                        heading.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    });
                    li.appendChild(a);
                    tocList.appendChild(li);
                });
                tocBox.style.display = 'block';
            }
        }

        // Wrap tables in responsive container if not already wrapped
        if (contentArea) {
            const tables = contentArea.querySelectorAll('table');
            tables.forEach(table => {
                if (!table.parentElement.classList.contains('table-responsive')) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'table-responsive';
                    wrapper.style.overflowX = 'auto';
                    wrapper.style.marginBottom = '20px';
                    table.parentNode.insertBefore(wrapper, table);
                    wrapper.appendChild(table);
                }
            });
        }
    });

    function toggleServiceToc() {
        const tocList = document.getElementById('serviceTocList');
        const toggleBtn = document.getElementById('serviceTocToggle');
        if (tocList && toggleBtn) {
            if (tocList.style.display === 'none') {
                tocList.style.display = 'block';
                toggleBtn.querySelector('span').textContent = '[Thu gọn]';
            } else {
                tocList.style.display = 'none';
                toggleBtn.querySelector('span').textContent = '[Mở rộng]';
            }
        }
    }
</script>
@endsection
