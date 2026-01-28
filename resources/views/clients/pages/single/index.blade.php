@extends('clients.layouts.master')

@section('title', ($product->meta_title ?? $product->name) . ' - ' . ($settings->site_name ?? 'Bảo Tín JSC'))

@section('head')
    <link rel="stylesheet" href="{{ asset('clients/assets/css/single.css') }}">
    
    @php
        $pageTitle = $product->meta_title ?? $product->name ?? 'Sản phẩm';
        $pageDescription = $product->meta_description ?? $product->excerpt ?? $product->name ?? '';
        $pageKeywords = $product->meta_keywords ?? '';
        $canonicalUrl = url('/' . ($product->slug ?? ''));
        $pageImage = ($product->primaryImage ?? null) && ($product->primaryImage->url ?? null)
            ? asset('clients/assets/img/clothes/' . $product->primaryImage->url)
            : asset('clients/assets/img/clothes/no-image.webp');
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
    <meta property="og:type" content="product">
    <meta property="og:site_name" content="{{ $settings->site_name ?? $settings->subname ?? 'Bảo Tín JSC' }}">
    <meta property="og:locale" content="vi_VN">

    <!-- 🐦 Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $pageImage }}">
    <meta name="twitter:creator" content="{{ $settings->site_name ?? $settings->subname ?? 'Bảo Tín JSC' }}">

    <!-- 🔗 Canonical & hreflang -->
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="alternate" hreflang="vi" href="{{ $canonicalUrl }}">
    <link rel="alternate" hreflang="x-default" href="{{ $canonicalUrl }}">
@endsection

@section('schema')
@php
    $productUrl = url('/' . ($product->slug ?? ''));
    $productImage = ($product->primaryImage ?? null) && ($product->primaryImage->url ?? null)
        ? asset('clients/assets/img/clothes/' . $product->primaryImage->url) 
        : asset('clients/assets/img/clothes/no-image.webp');
    
    // Xác định giá và availability
    $price = null;
    $salePrice = null;
    $priceCurrency = 'VND';
    $availability = 'https://schema.org/OutOfStock';
    
    $hasVariants = method_exists($product, 'hasVariants') ? ($product->hasVariants() ?? false) : false;
    $variantsCount = ($product->variants ?? collect())->count() ?? 0;
    
    if ($hasVariants && $variantsCount > 0) {
        // Nếu có variants, lấy giá từ variant đầu tiên có stock
        foreach (($product->variants ?? collect()) as $variant) {
            if (($variant->stock_quantity ?? null) === null || ($variant->stock_quantity ?? 0) > 0) {
                $availability = 'https://schema.org/InStock';
                if (($variant->sale_price ?? 0) && $variant->sale_price > 0) {
                    $salePrice = $variant->sale_price;
                    $price = $variant->price ?? $variant->sale_price;
                } elseif (($variant->price ?? 0) && $variant->price > 0) {
                    $price = $variant->price;
                }
                break;
            }
        }
    } else {
        // Nếu không có variants, dùng giá của product
        if (($quantityProductDetail ?? 0) > 0) {
            $availability = 'https://schema.org/InStock';
        }
        if (($product->sale_price ?? 0) && $product->sale_price > 0 && ($product->sale_price ?? 0) < ($product->price ?? 0)) {
            $salePrice = $product->sale_price;
            $price = $product->price ?? 0;
        } elseif (($product->price ?? 0) && $product->price > 0) {
            $price = $product->price;
        }
    }
    
    // Nếu giá < 1 thì không hiển thị giá trong schema
    if ($price && $price < 1) {
        $price = null;
        $salePrice = null;
                }
    
    // AggregateRating
    $aggregateRating = null;
    if (isset($ratingStats) && isset($ratingStats['average']) && ($ratingStats['average'] ?? 0) > 0) {
        $aggregateRating = [
            '@type' => 'AggregateRating',
            'ratingValue' => number_format($ratingStats['average'] ?? 0, 1),
            'reviewCount' => $ratingStats['count'] ?? $totalComments ?? 0,
            'bestRating' => '5',
            'worstRating' => '1'
        ];
    }
    
    // Reviews
    $reviews = [];
    $commentsCollection = $comments ?? collect();
    if ($commentsCollection->isNotEmpty()) {
        foreach ($commentsCollection->take(5) as $comment) {
            if (($comment->rating ?? 0) && $comment->rating > 0) {
                $reviews[] = [
                    '@type' => 'Review',
                    'author' => [
                        '@type' => 'Person',
                        'name' => $comment->name ?? 'Khách hàng'
                    ],
                    'datePublished' => ($comment->created_at ?? now())->format('Y-m-d'),
                    'reviewBody' => $comment->content ?? '',
                    'reviewRating' => [
                        '@type' => 'Rating',
                        'ratingValue' => $comment->rating ?? 0,
                        'bestRating' => '5',
                        'worstRating' => '1'
                    ]
                ];
            }
        }
    }
        @endphp
<script type="application/ld+json">
{
    "@context": "https://schema.org/",
    "@type": "Product",
    "name": "{{ addslashes($product->name ?? '') }}",
    "description": "{{ addslashes($product->meta_description ?? $product->excerpt ?? $product->name ?? '') }}",
    "image": "{{ $productImage }}",
    "sku": "{{ $product->sku ?? '' }}",
    "mpn": "{{ $product->sku ?? '' }}",
    "url": "{{ $productUrl }}",
    @if(($product->brand ?? null))
    "brand": {
        "@type": "Brand",
        "name": "{{ addslashes($product->brand->name ?? '') }}"
    },
    @endif
    @if($price || $salePrice)
    "offers": {
        "@type": "Offer",
        "url": "{{ $productUrl }}",
        "priceCurrency": "{{ $priceCurrency }}",
        @if($salePrice)
        "price": "{{ number_format($salePrice, 0, '', '') }}",
        "priceValidUntil": "{{ date('Y-m-d', strtotime('+1 year')) }}",
        @elseif($price)
        "price": "{{ number_format($price, 0, '', '') }}",
        "priceValidUntil": "{{ date('Y-m-d', strtotime('+1 year')) }}",
        @endif
        "availability": "{{ $availability }}",
        "itemCondition": "https://schema.org/NewCondition",
        "seller": {
            "@type": "Organization",
            "name": "{{ addslashes($settings->site_name ?? 'Bảo Tín JSC') }}"
        }
    },
    @endif
    @if($aggregateRating)
    "aggregateRating": {!! json_encode($aggregateRating, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!},
    @endif
    @if(!empty($reviews))
    "review": {!! json_encode($reviews, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    @endif
}
    </script>

@php
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
    
    // Category (nếu có)
    if (($product->primaryCategory ?? null)) {
        $categorySlug = $product->primaryCategory->slug ?? '';
        $categoryUrl = $categorySlug ? url('/' . $categorySlug) : url('/');
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => $product->primaryCategory->name ?? 'Danh mục',
            'item' => $categoryUrl
        ];
    }
    
    // Product
    $breadcrumbItems[] = [
        '@type' => 'ListItem',
        'position' => $position,
        'name' => $product->name ?? 'Sản phẩm',
        'item' => $productUrl
    ];
                    @endphp
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": {!! json_encode($breadcrumbItems, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
}
</script>

@php
    // WebPage Schema
    $webPageSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => ($product->meta_title ?? $product->name ?? 'Sản phẩm') . ' - ' . ($settings->site_name ?? 'Bảo Tín JSC'),
        'description' => $product->meta_description ?? $product->excerpt ?? $product->name ?? '',
        'url' => $productUrl,
        'inLanguage' => 'vi-VN',
        'isPartOf' => [
            '@type' => 'WebSite',
            'name' => $settings->site_name ?? 'Bảo Tín JSC',
            'url' => url('/')
        ],
        'about' => [
            '@type' => 'Product',
            'name' => $product->name ?? 'Sản phẩm',
            'url' => $productUrl
        ],
        'breadcrumb' => [
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbItems ?? []
        ]
    ];
    
    if ($productImage ?? null) {
        $webPageSchema['image'] = $productImage;
    }
    
    if (($settings->site_name ?? null)) {
        $webPageSchema['publisher'] = [
            '@type' => 'Organization',
            'name' => $settings->site_name ?? 'Bảo Tín JSC',
            'url' => url('/')
        ];
                }
                    @endphp
<script type="application/ld+json">
{!! json_encode($webPageSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>

                            @php
    // Organization Schema
    $organizationSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $settings->site_name ?? 'Bảo Tín JSC',
        'url' => url('/'),
        'logo' => ($settings->site_logo ?? null) ? asset('clients/assets/img/business/' . $settings->site_logo) : null,
        'description' => $settings->site_description ?? 'Nhà cung cấp thiết bị tự động hóa công nghiệp hàng đầu Việt Nam',
        'address' => [
            '@type' => 'PostalAddress',
            'addressCountry' => 'VN',
            'addressLocality' => $settings->contact_address ?? null
        ],
        'contactPoint' => []
    ];
    
    // Thêm contact phone nếu có
    if (($settings->contact_phone ?? null)) {
        $organizationSchema['contactPoint'][] = [
            '@type' => 'ContactPoint',
            'telephone' => $settings->contact_phone ?? '',
            'contactType' => 'customer service',
            'areaServed' => 'VN',
            'availableLanguage' => 'Vietnamese'
        ];
    }
    
    // Thêm email nếu có
    if (($settings->contact_email ?? null)) {
        $organizationSchema['email'] = $settings->contact_email ?? '';
    }
    
    // Thêm social media nếu có
    $sameAs = [];
    if (($settings->facebook_link ?? null)) {
        $sameAs[] = $settings->facebook_link ?? '';
    }
    if (($settings->twitter_link ?? null)) {
        $sameAs[] = $settings->twitter_link ?? '';
    }
    if (($settings->instagram_link ?? null)) {
        $sameAs[] = $settings->instagram_link ?? '';
    }
    if (!empty($sameAs)) {
        $organizationSchema['sameAs'] = array_filter($sameAs);
    }
    
    // Xóa các field null
    $organizationSchema = array_filter($organizationSchema, function($value) {
        return $value !== null && $value !== '';
    });
    
    // Xử lý address nếu có field null
    if (isset($organizationSchema['address'])) {
        $organizationSchema['address'] = array_filter($organizationSchema['address'], function($value) {
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
{!! json_encode($organizationSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
                        </script>
@endsection

@section('content')
        <!-- Breadcrumb -->
    <div class="breadcrumb">
        <div class="container">
            <a href="{{ route('client.home.index') }}">🏠 Trang chủ</a> / 
            @if($product->primaryCategory)
                <a href="/{{ $product->primaryCategory->slug ?? '#' }}">{{ $product->primaryCategory->name }}</a> / 
                        @endif
            <span>{{ $product->name }}</span>
                            </div>
                                </div>

    <!-- Main Content -->
    <main class="container">
        <div class="product-section">
            <!-- Product Images -->
            <div class="product-images">
                <div class="main-image">
                    @if($product->primaryImage && $product->primaryImage->url)
                        <img src="{{ asset('clients/assets/img/clothes/' . $product->primaryImage->url) }}" alt="{{ $product->name }}" id="mainImage">
                    @else
                        <img src="{{ asset('clients/assets/img/clothes/no-image.webp') }}" alt="{{ $product->name }}" id="mainImage">
                    @endif
                    <button class="zoom-btn">🔍 PHÓNG TO</button>
                            </div>
                <div class="thumbnail">
                    @if(isset($galleryImages) && $galleryImages->isNotEmpty())
                        @foreach($galleryImages as $image)
                            @if($image['url'])
                                <img src="{{ asset('clients/assets/img/clothes/' . $image['url']) }}" alt="{{ $product->name }}" class="thumb-img {{ $loop->first ? 'active' : '' }}" data-image-id="{{ $image['id'] ?? '' }}">
                    @endif
                        @endforeach
                    @elseif($product->primaryImage && $product->primaryImage->url)
                        <img src="{{ asset('clients/assets/img/clothes/' . $product->primaryImage->url) }}" alt="{{ $product->name }}" class="thumb-img active">
                    @endif
                            </div>
                    </div>

            <!-- Product Info -->
            <div class="product-info">
                <h1>{{ strtoupper($product->name) }}</h1>
                

                <!-- Product Info Details -->
                <div class="product-info-details">
                            @if($product->brand)
                    <div class="brand">
                        <strong>Thương hiệu:</strong> <a href="/cua-hang?brands={{ $product->brand->slug ?? '#' }}">{{ $product->brand->name ?? '' }}</a>
                    </div>
                            @endif
                    <div class="info-item">
                        <span class="info-label">Bảo hành:</span>
                        <span class="info-value">12 tháng</span>
                        </div>
                    <div class="info-item">
                        <span class="info-label">CO CQ:</span>
                        <span class="info-value">Đầy đủ</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tình trạng:</span>
                        <span class="info-value {{ $quantityProductDetail > 0 ? 'in-stock' : 'out-of-stock' }}">
                            @if($product->hasVariants() && $product->variants->count() > 0)
                                @php
                                    $hasStock = false;
                                    foreach ($product->variants as $variant) {
                                        if ($variant->stock_quantity === null || $variant->stock_quantity > 0) {
                                            $hasStock = true;
                                                break;
                                        }
                                    }
                                @endphp
                                @if($hasStock)
                                    <span class="status-text">Còn hàng</span>
                                @else
                                    <span class="status-text">Liên hệ</span>
                                @endif
                            @else
                                @if($quantityProductDetail > 0)
                                    <span class="status-text">Còn hàng</span>
                                @else
                                    <span class="status-text">Liên hệ</span>
                                @endif
                            @endif
                            </span>
                        </div>
                    </div>

                <!-- Product Variants -->
                @if($product->hasVariants() && $product->variants->count() > 0)
                    <div class="product-variants">
                        <div class="variants-list">
                            @foreach($product->variants as $variant)
                                    @php
                                        $variantStock = $variant->stock_quantity;
                                    $variantInStock = $variantStock === null || $variantStock > 0;
                                    @endphp
                                    <button type="button" 
                                        class="variant-option {{ $loop->first ? 'active' : '' }} {{ !$variantInStock ? 'out-of-stock' : '' }}" 
                                        data-variant-id="{{ $variant->id }}"
                                        data-variant-name="{{ $variant->name }}"
                                        data-variant-price="{{ $variant->price }}"
                                        data-variant-sale-price="{{ $variant->sale_price ?? '' }}"
                                        data-variant-image="{{ $variant->primaryVariantImage ? asset('clients/assets/img/clothes/' . $variant->primaryVariantImage->url) : ($product->primaryImage ? asset('clients/assets/img/clothes/' . $product->primaryImage->url) : '') }}"
                                        data-variant-stock="{{ $variantStock ?? 'unlimited' }}"
                                        data-variant-in-stock="{{ $variantInStock ? '1' : '0' }}">
                                    {{ $variant->name }}
                                    @if($variant->price)
                                        <span class="variant-price">
                                            @if($variant->sale_price && $variant->sale_price < $variant->price)
                                                <span class="sale-price">{{ number_format($variant->sale_price, 0, ',', '.') }} đ</span>
                                                <span class="old-price">{{ number_format($variant->price, 0, ',', '.') }} đ</span>
                                                
                                            @else
                                                {{ number_format($variant->price, 0, ',', '.') }} đ
                                            @endif
                        </span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                @if($product->price && $product->price >= 1)
                    <div class="product-price" id="product-price">
                        @if($product->sale_price && $product->sale_price < $product->price)
                            <span class="sale-price">{{ number_format($product->sale_price, 0, ',', '.') }} đ</span>
                            <span class="old-price">{{ number_format($product->price, 0, ',', '.') }} đ</span></span>
                            
                            @else
                            <strong>{{ number_format($product->price, 0, ',', '.') }} đ</strong>
                            @endif
                    </div>
                        @else
                    <div class="contact-price">
                        <strong>Liên hệ</strong>
                    </div>
                    <div class="hotline">
                        Nhận báo giá <strong style="color: #E74C3C;">{{ $settings->contact_phone ?? '0903 223 663' }}</strong>
                    </div>
                                @endif

                @if($product->price && $product->price >= 1)
                    <div class="quantity-selector">
                        <button class="qty-btn" onclick="decreaseQty()">-</button>
                        <input type="number" value="1" id="quantity" min="1">
                        <button class="qty-btn" onclick="increaseQty()">+</button>
                            </div>
                @endif

                <!-- Action Buttons -->
                <div class="action-buttons">
                    @if($product->price && $product->price >= 1)
                        <div class="action-buttons-row">
                            <form action="{{ route('client.cart.store') }}" method="POST" class="add-to-cart-form">
                            @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="product_variant_id" value="" id="product-variant-id">
                                <input type="hidden" name="quantity" value="1" id="cart-quantity">
                                <button type="submit" class="btn-add-cart" id="btn-add-cart" 
                                        @if($product->hasVariants() && $product->variants->count() > 0) disabled @elseif($quantityProductDetail <= 0) disabled @endif>
                                    <i class="fas fa-shopping-cart"></i> 
                                    <span id="btn-add-cart-text">Thêm vào giỏ hàng</span>
                                                </button>
                            </form>
                            <button class="btn-favorite" 
                                    data-product-id="{{ $product->id }}"
                                    data-wishlist-add="{{ route('client.product.wishlist.add') }}"
                                    data-wishlist-remove="{{ route('client.product.wishlist.remove') }}">
                                <i class="fas fa-heart"></i> Yêu thích
                                                </button>
                                            </div>
                    @else
                        <div class="action-buttons-row">
                            <button class="quote-btn">
                                <i class="fas fa-phone"></i> NHẬN BÁO GIÁ
                            </button>
                                        </div>
                    @endif
                    <div class="action-buttons-row">
                        <a href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', $settings->contact_phone ?? '0985214595') }}?text={{ urlencode('Xin chào, tôi muốn tư vấn về sản phẩm: ' . $product->name) }}" 
                           target="_blank" 
                           class="btn-zalo">
                            <i class="fab fa-zalo"></i> Liên hệ qua Zalo
                        </a>
                                                        </div>
                                                    </div>

                <!-- Features -->
                <div class="features">
                    <div class="feature-item">
                        <span class="icon">🚚</span>
                        <div>
                            <strong>Giao hàng trong 24h</strong>
                            <p>Áp dụng với đơn hàng trên 900.000 đ tại Hà Nội</p>
                                            </div>
                                        </div>
                    <div class="feature-item">
                        <span class="icon">✓</span>
                        <div>
                            <strong>Bảo đảm chất lượng</strong>
                            <p>Tất cả các sản phẩm {{ $settings->site_name ?? 'Bảo Tín JSC' }} cung cấp đều được bảo đảm chất lượng từ đầu đến cuối</p>
                                    </div>
                        </div>
                    <div class="feature-item">
                        <span class="icon">⚙</span>
                        <div>
                            <strong>Sản phẩm chính hãng</strong>
                            <p>Chúng tôi cung cấp các sản phẩm nhập khẩu chính hãng, bảo hành lâu dài</p>
                                    </div>
                        </div>
                </div>

                {{-- Danh sách CSKH --}}
                @if(isset($supportStaff) && $supportStaff->count())
                    <div class="support-staff-section">
                        <h3 class="support-staff-title">Danh sách CSKH</h3>
                        <div class="support-staff-grid">
                            @foreach($supportStaff as $staff)
                                <div class="support-staff-card" @if($staff->color) style="background-color: {{ $staff->color }};" @endif>
                                    @if($staff->avatar)
                                        <div class="support-staff-avatar">
                                            <img src="{{ asset('clients/assets/img/avatars/' . $staff->avatar) }}"
                                                 alt="{{ $staff->name }}">
                                        </div>
                                    @endif
                                    <div class="support-staff-info">
                                        <div class="support-staff-name">{{ $staff->name }}</div>
                                        @if($staff->role)
                                            <div class="support-staff-role">{{ $staff->role }}</div>
                                        @endif
                                        <div class="support-staff-contact">
                                            @if($staff->phone)
                                                <a href="tel:{{ $staff->phone }}" class="support-staff-phone">
                                                    📞 {{ $staff->phone }}
                                                </a>
                                            @endif
                                            @if($staff->zalo)
                                                <a href="https://zalo.me/{{ $staff->zalo }}"
                                                   target="_blank"
                                                   class="support-staff-zalo">
                                                    Zalo
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Related Products Sidebar -->
                {{-- @if($productFeatured && $productFeatured->isNotEmpty())
                    <div class="related-products-sidebar">
                        <h3>SẢN PHẨM NỔI BẬT</h3>
                        @foreach($productFeatured->take(4) as $featured)
                            <div class="sidebar-product">
                                @if($featured->primaryImage && $featured->primaryImage->url)
                                    <img src="{{ asset('clients/assets/img/clothes/' . $featured->primaryImage->url) }}" alt="{{ $featured->name }}">
                                @else
                                    <img src="{{ asset('clients/assets/img/clothes/no-image.webp') }}" alt="{{ $featured->name }}">
                                    @endif
                                <div>
                                    <a href="/{{ $featured->slug ?? '#' }}">{{ $featured->name }}</a>
                                    @if($featured->price && $featured->price >= 1)
                                        @if($featured->sale_price && $featured->sale_price < $featured->price)
                                            <p class="price">
                                                <span class="old-price">{{ number_format($featured->price, 0, ',', '.') }} đ</span>
                                                <span class="sale-price">{{ number_format($featured->sale_price, 0, ',', '.') }} đ</span>
                                            </p>
                                        @else
                                            <p class="price">{{ number_format($featured->price, 0, ',', '.') }} đ</p>
                                    @endif
                                    @else
                                        <p class="contact">Liên hệ</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif --}}
                        </div>
                    </div>

        <!-- Product Description -->
        <div class="product-description">
            <!-- Tabs -->
            <div class="desc-tabs">
                <button class="tab-btn active" data-tab="description-tab">Mô tả chi tiết</button>
                <button class="tab-btn" data-tab="catalog-tab">Catalog</button>
                <button class="tab-btn" data-tab="reviews-tab">Đánh giá</button>
                    </div>

            <!-- Tab Content -->
            <div id="description-tab" class="tab-content active">
                <div class="description-content">
                    @if($product->description)
                        <div class="description-text">
                            {!! $product->description !!}
                        </div>
                    @elseif($product->excerpt)
                        <p>{{ $product->short_description }}</p>
                            @endif
                    
                    
                        </div>
                    </div>

            <div id="catalog-tab" class="tab-content">
                <div class="catalog-content">
                    @if($product->link_catalog && is_array($product->link_catalog) && !empty($product->link_catalog))
                        <div class="catalog-list">
                            @foreach($product->link_catalog as $catalog)
                                @if(is_string($catalog) && !empty($catalog))
                                    <div class="catalog-item">
                                        <a href="{{ $catalog }}" target="_blank" rel="noopener noreferrer">
                                            <i class="fas fa-file-pdf"></i> {{ basename($catalog) }}
                                        </a>
                    </div>
                            @endif
                        @endforeach
                                        </div>
                    @else
                        <p>Chưa có catalog cho sản phẩm này.</p>
                        @endif
                    </div>
                </div>

            <div id="reviews-tab" class="tab-content">
                <div class="reviews-content">
                    <!-- Admin Reviews Section -->
                    @if(isset($adminReviews) && $adminReviews->isNotEmpty())
                        <div class="admin-reviews-section">
                            <h3 class="reviews-section-title">Đánh giá từ Bảo Tín JSC</h3>
                            <div class="reviews-list">
                                @foreach($adminReviews as $review)
                                    <div class="review-item admin-review">
                                        <div class="review-header">
                                            <div class="review-author">
                                                <strong>{{ $review->account->name ?? 'Bảo Tín JSC' }}</strong>
                                                <span class="admin-badge">Quản trị viên</span>
            </div>
                                            @if(isset($review->rating))
                                                <div class="review-rating">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $review->rating ? 'active' : '' }}"></i>
                                                    @endfor
                        </div>
                                            @endif
                                            <span class="review-date">{{ $review->created_at->format('d/m/Y') }}</span>
                        </div>
                                        <div class="review-body">
                                            <p>{{ $review->content ?? '' }}</p>
                </div>
            </div>
                                @endforeach
            </div>
                </div>
                    @endif

                    <!-- User Reviews Section -->
                    <div class="user-reviews-section">
                        <h3 class="reviews-section-title">Đánh giá từ khách hàng</h3>
                        
                        <!-- Review Form -->
                        <div class="review-form-container">
                            <h4>Viết đánh giá của bạn</h4>
                            <form id="review-form" class="review-form">
                                @csrf
                                <input type="hidden" name="commentable_type" value="product">
                                <input type="hidden" name="commentable_id" value="{{ $product->id }}">
                                
                                <div class="form-group">
                                    <label>Đánh giá của bạn *</label>
                                    <div class="rating-input">
                                        <input type="radio" name="rating" id="rating-5" value="5" required>
                                        <label for="rating-5" class="star-label"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="rating" id="rating-4" value="4" required>
                                        <label for="rating-4" class="star-label"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="rating" id="rating-3" value="3" required>
                                        <label for="rating-3" class="star-label"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="rating" id="rating-2" value="2" required>
                                        <label for="rating-2" class="star-label"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="rating" id="rating-1" value="1" required>
                                        <label for="rating-1" class="star-label"><i class="fas fa-star"></i></label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="review-name">Tên của bạn *</label>
                                    <input type="text" id="review-name" name="name" required 
                                           value="{{ auth('web')->user()->name ?? '' }}"
                                           placeholder="Nhập tên của bạn">
                            </div>
                                
                                <div class="form-group">
                                    <label for="review-email">Email *</label>
                                    <input type="email" id="review-email" name="email" required
                                           value="{{ auth('web')->user()->email ?? '' }}"
                                           placeholder="Nhập email của bạn">
                                            </div>
                                
                                <div class="form-group">
                                    <label for="review-content">Nội dung đánh giá *</label>
                                    <textarea id="review-content" name="content" rows="5" required 
                                              placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..."></textarea>
                                            </div>
                                
                                <button type="submit" class="btn-submit-review">Gửi đánh giá</button>
                            </form>
                                        </div>
                        
                        <!-- User Reviews List -->
                        @if(isset($comments) && $comments->isNotEmpty())
                            <div class="reviews-list">
                                @foreach($comments as $comment)
                                    <div class="review-item user-review">
                                        <div class="review-header">
                                            <strong>{{ $comment->name ?? 'Khách hàng' }}</strong>
                                            @if(isset($comment->rating))
                                                <div class="review-rating">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $comment->rating ? 'active' : '' }}"></i>
                                                    @endfor
                                    </div>
                                    @endif
                                            <span class="review-date">{{ $comment->created_at->format('d/m/Y') }}</span>
                                </div>
                                        <div class="review-body">
                                            <p>{{ $comment->content ?? '' }}</p>
                        </div>
                                        @if($comment->adminReply)
                                            <div class="admin-reply">
                                                <div class="admin-reply-header">
                                                    <strong>{{ $comment->adminReply->account->name ?? 'Bảo Tín JSC' }}</strong>
                                                    <span class="admin-badge">Quản trị viên</span>
                    </div>
                                                <div class="admin-reply-body">
                                                    <p>{{ $comment->adminReply->content }}</p>
                    </div>
                    </div>
                                        @endif
                </div>
                                @endforeach
            </div>
                            @else
                            <p class="no-reviews">Chưa có đánh giá nào từ khách hàng cho sản phẩm này.</p>
                            @endif
                        </div>
                    </div>
                    </div>
                    </div>

        <!-- Related Products Section -->
        @if($productRelated && $productRelated->isNotEmpty())
            <section class="related-products">
                <h2>SẢN PHẨM LIÊN QUAN</h2>
                <div class="product-grid">
                    @foreach($productRelated->take(10) as $related)
                        <div class="product-card">
                            <a href="/{{ $related->slug ?? '#' }}">
                                @if($related->primaryImage && $related->primaryImage->url)
                                    <img src="{{ asset('clients/assets/img/clothes/' . $related->primaryImage->url) }}" alt="{{ $related->name }}">
                                @else
                                    <img src="{{ asset('clients/assets/img/clothes/no-image.webp') }}" alt="{{ $related->name }}">
                @endif
                                <h4>{{ $related->name }}</h4>
                                <p class="contact">Liên hệ {{ $settings->contact_phone ?? '0903 223 663' }}</p>
                        </a>
                    </div>
                    @endforeach
            </div>
        </section>
    @endif
    </main>
@endsection

@section('foot')
    <script src="{{ asset('clients/assets/js/single.js') }}"></script>
    <script>
        window.phoneNumber = '{{ $settings->contact_phone ?? "0903 223 663" }}';
    </script>
@endsection
