@extends('clients.layouts.master')

@section('title', 'Thanh toán - ' . ($settings->site_name ?? 'Bảo Tín JSC'))

@section('head')
    <link rel="stylesheet" href="{{ asset('clients/assets/css/checkout.css?v='.time()) }}">
    <meta name="robots" content="follow, noindex"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('foot')
    <script src="{{ asset('clients/assets/js/checkout.js') }}"></script>
@endsection

@section('content')
<!-- Breadcrumb -->
<section class="checkout-breadcrumb">
    <div class="container">
        <div class="breadcrumb-wrapper">
            <a href="{{ route('client.home.index') }}">Trang chủ</a>
            <span class="separator">/</span>
            <a href="{{ route('client.cart.index') }}">Giỏ hàng</a>
            <span class="separator">/</span>
            <span class="current">Thanh toán</span>
        </div>
    </div>
</section>

@if($cartItems->isEmpty())
    <div class="container">
        <div class="checkout-empty">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="80" height="80">
                <path fill="currentColor" d="M0 24C0 10.7 10.7 0 24 0L69.5 0c22 0 41.5 12.8 50.6 32l411 0c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3l-288.5 0 5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5L488 336c13.3 0 24 10.7 24 24s-10.7 24-24 24l-288.3 0c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5L24 48C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"/>
            </svg>
            <h2>Giỏ hàng trống</h2>
            <p>Bạn chưa có sản phẩm nào trong giỏ hàng.</p>
            <a href="{{ route('client.shop.index') }}" class="btn-primary">Tiếp tục mua sắm</a>
        </div>
    </div>
@else
    <main class="checkout-page">
        <div class="container">
            <h1 class="checkout-title">Thanh toán</h1>
            
            <form id="checkout-form" class="checkout-form" method="POST" action="{{ route('client.checkout.store') }}">
                @csrf
                
                <div class="checkout-layout">
                    <!-- Left Column: Customer Info & Shipping -->
                    <div class="checkout-left">
                        <!-- Customer Information -->
                        <section class="checkout-section">
                            <h2 class="section-title">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="20" height="20">
                                    <path fill="currentColor" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/>
                                </svg>
                                Thông tin khách hàng
                            </h2>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="fullname">Họ và tên <span class="required">*</span></label>
                                    <input type="text" id="fullname" name="fullname" 
                                           value="{{ auth()->check() ? auth()->user()->name : old('fullname') }}" 
                                           required>
                                    @error('fullname')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email <span class="required">*</span></label>
                                    <input type="email" id="email" name="email" 
                                           value="{{ auth()->check() ? auth()->user()->email : old('email') }}" 
                                           required>
                                    @error('email')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="phone">Số điện thoại <span class="required">*</span></label>
                                    <input type="tel" id="phone" name="phone" 
                                           value="{{ old('phone') }}" 
                                           required>
                                    @error('phone')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </section>
                        
                        <!-- Shipping Address -->
                        <section class="checkout-section">
                            <h2 class="section-title">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="20" height="20">
                                    <path fill="currentColor" d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z"/>
                                </svg>
                                Địa chỉ giao hàng
                            </h2>
                            
                            <div class="form-group">
                                <label for="address">Địa chỉ chi tiết <span class="required">*</span></label>
                                <input type="text" id="address" name="address" 
                                       value="{{ $defaultAddress->detail_address ?? old('address') }}" 
                                       placeholder="Số nhà, tên đường..." required>
                                @error('address')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="province">Tỉnh/Thành phố <span class="required">*</span></label>
                                    <input type="text" id="province" name="province" 
                                           value="{{ $defaultAddress->province ?? old('province') }}" 
                                           required>
                                    <input type="hidden" id="provinceId" name="provinceId" value="{{ $defaultAddress->province_code ?? old('provinceId', '79') }}">
                                    @error('province')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="district">Quận/Huyện <span class="required">*</span></label>
                                    <input type="text" id="district" name="district" 
                                           value="{{ $defaultAddress->district ?? old('district') }}" 
                                           required>
                                    <input type="hidden" id="districtId" name="districtId" value="{{ $defaultAddress->district_code ?? old('districtId', '760') }}">
                                    @error('district')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="ward">Phường/Xã <span class="required">*</span></label>
                                    <input type="text" id="ward" name="ward" 
                                           value="{{ $defaultAddress->ward ?? old('ward') }}" 
                                           required>
                                    <input type="hidden" id="wardId" name="wardId" value="{{ $defaultAddress->ward_code ?? old('wardId', '26734') }}">
                                    @error('ward')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="customer_note">Ghi chú đơn hàng (tùy chọn)</label>
                                <textarea id="customer_note" name="customer_note" rows="3" 
                                          placeholder="Ghi chú cho đơn hàng...">{{ old('customer_note') }}</textarea>
                            </div>
                        </section>
                        
                        <!-- Payment Method -->
                        <section class="checkout-section">
                            <h2 class="section-title">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="20" height="20">
                                    <path fill="currentColor" d="M512 80c8.8 0 16 7.2 16 16v32H48V96c0-8.8 7.2-16 16-16H512zm16 144V288H512V224H384v64H192V224H64v64H48V224H0V416c0 17.7 14.3 32 32 32H544c17.7 0 32-14.3 32-32V224H528z"/>
                                </svg>
                                Phương thức thanh toán
                            </h2>
                            
                            <div class="payment-methods">
                                <label class="payment-method">
                                    <input type="radio" name="payment" value="cod" checked required>
                                    <div class="payment-method-content">
                                        <div class="payment-method-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" width="30" height="30">
                                                <path fill="currentColor" d="M211.8 0c7.8 0 14.3 5.7 16.7 13.2C240.8 51.9 277.1 80 320 80s79.2-28.1 91.5-66.8C413.9 5.7 420.4 0 428.2 0h12.6c22.5 0 44.2 7.9 61.5 22.3L628.5 127.4c6.6 5.5 10.7 13.5 11.4 22.1s-2.1 17.1-7.8 23.6l-56 64c-11.4 13.1-28.6 20.8-46.4 22.2c-17.8 1.4-35.1-3.9-48.4-14.6L433.1 200.1c-9.5-7.7-22.8-9.7-34.1-4.9l-53.4 21.8c-9.3 3.8-19.1 5.8-29 5.8H133.4c-7.8 0-15.5-2.3-22.2-6.7L69.5 189.1c-8.2-5.4-18.4-6.7-27.6-3.4l-58 17.5c-9.2 3.3-16.9 9.6-21.5 17.6s-6.2 17.4-4.4 26.1l12.2 46.3c3.2 12.2 12.9 21.6 25.2 24.4l33.9 6.8c-2.1 10.8-2.1 21.7 0 32.5l-33.9 6.8c-12.3 2.8-22 12.2-25.2 24.4L5.4 431.5c-1.8 8.7-.4 17.8 4.4 26.1s12.3 14.3 21.5 17.6l58 17.5c9.2 3.3 19.4 2 27.6-3.4l41.7-27.3c6.8-4.4 14.4-6.7 22.2-6.7H345.5c9.9 0 19.7-2 29-5.8l53.4-21.8c11.3-4.8 24.6-2.8 34.1 4.9l47.2 38.3c13.3 10.7 30.6 16.1 48.4 14.6c17.8-1.4 35-9.1 46.4-22.2l56-64c5.7-6.5 8.8-15 7.8-23.6s-4.8-16.6-11.4-22.1L542.3 22.3C525 7.9 503.3 0 480.8 0H468.2zM112 192h48c8.8 0 16 7.2 16 16s-7.2 16-16 16H112c-8.8 0-16-7.2-16-16s7.2-16 16-16zm336 128c0-8.8 7.2-16 16-16h48c8.8 0 16 7.2 16 16s-7.2 16-16 16H464c-8.8 0-16-7.2-16-16z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <strong>Thanh toán khi nhận hàng (COD)</strong>
                                            <p>Thanh toán bằng tiền mặt khi nhận được hàng</p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </section>
                    </div>
                    
                    <!-- Right Column: Order Summary -->
                    <div class="checkout-right">
                        <div class="order-summary">
                            <h2 class="summary-title">Đơn hàng của bạn</h2>
                            
                            <div class="order-items">
                                @foreach($cartItems as $item)
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
                                            <h4 class="item-name">{{ $item->product->name ?? 'Sản phẩm đã xóa' }}</h4>
                                            @if($item->variant)
                                                <p class="item-variant">{{ $item->variant->name }}</p>
                                            @endif
                                            <div class="item-quantity-price">
                                                <span class="item-quantity">x{{ $item->quantity }}</span>
                                                <span class="item-price">{{ number_format($item->subtotal, 0, ',', '.') }} đ</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="order-totals">
                                <div class="total-row">
                                    <span>Tạm tính:</span>
                                    <span>{{ number_format($cartSubtotal, 0, ',', '.') }} đ</span>
                                </div>
                                <div class="total-row">
                                    <span>Phí vận chuyển:</span>
                                    <span>0 đ</span>
                                </div>
                                <div class="total-row total-final">
                                    <span>Tổng cộng:</span>
                                    <strong>{{ number_format($cartSubtotal, 0, ',', '.') }} đ</strong>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn-checkout" id="checkout-submit">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="20" height="20">
                                    <path fill="currentColor" d="M0 24C0 10.7 10.7 0 24 0L69.5 0c22 0 41.5 12.8 50.6 32l411 0c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3l-288.5 0 5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5L488 336c13.3 0 24 10.7 24 24s-10.7 24-24 24l-288.3 0c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5L24 48C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"/>
                                </svg>
                                Đặt hàng
                            </button>
                            
                            <p class="checkout-note">
                                Bằng việc đặt hàng, bạn đồng ý với 
                                <a href="{{ route('client.policy.terms') }}">Điều khoản & Điều kiện</a> 
                                của chúng tôi.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Hidden fields for shipping -->
                <input type="hidden" name="shipping_fee_original" value="0">
                <input type="hidden" name="shipping_fee" value="0">
                <input type="hidden" name="postal_code" value="">
                <input type="hidden" name="country" value="Việt Nam">
            </form>
        </div>
    </main>
@endif
@endsection
