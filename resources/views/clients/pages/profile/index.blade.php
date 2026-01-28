@extends('clients.layouts.master')

@section('title', 'Tài khoản của tôi')

@push('head')
    <meta name="robots" content="noindex, nofollow">
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('clients/assets/css/profile.css') }}">
@endpush

@section('content')
    <div class="profile-container">
        <div class="profile-breadcrumb">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('client.home.index') }}">Trang chủ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tài khoản của tôi</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="profile-header">
            <div class="container">
                <div class="profile-header-content">
                    <div class="profile-avatar">
                        <div class="avatar-circle">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    <div class="profile-info">
                        <h1 class="profile-name">{{ $account->name ?? 'Người dùng' }}</h1>
                        <p class="profile-email">{{ $account->email }}</p>
                        @if($account->phone)
                            <p class="profile-phone"><i class="fas fa-phone"></i> {{ $account->phone }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-stats">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">{{ $stats['total_orders'] ?? 0 }}</div>
                            <div class="stat-label">Tổng đơn hàng</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">{{ $stats['pending_orders'] ?? 0 }}</div>
                            <div class="stat-label">Đơn chờ xử lý</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon completed">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">{{ $stats['completed_orders'] ?? 0 }}</div>
                            <div class="stat-label">Đơn hoàn thành</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon favorite">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">{{ $stats['total_favorites'] ?? 0 }}</div>
                            <div class="stat-label">Sản phẩm yêu thích</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-main">
            <div class="container">
                <div class="profile-tabs">
                    <ul class="tab-nav" role="tablist">
                        <li class="tab-item active">
                            <a href="#profile-info" class="tab-link active" data-tab="profile-info">
                                <i class="fas fa-user"></i> Thông tin cá nhân
                            </a>
                        </li>
                        <li class="tab-item">
                            <a href="#change-password" class="tab-link" data-tab="change-password">
                                <i class="fas fa-lock"></i> Đổi mật khẩu
                            </a>
                        </li>
                        <li class="tab-item">
                            <a href="#orders" class="tab-link" data-tab="orders">
                                <i class="fas fa-shopping-bag"></i> Đơn hàng
                            </a>
                        </li>
                        <li class="tab-item">
                            <a href="#addresses" class="tab-link" data-tab="addresses">
                                <i class="fas fa-map-marker-alt"></i> Địa chỉ
                            </a>
                        </li>
                        <li class="tab-item">
                            <a href="#favorites" class="tab-link" data-tab="favorites">
                                <i class="fas fa-heart"></i> Yêu thích
                            </a>
                        </li>
                        <li class="tab-item">
                            <a href="#recent-views" class="tab-link" data-tab="recent-views">
                                <i class="fas fa-eye"></i> Đã xem gần đây
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle"></i> Vui lòng kiểm tra lại thông tin đã nhập.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Tab: Thông tin cá nhân -->
                        <div id="profile-info" class="tab-pane active">
                            <div class="profile-card">
                                <h3 class="card-title">
                                    <i class="fas fa-user"></i> Thông tin cá nhân
                                </h3>
                                <form action="{{ route('client.profile.update') }}" method="POST" class="profile-form">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="form-group">
                                        <label for="name" class="form-label">
                                            Họ và tên <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               id="name" 
                                               name="name" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               value="{{ old('name', $account->name) }}" 
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="email" class="form-label">
                                            Email <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" 
                                               id="email" 
                                               name="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               value="{{ old('email', $account->email) }}" 
                                               required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="phone" class="form-label">Số điện thoại</label>
                                        <input type="tel" 
                                               id="phone" 
                                               name="phone" 
                                               class="form-control @error('phone') is-invalid @enderror" 
                                               value="{{ old('phone', $account->phone) }}" 
                                               placeholder="Nhập số điện thoại">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Lưu thay đổi
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Tab: Đổi mật khẩu -->
                        <div id="change-password" class="tab-pane">
                            <div class="profile-card">
                                <h3 class="card-title">
                                    <i class="fas fa-lock"></i> Đổi mật khẩu
                                </h3>
                                <form action="{{ route('client.profile.change-password') }}" method="POST" class="profile-form">
                                    @csrf
                                    
                                    <div class="form-group">
                                        <label for="current_password" class="form-label">
                                            Mật khẩu hiện tại <span class="text-danger">*</span>
                                        </label>
                                        <input type="password" 
                                               id="current_password" 
                                               name="current_password" 
                                               class="form-control @error('current_password') is-invalid @enderror" 
                                               required>
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="password" class="form-label">
                                            Mật khẩu mới <span class="text-danger">*</span>
                                        </label>
                                        <input type="password" 
                                               id="password" 
                                               name="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               required 
                                               minlength="8">
                                        <small class="form-text text-muted">Mật khẩu phải có ít nhất 8 ký tự</small>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="password_confirmation" class="form-label">
                                            Xác nhận mật khẩu mới <span class="text-danger">*</span>
                                        </label>
                                        <input type="password" 
                                               id="password_confirmation" 
                                               name="password_confirmation" 
                                               class="form-control" 
                                               required 
                                               minlength="8">
                                    </div>

                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-key"></i> Đổi mật khẩu
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Tab: Đơn hàng -->
                        <div id="orders" class="tab-pane">
                            <div class="profile-card">
                                <h3 class="card-title">
                                    <i class="fas fa-shopping-bag"></i> Đơn hàng của tôi
                                </h3>
                                @if($account->orders && $account->orders->count() > 0)
                                    <div class="orders-list">
                                        @foreach($account->orders as $order)
                                            <div class="order-item">
                                                <div class="order-header">
                                                    <div class="order-info">
                                                        <h4 class="order-code">
                                                            <a href="{{ route('client.order.show', $order->code) }}">
                                                                {{ $order->code }}
                                                            </a>
                                                        </h4>
                                                        <span class="order-date">
                                                            <i class="fas fa-calendar"></i> 
                                                            {{ $order->created_at->format('d/m/Y H:i') }}
                                                        </span>
                                                    </div>
                                                    <div class="order-status">
                                                        <span class="badge badge-{{ $order->status }}">
                                                            @if($order->status === 'pending') Chờ xử lý
                                                            @elseif($order->status === 'processing') Đang xử lý
                                                            @elseif($order->status === 'completed') Hoàn thành
                                                            @else Đã hủy
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="order-details">
                                                    <div class="order-item-count">
                                                        <i class="fas fa-box"></i> {{ $order->items_count ?? 0 }} sản phẩm
                                                    </div>
                                                    <div class="order-total">
                                                        <strong>{{ number_format($order->final_price, 0, ',', '.') }}đ</strong>
                                                    </div>
                                                </div>
                                                <div class="order-actions">
                                                    <a href="{{ route('client.order.show', $order->code) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> Xem chi tiết
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="text-center mt-4">
                                        <a href="{{ route('client.order.index') }}" class="btn btn-primary">
                                            <i class="fas fa-list"></i> Xem tất cả đơn hàng
                                        </a>
                                    </div>
                                @else
                                    <div class="empty-state">
                                        <i class="fas fa-shopping-bag"></i>
                                        <p>Bạn chưa có đơn hàng nào</p>
                                        <a href="{{ route('client.shop.index') }}" class="btn btn-primary">
                                            <i class="fas fa-shopping-cart"></i> Mua sắm ngay
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Tab: Địa chỉ -->
                        <div id="addresses" class="tab-pane">
                            <div class="profile-card">
                                <div class="card-header-actions">
                                    <h3 class="card-title">
                                        <i class="fas fa-map-marker-alt"></i> Địa chỉ giao hàng
                                    </h3>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="openAddressModal()">
                                        <i class="fas fa-plus"></i> Thêm địa chỉ
                                    </button>
                                </div>

                                @if($account->addresses && $account->addresses->count() > 0)
                                    <div class="addresses-list">
                                        @foreach($account->addresses as $address)
                                            <div class="address-item" id="address-{{ $address->id }}">
                                                <div class="address-content">
                                                    <div class="address-header">
                                                        <h4 class="address-name">{{ $address->full_name }}</h4>
                                                        @if($address->is_default)
                                                            <span class="badge badge-primary">Mặc định</span>
                                                        @endif
                                                    </div>
                                                    <p class="address-phone">
                                                        <i class="fas fa-phone"></i> {{ $address->phone_number }}
                                                    </p>
                                                    <p class="address-detail">{{ $address->detail_address }}</p>
                                                    <p class="address-location">
                                                        {{ $address->ward }}, {{ $address->district }}, {{ $address->province }}
                                                    </p>
                                                    @if($address->notes)
                                                        <p class="address-notes">
                                                            <i class="fas fa-sticky-note"></i> {{ $address->notes }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="address-actions">
                                                    @if(!$address->is_default)
                                                        <form action="{{ route('client.profile.address.set-default', $address->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-primary" title="Đặt làm mặc định">
                                                                <i class="fas fa-star"></i> Mặc định
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="openAddressModal({{ $address->id }})" title="Sửa địa chỉ">
                                                        <i class="fas fa-edit"></i> Sửa
                                                    </button>
                                                    <form action="{{ route('client.profile.address.delete', $address->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa địa chỉ này?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa địa chỉ">
                                                            <i class="fas fa-trash"></i> Xóa
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="empty-state">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <p>Bạn chưa có địa chỉ nào</p>
                                        <p class="text-muted">Thêm địa chỉ để đặt hàng nhanh hơn</p>
                                        <button type="button" class="btn btn-primary mt-3" onclick="openAddressModal()">
                                            <i class="fas fa-plus"></i> Thêm địa chỉ đầu tiên
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <!-- Modal thêm/sửa địa chỉ -->
                            <div id="addressModal" class="address-modal">
                                <div class="address-modal-content">
                                    <div class="address-modal-header">
                                        <h3 id="addressModalTitle">Thêm địa chỉ mới</h3>
                                        <button type="button" class="close-btn" onclick="closeAddressModal()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <form id="addressForm" method="POST">
                                        @csrf
                                        <input type="hidden" id="addressId" name="address_id">
                                        <div id="addressFormMethod"></div>
                                        
                                        <div class="form-group">
                                            <label for="full_name">Họ và tên <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                                                   id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                                            @error('full_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="phone_number">Số điện thoại <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                                                   id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
                                            @error('phone_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="detail_address">Địa chỉ chi tiết <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('detail_address') is-invalid @enderror" 
                                                   id="detail_address" name="detail_address" value="{{ old('detail_address') }}" required>
                                            @error('detail_address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="ward">Phường/Xã <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('ward') is-invalid @enderror" 
                                                           id="ward" name="ward" value="{{ old('ward') }}" required>
                                                    @error('ward')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="district">Quận/Huyện <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('district') is-invalid @enderror" 
                                                           id="district" name="district" value="{{ old('district') }}" required>
                                                    @error('district')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="province">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('province') is-invalid @enderror" 
                                                           id="province" name="province" value="{{ old('province') }}" required>
                                                    @error('province')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="notes">Ghi chú</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                      id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="is_default" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_default">
                                                    Đặt làm địa chỉ mặc định
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-actions">
                                            <button type="button" class="btn btn-secondary" onclick="closeAddressModal()">Hủy</button>
                                            <button type="submit" class="btn btn-primary">Lưu địa chỉ</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Tab: Yêu thích -->
                        <div id="favorites" class="tab-pane">
                            <div class="profile-card">
                                <h3 class="card-title">
                                    <i class="fas fa-heart"></i> Sản phẩm yêu thích
                                </h3>
                                @if($account->favorites && $account->favorites->count() > 0)
                                    <div class="favorites-grid">
                                        @foreach($account->favorites as $favorite)
                                            @if($favorite->product)
                                                <div class="favorite-item">
                                                    <a href="{{ route('client.product.detail', $favorite->product->slug) }}" class="favorite-image">
                                                        <img src="{{ asset('clients/assets/img/clothes/' . ($favorite->product->primaryImage->url ?? 'no-image.webp')) }}" 
                                                             alt="{{ $favorite->product->name }}">
                                                    </a>
                                                    <div class="favorite-info">
                                                        <h4 class="favorite-name">
                                                            <a href="{{ route('client.product.detail', $favorite->product->slug) }}">
                                                                {{ $favorite->product->name }}
                                                            </a>
                                                        </h4>
                                                        <div class="favorite-price">
                                                            {{ number_format($favorite->product->price, 0, ',', '.') }}đ
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    <div class="text-center mt-4">
                                        <a href="{{ route('client.wishlist.index') }}" class="btn btn-primary">
                                            <i class="fas fa-heart"></i> Xem tất cả yêu thích
                                        </a>
                                    </div>
                                @else
                                    <div class="empty-state">
                                        <i class="fas fa-heart"></i>
                                        <p>Bạn chưa có sản phẩm yêu thích nào</p>
                                        <a href="{{ route('client.shop.index') }}" class="btn btn-primary">
                                            <i class="fas fa-shopping-cart"></i> Khám phá sản phẩm
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Tab: Đã xem gần đây -->
                        <div id="recent-views" class="tab-pane">
                            <div class="profile-card">
                                <h3 class="card-title">
                                    <i class="fas fa-eye"></i> Sản phẩm đã xem gần đây
                                </h3>
                                @if($recentViews && count($recentViews) > 0)
                                    <div class="recent-views-grid">
                                        @foreach($recentViews as $product)
                                            <div class="recent-view-item">
                                                <a href="{{ route('client.product.detail', $product->slug) }}" class="recent-view-image">
                                                    <img src="{{ asset('clients/assets/img/clothes/' . ($product->primaryImage->url ?? 'no-image.webp')) }}" 
                                                         alt="{{ $product->name }}">
                                                </a>
                                                <div class="recent-view-info">
                                                    <h4 class="recent-view-name">
                                                        <a href="{{ route('client.product.detail', $product->slug) }}">
                                                            {{ $product->name }}
                                                        </a>
                                                    </h4>
                                                    <div class="recent-view-price">
                                                        {{ number_format($product->price, 0, ',', '.') }}đ
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="empty-state">
                                        <i class="fas fa-eye"></i>
                                        <p>Bạn chưa xem sản phẩm nào</p>
                                        <a href="{{ route('client.shop.index') }}" class="btn btn-primary">
                                            <i class="fas fa-shopping-cart"></i> Khám phá sản phẩm
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('foot')
    <script src="{{ asset('clients/assets/js/profile.js') }}"></script>
@endsection
