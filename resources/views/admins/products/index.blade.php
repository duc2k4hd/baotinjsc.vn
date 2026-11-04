@extends('admins.layouts.master')

@section('title', 'Quản lý sản phẩm')
@section('page-title', 'Sản phẩm')

@push('head')
    <link rel="shortcut icon" href="{{ asset('admins/img/icons/products-icon.png') }}" type="image/x-icon">
@endpush

@push('styles')
    <style>
        .products-container {
            margin: 0 auto;
        }

        /* Header Box */
        .page-header-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e2e8f0;
        }
        .page-header-box h2 {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .page-header-box p {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 13px;
        }
        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* Quick Filter Tabs / Stats Bar */
        .status-tabs-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }
        .status-tab-item {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-decoration: none;
            color: #334155;
            transition: all 0.15s ease;
            position: relative;
            overflow: hidden;
        }
        .status-tab-item:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
            color: #0f172a;
        }
        .status-tab-item.active {
            border-color: #3b82f6;
            background: #ffffff;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.12), 0 4px 12px rgba(59, 130, 246, 0.06);
        }
        .status-tab-item.active::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: #3b82f6;
        }
        .status-tab-title {
            font-size: 12.5px;
            font-weight: 600;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .status-tab-item.active .status-tab-title {
            color: #1e40af;
        }
        .status-tab-count {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }
        .status-tab-item.active .status-tab-count {
            color: #2563eb;
        }

        /* Filter Panel */
        .filter-panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        }
        .filter-panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #e2e8f0;
        }
        .filter-panel-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 7px;
        }
        .filter-results-summary {
            font-size: 12.5px;
            color: #64748b;
        }
        .filter-results-summary strong {
            color: #0f172a;
        }
        .filter-grid {
            display: grid;
            grid-template-columns: 2fr 1.2fr 1.3fr 1.1fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }
        .filter-grid-secondary {
            display: grid;
            grid-template-columns: 1.2fr 1.2fr 1.3fr auto;
            gap: 12px;
            align-items: flex-end;
        }
        @media (max-width: 1280px) {
            .filter-grid, .filter-grid-secondary {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 768px) {
            .filter-grid, .filter-grid-secondary {
                grid-template-columns: 1fr;
            }
        }
        .filter-field label {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 5px;
            display: block;
        }
        .filter-field input,
        .filter-field select {
            width: 100%;
            height: 38px;
            padding: 7px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 13px;
            color: #1e293b;
            background-color: #f8fafc;
            transition: all 0.15s ease;
        }
        .filter-field input:focus,
        .filter-field select:focus {
            background-color: #ffffff;
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);
        }
        .filter-field-search {
            position: relative;
        }
        .filter-field-search input {
            padding-left: 34px;
        }
        .filter-field-search .search-icon {
            position: absolute;
            left: 12px;
            top: 31px;
            color: #94a3b8;
            font-size: 13px;
            pointer-events: none;
        }

        .filter-actions-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Table Card */
        .table-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            margin-bottom: 24px;
        }
        .table-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding: 14px 18px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        .table-card-header h3 {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Bulk Actions Bar */
        .bulk-actions-bar {
            display: none;
            align-items: center;
            gap: 10px;
            padding: 6px 12px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            font-size: 12.5px;
            color: #1e40af;
            animation: fadeIn 0.2s ease;
        }
        .bulk-actions-bar.active {
            display: inline-flex;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Clean Table */
        .clean-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin: 0;
        }
        .clean-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 12px 14px;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }
        .clean-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: #334155;
        }
        .clean-table tbody tr:hover td {
            background: #f8fafc;
        }

        /* Product Cell */
        .product-cell {
            display: flex;
            align-items: center;
            gap: 12px;
            max-width: 380px;
        }
        .product-thumb-wrap {
            width: 46px;
            height: 46px;
            border-radius: 8px;
            overflow: hidden;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .product-thumb-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .product-thumb-placeholder {
            color: #94a3b8;
            font-size: 18px;
        }
        .product-info {
            display: flex;
            flex-direction: column;
            gap: 3px;
            min-width: 0;
        }
        .product-name {
            font-weight: 600;
            color: #0f172a;
            font-size: 13.5px;
            line-height: 1.35;
            text-decoration: none;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .product-name:hover {
            color: #2563eb;
        }
        .product-meta-row {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .sku-badge {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 11px;
            color: #475569;
            background: #f1f5f9;
            padding: 1px 6px;
            border-radius: 4px;
            font-weight: 600;
        }

        /* Feature Badges */
        .feature-badge {
            font-size: 10.5px;
            font-weight: 600;
            padding: 1px 6px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            line-height: 1.4;
        }
        .badge-featured {
            background: #fffbeb;
            color: #d97706;
            border: 1px solid #fde68a;
        }
        .badge-variants {
            background: #f5f3ff;
            color: #7c3aed;
            border: 1px solid #ddd6fe;
        }

        /* Price display */
        .price-box {
            display: flex;
            flex-direction: column;
            gap: 1px;
            white-space: nowrap;
        }
        .price-current {
            font-weight: 700;
            color: #0f172a;
            font-size: 13.5px;
        }
        .price-current.has-sale {
            color: #dc2626;
        }
        .price-original {
            font-size: 11.5px;
            color: #94a3b8;
            text-decoration: line-through;
        }

        /* Stock Pill */
        .stock-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
            white-space: nowrap;
        }
        .stock-in {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }
        .stock-low {
            background: #fffbeb;
            color: #d97706;
            border: 1px solid #fde68a;
        }
        .stock-out {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        /* Status Toggle Switch */
        .form-check.form-switch .form-check-input {
            width: 2.5em;
            height: 1.3em;
            cursor: pointer;
        }

        /* Action Buttons */
        .action-btn-group {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: 6px;
            white-space: nowrap;
        }
        .btn-table-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            font-size: 12.5px;
            text-decoration: none;
            transition: all 0.15s ease-in-out;
            border: 1px solid transparent;
            cursor: pointer;
            line-height: 1;
            flex-shrink: 0;
            background: #ffffff;
        }
        .btn-action-view {
            color: #0284c7;
            border-color: #bae6fd;
            background: #f0f9ff;
        }
        .btn-action-view:hover {
            background: #0284c7;
            color: #ffffff;
        }
        .btn-action-edit {
            color: #475569;
            border-color: #cbd5e1;
        }
        .btn-action-edit:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
        .btn-action-del {
            color: #dc2626;
            border-color: #fecaca;
        }
        .btn-action-del:hover {
            background: #fef2f2;
            color: #b91c1c;
        }
        .btn-action-restore {
            color: #16a34a;
            border-color: #bbf7d0;
            background: #f0fdf4;
        }
        .btn-action-restore:hover {
            background: #16a34a;
            color: #ffffff;
        }
        .btn-action-force-del {
            color: #ffffff;
            background: #0f172a;
            border-color: #0f172a;
        }
        .btn-action-force-del:hover {
            background: #dc2626;
            border-color: #dc2626;
        }

        /* Buttons Standard */
        .btn-modern-primary {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            background: #2563eb;
            color: #ffffff;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            border: 1px solid #2563eb;
            transition: all 0.15s ease;
        }
        .btn-modern-primary:hover {
            background: #1d4ed8;
            color: #ffffff;
        }
        .btn-modern-secondary {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            background: #ffffff;
            color: #334155;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            border: 1px solid #cbd5e1;
            transition: all 0.15s ease;
        }
        .btn-modern-secondary:hover {
            background: #f8fafc;
            color: #0f172a;
        }
    </style>
@endpush

@section('content')
<div class="products-container">

    {{-- Page Header --}}
    <div class="page-header-box">
        <div>
            <h2>
                <i class="fa-solid fa-boxes-stacked text-primary"></i>
                Quản lý sản phẩm
            </h2>
            <p>Kiểm soát kho hàng, cấu hình bảng giá, thuộc tính biến thể và quản lý trạng thái kinh doanh.</p>
        </div>

        <div class="header-actions">
            @if(Route::has('admin.products.export-excel'))
                <a href="{{ route('admin.products.export-excel') }}" class="btn btn-outline-success btn-sm" title="Xuất toàn bộ danh sách sản phẩm ra file Excel">
                    <i class="fa-solid fa-file-arrow-down"></i> Xuất Excel
                </a>
            @endif
            @if(Route::has('admin.products.import-excel'))
                <a href="{{ route('admin.products.import-excel') }}" class="btn btn-outline-primary btn-sm" title="Nhập danh sách sản phẩm từ file Excel">
                    <i class="fa-solid fa-file-excel"></i> Import Excel
                </a>
            @endif
            <a href="{{ route('admin.products.create') }}" class="btn-modern-primary" title="Tạo sản phẩm mới">
                <i class="fa-solid fa-plus"></i> Thêm sản phẩm
            </a>
        </div>
    </div>

    {{-- Quick Status Filter Tabs --}}
    @php
        $currentStatus = request('status', '');
    @endphp
    <div class="status-tabs-row">
        {{-- Tất cả --}}
        <a href="{{ route('admin.products.index', request()->except(['page', 'status'])) }}"
           class="status-tab-item {{ $currentStatus === '' ? 'active' : '' }}">
            <span class="status-tab-title">
                <i class="fa-solid fa-layer-group"></i> Tất cả sản phẩm
            </span>
            <span class="status-tab-count">{{ number_format($stats->total ?? 0) }}</span>
        </a>

        {{-- Đang bán --}}
        <a href="{{ route('admin.products.index', array_merge(request()->except(['page', 'status']), ['status' => 'active'])) }}"
           class="status-tab-item {{ $currentStatus === 'active' ? 'active' : '' }}">
            <span class="status-tab-title" style="color: #059669;">
                <i class="fa-solid fa-circle-check"></i> Đang bán
            </span>
            <span class="status-tab-count">{{ number_format($stats->active ?? 0) }}</span>
        </a>

        {{-- Tạm ẩn --}}
        <a href="{{ route('admin.products.index', array_merge(request()->except(['page', 'status']), ['status' => 'inactive'])) }}"
           class="status-tab-item {{ $currentStatus === 'inactive' ? 'active' : '' }}">
            <span class="status-tab-title" style="color: #64748b;">
                <i class="fa-solid fa-eye-slash"></i> Tạm ẩn
            </span>
            <span class="status-tab-count">{{ number_format($stats->inactive ?? 0) }}</span>
        </a>

        {{-- Hết hàng --}}
        <a href="{{ route('admin.products.index', array_merge(request()->except(['page', 'status']), ['status' => 'out_of_stock'])) }}"
           class="status-tab-item {{ $currentStatus === 'out_of_stock' ? 'active' : '' }}">
            <span class="status-tab-title" style="color: #dc2626;">
                <i class="fa-solid fa-triangle-exclamation"></i> Hết hàng
            </span>
            <span class="status-tab-count">{{ number_format($stats->out_of_stock ?? 0) }}</span>
        </a>

        {{-- Thùng rác --}}
        <a href="{{ route('admin.products.index', array_merge(request()->except(['page', 'status']), ['status' => 'trash'])) }}"
           class="status-tab-item {{ $currentStatus === 'trash' ? 'active' : '' }}">
            <span class="status-tab-title" style="color: #b91c1c;">
                <i class="fa-regular fa-trash-can"></i> Thùng rác
            </span>
            <span class="status-tab-count">{{ number_format($stats->trash ?? 0) }}</span>
        </a>
    </div>

    {{-- Filter Panel --}}
    <div class="filter-panel">
        <div class="filter-panel-header">
            <span class="filter-panel-title">
                <i class="fa-solid fa-filter text-primary"></i> Bộ lọc & Tìm kiếm sản phẩm
            </span>
            <div class="filter-results-summary">
                Hiển thị <strong>{{ number_format($products->firstItem() ?? 0) }} - {{ number_format($products->lastItem() ?? 0) }}</strong> trên tổng <strong>{{ number_format($products->total()) }}</strong> kết quả
            </div>
        </div>

        <form method="GET" action="{{ route('admin.products.index') }}" id="filter-form">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            {{-- Hàng lọc chính --}}
            <div class="filter-grid">
                {{-- Từ khóa --}}
                <div class="filter-field filter-field-search">
                    <label for="filter-keyword">Tìm kiếm</label>
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input
                        id="filter-keyword"
                        type="text"
                        name="keyword"
                        value="{{ request('keyword') }}"
                        placeholder="Tên sản phẩm hoặc mã SKU..."
                    >
                </div>

                {{-- Hãng --}}
                <div class="filter-field">
                    <label for="filter-brand">Hãng / Thương hiệu</label>
                    <select id="filter-brand" name="brand_id">
                        <option value="">Tất cả hãng</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ (string) request('brand_id') === (string) $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Danh mục --}}
                <div class="filter-field">
                    <label for="filter-category">Danh mục</label>
                    <select id="filter-category" name="category_id">
                        <option value="">Tất cả danh mục</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (string) request('category_id') === (string) $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Kho hàng --}}
                <div class="filter-field">
                    <label for="filter-stock-status">Tồn kho</label>
                    <select id="filter-stock-status" name="stock_status">
                        <option value="">Tất cả kho hàng</option>
                        <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>Còn hàng</option>
                        <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Hết hàng</option>
                    </select>
                </div>

                {{-- Số lượng mỗi trang --}}
                <div class="filter-field">
                    <label for="filter-per-page">Hiển thị / trang</label>
                    <select id="filter-per-page" name="per_page">
                        @foreach([20, 50, 100, 200] as $option)
                            <option value="{{ $option }}" {{ (int) request('per_page', 20) === (int) $option ? 'selected' : '' }}>
                                {{ $option }} dòng
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Hàng lọc mở rộng --}}
            <div class="filter-grid-secondary">
                {{-- Nổi bật --}}
                <div class="filter-field">
                    <label for="filter-featured">Sản phẩm nổi bật</label>
                    <select id="filter-featured" name="is_featured">
                        <option value="">Tất cả</option>
                        <option value="1" {{ request('is_featured') === '1' ? 'selected' : '' }}>Chỉ nổi bật</option>
                        <option value="0" {{ request('is_featured') === '0' ? 'selected' : '' }}>Không nổi bật</option>
                    </select>
                </div>

                {{-- Sắp xếp --}}
                <div class="filter-field">
                    <label for="filter-sort">Sắp xếp theo</label>
                    <select id="filter-sort" name="sort_by">
                        <option value="latest" {{ request('sort_by', 'latest') === 'latest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="oldest" {{ request('sort_by') === 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                        <option value="name_asc" {{ request('sort_by') === 'name_asc' ? 'selected' : '' }}>Tên A &rarr; Z</option>
                        <option value="price_asc" {{ request('sort_by') === 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                        <option value="price_desc" {{ request('sort_by') === 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                        <option value="stock_desc" {{ request('sort_by') === 'stock_desc' ? 'selected' : '' }}>Tồn kho cao nhất</option>
                        <option value="stock_asc" {{ request('sort_by') === 'stock_asc' ? 'selected' : '' }}>Tồn kho thấp nhất</option>
                    </select>
                </div>

                {{-- Nút bấm Lọc & Reset --}}
                <div class="filter-actions-group" style="padding-bottom: 2px;">
                    <button type="submit" class="btn-modern-primary" style="height: 38px;">
                        <i class="fa-solid fa-filter"></i> Lọc
                    </button>
                    <a href="{{ route('admin.products.index', request('status') ? ['status' => request('status')] : []) }}"
                       class="btn-modern-secondary" style="height: 38px;" title="Xóa bộ lọc">
                        <i class="fa-solid fa-arrow-rotate-left"></i> Đặt lại
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Main Table Card --}}
    <div class="table-card">
        <div class="table-card-header">
            <h3>
                <i class="fa-solid fa-list-check text-primary"></i>
                Danh sách kết quả
                @if(request('status') === 'trash')
                    <span class="badge bg-danger ms-2" style="font-size: 11px; font-weight: 500;">Thùng rác</span>
                @endif
            </h3>

            {{-- Bulk Actions Bar (Tự động hiện khi tick checkbox) --}}
            <div class="bulk-actions-bar" id="bulkActionsBar">
                <span class="d-inline-flex align-items-center gap-1">
                    <strong id="selectedCountText">0</strong> sản phẩm được chọn:
                </span>

                <form action="{{ route('admin.products.bulk-action') }}" method="POST" id="bulk-action-form" class="d-inline-flex gap-1 m-0">
                    @csrf

                    @if(request('status') === 'trash')
                        <button type="submit" class="btn btn-sm btn-outline-success bg-white" name="bulk_action" value="restore">
                            <i class="fa-solid fa-rotate-left"></i> Khôi phục đã chọn
                        </button>
                        <button
                            type="submit"
                            class="btn btn-sm btn-danger"
                            name="bulk_action"
                            value="force_delete"
                            onclick="return confirm('CẢNH BÁO: Xóa vĩnh viễn các sản phẩm đã chọn? Dữ liệu này sẽ mất hoàn toàn và KHÔNG thể khôi phục!')"
                        >
                            <i class="fa-solid fa-trash-can"></i> Xóa vĩnh viễn đã chọn
                        </button>
                    @else
                        <button type="submit" class="btn btn-sm btn-outline-success bg-white" name="bulk_action" value="show" title="Hiển thị sản phẩm ra website">
                            <i class="fa-solid fa-eye"></i> Hiện
                        </button>
                        <button type="submit" class="btn btn-sm btn-outline-secondary bg-white" name="bulk_action" value="hide" title="Tạm ẩn sản phẩm khỏi website">
                            <i class="fa-solid fa-eye-slash"></i> Ẩn
                        </button>
                        <button
                            type="submit"
                            class="btn btn-sm btn-outline-danger bg-white"
                            name="bulk_action"
                            value="delete"
                            onclick="return confirm('Chuyển các sản phẩm đã chọn vào Thùng rác? Bạn vẫn có thể khôi phục sau.')"
                        >
                            <i class="fa-regular fa-trash-can"></i> Bỏ vào thùng rác
                        </button>
                    @endif
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="clean-table" id="productsTable">
                <thead>
                    <tr>
                        <th style="width: 44px; text-align: center;">
                            <input type="checkbox" id="select-all-products" class="form-check-input mt-0" style="cursor: pointer;">
                        </th>
                        <th>Sản phẩm</th>
                        <th style="width: 140px;">Hãng</th>
                        <th style="width: 160px;">Danh mục</th>
                        <th style="width: 130px;">Giá bán</th>
                        <th style="width: 110px; text-align: center;">Tồn kho</th>
                        <th style="width: 120px; text-align: center;">Trạng thái</th>
                        <th style="width: 130px; text-align: right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        @php
                            $primaryImg = $product->primary_image;
                            $imgUrl = $primaryImg ? ($primaryImg->full_url ?? null) : null;
                            $hasDiscount = $product->sale_price && $product->sale_price < $product->price;
                        @endphp
                        <tr>
                            {{-- Checkbox --}}
                            <td style="text-align: center;">
                                <input
                                    type="checkbox"
                                    name="selected[]"
                                    value="{{ $product->id }}"
                                    class="product-checkbox form-check-input mt-0"
                                    form="bulk-action-form"
                                    style="cursor: pointer;"
                                    data-id="{{ $product->id }}"
                                >
                            </td>

                            {{-- Sản phẩm (Ảnh + Tên + SKU + Badges) --}}
                            <td>
                                <div class="product-cell">
                                    <div class="product-thumb-wrap">
                                        @if($imgUrl)
                                            <img
                                                src="{{ $imgUrl }}"
                                                alt="{{ $product->name }}"
                                                class="product-thumb-img"
                                                loading="lazy"
                                                onerror="this.onerror=null;this.parentElement.innerHTML='<span class=\'product-thumb-placeholder\'><i class=\'fa-solid fa-box\'></i></span>';"
                                            >
                                        @else
                                            <span class="product-thumb-placeholder">
                                                <i class="fa-solid fa-box"></i>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="product-info">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="product-name" title="{{ $product->name }}">
                                            {{ $product->name }}
                                        </a>
                                        <div class="product-meta-row">
                                            @if($product->sku)
                                                <span class="sku-badge" title="Mã SKU">
                                                    {{ $product->sku }}
                                                </span>
                                            @endif

                                            @if($product->is_featured)
                                                <span class="feature-badge badge-featured" title="Sản phẩm nổi bật">
                                                    <i class="fa-solid fa-star"></i> Nổi bật
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Hãng --}}
                            <td>
                                @if($product->brand)
                                    <span style="font-weight: 500; color: #1e293b;">{{ $product->brand->name }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>

                            {{-- Danh mục --}}
                            <td>
                                @if($product->primaryCategory)
                                    <span style="color: #475569; font-size: 12.5px;">{{ $product->primaryCategory->name }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>

                            {{-- Giá bán --}}
                            <td>
                                <div class="price-box">
                                    @if($hasDiscount)
                                        <span class="price-current has-sale">{{ number_format($product->sale_price) }}đ</span>
                                        <span class="price-original">{{ number_format($product->price) }}đ</span>
                                    @else
                                        <span class="price-current">{{ number_format($product->price) }}đ</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Tồn kho --}}
                            <td style="text-align: center;">
                                @if($product->stock_quantity > 10)
                                    <span class="stock-pill stock-in" title="Số lượng: {{ number_format($product->stock_quantity) }}">
                                        {{ number_format($product->stock_quantity) }} sp
                                    </span>
                                @elseif($product->stock_quantity > 0)
                                    <span class="stock-pill stock-low" title="Sắp hết hàng!">
                                        {{ number_format($product->stock_quantity) }} sp
                                    </span>
                                @else
                                    <span class="stock-pill stock-out">
                                        Hết hàng
                                    </span>
                                @endif
                            </td>

                            {{-- Trạng thái Switch --}}
                            <td style="text-align: center;">
                                @if($product->trashed())
                                    <span class="badge bg-danger">Đã xóa</span>
                                @else
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input status-toggle-switch" 
                                               type="checkbox" 
                                               data-id="{{ $product->id }}"
                                               data-url="{{ route('admin.products.toggle-status', $product) }}"
                                               {{ $product->is_active ? 'checked' : '' }}
                                               title="{{ $product->is_active ? 'Đang bán (Click để tạm ẩn)' : 'Đang tạm ẩn (Click để bật bán)' }}">
                                    </div>
                                @endif
                            </td>

                            {{-- Thao tác --}}
                            <td style="text-align: right;">
                                <div class="action-btn-group">
                                    @if($product->trashed())
                                        {{-- Nút Khôi phục --}}
                                        <form action="{{ route('admin.products.restore', $product->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn-table-action btn-action-restore" title="Khôi phục sản phẩm này">
                                                <i class="fa-solid fa-rotate-left"></i>
                                            </button>
                                        </form>

                                        {{-- Nút Xóa vĩnh viễn --}}
                                        <form
                                            action="{{ route('admin.products.force-delete', $product->id) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('CẢNH BÁO: Xóa vĩnh viễn sản phẩm &quot;{{ addslashes($product->name) }}&quot;? Hành động này KHÔNG thể hoàn tác!')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-table-action btn-action-force-del" title="Xóa vĩnh viễn">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @else
                                        {{-- Nút Xem ngoài web --}}
                                        @if($product->slug)
                                            <a href="{{ url('/' . $product->slug) }}" target="_blank" class="btn-table-action btn-action-view" title="Xem sản phẩm ngoài website">
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                        @endif

                                        {{-- Nút Chỉnh sửa --}}
                                        <a href="{{ route('admin.products.edit', $product) }}" class="btn-table-action btn-action-edit" title="Chỉnh sửa chi tiết">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        {{-- Nút Xóa mềm (Bỏ vào thùng rác) --}}
                                        <form
                                            action="{{ route('admin.products.destroy', $product) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Chuyển sản phẩm &quot;{{ addslashes($product->name) }}&quot; vào Thùng rác?')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-table-action btn-action-del" title="Chuyển vào thùng rác">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-box-open fa-3x mb-3 text-secondary"></i>
                                    <p class="mb-0 fs-6">Không tìm thấy sản phẩm nào phù hợp với điều kiện tìm kiếm.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Box --}}
        @if($products->hasPages())
            <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                    Trang {{ $products->currentPage() }} / {{ $products->lastPage() }}
                </div>
                <div>
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Checkbox All & Bulk Actions
    const selectAll = document.getElementById('select-all-products');
    const productCheckboxes = document.querySelectorAll('.product-checkbox');
    const bulkBar = document.getElementById('bulkActionsBar');
    const selectedCountText = document.getElementById('selectedCountText');

    function updateBulkBar() {
        const checked = document.querySelectorAll('.product-checkbox:checked');
        const count = checked.length;
        if (selectedCountText) selectedCountText.textContent = count;
        if (bulkBar) {
            if (count > 0) {
                bulkBar.classList.add('active');
            } else {
                bulkBar.classList.remove('active');
            }
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            productCheckboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateBulkBar();
        });
    }

    productCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            updateBulkBar();
            if (selectAll) {
                selectAll.checked = document.querySelectorAll('.product-checkbox:checked').length === productCheckboxes.length;
            }
        });
    });

    // Toggle Status via AJAX
    document.querySelectorAll('.status-toggle-switch').forEach(toggle => {
        toggle.addEventListener('change', async function () {
            const url = this.dataset.url;
            const originalState = !this.checked;
            
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    }
                });

                const data = await res.json();
                if (!data.success) {
                    this.checked = originalState;
                    alert(data.message || 'Có lỗi xảy ra');
                }
            } catch (e) {
                this.checked = originalState;
                alert('Lỗi kết nối máy chủ khi cập nhật trạng thái');
            }
        });
    });
});
</script>
@endpush
