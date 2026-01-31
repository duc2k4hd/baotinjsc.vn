@extends('clients.layouts.master')

@section('title', 'Dịch vụ kỹ thuật - ' . ($settings->site_name ?? 'Bảo Tín JSC'))

@section('schema')
@php
    $canonicalUrl = route('client.technical-service.index');
    
    // BreadcrumbList Schema
    $breadcrumbItems = [];
    $position = 1;
    
    // Trang chủ
    $breadcrumbItems[] = [
        '@type' => 'ListItem',
        'position' => $position++,
        'name' => 'Trang chủ',
        'item' => url('/')
    ];
    
    // Dịch vụ kỹ thuật
    $breadcrumbItems[] = [
        '@type' => 'ListItem',
        'position' => $position++,
        'name' => 'Dịch vụ kỹ thuật',
        'item' => $canonicalUrl
    ];
    
    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $breadcrumbItems
    ];
    
    // WebPage Schema
    $webPageSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => 'Dịch vụ kỹ thuật - ' . ($settings->site_name ?? 'Bảo Tín JSC'),
        'description' => 'Dịch vụ kỹ thuật chuyên nghiệp - Giải pháp tự động hóa công nghiệp hàng đầu Việt Nam',
        'url' => $canonicalUrl,
        'inLanguage' => 'vi-VN',
        'isPartOf' => [
            '@type' => 'WebSite',
            'name' => $settings->site_name ?? 'Bảo Tín JSC',
            'url' => url('/')
        ],
        'breadcrumb' => $breadcrumbSchema
    ];
    
    if (($settings->site_name ?? null)) {
        $webPageSchema['publisher'] = [
            '@type' => 'Organization',
            'name' => $settings->site_name ?? 'Bảo Tín JSC',
            'url' => url('/')
        ];
    }
@endphp

<script type="application/ld+json">
{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>

<script type="application/ld+json">
{!! json_encode($webPageSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endsection

@section('content')
<div class="technical-service-page">
    <!-- Hero Section -->
    <section class="service-hero">
        <div class="container">
            <div class="hero-content">
                <h1>Dịch vụ kỹ thuật chuyên nghiệp</h1>
                <p class="hero-subtitle">Giải pháp tự động hóa công nghiệp hàng đầu Việt Nam</p>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services-section">
        <div class="container">
            <div class="section-header">
                <h2>Dịch vụ của chúng tôi</h2>
                <p>Bảo Tín JSC cung cấp các dịch vụ kỹ thuật chuyên nghiệp, đáp ứng mọi nhu cầu tự động hóa</p>
            </div>

            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-cog"></i>
                    </div>
                    <h3>Tư vấn kỹ thuật</h3>
                    <p>Đội ngũ kỹ sư giàu kinh nghiệm tư vấn giải pháp tự động hóa phù hợp với từng dự án, giúp tối ưu hóa hiệu quả sản xuất và giảm chi phí vận hành.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h3>Lắp đặt & Vận hành</h3>
                    <p>Dịch vụ lắp đặt chuyên nghiệp, đảm bảo hệ thống hoạt động ổn định. Hỗ trợ vận hành và đào tạo nhân viên vận hành hệ thống.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-wrench"></i>
                    </div>
                    <h3>Bảo trì & Sửa chữa</h3>
                    <p>Dịch vụ bảo trì định kỳ và sửa chữa nhanh chóng, đảm bảo hệ thống luôn hoạt động ở trạng thái tốt nhất. Hỗ trợ 24/7 cho các sự cố khẩn cấp.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>Đào tạo kỹ thuật</h3>
                    <p>Chương trình đào tạo chuyên sâu về vận hành, bảo trì và xử lý sự cố, giúp đội ngũ kỹ thuật của khách hàng tự chủ trong vận hành hệ thống.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>Hỗ trợ kỹ thuật</h3>
                    <p>Hotline hỗ trợ kỹ thuật 24/7, giải đáp mọi thắc mắc và hỗ trợ xử lý sự cố nhanh chóng, đảm bảo không gián đoạn sản xuất.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <h3>Nâng cấp hệ thống</h3>
                    <p>Tư vấn và thực hiện nâng cấp hệ thống hiện có, cải thiện hiệu suất và tích hợp công nghệ mới phù hợp với xu hướng công nghiệp 4.0.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    {{-- <section class="why-choose-section">
        <div class="container">
            <div class="section-header">
                <h2>Tại sao chọn Bảo Tín JSC?</h2>
            </div>

            <div class="features-grid">
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <h4>Kinh nghiệm 15+ năm</h4>
                    <p>Hơn 15 năm kinh nghiệm trong lĩnh vực tự động hóa công nghiệp</p>
                </div>

                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <h4>Đội ngũ chuyên nghiệp</h4>
                    <p>Kỹ sư được đào tạo bài bản, có chứng chỉ quốc tế</p>
                </div>

                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <h4>Hỗ trợ 24/7</h4>
                    <p>Luôn sẵn sàng hỗ trợ khách hàng mọi lúc, mọi nơi</p>
                </div>

                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <h4>Giá cả hợp lý</h4>
                    <p>Cam kết giá cạnh tranh, minh bạch, không phát sinh chi phí</p>
                </div>

                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <h4>Bảo hành dài hạn</h4>
                    <p>Chế độ bảo hành toàn diện, bảo trì định kỳ</p>
                </div>

                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <h4>Giải pháp tối ưu</h4>
                    <p>Tư vấn giải pháp phù hợp nhất với nhu cầu và ngân sách</p>
                </div>
            </div>
        </div>
    </section> --}}

    <!-- Process Section -->
    <section class="process-section">
        <div class="container">
            <div class="section-header">
                <h2>Quy trình làm việc</h2>
            </div>

            <div class="process-steps">
                <div class="process-step">
                    <div class="step-number">1</div>
                    <h4>Tiếp nhận yêu cầu</h4>
                    <p>Khách hàng liên hệ và mô tả nhu cầu, chúng tôi sẽ tiếp nhận và phân tích yêu cầu</p>
                </div>

                <div class="process-step">
                    <div class="step-number">2</div>
                    <h4>Tư vấn & Báo giá</h4>
                    <p>Kỹ sư sẽ đến khảo sát thực tế và đưa ra giải pháp tối ưu cùng báo giá chi tiết</p>
                </div>

                <div class="process-step">
                    <div class="step-number">3</div>
                    <h4>Ký kết hợp đồng</h4>
                    <p>Thống nhất phương án và ký kết hợp đồng với các điều khoản rõ ràng</p>
                </div>

                <div class="process-step">
                    <div class="step-number">4</div>
                    <h4>Thực hiện dự án</h4>
                    <p>Tiến hành lắp đặt, cấu hình và vận hành hệ thống theo đúng tiến độ</p>
                </div>

                <div class="process-step">
                    <div class="step-number">5</div>
                    <h4>Nghiệm thu & Bàn giao</h4>
                    <p>Kiểm tra, nghiệm thu và bàn giao hệ thống, đào tạo vận hành cho khách hàng</p>
                </div>

                <div class="process-step">
                    <div class="step-number">6</div>
                    <h4>Bảo hành & Hỗ trợ</h4>
                    <p>Hỗ trợ kỹ thuật và bảo hành dài hạn, đảm bảo hệ thống hoạt động ổn định</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    @if(isset($featuredProducts) && $featuredProducts->isNotEmpty())
    <section class="featured-products-section">
        <div class="container">
            <div class="section-header">
                <h2>Sản phẩm nổi bật</h2>
                <p>Một số sản phẩm tự động hóa chất lượng cao của chúng tôi</p>
            </div>

            <div class="products-grid">
                @foreach($featuredProducts as $product)
                <div class="product-card">
                    <a href="/{{ $product->slug ?? '#' }}">
                        @if($product->primaryImage && $product->primaryImage->url)
                            <img src="{{ asset('clients/assets/img/clothes/' . $product->primaryImage->url) }}" alt="{{ $product->name }}">
                        @else
                            <img src="{{ asset('clients/assets/img/clothes/no-image.webp') }}" alt="{{ $product->name }}">
                        @endif
                        <h4>{{ $product->name }}</h4>
                        @if($product->price && $product->price >= 1)
                            @if($product->sale_price && $product->sale_price < $product->price)
                                <p class="price">
                                    <span class="old-price">{{ number_format($product->price, 0, ',', '.') }} đ</span>
                                    <span class="sale-price">{{ number_format($product->sale_price, 0, ',', '.') }} đ</span>
                                </p>
                            @else
                                <p class="price">{{ number_format($product->price, 0, ',', '.') }} đ</p>
                            @endif
                        @else
                            <p class="contact">Liên hệ {{ $settings->contact_phone ?? '0985214595' }}</p>
                        @endif
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Bạn cần tư vấn về dịch vụ kỹ thuật?</h2>
                <p>Liên hệ ngay với chúng tôi để được tư vấn miễn phí và nhận báo giá tốt nhất</p>
                <div class="cta-buttons">
                    <a href="{{ route('client.contact.index') }}" class="btn btn-primary">Liên hệ ngay</a>
                    <a href="tel:{{ $settings->contact_phone ?? '0985214595' }}" class="btn btn-secondary">
                        <i class="fas fa-phone"></i> {{ $settings->contact_phone ?? '0985214595' }}
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
