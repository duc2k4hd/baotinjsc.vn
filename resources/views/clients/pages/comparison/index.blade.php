@extends('clients.layouts.master')

@section('title', 'So sánh sản phẩm')

@push('styles')
    <link rel="stylesheet" href="{{ asset('clients/assets/css/comparison.css') }}">
@endpush

@section('content')
    <div class="comparison-container">
        <div class="container">
            <!-- Breadcrumb -->
            <nav class="comparison-breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('client.home.index') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">So sánh sản phẩm</li>
                </ol>
            </nav>

            <!-- Header -->
            <div class="comparison-header">
                <h1 class="comparison-title">So sánh sản phẩm</h1>
                @if($products->count() > 0)
                    <button type="button" class="btn-clear-all" onclick="clearComparison()">
                        <i class="fas fa-trash"></i> Xóa tất cả
                    </button>
                @endif
            </div>

            @if($products->count() == 0)
                <!-- Empty State -->
                <div class="comparison-empty">
                    <i class="fas fa-balance-scale"></i>
                    <h2>Chưa có sản phẩm để so sánh</h2>
                    <p>Thêm sản phẩm vào danh sách so sánh để xem chi tiết</p>
                    <a href="{{ route('client.shop.index') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i> Mua sắm ngay
                    </a>
                </div>
            @else
                <!-- Comparison Table -->
                <div class="comparison-table-wrapper">
                    <table class="comparison-table">
                        <thead>
                            <tr>
                                <th class="comparison-label">Thông tin</th>
                                @foreach($products as $product)
                                    <th class="comparison-product">
                                        <div class="product-header">
                                            <button type="button" class="btn-remove" onclick="removeProduct({{ $product->id }})" title="Xóa">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <a href="{{ route('client.product.detail', $product->slug) }}" class="product-image">
                                                <img src="{{ asset('clients/assets/img/clothes/' . ($product->primaryImage->url ?? 'no-image.webp')) }}" 
                                                     alt="{{ $product->name }}">
                                            </a>
                                            <h3 class="product-name">
                                                <a href="{{ route('client.product.detail', $product->slug) }}">
                                                    {{ $product->name }}
                                                </a>
                                            </h3>
                                            @if($product->brand)
                                                <p class="product-brand">{{ $product->brand->name }}</p>
                                            @endif
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Giá -->
                            <tr>
                                <td class="comparison-label">Giá</td>
                                @foreach($products as $product)
                                    <td class="comparison-value">
                                        @if($product->sale_price && $product->sale_price < $product->price)
                                            <span class="price-old">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                                            <span class="price-new">{{ number_format($product->sale_price, 0, ',', '.') }}đ</span>
                                        @else
                                            <span class="price-current">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            <!-- SKU -->
                            <tr>
                                <td class="comparison-label">Mã sản phẩm</td>
                                @foreach($products as $product)
                                    <td class="comparison-value">{{ $product->sku ?? 'N/A' }}</td>
                                @endforeach
                            </tr>

                            <!-- Tồn kho -->
                            <tr>
                                <td class="comparison-label">Tình trạng</td>
                                @foreach($products as $product)
                                    <td class="comparison-value">
                                        @if($product->stock_quantity > 0)
                                            <span class="stock-in">Còn hàng ({{ $product->stock_quantity }})</span>
                                        @else
                                            <span class="stock-out">Liên hệ</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Mô tả ngắn -->
                            @if($products->some(fn($p) => $p->short_description))
                                <tr>
                                    <td class="comparison-label">Mô tả</td>
                                    @foreach($products as $product)
                                        <td class="comparison-value">
                                            {!! $product->short_description ? Str::limit($product->short_description, 500) : 'N/A' !!}
                                        </td>
                                    @endforeach
                                </tr>
                            @endif

                            <!-- Đánh giá -->
                            @if($products->some(fn($p) => $p->approved_comments_avg_rating))
                                <tr>
                                    <td class="comparison-label">Đánh giá</td>
                                    @foreach($products as $product)
                                        <td class="comparison-value">
                                            @if($product->approved_comments_avg_rating)
                                                <div class="rating">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= round($product->approved_comments_avg_rating) ? 'active' : '' }}"></i>
                                                    @endfor
                                                    <span class="rating-text">({{ $product->approved_comments_count ?? 0 }})</span>
                                                </div>
                                            @else
                                                Chưa có đánh giá
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endif

                            <!-- Danh mục -->
                            @if($products->some(fn($p) => $p->primaryCategory))
                                <tr>
                                    <td class="comparison-label">Danh mục</td>
                                    @foreach($products as $product)
                                        <td class="comparison-value">
                                            {{ $product->primaryCategory->name ?? 'N/A' }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endif

                            <!-- Hành động -->
                            <tr class="comparison-actions">
                                <td class="comparison-label">Thao tác</td>
                                @foreach($products as $product)
                                    <td class="comparison-value">
                                        <div class="action-buttons">
                                            <a href="{{ route('client.product.detail', $product->slug) }}" class="btn-view">
                                                <i class="fas fa-eye"></i> Xem chi tiết
                                            </a>
                                            <button type="button" class="btn-add-cart" onclick="addToCart({{ $product->id }})">
                                                <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                                            </button>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile View (Cards) -->
                <div class="comparison-mobile">
                    @foreach($products as $product)
                        <div class="comparison-card">
                            <div class="card-header">
                                <button type="button" class="btn-remove" onclick="removeProduct({{ $product->id }})">
                                    <i class="fas fa-times"></i>
                                </button>
                                <a href="{{ route('client.product.detail', $product->slug) }}" class="product-image">
                                    <img src="{{ asset('clients/assets/img/clothes/' . ($product->primaryImage->url ?? 'no-image.webp')) }}" 
                                         alt="{{ $product->name }}">
                                </a>
                                <h3 class="product-name">
                                    <a href="{{ route('client.product.detail', $product->slug) }}">
                                        {{ $product->name }}
                                    </a>
                                </h3>
                                @if($product->brand)
                                    <p class="product-brand">{{ $product->brand->name }}</p>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="info-row">
                                    <span class="info-label">Giá:</span>
                                    <span class="info-value">
                                        @if($product->sale_price && $product->sale_price < $product->price)
                                            <span class="price-old">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                                            <span class="price-new">{{ number_format($product->sale_price, 0, ',', '.') }}đ</span>
                                        @else
                                            <span class="price-current">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Mã SP:</span>
                                    <span class="info-value">{{ $product->sku ?? 'N/A' }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Tình trạng:</span>
                                    <span class="info-value">
                                        @if($product->stock_quantity > 0)
                                            <span class="stock-in">Còn hàng</span>
                                        @else
                                            <span class="stock-out">Liên hệ</span>
                                        @endif
                                    </span>
                                </div>
                                @if($product->short_description)
                                    <div class="info-row">
                                        <span class="info-label">Mô tả:</span>
                                        <span class="info-value">{{ Str::limit($product->short_description, 80) }}</span>
                                    </div>
                                @endif
                                <div class="action-buttons">
                                    <a href="{{ route('client.product.detail', $product->slug) }}" class="btn-view">
                                        <i class="fas fa-eye"></i> Xem chi tiết
                                    </a>
                                    <button type="button" class="btn-add-cart" onclick="addToCart({{ $product->id }})">
                                        <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection

@push('foot')
    <script src="{{ asset('clients/assets/js/comparison.js') }}"></script>
@endpush
