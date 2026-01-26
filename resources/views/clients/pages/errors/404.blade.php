@extends('clients.layouts.master')

@section('title', '404 - Rất Tiếc, Không Tìm Thấy Trang | ' . ($settings->site_name ?? 'Bảo Tín JSC'))

@section('head')
    <link rel="stylesheet" href="{{ asset('clients/assets/css/404.css') }}">
    <meta name="theme-color" content="#ffffff">
    <meta name="robots" content="nofollow, noindex" />
@endsection

@section('content')
<div class="error404_container" bis_skin_checked="1">
    <header class="error404_error-header">
        <h1 class="error404_error-code">4<span class="error404_glow" data-text="0">0</span>4</h1>
    </header>

    <section class="error404_error-content">
        <h2 class="error404_error-title">Sản Phẩm Đang Lạc Lối, Hay Là...?</h2>
        <p class="error404_error-message">
            Đường dẫn bạn truy cập hiện không khả dụng. Đừng lo lắng, hãy quay lại khám phá những bộ sưu tập mới nhất!
        </p>

        <div class="error404_action-buttons" bis_skin_checked="1">
            <a href="{{ route('client.home.index') }}" class="error404_btn error404_primary-btn">
                <i class="error404_icon fas fa-home"></i> Trở Về Trang Chủ
            </a>
            <a href="{{ route('client.shop.index') }}" class="error404_btn error404_secondary-btn">
                <i class="error404_icon fas fa-gift"></i> Khám Phá Hàng Mới
            </a>
            <a href="{{ route('client.contact.index') }}" class="error404_btn error404_secondary-btn">
                <i class="error404_icon fas fa-gift"></i> Liên hệ hỗ trợ
            </a>
        </div>

        <div class="error404_suggestion-area" bis_skin_checked="1">
            <h3>✨ Gợi Ý Dành Cho Bạn</h3>
            <div id="product-suggestions" class="error404_product-list" bis_skin_checked="1">
            </div>
        </div>
    </section>

    <footer class="error404_footer">
        <p>© {{ date('Y') }} - {{ $settings->site_name ?? 'Bảo Tín JSC' }}.</p>
    </footer>
</div>
@endsection
