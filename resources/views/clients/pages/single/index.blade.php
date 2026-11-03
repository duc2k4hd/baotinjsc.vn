@extends('clients.layouts.master')

@section('title', ($product->meta_title ?? $product->name) . ' - ' . ($settings->site_name ?? 'Bảo Tín JSC'))

@section('head')
    <link rel="stylesheet" href="{{ asset('clients/assets/css/single.css?v=' . time()) }}">

    @php
        $pageTitle = $product->meta_title ?? ($product->name ?? 'Sản phẩm');
        $pageDescription = $product->meta_description ?? ($product->excerpt ?? ($product->name ?? ''));
        $pageKeywords = $product->meta_keywords ?? '';
        $canonicalUrl = url('/' . ($product->slug ?? ''));
        $pageImage =
            ($product->primaryImage ?? null) && ($product->primaryImage->url ?? null)
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
        $productUrl = url('/' . ($product->slug ?? ''));
        $productImage =
            ($product->primaryImage ?? null) && ($product->primaryImage->url ?? null)
                ? asset('clients/assets/img/clothes/' . $product->primaryImage->url)
                : asset('clients/assets/img/clothes/no-image.webp');

        // Xác định giá và availability
        $price = null;
        $salePrice = null;
        $priceCurrency = 'VND';
        $availability = 'https://schema.org/OutOfStock';

        $hasVariants = method_exists($product, 'hasVariants') ? $product->hasVariants() ?? false : false;
        $variantsCount = ($product->variants ?? collect())->count() ?? 0;

        if ($hasVariants && $variantsCount > 0) {
            foreach ($product->variants ?? collect() as $variant) {
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
            if (($quantityProductDetail ?? 0) > 0) {
                $availability = 'https://schema.org/InStock';
            }
            if (
                ($product->sale_price ?? 0) &&
                $product->sale_price > 0 &&
                ($product->sale_price ?? 0) < ($product->price ?? 0)
            ) {
                $salePrice = $product->sale_price;
                $price = $product->price ?? 0;
            } elseif (($product->price ?? 0) && $product->price > 0) {
                $price = $product->price;
            }
        }

        $aggregateRating = null;
        if (isset($ratingStats) && ($ratingStats['total_comments'] ?? 0) > 0) {
            $aggregateRating = [
                '@type' => 'AggregateRating',
                'ratingValue' => (string) ($ratingStats['average_rating'] ?? 5.0),
                'reviewCount' => (string) ($ratingStats['total_comments'] ?? 1),
                'bestRating' => '5',
                'worstRating' => '1',
            ];
        }

        $reviews = [];
        if (isset($comments) && $comments->isNotEmpty()) {
            foreach ($comments->take(5) as $comment) {
                $reviews[] = [
                    '@type' => 'Review',
                    'reviewRating' => [
                        '@type' => 'Rating',
                        'ratingValue' => (string) ($comment->rating ?? 5),
                        'bestRating' => '5',
                        'worstRating' => '1',
                    ],
                    'author' => [
                        '@type' => 'Person',
                        'name' => $comment->name ?? 'Khách hàng',
                    ],
                    'datePublished' => $comment->created_at ? $comment->created_at->format('Y-m-d') : date('Y-m-d'),
                    'reviewBody' => strip_tags($comment->content ?? ''),
                ];
            }
        }

        // 1. Product Schema
        $productSchema = [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $product->name ?? '',
            'image' => [$productImage],
            'description' => $pageDescription,
            'sku' => $product->sku ?: 'BT-' . $product->id,
            'mpn' => $product->sku ?: 'BT-' . $product->id,
            'url' => $productUrl,
        ];
        if ($product->brand ?? null) {
            $productSchema['brand'] = [
                '@type' => 'Brand',
                'name' => $product->brand->name ?? '',
            ];
        }
        if ($price || $salePrice) {
            $productSchema['offers'] = [
                '@type' => 'Offer',
                'url' => $productUrl,
                'priceCurrency' => $priceCurrency,
                'price' => number_format($salePrice ?: $price, 0, '', ''),
                'priceValidUntil' => date('Y-m-d', strtotime('+1 year')),
                'availability' => $availability,
                'itemCondition' => 'https://schema.org/NewCondition',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => $settings->site_name ?? 'Bảo Tín JSC',
                ],
            ];
        }
        if ($aggregateRating) {
            $productSchema['aggregateRating'] = $aggregateRating;
        }
        if (!empty($reviews)) {
            $productSchema['review'] = $reviews;
        }

        // 2. BreadcrumbList Schema
        $breadcrumbItems = [];
        $position = 1;
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Trang chủ',
            'item' => url('/'),
        ];
        if ($product->primaryCategory ?? null) {
            $categorySlug = $product->primaryCategory->slug ?? '';
            $categoryUrl = $categorySlug ? url('/' . $categorySlug) : url('/');
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $product->primaryCategory->name ?? 'Danh mục',
                'item' => $categoryUrl,
            ];
        }
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $product->name ?? 'Sản phẩm',
            'item' => $productUrl,
        ];

        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbItems,
        ];

        // 3. WebPage Schema
        $webPageSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => ($product->meta_title ?? ($product->name ?? 'Sản phẩm')) . ' - ' . ($settings->site_name ?? 'Bảo Tín JSC'),
            'description' => $product->meta_description ?? ($product->excerpt ?? ($product->name ?? '')),
            'url' => $productUrl,
            'inLanguage' => 'vi-VN',
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => $settings->site_name ?? 'Bảo Tín JSC',
                'url' => url('/'),
            ],
            'about' => [
                '@type' => 'Product',
                'name' => $product->name ?? 'Sản phẩm',
                'url' => $productUrl,
            ],
            'breadcrumb' => [
                '@type' => 'BreadcrumbList',
                'itemListElement' => $breadcrumbItems,
            ],
        ];

        // 4. Organization Schema
        $organizationSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $settings->site_name ?? 'Bảo Tín JSC',
            'url' => url('/'),
            'logo' => $settings->site_logo ?? null ? asset('clients/assets/img/business/' . $settings->site_logo) : null,
            'description' => $settings->site_description ?? 'Nhà cung cấp thiết bị tự động hóa công nghiệp hàng đầu Việt Nam',
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => 'VN',
                'addressLocality' => $settings->contact_address ?? null,
            ],
            'contactPoint' => [],
        ];
        if ($settings->contact_phone ?? null) {
            $organizationSchema['contactPoint'][] = [
                '@type' => 'ContactPoint',
                'telephone' => $settings->contact_phone ?? '',
                'contactType' => 'customer service',
                'areaServed' => 'VN',
                'availableLanguage' => 'Vietnamese',
            ];
        }
        if ($settings->contact_email ?? null) {
            $organizationSchema['email'] = $settings->contact_email ?? '';
        }
        $sameAs = [];
        if ($settings->facebook_link ?? null) { $sameAs[] = $settings->facebook_link; }
        if ($settings->twitter_link ?? null) { $sameAs[] = $settings->twitter_link; }
        if ($settings->instagram_link ?? null) { $sameAs[] = $settings->instagram_link; }
        if (!empty($sameAs)) { $organizationSchema['sameAs'] = array_filter($sameAs); }

        $organizationSchema = array_filter($organizationSchema, function ($value) {
            return $value !== null && $value !== '';
        });
        if (isset($organizationSchema['address'])) {
            $organizationSchema['address'] = array_filter($organizationSchema['address'], function ($value) {
                return $value !== null && $value !== '';
            });
            if (empty($organizationSchema['address'])) {
                unset($organizationSchema['address']);
            }
        }
        if (empty($organizationSchema['contactPoint'])) {
            unset($organizationSchema['contactPoint']);
        }
    @endphp

    <script type="application/ld+json">
        {!! json_encode($productSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    <script type="application/ld+json">
        {!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    <script type="application/ld+json">
        {!! json_encode($webPageSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    <script type="application/ld+json">
        {!! json_encode($organizationSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('content')
    {{-- 1. BREADCRUMB --}}
    <nav class="single-breadcrumb" aria-label="Breadcrumb">
        <div class="container">
            <a href="{{ route('client.home.index') }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span>Trang chủ</span>
            </a>
            @if ($product->primaryCategory)
                <span class="single-breadcrumb-separator">›</span>
                <a href="/{{ $product->primaryCategory->slug ?? '#' }}">{{ $product->primaryCategory->name }}</a>
            @endif
            <span class="single-breadcrumb-separator">›</span>
            <span class="single-breadcrumb-current">{{ $product->name }}</span>
        </div>
    </nav>

    {{-- 2. TOP PRODUCT SECTION (GALLERY & INFO GRID) --}}
    <main class="container single-product-top">
        <div class="product-section">
            {{-- CỘT TRÁI: GALLERY & ẢNH SẢN PHẨM --}}
            <div class="product-images">
                <div class="main-image">
                    @php
                        $mainImgSrc = ($product->primaryImage && $product->primaryImage->url)
                            ? asset('clients/assets/img/clothes/' . $product->primaryImage->url)
                            : asset('clients/assets/img/clothes/no-image.webp');
                    @endphp
                    <img src="{{ $mainImgSrc }}" alt="{{ $product->name }}" id="mainImage" fetchpriority="high">
                    <button class="zoom-btn" type="button" aria-label="Phóng to ảnh">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            <line x1="11" y1="8" x2="11" y2="14"></line>
                            <line x1="8" y1="11" x2="14" y2="11"></line>
                        </svg>
                        <span>Phóng to</span>
                    </button>
                </div>

                {{-- Thumbnails Slider --}}
                @php
                    $galleryCount = isset($galleryImages) ? $galleryImages->count() : 0;
                @endphp
                @if ($galleryCount > 1)
                    <div class="thumbnails-container">
                        @if ($galleryCount > 4)
                            <button type="button" class="thumb-nav-btn prev" id="thumbPrev" aria-label="Ảnh trước">‹</button>
                        @endif
                        <div class="thumbnails-track" id="thumbnailsTrack">
                            @foreach ($galleryImages as $image)
                                @if (!empty($image['url']))
                                    <img src="{{ asset('clients/assets/img/clothes/' . $image['url']) }}"
                                         alt="{{ $product->name }}"
                                         class="thumb-img {{ $loop->first ? 'active' : '' }}"
                                         data-image-id="{{ $image['id'] ?? '' }}"
                                         loading="lazy">
                                @endif
                            @endforeach
                        </div>
                        @if ($galleryCount > 4)
                            <button type="button" class="thumb-nav-btn next" id="thumbNext" aria-label="Ảnh tiếp">›</button>
                        @endif
                    </div>
                @endif

                {{-- Social Share Bar --}}
                <div class="product-share-bar">
                    <span class="product-share-title">Chia sẻ:</span>
                    <div class="product-share-list">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
                           target="_blank" rel="noopener noreferrer" class="share-btn share-fb" title="Chia sẻ Facebook" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://zalo.me/share?url={{ urlencode(request()->fullUrl()) }}"
                           target="_blank" rel="noopener noreferrer" class="share-btn share-zalo" title="Chia sẻ Zalo" aria-label="Zalo">
                            <i class="fab fa-zalo"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($product->name) }}"
                           target="_blank" rel="noopener noreferrer" class="share-btn share-twitter" title="Chia sẻ X (Twitter)" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <button type="button" class="share-btn share-copy" id="shareCopyLinkBtn" title="Sao chép liên kết" aria-label="Sao chép liên kết">
                            <i class="fas fa-link"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI: THÔNG TIN SẢN PHẨM & ĐẶT HÀNG --}}
            <div class="product-info">
                <h1>{{ $product->name }}</h1>

                {{-- Đánh giá sao --}}
                @php
                    $avgRating = round((float) ($ratingStats['average_rating'] ?? 5.0), 1);
                    $fullStars = floor($avgRating);
                    $totalReviewsCount = (int) ($ratingStats['total_comments'] ?? ($totalComments ?? 0));
                @endphp
                <div class="product-rating-row">
                    <div class="product-stars" aria-label="{{ $avgRating }} trên 5 sao">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $fullStars ? '' : 'text-gray-300' }}"></i>
                        @endfor
                    </div>
                    <span class="product-review-count">({{ $totalReviewsCount }} đánh giá)</span>
                    <a href="#tab-reviews" class="product-view-reviews-link" onclick="openReviewsTab(event)">Xem đánh giá</a>
                </div>

                {{-- Hộp giá sản phẩm --}}
                @php
                    $hasVariants = $product->hasVariants() && $product->variants->count() > 0;
                    $isProductInStock = false;
                    if ($hasVariants) {
                        $isProductInStock = $product->variants->contains(function ($v) {
                            return $v->stock_quantity === null || $v->stock_quantity > 0;
                        }) || (($product->stock_quantity ?? 0) > 0);
                    } else {
                        $isProductInStock = (($quantityProductDetail ?? 0) > 0) || ($product->stock_quantity === null) || (($product->stock_quantity ?? 0) > 0);
                    }

                    $hasValidProductPrice = ($product->price && $product->price >= 1);
                    $hasDiscount = ($hasValidProductPrice && $product->sale_price && $product->sale_price < $product->price);
                    $discountPct = $hasDiscount ? round((1 - $product->sale_price / $product->price) * 100) : 0;
                @endphp

                <div class="product-price-box" id="product-price-box">
                    @if ($hasValidProductPrice)
                        @if ($hasDiscount)
                            <span class="product-price-sale" id="display-price">{{ number_format($product->sale_price, 0, ',', '.') }} đ</span>
                            <span class="product-price-original" id="display-old-price">{{ number_format($product->price, 0, ',', '.') }} đ</span>
                            <span class="product-price-badge" id="display-badge">-{{ $discountPct }}%</span>
                        @else
                            <span class="product-price-sale" id="display-price">{{ number_format($product->price, 0, ',', '.') }} đ</span>
                            <span class="product-price-original" id="display-old-price" style="display: none;"></span>
                            <span class="product-price-badge" id="display-badge" style="display: none;"></span>
                        @endif
                    @else
                        <span class="product-price-contact" id="display-price">Liên hệ</span>
                        <span class="product-price-hotline-note">Nhận báo giá dự án: <strong>{{ $settings->contact_phone ?? '0903 223 663' }}</strong></span>
                    @endif
                </div>

                {{-- Biến thể sản phẩm (nếu có) --}}
                @if ($hasVariants)
                    <div class="product-variants-wrap">
                        <div class="variants-title">
                            <i class="fas fa-layer-group" style="color: var(--single-primary);"></i>
                            <span>Chọn biến thể / quy cách:</span>
                        </div>
                        <div class="variants-list">
                            @foreach ($product->variants as $variant)
                                @php
                                    $variantStock = $variant->stock_quantity;
                                    $variantInStock = $variantStock === null || $variantStock > 0;
                                    $vPrice = (float) ($variant->price ?? 0);
                                    $vSalePrice = (float) ($variant->sale_price ?? 0);
                                    $vHasDiscount = ($vSalePrice > 0 && $vSalePrice < $vPrice);
                                    $vImage = $variant->primaryVariantImage
                                        ? asset('clients/assets/img/clothes/' . $variant->primaryVariantImage->url)
                                        : ($product->primaryImage ? asset('clients/assets/img/clothes/' . $product->primaryImage->url) : '');
                                @endphp
                                <button type="button"
                                        class="variant-option {{ $loop->first ? 'active' : '' }} {{ !$variantInStock ? 'out-of-stock' : '' }}"
                                        data-variant-id="{{ $variant->id }}"
                                        data-variant-name="{{ $variant->name }}"
                                        data-variant-price="{{ $vPrice }}"
                                        data-variant-sale-price="{{ $vSalePrice }}"
                                        data-variant-image="{{ $vImage }}"
                                        data-variant-stock="{{ $variantStock ?? 'unlimited' }}"
                                        data-variant-in-stock="{{ $variantInStock ? '1' : '0' }}">
                                    <span class="variant-name">{{ $variant->name }}</span>
                                    @if ($vPrice <= 0 && $vSalePrice <= 0)
                                        <span class="variant-price-display">Liên hệ</span>
                                    @elseif ($vHasDiscount)
                                        <span class="variant-price-display">{{ number_format($vSalePrice, 0, ',', '.') }} đ</span>
                                    @else
                                        <span class="variant-price-display">{{ number_format($vPrice, 0, ',', '.') }} đ</span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Bảng thông số cơ bản (Meta List) --}}
                <dl class="product-meta-list">
                    <div class="product-meta-item">
                        <dt>Tình trạng:</dt>
                        <dd>
                            <span class="stock-badge">
                                <span class="stock-dot {{ $isProductInStock ? '' : 'out-of-stock' }}"></span>
                                <span>{{ $isProductInStock ? 'Còn hàng' : 'Liên hệ đặt trước' }}</span>
                            </span>
                        </dd>
                    </div>
                    <div class="product-meta-item">
                        <dt>Mã SKU:</dt>
                        <dd>{{ $product->sku ?: 'BT-' . $product->id }}</dd>
                    </div>
                    @if ($product->brand)
                        <div class="product-meta-item">
                            <dt>Thương hiệu:</dt>
                            <dd><a href="/cua-hang?brands={{ $product->brand->slug ?? '#' }}">{{ $product->brand->name }}</a></dd>
                        </div>
                    @endif
                    @if ($product->primaryCategory)
                        <div class="product-meta-item">
                            <dt>Danh mục:</dt>
                            <dd><a href="/{{ $product->primaryCategory->slug ?? '#' }}">{{ $product->primaryCategory->name }}</a></dd>
                        </div>
                    @endif
                    <div class="product-meta-item">
                        <dt>Bảo hành:</dt>
                        <dd>12 tháng chính hãng</dd>
                    </div>
                    <div class="product-meta-item">
                        <dt>Chứng từ:</dt>
                        <dd>CO / CQ đầy đủ</dd>
                    </div>
                </dl>

                {{-- Tóm tắt / Mô tả ngắn --}}
                @if ($product->short_description)
                    <div class="product-short-summary">
                        {!! $product->short_description !!}
                    </div>
                @endif

                {{-- Khu vực Mua hàng & Thao tác --}}
                <div class="product-purchase-box">
                    @if ($hasValidProductPrice)
                        <form action="{{ route('client.cart.store') }}" method="POST" class="add-to-cart-form" id="addToCartForm">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="product_variant_id" value="{{ $hasVariants ? ($product->variants->first()->id ?? '') : '' }}" id="product-variant-id">
                            <input type="hidden" name="quantity" value="1" id="cart-quantity">

                            <div class="purchase-row">
                                <div class="quantity-selector">
                                    <button type="button" class="qty-btn" onclick="decreaseQty()" aria-label="Giảm số lượng">-</button>
                                    <input type="number" value="1" id="quantity" min="1" max="99" aria-label="Số lượng đặt">
                                    <button type="button" class="qty-btn" onclick="increaseQty()" aria-label="Tăng số lượng">+</button>
                                </div>
                                <button type="submit" class="btn-add-cart" id="btn-add-cart"
                                        data-in-stock="{{ $isProductInStock ? '1' : '0' }}"
                                        @if (!$isProductInStock) disabled @endif>
                                    <i class="fas fa-shopping-cart"></i>
                                    <span id="btn-add-cart-text">{{ $isProductInStock ? 'Thêm vào giỏ hàng' : 'Hết hàng' }}</span>
                                </button>
                                <button type="button" class="btn-favorite" data-product-id="{{ $product->id }}"
                                        data-wishlist-add="{{ route('client.product.wishlist.add') }}"
                                        data-wishlist-remove="{{ route('client.product.wishlist.remove') }}"
                                        title="Lưu vào danh sách yêu thích" aria-label="Yêu thích">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </div>

                            @if ($isProductInStock)
                                <button type="button" class="btn-buy-now" id="btnBuyNow">
                                    <span>Mua ngay</span>
                                </button>
                            @endif
                        </form>
                    @endif

                    {{-- Thanh tiện ích phụ chuẩn Misutech: CHIA SẺ | ĐẶT CÂU HỎI | FAQ --}}
                    <div class="product-action-meta-bar">
                        <button type="button" class="product-action-meta-item" id="shareCopyLinkBtn2" title="Sao chép liên kết chia sẻ">
                            <i class="fas fa-share"></i>
                            <span>CHIA SẺ</span>
                        </button>
                        <a href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', $settings->contact_phone ?? '0903223663') }}?text={{ urlencode('Xin chào Bảo Tín JSC, tôi cần tư vấn về sản phẩm: ' . $product->name) }}"
                           target="_blank" rel="noopener noreferrer" class="product-action-meta-item" title="Tư vấn trực tiếp">
                            <i class="fas fa-comment-dots"></i>
                            <span>ĐẶT CÂU HỎI</span>
                        </a>
                        <a href="#tab-delivery" class="product-action-meta-item" onclick="openDeliveryTab(event)" title="Chính sách & câu hỏi thường gặp">
                            <i class="fas fa-list-alt"></i>
                            <span>FAQ</span>
                        </a>
                    </div>

                    {{-- Nút Liên hệ / Zalo nhanh --}}
                    <div class="quick-contact-row">
                        <a href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', $settings->contact_phone ?? '0903223663') }}?text={{ urlencode('Xin chào Bảo Tín JSC, tôi cần tư vấn & báo giá sản phẩm: ' . $product->name) }}"
                           target="_blank" rel="noopener noreferrer" class="btn-contact-action btn-contact-zalo">
                            <i class="fab fa-zalo"></i>
                            <span>Chat Zalo tư vấn kỹ thuật</span>
                        </a>
                        <a href="tel:{{ preg_replace('/[^0-9]/', '', $settings->contact_phone ?? '0903223663') }}" class="btn-contact-action btn-contact-call">
                            <i class="fas fa-phone-alt"></i>
                            <span>Hotline: <strong>{{ $settings->contact_phone ?? '0903 223 663' }}</strong></span>
                        </a>
                    </div>
                </div>

                {{-- Hộp cam kết mua hàng ngắn --}}
                <div class="product-reassurance-box">
                    <div class="reassurance-item">
                        <i class="fas fa-check-circle reassurance-icon"></i>
                        <span>Sản phẩm chính hãng Bảo Tín JSC - Bảo đảm chất lượng 100%</span>
                    </div>
                    <div class="reassurance-item">
                        <i class="fas fa-certificate reassurance-icon"></i>
                        <span>Đầy đủ chứng từ chứng nhận xuất xứ (CO) và chất lượng (CQ)</span>
                    </div>
                    <div class="reassurance-item">
                        <i class="fas fa-truck-moving reassurance-icon"></i>
                        <span>Giao hàng nhanh toàn quốc - Kỹ sư tư vấn lắp đặt và vận hành</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- 3. PRODUCT DETAILS 70/30 GRID & STICKY SIDEBAR --}}
    <section class="container product-details-wrap">
        <div class="product-details-grid">
            {{-- CỘT TRÁI (70%): TABS CHI TIẾT SẢN PHẨM --}}
            <div class="product-tabs-main">
                <div class="product-tabs-header" role="tablist">
                    <button class="tab-btn active" data-tab="tab-description" role="tab" aria-selected="true">
                        <i class="fas fa-align-left"></i>
                        <span>Mô tả chi tiết</span>
                    </button>
                    <button class="tab-btn" data-tab="tab-catalog" role="tab" aria-selected="false">
                        <i class="fas fa-file-pdf"></i>
                        <span>Tài liệu & Catalog</span>
                    </button>
                    <button class="tab-btn" data-tab="tab-delivery" role="tab" aria-selected="false">
                        <i class="fas fa-shipping-fast"></i>
                        <span>Chính sách giao hàng</span>
                    </button>
                    <button class="tab-btn" data-tab="tab-warranty" role="tab" aria-selected="false">
                        <i class="fas fa-shield-alt"></i>
                        <span>Bảo hành & Đổi trả</span>
                    </button>
                    <button class="tab-btn" data-tab="tab-reviews" role="tab" aria-selected="false" id="tabReviewsBtn">
                        <i class="fas fa-star"></i>
                        <span>Đánh giá ({{ $totalReviewsCount }})</span>
                    </button>
                </div>

                {{-- TAB 1: MÔ TẢ CHI TIẾT --}}
                <div id="tab-description" class="tab-content active" role="tabpanel">
                    @if ($product->description)
                        <div class="description-text">
                            {!! $product->description !!}
                        </div>
                    @elseif($product->short_description)
                        <div class="description-text">
                            {!! $product->short_description !!}
                        </div>
                    @else
                        <p class="text-muted">Đang cập nhật nội dung chi tiết cho sản phẩm này.</p>
                    @endif
                </div>

                {{-- TAB 2: TÀI LIỆU & CATALOG --}}
                <div id="tab-catalog" class="tab-content" role="tabpanel">
                    @if ($product->link_catalog && is_array($product->link_catalog) && !empty($product->link_catalog))
                        <div class="catalog-list">
                            @foreach ($product->link_catalog as $catalog)
                                @if (is_string($catalog) && !empty($catalog))
                                    @php $filename = basename($catalog); @endphp
                                    <div class="catalog-item">
                                        <div class="catalog-info">
                                            <div class="catalog-icon">
                                                <i class="fas fa-file-pdf"></i>
                                            </div>
                                            <div>
                                                <div class="catalog-name">{{ $filename }}</div>
                                                <span class="catalog-size">Tài liệu kỹ thuật / Catalog sản phẩm</span>
                                            </div>
                                        </div>
                                        <div class="catalog-actions">
                                            <a href="{{ $catalog }}" target="_blank" rel="noopener noreferrer" class="catalog-btn">
                                                <i class="fas fa-external-link-alt"></i> Mở xem
                                            </a>
                                            <a href="{{ $catalog }}" download class="catalog-btn">
                                                <i class="fas fa-download"></i> Tải về
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="policy-box">
                            <h3 class="policy-heading">
                                <i class="fas fa-file-alt" style="color: var(--single-primary);"></i>
                                <span>Tài liệu thông số kỹ thuật</span>
                            </h3>
                            <p style="margin: 0; color: #475569; font-size: 14.5px; line-height: 1.7;">
                                Quý khách cần tải Catalog chi tiết, bản vẽ 2D/3D hoặc tài liệu hướng dẫn kỹ thuật cho model <strong>{{ $product->name }}</strong>, vui lòng liên hệ trực tiếp bộ phận kỹ thuật Bảo Tín JSC qua Hotline <strong>{{ $settings->contact_phone ?? '0903 223 663' }}</strong> hoặc Zalo để được gửi tài liệu chuẩn ngay lập tức.
                            </p>
                        </div>
                    @endif
                </div>

                {{-- TAB 3: CHÍNH SÁCH GIAO HÀNG --}}
                <div id="tab-delivery" class="tab-content" role="tabpanel">
                    <div class="policy-box">
                        <h3 class="policy-heading">
                            <i class="fas fa-truck-moving" style="color: var(--single-primary);"></i>
                            <span>Chính sách vận chuyển & Giao nhận Bảo Tín JSC</span>
                        </h3>
                        <ul class="policy-list">
                            <li><strong>Nội thành & Các KCN lân cận (Hà Nội, Hải Phòng, Bắc Ninh, Hưng Yên...):</strong> Giao hàng hỏa tốc trong ngày từ 2 – 4 giờ đối với hàng có sẵn tại kho.</li>
                            <li><strong>Toàn quốc:</strong> Giao hàng từ 1 – 3 ngày làm việc thông qua Viettel Post, chành xe uy tín hoặc xe tải vận tải chuyên dụng của công ty.</li>
                            <li><strong>Đồng kiểm hàng hóa:</strong> Khách hàng được quyền kiểm tra quy cách đóng gói, tem niêm phong và thiết bị nguyên vẹn trước khi ký biên bản bàn giao.</li>
                            <li><strong>Miễn phí vận chuyển:</strong> Áp dụng cho các đơn hàng đạt định mức giá trị hoặc hợp đồng cung ứng định kỳ theo thỏa thuận.</li>
                        </ul>
                    </div>
                </div>

                {{-- TAB 4: CHÍNH SÁCH BẢO HÀNH --}}
                <div id="tab-warranty" class="tab-content" role="tabpanel">
                    <div class="policy-box">
                        <h3 class="policy-heading">
                            <i class="fas fa-shield-alt" style="color: var(--single-primary);"></i>
                            <span>Chính sách bảo hành & Đổi trả chính hãng</span>
                        </h3>
                        <ul class="policy-list">
                            <li><strong>Bảo hành chính hãng:</strong> Tiêu chuẩn 12 tháng theo đúng quy định kỹ thuật của nhà sản xuất.</li>
                            <li><strong>Đổi mới 1 - 1 trong 7 ngày:</strong> Đổi sản phẩm mới tương đương ngay lập tức nếu phát sinh lỗi kỹ thuật do quá trình chế tạo.</li>
                            <li><strong>Hỗ trợ kỹ thuật 24/7:</strong> Kỹ sư giàu kinh nghiệm hỗ trợ tư vấn sơ đồ kết nối, cài đặt thông số và xử lý sự cố online hoặc tại nhà máy.</li>
                            <li><strong>Cam kết chứng từ:</strong> Cung cấp đầy đủ hóa đơn GTGT (VAT), chứng chỉ nguồn gốc xuất xứ (CO) và chất lượng xuất xưởng (CQ).</li>
                        </ul>
                    </div>
                </div>

                {{-- TAB 5: ĐÁNH GIÁ & NHẬN XÉT --}}
                <div id="tab-reviews" class="tab-content" role="tabpanel">
                    <div class="reviews-section">
                        {{-- Thống kê điểm đánh giá --}}
                        <div class="reviews-summary-card">
                            <div class="review-score-big">
                                <div class="score-number">{{ number_format($avgRating, 1) }}</div>
                                <div class="score-stars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $fullStars ? '' : 'text-gray-300' }}"></i>
                                    @endfor
                                </div>
                                <div class="score-count">{{ $totalReviewsCount }} lượt đánh giá</div>
                            </div>
                            <div class="rating-bars">
                                @for ($s = 5; $s >= 1; $s--)
                                    @php
                                        $sCount = $ratingStats["star_{$s}_count"] ?? 0;
                                        $sPct = $totalReviewsCount > 0 ? round(($sCount / $totalReviewsCount) * 100) : ($s === 5 ? 100 : 0);
                                    @endphp
                                    <div class="rating-bar-row">
                                        <span class="bar-star-label">{{ $s }} sao</span>
                                        <div class="bar-track">
                                            <div class="bar-fill" style="width: {{ $sPct }}%;"></div>
                                        </div>
                                        <span style="min-width: 30px; text-align: right; color: #64748B;">{{ $sPct }}%</span>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        {{-- Đánh giá từ Quản trị viên (nếu có) --}}
                        @if (isset($adminReviews) && $adminReviews->isNotEmpty())
                            <div>
                                <h3 style="font-size: 16px; font-weight: 700; margin: 0 0 14px; color: var(--single-text-dark);">
                                    <i class="fas fa-user-shield" style="color: var(--single-primary);"></i> Đánh giá chuyên môn từ Bảo Tín JSC
                                </h3>
                                <div class="reviews-list">
                                    @foreach ($adminReviews as $rev)
                                        <div class="review-item">
                                            <div class="review-header">
                                                <span class="review-author">{{ $rev->account->name ?? 'Bảo Tín JSC' }}</span>
                                                <span class="admin-badge">Quản trị viên</span>
                                                <div class="review-rating-stars">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= ($rev->rating ?? 5) ? '' : 'text-gray-300' }}"></i>
                                                    @endfor
                                                </div>
                                                <span class="review-date">{{ $rev->created_at ? $rev->created_at->format('d/m/Y') : '' }}</span>
                                            </div>
                                            <div class="review-body">{{ $rev->content }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Form viết đánh giá mới --}}
                        <div class="review-form-card">
                            <h3 class="review-form-title">
                                <i class="fas fa-edit" style="color: var(--single-primary);"></i> Viết đánh giá của bạn
                            </h3>
                            <form id="review-form">
                                @csrf
                                <input type="hidden" name="commentable_type" value="product">
                                <input type="hidden" name="commentable_id" value="{{ $product->id }}">
                                <input type="hidden" name="rating" id="review-rating" value="5">

                                <div class="star-rating-select">
                                    <span style="font-size: 13.5px; font-weight: 600; color: var(--single-text-dark);">Mức độ hài lòng:</span>
                                    <div class="interactive-star-picker" id="starPicker">
                                        <button type="button" class="star-btn active" data-score="1" aria-label="1 sao">★</button>
                                        <button type="button" class="star-btn active" data-score="2" aria-label="2 sao">★</button>
                                        <button type="button" class="star-btn active" data-score="3" aria-label="3 sao">★</button>
                                        <button type="button" class="star-btn active" data-score="4" aria-label="4 sao">★</button>
                                        <button type="button" class="star-btn active" data-score="5" aria-label="5 sao">★</button>
                                    </div>
                                    <span class="star-label-text" id="starRatingLabel">Rất tốt (5 sao)</span>
                                </div>

                                <div class="review-form-grid">
                                    <div class="review-form-group">
                                        <label for="review-name">Họ và tên *</label>
                                        <input type="text" id="review-name" name="name" required
                                               value="{{ auth('web')->user()->name ?? '' }}" placeholder="Nhập họ và tên...">
                                    </div>
                                    <div class="review-form-group">
                                        <label for="review-email">Email (bảo mật) *</label>
                                        <input type="email" id="review-email" name="email" required
                                               value="{{ auth('web')->user()->email ?? '' }}" placeholder="Nhập địa chỉ email...">
                                    </div>
                                </div>

                                <div class="review-form-group" style="margin-bottom: 16px;">
                                    <label for="review-content">Nội dung đánh giá *</label>
                                    <textarea id="review-content" name="content" rows="4" required
                                              placeholder="Chia sẻ trải nghiệm thực tế về chất lượng sản phẩm, hiệu năng, đóng gói và dịch vụ giao hàng..."></textarea>
                                </div>

                                <button type="submit" class="btn-submit-review">
                                    <i class="fas fa-paper-plane"></i> Gửi đánh giá
                                </button>
                            </form>
                        </div>

                        {{-- Danh sách nhận xét khách hàng --}}
                        <div>
                            <h3 style="font-size: 16px; font-weight: 700; margin: 0 0 14px; color: var(--single-text-dark);">
                                <i class="fas fa-comments" style="color: var(--single-primary);"></i> Nhận xét từ khách hàng
                            </h3>
                            @if (isset($comments) && $comments->isNotEmpty())
                                <div class="reviews-list">
                                    @foreach ($comments as $comment)
                                        <div class="review-item">
                                            <div class="review-header">
                                                <span class="review-author">{{ $comment->name ?? 'Khách hàng' }}</span>
                                                @if (isset($comment->rating))
                                                    <div class="review-rating-stars">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star {{ $i <= $comment->rating ? '' : 'text-gray-300' }}"></i>
                                                        @endfor
                                                    </div>
                                                @endif
                                                <span class="review-date">{{ $comment->created_at ? $comment->created_at->format('d/m/Y') : '' }}</span>
                                            </div>
                                            <div class="review-body">{{ $comment->content ?? '' }}</div>
                                            @if ($comment->adminReply)
                                                <div class="admin-reply-box">
                                                    <div style="font-weight: 700; color: var(--single-primary); margin-bottom: 4px;">
                                                        <i class="fas fa-reply"></i> {{ $comment->adminReply->account->name ?? 'Bảo Tín JSC' }} phản hồi:
                                                    </div>
                                                    <div>{{ $comment->adminReply->content }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p style="color: #64748B; font-size: 14px; margin: 0; font-style: italic;">Chưa có nhận xét nào từ khách hàng. Hãy là người đầu tiên chia sẻ cảm nhận về sản phẩm!</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI (30%): STICKY SIDEBAR --}}
            <aside class="product-tabs-sidebar">
                {{-- CARD 1: HỖ TRỢ KỸ THUẬT & BÁO GIÁ NHANH --}}
                <div class="side-card">
                    <div class="side-card-header">
                        <div class="side-card-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div>
                            <h3 class="side-card-title">Hỗ trợ kỹ thuật & Báo giá</h3>
                            <p class="side-card-subtitle">Tư vấn chọn mã thay thế, chiết khấu dự án</p>
                        </div>
                    </div>
                    <div class="side-support-actions">
                        <a href="tel:{{ preg_replace('/[^0-9]/', '', $settings->contact_phone ?? '0903223663') }}" class="side-support-btn call-btn">
                            <i class="fas fa-phone-alt"></i>
                            <span>Hotline: <strong>{{ $settings->contact_phone ?? '0903 223 663' }}</strong></span>
                        </a>
                        <a href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', $settings->contact_phone ?? '0903223663') }}?text={{ urlencode('Xin chào Bảo Tín JSC, tôi cần tư vấn kỹ thuật sản phẩm: ' . $product->name) }}"
                           target="_blank" rel="noopener noreferrer" class="side-support-btn zalo-btn">
                            <i class="fab fa-zalo"></i>
                            <span>Chat Zalo hỗ trợ kỹ thuật</span>
                        </a>
                        <button type="button" class="side-support-btn quote-btn" onclick="openQuoteModal()">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <span>Yêu cầu báo giá số lượng lớn</span>
                        </button>
                    </div>
                </div>

                {{-- CARD 2: CHUYÊN VIÊN CSKH --}}
                @if (isset($supportStaff) && $supportStaff->isNotEmpty())
                    <div class="side-card">
                        <div class="side-card-header">
                            <div class="side-card-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div>
                                <h3 class="side-card-title">Đội ngũ CSKH chuyên môn</h3>
                                <p class="side-card-subtitle">Hỗ trợ trực tiếp mọi yêu cầu đặt hàng</p>
                            </div>
                        </div>
                        <div class="side-staff-list">
                            @foreach ($supportStaff as $staff)
                                <div class="side-staff-item">
                                    @if ($staff->avatar)
                                        <div class="side-staff-avatar">
                                            <img src="{{ asset('clients/assets/img/avatars/' . $staff->avatar) }}" alt="{{ $staff->name }}" loading="lazy">
                                        </div>
                                    @else
                                        <div class="side-staff-avatar placeholder">
                                            <span>{{ mb_substr($staff->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div class="side-staff-info">
                                        <div class="side-staff-name" title="{{ $staff->name }}">{{ $staff->name }}</div>
                                        <div class="side-staff-role">{{ $staff->role ?? 'Chuyên viên kỹ thuật' }}</div>
                                    </div>
                                    <div class="side-staff-links">
                                        @if ($staff->phone)
                                            <a href="tel:{{ $staff->phone }}" class="staff-icon-link phone" title="Gọi {{ $staff->phone }}" aria-label="Gọi điện">
                                                <i class="fas fa-phone-alt"></i>
                                            </a>
                                        @endif
                                        @if ($staff->zalo)
                                            <a href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', $staff->zalo) }}" target="_blank" rel="noopener noreferrer" class="staff-icon-link zalo" title="Zalo" aria-label="Zalo">
                                                <i class="fab fa-zalo"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- CARD 3: SẢN PHẨM NỔI BẬT / TƯƠNG ĐƯƠNG --}}
                @if (isset($productFeatured) && $productFeatured->isNotEmpty())
                    <div class="side-card">
                        <div class="side-card-header">
                            <div class="side-card-icon">
                                <i class="fas fa-fire"></i>
                            </div>
                            <div>
                                <h3 class="side-card-title">Sản phẩm nổi bật cùng ngành</h3>
                                <p class="side-card-subtitle">Các model bán chạy được đánh giá cao</p>
                            </div>
                        </div>
                        <div class="side-products-list">
                            @foreach ($productFeatured->take(4) as $featured)
                                @php
                                    $featThumb = ($featured->primaryImage && $featured->primaryImage->url)
                                        ? asset('clients/assets/img/clothes/' . $featured->primaryImage->url)
                                        : asset('clients/assets/img/clothes/no-image.webp');
                                    $featPrice = (float) ($featured->price ?? 0);
                                    $featSalePrice = (float) ($featured->sale_price ?? 0);
                                    $featHasDiscount = ($featSalePrice > 0 && $featSalePrice < $featPrice);
                                @endphp
                                <a href="/{{ $featured->slug ?? '#' }}" class="side-product-item">
                                    <img src="{{ $featThumb }}" alt="{{ $featured->name }}" class="side-product-thumb" loading="lazy">
                                    <div class="side-product-meta">
                                        <div class="side-product-name" title="{{ $featured->name }}">{{ $featured->name }}</div>
                                        <div class="side-product-price">
                                            @if ($featPrice <= 0 && $featSalePrice <= 0)
                                                <span style="color: var(--single-primary);">Liên hệ</span>
                                            @elseif ($featHasDiscount)
                                                <span>{{ number_format($featSalePrice, 0, ',', '.') }} đ</span>
                                            @else
                                                <span>{{ number_format($featPrice, 0, ',', '.') }} đ</span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- CARD 4: CAM KẾT BÁN HÀNG BẢO TÍN --}}
                <div class="side-card">
                    <div class="side-card-header">
                        <div class="side-card-icon">
                            <i class="fas fa-award"></i>
                        </div>
                        <div>
                            <h3 class="side-card-title">Cam kết từ Bảo Tín JSC</h3>
                            <p class="side-card-subtitle">Uy tín tạo nên thương hiệu bền vững</p>
                        </div>
                    </div>
                    <ul class="commitment-list">
                        <li class="commitment-item">
                            <i class="fas fa-check-circle commitment-icon"></i>
                            <span>100% sản phẩm chính hãng, xuất xứ minh bạch</span>
                        </li>
                        <li class="commitment-item">
                            <i class="fas fa-check-circle commitment-icon"></i>
                            <span>Cung cấp đầy đủ hóa đơn VAT, chứng từ CO/CQ</span>
                        </li>
                        <li class="commitment-item">
                            <i class="fas fa-check-circle commitment-icon"></i>
                            <span>Đội ngũ kỹ sư hỗ trợ kỹ thuật suốt vòng đời máy</span>
                        </li>
                        <li class="commitment-item">
                            <i class="fas fa-check-circle commitment-icon"></i>
                            <span>Giao hàng nhanh chóng và bảo đảm an toàn tuyệt đối</span>
                        </li>
                    </ul>
                </div>
            </aside>
        </div>
    </section>

    {{-- 4. BENEFIT GRID (DẢI LỢI ÍCH MUA HÀNG) --}}
    <section class="container product-benefits-section" aria-label="Lợi ích mua hàng tại Bảo Tín JSC">
        <div class="benefit-grid">
            <div class="benefit-card">
                <div class="benefit-icon-box">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <div>
                    <h3 class="benefit-title">MIỄN PHÍ VẬN CHUYỂN</h3>
                    <p class="benefit-desc">Miễn phí giao hàng cho các đơn hàng dự án hoặc theo thỏa thuận cung ứng trên toàn quốc.</p>
                </div>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon-box">
                    <i class="fas fa-tools"></i>
                </div>
                <div>
                    <h3 class="benefit-title">HỖ TRỢ KỸ THUẬT 24/7</h3>
                    <p class="benefit-desc">Kỹ sư giàu kinh nghiệm hỗ trợ tư vấn sơ đồ, cài đặt và xử lý sự cố thiết bị nhanh chóng.</p>
                </div>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon-box">
                    <i class="fas fa-undo-alt"></i>
                </div>
                <div>
                    <h3 class="benefit-title">ĐỔI TRẢ & BẢO HÀNH</h3>
                    <p class="benefit-desc">Bảo hành 12 tháng chính hãng, đổi mới trong 7 ngày nếu có lỗi kỹ thuật từ nhà sản xuất.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 5. RELATED PRODUCTS SECTION (SẢN PHẨM LIÊN QUAN) --}}
    @if (isset($productRelated) && $productRelated->isNotEmpty())
        <section class="container related-products-section">
            <div class="section-heading-wrap">
                <h2 class="section-heading-title">SẢN PHẨM LIÊN QUAN</h2>
            </div>
            <div class="related-products-grid">
                @foreach ($productRelated->take(10) as $related)
                    @php
                        $relThumb = ($related->primaryImage && $related->primaryImage->url)
                            ? asset('clients/assets/img/clothes/' . $related->primaryImage->url)
                            : asset('clients/assets/img/clothes/no-image.webp');
                        $relPrice = (float) ($related->price ?? 0);
                        $relSalePrice = (float) ($related->sale_price ?? 0);
                        $relHasDiscount = ($relSalePrice > 0 && $relSalePrice < $relPrice);
                    @endphp
                    <a href="/{{ $related->slug ?? '#' }}" class="related-product-card">
                        <div class="related-product-media">
                            <img src="{{ $relThumb }}" alt="{{ $related->name }}" loading="lazy">
                        </div>
                        <div class="related-product-info">
                            <h3 class="related-product-name" title="{{ $related->name }}">{{ $related->name }}</h3>
                            <div class="related-product-price">
                                @if ($relPrice <= 0 && $relSalePrice <= 0)
                                    <span style="color: var(--single-primary); font-size: 13.5px;">Liên hệ</span>
                                @elseif ($relHasDiscount)
                                    <span>{{ number_format($relSalePrice, 0, ',', '.') }} đ</span>
                                @else
                                    <span>{{ number_format($relPrice, 0, ',', '.') }} đ</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
@endsection

@section('foot')
    <script src="{{ asset('clients/assets/js/single.js?v=' . time()) }}"></script>
    <script>
        window.phoneNumber = '{{ $settings->contact_phone ?? '0903 223 663' }}';

        // Hàm mở tab Đánh giá mượt mà từ link trên đầu trang
        function openReviewsTab(e) {
            if (e) e.preventDefault();
            const tabBtn = document.getElementById('tabReviewsBtn');
            if (tabBtn) {
                tabBtn.click();
                tabBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        // Hàm mở popup báo giá nhanh
        function openQuoteModal() {
            const prodName = @json($product->name);
            const phone = window.phoneNumber || '0903 223 663';
            if (typeof showCustomToast === 'function') {
                showCustomToast(`Yêu cầu báo giá số lượng lớn cho: ${prodName}. Vui lòng liên hệ Hotline: ${phone}`, 'info', 7000);
            } else {
                alert(`Yêu cầu báo giá số lượng lớn cho: ${prodName}\nHotline: ${phone}`);
            }
        }
    </script>
@endsection
