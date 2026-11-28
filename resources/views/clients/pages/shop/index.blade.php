@extends('clients.layouts.master')

@section('title', $pageTitle)

@section('head')
    <link rel="stylesheet" href="{{ asset('clients/assets/css/shop.css?v='.time()) }}">

    <!-- 🔑 Keywords -->
    <meta name="keywords" content="{{ $pageKeywords }}">

    <!-- 📝 Description -->
    <meta name="description" content="{{ $pageDescription }}">

    <!-- 🤖 Robots -->
    {{-- @php
        $productCount = $productsMain->total() ?? 0;
    @endphp --}}
    @php
        // Noindex cho URL filter (query string): ?brand=omron, ?category=..., ?tags=...
        // Index cho URL đẹp: /cam-bien-omron, /cam-bien, /cam-bien-tiem-can
        $hasFilterQuery = request()->has('category') || 
                         request()->has('keyword') || 
                         request()->has('tags') || 
                         request()->has('brand') || 
                         request()->has('brands') ||
                         request()->has('minPriceRange') ||
                         request()->has('maxPriceRange') ||
                         request()->has('minRating') ||
                         request()->has('sort') ||
                         request()->has('perPage');
        
        // Nếu là category-brand page (URL đẹp) → index
        // Nếu có filter query string → noindex
        $shouldIndex = !$hasFilterQuery;
    @endphp
    @if (!$shouldIndex)
        <meta name="robots" content="noindex, follow" />
    @else
        <meta name="robots" content="index, follow, max-snippet:-1, max-video-preview:-1, max-image-preview:large" />
    @endif

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


@section('foot')
    <script>
        // AI chat context for category/shop pages
        window.aiPageContext = {
            page: @json($category ? 'category' : 'shop'),
            url: window.location.href,
            title: document.title || @json($pageTitle ?? 'Cửa hàng'),
            category_id: @json($category->id ?? null),
            category_slug: @json($category->slug ?? null),
            category_name: @json($category->name ?? null),
            category_ids: @json($category ? [$category->id] : []),
        };
    </script>
@endsection

@section('schema')
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
    if (($category ?? null)) {
        $categoryPath = collect();
        $currentCategory = $category;
        
        while ($currentCategory) {
            $categoryPath->prepend($currentCategory);
            $currentCategory = $currentCategory->parent ?? null;
        }
        
        foreach ($categoryPath as $cat) {
            $categorySlug = $cat->slug ?? '';
            $categoryUrl = $categorySlug ? url('/' . $categorySlug) : url('/');
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $cat->name ?? 'Danh mục',
                'item' => $categoryUrl
            ];
        }
    } else {
        // Cửa hàng
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Cửa hàng',
            'item' => $canonicalUrl ?? url('/cua-hang')
        ];
    }
    
    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $breadcrumbItems
    ];
    
    // WebPage Schema
    $webPageSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => $pageTitle ?? 'Cửa hàng',
        'description' => $pageDescription ?? 'Danh sách sản phẩm',
        'url' => $canonicalUrl ?? url('/cua-hang'),
        'inLanguage' => 'vi-VN',
        'isPartOf' => [
            '@type' => 'WebSite',
            'name' => $settings->site_name ?? 'Bảo Tín JSC',
            'url' => url('/')
        ],
        'breadcrumb' => $breadcrumbSchema
    ];
    
    if (($pageImage ?? null)) {
        $webPageSchema['image'] = $pageImage ?? '';
    }
    
    if (($settings->site_name ?? null)) {
        $webPageSchema['publisher'] = [
            '@type' => 'Organization',
            'name' => $settings->site_name ?? 'Bảo Tín JSC',
            'url' => url('/')
        ];
    }
    
    // CollectionPage Schema (cho trang danh mục)
    $collectionPageSchema = null;
    if (($productsMain ?? null) && method_exists($productsMain, 'items') && $productsMain->count() > 0) {
        $items = [];
        $itemPosition = 1;
        
        foreach ($productsMain->items() as $product) {
            $productSlug = $product->slug ?? '';
            $productUrl = $productSlug ? url('/' . $productSlug) : url('/');
            
            // ItemList chỉ dùng ListItem đơn giản, không dùng Product schema
            $items[] = [
                '@type' => 'ListItem',
                'position' => $itemPosition++,
                'name' => $product->name ?? 'Sản phẩm',
                'url' => $productUrl
            ];
        }
        
        if (!empty($items)) {
            $collectionPageSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => $pageTitle ?? 'Danh sách sản phẩm',
                'description' => $pageDescription ?? 'Danh sách sản phẩm',
                'url' => $canonicalUrl ?? url('/cua-hang'),
                'mainEntity' => [
                    '@type' => 'ItemList',
                    'numberOfItems' => $productsMain->total() ?? count($items),
                    'itemListElement' => $items
                ]
            ];
        }
    }
@endphp

<script type="application/ld+json">
{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>

<script type="application/ld+json">
{!! json_encode($webPageSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>

@if($collectionPageSchema)
<script type="application/ld+json">
{!! json_encode($collectionPageSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endif
@endsection

@section('content')
    <!-- Breadcrumb -->
    <section>
        <div class="autosensor_shop_breadcrumb">
            <a href="{{ route('client.home.index') }}">Trang chủ</a>
            <span class="separator">></span>

            @if ($category)
                @php
                    // Tạo breadcrumb path từ danh mục hiện tại lên danh mục gốc
                    $breadcrumbPath = collect();
                    $currentCategory = $category;

                    while ($currentCategory) {
                        $breadcrumbPath->prepend($currentCategory);
                        $currentCategory = $currentCategory->parent;
                    }
                @endphp

                @foreach ($breadcrumbPath as $breadcrumb)
                    @if ($loop->last)
                        <span class="breadcrumb-current">{{ $breadcrumb->name }}</span>
                    @else
                        <a href="{{ url('/'.$breadcrumb->slug) }}">{{ $breadcrumb->name }}</a>
                        <span class="separator">></span>
                    @endif
                @endforeach
            @else
                <span>Cửa hàng</span>
            @endif
        </div>
    </section>
    <main class="autosensor_shop">

        <!-- Banner -->
        {{-- <section>
            <div class="autosensor_shop_banner">
                @if ($banner && $banner->count() > 0)
                    <img class="autosensor_shop_banner_image"
                        src="{{ asset('clients/assets/img/banners/' . $banner->image) }}" alt="{{ $banner->title }}">
                @endif
            </div>
        </section> --}}

        <!-- Bộ lọc -->
        <section>
            @php
                $currentSort = $sort ?? 'default';
            @endphp
            <div class="autosensor_shop_products">
                <div class="autosensor_shop_products_filter">
                    <div class="autosensor_shop_products_filter_new_products">
                        <h4 class="autosensor_shop_products_filter_new_products_title">Sản phẩm mới</h4>
                        <div class="autosensor_shop_products_filter_new_products_description">
                            <p>Khám phá những thiết bị tự động hóa mới nhất tại {{ $settings->site_name ?? $settings->subname ?? 'Bảo Tín JSC' }}. Chúng tôi luôn cập
                                nhật
                                những sản phẩm mới, chất lượng cao và công nghệ tiên tiến để phục vụ nhu cầu tự động hóa của doanh nghiệp.</p>
                        </div>
                        @if (!empty($newProducts) && $newProducts->count() > 0)
                            @foreach ($newProducts as $product)
                                <div class="autosensor_shop_products_filter_new_products_item">
                                    <div class="autosensor_shop_products_filter_new_products_item_image">
                                        <a href="{{ $product->meta_canonical ?? ($settings->site_url ?? 'https://baotinjsc.vn'). $product->slug }}">
                                            <img class="autosensor_shop_products_filter_new_products_item_image_img"
                                                src="{{ asset('clients/assets/img/clothes/' . ($product?->primaryImage?->url ?? 'no-image.webp')) }}"
                                                alt="{{ $product?->primaryImage?->alt ?? $product?->name }}"
                                                title="{{ $product?->primaryImage?->title }}">
                                        </a>
                                    </div>
                                    <div class="autosensor_shop_products_filter_new_products_item_info">
                                        <a href="{{ $product->meta_canonical ?? $settings->site_url ?? 'https://baotinjsc.vn'. $product->slug }}">
                                            <h4 class="autosensor_shop_products_filter_new_products_item_info_title">
                                                {{ $product->name }}</h4>
                                        </a>
                                        <p class="autosensor_shop_products_filter_new_products_item_info_price">
                                            {{ number_format($product->price, 0, ',', '.') }}đ</p>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="autosensor_shop_products_content">
                    <div class="autosensor_shop_products_content_filter">
                        <div class="autosensor_shop_products_content_filters">
                            <div class="autosensor_shop_products_content_filter_total">
                                Tổng <span>{{ $productsMain->total() ?? 0 }}</span> sản phẩm
                            </div>
                            @if (!empty($keyword))
                                <div class="autosensor_shop_products_content_filter_keyword">
                                    Từ khóa: <strong>"{{ $keyword }}"</strong>
                                </div>
                            @endif
                        </div>
                        @if (request()->query())
                            {{-- Có ít nhất 1 bộ lọc đang được áp dụng --}}
                            <div class="autosensor_shop_products_content_filter_delete_all">
                                <button class="autosensor_shop_products_content_filter_delete_all_btn"
                                    onclick="window.location.href='{{ route('client.shop.index') }}'">
                                    Xóa tất cả bộ lọc
                                </button>
                            </div>
                        @endif
                        <div class="autosensor_shop_products_content_filter_select">
                            <div class="autosensor_shop_products_content_filter_select_sort">
                                <label for="sort">Sắp xếp theo:</label>
                                <form action="{{ request()->url() }}" method="GET"
                                    class="autosensor_shop_products_content_filter_select_sort_form">
                                    <input type="hidden" name="page" value="1">
                                    <input type="hidden" name="perPage" value="{{ $perPage ?? 30 }}">
                                    @if (!is_null($minPriceRange))
                                        <input type="hidden" name="minPriceRange" value="{{ $minPriceRange }}">
                                    @endif
                                    @if (!is_null($maxPriceRange))
                                        <input type="hidden" name="maxPriceRange" value="{{ $maxPriceRange }}">
                                    @endif
                                    @if (!empty($keyword))
                                        <input type="hidden" name="keyword" value="{{ $keyword }}">
                                    @endif
                                    @if (!empty($selectedCategorySlug))
                                        <input type="hidden" name="category" value="{{ $selectedCategorySlug }}">
                                    @endif
                                    @isset($minRating)
                                        <input type="hidden" name="minRating" value="{{ $minRating }}">
                                    @endisset
                                    @if (!empty($tags))
                                        @foreach ($tags as $tagId)
                                            <input type="hidden" name="tags[]" value="{{ $tagId }}">
                                        @endforeach
                                    @endif
                                    @if (!empty($selectedBrandSlugs))
                                        <input type="hidden" name="brands" value="{{ implode(',', $selectedBrandSlugs) }}">
                                    @endif
                                    <select name="sort" id="sort" onchange="this.form.submit()">
                                        <option value="default" {{ $currentSort === 'default' ? 'selected' : '' }}>
                                            Mặc định (Mới nhất)
                                        </option>
                                        <option value="newest" {{ $currentSort === 'newest' ? 'selected' : '' }}>
                                            Hàng mới về
                                        </option>
                                        <option value="price-asc" {{ $currentSort === 'price-asc' ? 'selected' : '' }}>
                                            Giá: Thấp đến Cao
                                        </option>
                                        <option value="price-desc" {{ $currentSort === 'price-desc' ? 'selected' : '' }}>
                                            Giá: Cao đến Thấp
                                        </option>
                                        <option value="name-asc" {{ $currentSort === 'name-asc' ? 'selected' : '' }}>
                                            Tên: A → Z
                                        </option>
                                        <option value="name-desc" {{ $currentSort === 'name-desc' ? 'selected' : '' }}>
                                            Tên: Z → A
                                        </option>
                                    </select>
                                </form>
                            </div>

                            <div class="autosensor_shop_products_content_filter_select_show">
                                <label for="show">Hiển thị:</label>
                                <form action="{{ request()->url() }}" method="GET"
                                    class="autosensor_shop_products_content_filter_select_show_form">
                                    {{-- Giữ lại các filter hiện tại --}}
                                    <input type="hidden" name="page" value="1">
                                    <input type="hidden" name="sort" value="{{ $currentSort }}">
                                    @if (!empty($keyword))
                                        <input type="hidden" name="keyword" value="{{ $keyword }}">
                                    @endif
                                    @if (!empty($selectedCategorySlug))
                                        <input type="hidden" name="category" value="{{ $selectedCategorySlug }}">
                                    @endif
                                    @isset($minRating)
                                        <input type="hidden" name="minRating" value="{{ $minRating }}">
                                    @endisset
                                    @if (!empty($tags))
                                        @foreach ($tags as $tagId)
                                            <input type="hidden" name="tags[]" value="{{ $tagId }}">
                                        @endforeach
                                    @endif
                                    @if (!empty($selectedBrandSlugs))
                                        <input type="hidden" name="brands" value="{{ implode(',', $selectedBrandSlugs) }}">
                                    @endif

                                    {{-- Select số sản phẩm --}}
                                    <select name="perPage" id="perPage" onchange="this.form.submit()">
                                        @foreach ([24, 30, 36, 48, 60, 72, 96] as $val)
                                            <option value="{{ $val }}" {{ (int) $perPage === $val ? 'selected' : '' }}>
                                                {{ $val }} sản phẩm
                                            </option>
                                        @endforeach
                                    </select>

                                    @if (isset($minPriceRange))
                                        <input type="hidden" name="minPriceRange" value="{{ $minPriceRange }}">
                                    @endif

                                    @if (isset($maxPriceRange))
                                        <input type="hidden" name="maxPriceRange" value="{{ $maxPriceRange }}">
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                    @if (!empty($productsMain) && $productsMain->count() > 0)
                        <div class="autosensor_shop_products_content_list">
                            @foreach ($productsMain as $product)
                                <div class="autosensor_shop_products_content_list_item">
                                    <div class="autosensor_shop_products_content_list_item_image">
                                        <a href="{{ route('client.product.detail', ['slug' => $product->slug]) }}">
                                            <img class="autosensor_shop_products_content_list_item_image_img"
                                                src="{{ asset('clients/assets/img/clothes/' . ($product?->primaryImage?->url ?? 'no-image.webp')) }}"
                                                alt="{{ $product?->primaryImage?->alt ?? $product?->name }}"
                                                title="{{ $product?->primaryImage?->title ?? $product?->name }}">
                                        </a>
                                    </div>
                                    <div class="autosensor_shop_products_content_list_item_category">
                                        <h5 class="autosensor_shop_products_content_list_item_category_name">
                                            {{ optional($product->brand)->name ?? ($settings->site_name ?? ($settings->subname ?? 'Bảo Tín JSC')) }}
                                        </h5>
                                    </div>
                                    <div class="autosensor_shop_products_content_list_item_title">
                                        <a href="{{ route('client.product.detail', ['slug' => $product->slug]) }}">
                                            <h4 class="autosensor_shop_products_content_list_item_title_name">
                                                {{ $product->name }}
                                            </h4>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="autosensor_shop_products_content_list_empty">
                            <p>Không có sản phẩm nào phù hợp với bộ lọc của bạn.</p>
                            <p>Hãy thử lọc sản phẩm khác hoặc thử tìm kiếm sản phẩm tương tự.</p>
                            <a href="{{ route('client.shop.index') }}" class="autosensor_shop_products_content_list_empty_button">
                                Xóa bộ lọc
                            </a>
                        </div>
                    @endif

                    @if (!empty($productsMain) && $productsMain->count() > 0)
                        <div class="autosensor_shop_products_content_pagination">
                            {{ $productsMain->links('pagination.custom') }}
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>

    {{-- Mô tả chi tiết danh mục --}}
    @if (!empty($category) && !empty($category->description))
        <div class="autosensor_shop_category_description">
            <div class="autosensor_shop_category_description_content">
                <h3 class="autosensor_shop_category_description_title">{{ $category->name }}</h3>
                <div class="autosensor_shop_category_description_text">
                    {!! $category->description !!}
                </div>
            </div>
        </div>
    @endif

@endsection
