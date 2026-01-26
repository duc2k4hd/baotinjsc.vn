@extends('clients.layouts.master')

@section('title', 'Chi tiết đơn hàng #' . $order->code . ' - ' . ($settings->site_name ?? 'Bảo Tín JSC'))

@section('head')
    <link rel="stylesheet" href="{{ asset('clients/assets/css/order-detail.css?v='.time()) }}">
    <meta name="robots" content="follow, noindex"/>
@endsection

@section('content')
<!-- Breadcrumb -->
<section class="order-breadcrumb">
    <div class="container">
        <div class="breadcrumb-wrapper">
            <a href="{{ route('client.home.index') }}">Trang chủ</a>
            <span class="separator">/</span>
            <a href="{{ route('client.order.index') }}">Đơn hàng</a>
            <span class="separator">/</span>
            <span class="current">Chi tiết đơn hàng</span>
        </div>
    </div>
</section>

<main class="order-detail-page">
    <div class="container">
        <!-- Order Header -->
        <div class="order-header">
            <div class="order-header-left">
                <h1 class="order-title">Đơn hàng #{{ $order->code }}</h1>
                <p class="order-date">Đặt ngày: {{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="order-header-right">
                <span class="order-status-badge order-status-{{ $normalizedStatus }}">
                    {{ collect($statusFlow)->firstWhere('key', $normalizedStatus)['label'] ?? 'Chờ xác nhận' }}
                </span>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <!-- Order Status Timeline -->
        <section class="order-section order-status-timeline">
            <h2 class="section-title">Trạng thái đơn hàng</h2>
            <div class="timeline">
                @foreach($statusFlow as $index => $status)
                    <div class="timeline-item {{ $index <= $currentStatusIndex ? 'completed' : '' }} {{ $index === $currentStatusIndex ? 'current' : '' }}">
                        <div class="timeline-marker">
                            @if($index < $currentStatusIndex)
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20">
                                    <path fill="currentColor" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/>
                                </svg>
                            @elseif($index === $currentStatusIndex)
                                <div class="current-dot"></div>
                            @else
                                <div class="pending-dot"></div>
                            @endif
                        </div>
                        <div class="timeline-content">
                            <h3 class="timeline-title">{{ $status['label'] }}</h3>
                            <p class="timeline-description">{{ $status['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="order-layout">
            <!-- Left Column: Order Items -->
            <div class="order-left">
                <!-- Order Items -->
                <section class="order-section order-items-section">
                    <h2 class="section-title">Sản phẩm</h2>
                    <div class="order-items-list">
                        @foreach($order->items as $item)
                            <div class="order-item">
                                <div class="item-image">
                                    @if($item->product && $item->product->primaryImage && $item->product->primaryImage->url)
                                        <img src="{{ asset('clients/assets/img/clothes/' . $item->product->primaryImage->url) }}" 
                                             alt="{{ $item->product->name }}">
                                    @else
                                        <div class="no-image">No Image</div>
                                    @endif
                                </div>
                                <div class="item-details">
                                    <h3 class="item-name">
                                        <a href="{{ route('client.shop.index', ['product' => $item->product->slug ?? '']) }}">
                                            {{ $item->product->name ?? 'Sản phẩm đã xóa' }}
                                        </a>
                                    </h3>
                                    @if($item->variant)
                                        <p class="item-variant">{{ $item->variant->name }}</p>
                                    @endif
                                    @if($item->product && $item->product->sku)
                                        <p class="item-sku">SKU: {{ $item->product->sku }}</p>
                                    @endif
                                    <div class="item-meta">
                                        <span class="item-quantity">Số lượng: {{ $item->quantity }}</span>
                                        <span class="item-price">{{ number_format($item->total, 0, ',', '.') }} đ</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <!-- Shipping Address -->
                <section class="order-section order-address-section">
                    <h2 class="section-title">Địa chỉ giao hàng</h2>
                    <div class="address-info">
                        <p><strong>{{ $order->receiver_name }}</strong></p>
                        <p>{{ $order->receiver_phone }}</p>
                        <p>{{ $order->shipping_address }}</p>
                        <p>
                            {{ $addressNames['ward'] ?? '' }}, 
                            {{ $addressNames['district'] ?? '' }}, 
                            {{ $addressNames['province'] ?? '' }}
                        </p>
                        @if($order->customer_note)
                            <div class="customer-note">
                                <strong>Ghi chú:</strong> {{ $order->customer_note }}
                            </div>
                        @endif
                    </div>
                </section>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="order-right">
                <div class="order-summary-card">
                    <h2 class="summary-title">Thông tin đơn hàng</h2>
                    
                    <div class="summary-info">
                        <div class="info-row">
                            <span>Mã đơn hàng:</span>
                            <strong>{{ $order->code }}</strong>
                        </div>
                        <div class="info-row">
                            <span>Ngày đặt:</span>
                            <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="info-row">
                            <span>Phương thức thanh toán:</span>
                            <span>
                                @if($order->payment_method === 'cod')
                                    Thanh toán khi nhận hàng (COD)
                                @else
                                    {{ $order->payment_method }}
                                @endif
                            </span>
                        </div>
                        <div class="info-row">
                            <span>Trạng thái thanh toán:</span>
                            <span class="payment-status payment-status-{{ $order->payment_status }}">
                                @if($order->payment_status === 'paid')
                                    Đã thanh toán
                                @elseif($order->payment_status === 'pending')
                                    Chưa thanh toán
                                @else
                                    {{ $order->payment_status }}
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="order-totals">
                        <div class="total-row">
                            <span>Tạm tính:</span>
                            <span>{{ number_format($order->total_price, 0, ',', '.') }} đ</span>
                        </div>
                        @if($order->voucher_discount > 0)
                            <div class="total-row">
                                <span>Giảm giá:</span>
                                <span class="discount">-{{ number_format($order->voucher_discount, 0, ',', '.') }} đ</span>
                            </div>
                        @endif
                        <div class="total-row">
                            <span>Phí vận chuyển:</span>
                            <span>{{ number_format($order->shipping_fee, 0, ',', '.') }} đ</span>
                        </div>
                        <div class="total-row total-final">
                            <span>Tổng cộng:</span>
                            <strong>{{ number_format($order->final_price, 0, ',', '.') }} đ</strong>
                        </div>
                    </div>

                    <div class="order-actions">
                        @if($order->status === 'pending' && $order->payment_status !== 'paid')
                            <form action="{{ route('client.order.cancel', $order->code) }}" method="POST" 
                                  onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');">
                                @csrf
                                <button type="submit" class="btn-action btn-cancel">Hủy đơn hàng</button>
                            </form>
                        @endif
                        
                        <form action="{{ route('client.order.reorder', $order->code) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-action btn-reorder">Mua lại</button>
                        </form>
                        
                        <a href="{{ route('client.order.invoice', $order->code) }}" target="_blank" class="btn-action btn-invoice">
                            In hóa đơn
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
