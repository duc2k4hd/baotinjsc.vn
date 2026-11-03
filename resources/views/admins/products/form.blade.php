@extends('admins.layouts.master')

@php
    $isEdit = $product->exists;
    $pageTitle = $isEdit ? 'Chỉnh sửa sản phẩm' : 'Tạo sản phẩm mới';

    // Xử lý selected tags
    $selectedTagIds = old('tag_ids', []);
    if (empty($selectedTagIds) && $product->exists) {
        $selectedTagIds = is_array($product->tag_ids) ? $product->tag_ids : [];
    }

    $selectedTags = !empty($selectedTagIds) ? \App\Models\Tag::whereIn('id', $selectedTagIds)->get() : collect();
    $selectedTagNames = $selectedTags->pluck('name')->toArray();
    $tagNamesInput = old('tag_names', '');

    $mediaScanner = app(\App\Services\Media\MediaScannerService::class);
    $productMediaFolders = collect(config('media.directories', []))
        ->map(function ($path, $key) {
            $label = \Illuminate\Support\Str::headline(str_replace('_', ' ', $key));
            $scope = str_starts_with($path, 'clients/') ? 'Frontend' : 'Admin';
            return [
                'key' => $key,
                'path' => $path,
                'label' => $label,
                'scope' => $scope,
            ];
        })
        ->values();
    $productMediaTypeFilters = ['all' => 'Tất cả loại'] + $mediaScanner->getTypeLabels();
    $productMediaStatusFilters = $mediaScanner->getStatusLabels();
    $productMediaUploadTargets = [
        'product' => 'Sản phẩm',
        'post' => 'Bài viết',
        'category' => 'Danh mục',
        'banner_desktop' => 'Banner desktop',
        'banner_mobile' => 'Banner mobile',
    ];
    $productMediaInitialStats = [
        'library_items' => 0,
        'tracked_records' => 0,
        'physical_files' => 0,
        'orphan_files' => 0,
        'missing_files' => 0,
        'unassigned_records' => 0,
        'estimated_size' => '0 B',
        'status_counts' => collect(array_keys($productMediaStatusFilters))->mapWithKeys(fn ($k) => [$k => 0])->all(),
    ];
    $productMediaInitialPagination = [
        'total' => 0,
        'per_page' => 50,
        'current_page' => 1,
        'last_page' => 1,
        'from' => 0,
        'to' => 0,
    ];

    // Dữ liệu ảnh gallery
    $existingImages = [];
    if (old('images')) {
        $existingImages = old('images');
    } elseif ($product->exists && $product->images) {
        $existingImages = $product->images->map(function ($img, $idx) {
            return [
                'id' => $img->id,
                'title' => $img->name ?? $img->title ?? '',
                'notes' => $img->context ?? '',
                'alt' => $img->name ?? '',
                'order' => $idx,
                'existing_path' => $img->path ?? $img->url,
                'is_primary' => $idx === 0 ? 1 : 0,
            ];
        })->toArray();
    }

    // Dữ liệu biến thể
    $existingVariants = old('variants', $product->exists ? $product->allVariants->toArray() : []);
@endphp

@section('title', $pageTitle)
@section('page-title', $pageTitle)

@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css">
    <link rel="stylesheet" href="{{ asset('admins/css/media-manager.css') }}?v={{ env('APP_VERSION', '1.0') }}">
@endpush

@push('styles')
<style>
    /* 2 COLUMN LAYOUT - WordPress Style */
    #product-form .row.g-4 {
        display: flex !important;
        flex-wrap: wrap !important;
        margin-left: -12px !important;
        margin-right: -12px !important;
        align-items: flex-start !important;
    }

    #product-form .col-lg-8 {
        width: 66.66666667% !important;
        flex: 0 0 66.66666667% !important;
        max-width: 66.66666667% !important;
        padding-left: 12px !important;
        padding-right: 12px !important;
        box-sizing: border-box !important;
    }

    #product-form .col-lg-4 {
        width: 33.33333333% !important;
        flex: 0 0 33.33333333% !important;
        max-width: 33.33333333% !important;
        padding-left: 12px !important;
        padding-right: 12px !important;
        box-sizing: border-box !important;
    }

    @media (min-width: 992px) {
        #product-form .col-lg-4 {
            position: sticky !important;
            top: 20px !important;
            align-self: flex-start !important;
            max-height: calc(100vh - 40px) !important;
            overflow-y: auto !important;
        }
    }

    @media (max-width: 991.98px) {
        #product-form .col-lg-8,
        #product-form .col-lg-4 {
            width: 100% !important;
            flex: 0 0 100% !important;
            max-width: 100% !important;
            position: relative !important;
        }
    }

    .card {
        background: #fff;
        border-radius: 10px;
        padding: 16px 20px;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.08);
        border: 1px solid #e2e8f0;
        margin-bottom: 20px;
    }

    .card > h5, .card-body > h5 {
        margin: 0 0 14px;
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 12px 16px;
    }

    .grid-3 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 10px 14px;
    }

    .form-control, textarea, select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 13px;
    }

    .form-control:focus, textarea:focus, select:focus {
        border-color: #3b82f6;
        outline: none;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);
    }

    label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #334155;
    }

    .repeater-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .repeater-item {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 14px;
        background: #f8fafc;
    }

    .repeater-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        padding-bottom: 6px;
        border-bottom: 1px dashed #cbd5e1;
    }

    .btn-link {
        background: none;
        border: none;
        color: #ef4444;
        cursor: pointer;
        font-size: 13px;
        padding: 0;
        font-weight: 600;
    }

    .btn-link:hover {
        color: #b91c1c;
        text-decoration: underline;
    }

    .image-preview {
        margin-top: 10px;
    }

    .image-preview img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
    }

    .product-image-picker-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 8px;
    }

    .product-image-picker-status {
        margin-top: 8px;
        padding: 6px 10px;
        border-radius: 6px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e3a8a;
        font-size: 12px;
        word-break: break-all;
    }

    .product-image-picker-status.is-empty {
        background: #f8fafc;
        border-color: #e2e8f0;
        color: #64748b;
    }

    .tox-tinymce {
        border-radius: 6px !important;
        border: 1px solid #cbd5e1 !important;
    }

    /* CKEditor 5 Styles: Mô tả chi tiết min-height 800px, tự động mở rộng theo content (loại trừ figcaption) */
    .product-detail-editor-wrap .ck-editor__main > .ck-editor__editable,
    .product-detail-editor-wrap .ck-editor__editable_inline:not(.ck-editor__nested-editable) {
        min-height: 800px !important;
        height: auto !important;
    }

    /* CKEditor 5 Styles: Mô tả ngắn gọn gàng */
    .product-short-editor-wrap .ck-editor__main > .ck-editor__editable,
    .product-short-editor-wrap .ck-editor__editable_inline:not(.ck-editor__nested-editable) {
        min-height: 220px !important;
        height: auto !important;
    }

    /* figcaption của ảnh luôn fit-content, không bị ăn min-height của editor chính */
    .ck-content .image > figcaption,
    .ck-editor__editable figcaption,
    figcaption.ck-editor__nested-editable,
    figcaption.ck-editor__editable {
        min-height: auto !important;
        height: auto !important;
        max-height: none !important;
        width: fit-content !important;
        max-width: 100% !important;
        margin: 6px auto 0 !important;
        padding: 3px 8px !important;
        display: table !important;
        caption-side: bottom !important;
        font-size: 13px !important;
        color: #64748b !important;
        text-align: center !important;
    }
</style>
@endpush

@section('content')
<form id="product-form" action="{{ $isEdit ? route('admin.products.update', $product) : route('admin.products.store') }}" method="POST" enctype="multipart/form-data" spellcheck="false">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    {{-- Alert Locking --}}
    @if($isEdit && $product->locked_by === auth('web')->id())
        <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center gap-2">
            <i class="fa-solid fa-lock"></i>
            <span>Bạn đang khóa sản phẩm này để chỉnh sửa. Sau khi lưu, sản phẩm sẽ tự động mở khóa.</span>
        </div>
    @endif

    <div class="row g-4">
        <!-- Cột trái: Nội dung chính -->
        <div class="col-lg-8">
            {{-- Thông tin cơ bản --}}
            <div class="card">
                <h5><i class="fa-solid fa-file-pen text-primary me-2"></i>Thông tin cơ bản</h5>
                
                <div class="mb-3">
                    <label>Tên sản phẩm <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-lg fw-bold" name="name" id="product-name" value="{{ old('name', $product->name) }}" required placeholder="Nhập tên sản phẩm...">
                </div>

                <div class="mb-3">
                    <label>Slug (Đường dẫn tĩnh)</label>
                    <input type="text" class="form-control" name="slug" id="product-slug" value="{{ old('slug', $product->slug) }}" placeholder="Tự sinh nếu để trống">
                    <small class="text-muted">Đường dẫn thân thiện hiển thị trên thanh địa chỉ trình duyệt</small>
                </div>

                <div class="mb-3 product-short-editor-wrap">
                    <label>Mô tả ngắn (Tối đa 2000 ký tự)</label>
                    <textarea class="form-control tinymce-editor" name="short_description" id="product-short-description" rows="4" maxlength="2000" placeholder="Tóm tắt ngắn gọn các đặc điểm nổi bật nhất của sản phẩm...">{{ old('short_description', $product->short_description) }}</textarea>
                </div>

                <div class="mb-3 product-detail-editor-wrap">
                    <label>Mô tả chi tiết</label>
                    <textarea class="form-control tinymce-editor" name="description" id="product-description" rows="12">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="grid-2 mt-3">
                    <div>
                        <label>Link Catalog / Tài liệu</label>
                        <input type="text" class="form-control" name="link_catalog" value="{{ is_array($product->link_catalog) ? implode(', ', $product->link_catalog) : old('link_catalog', $product->link_catalog) }}" placeholder="Link file PDF hoặc tài liệu...">
                    </div>
                    <div>
                        <label>Video URL (Youtube)</label>
                        <input type="text" class="form-control" name="video_url" value="{{ old('video_url', $product->video_url) }}" placeholder="https://www.youtube.com/watch?v=...">
                    </div>
                </div>
            </div>

            {{-- Thư viện ảnh gallery --}}
            <div class="card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fa-solid fa-images text-primary me-2"></i>Thư viện ảnh sản phẩm</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary fw-bold" data-add="#image-list" data-template="#image-template">
                        + Thêm ảnh
                    </button>
                </div>
                <p class="text-muted small mb-3">
                    Bạn có thể chọn ảnh từ thư viện media có sẵn hoặc upload ảnh mới. Tích chọn "Ảnh chính" để làm đại diện.
                </p>

                <div class="repeater-list" id="image-list">
                    @foreach($existingImages as $index => $image)
                        @php
                            $storedValue = $image['existing_path'] ?? $image['path'] ?? $image['url'] ?? null;
                            $storedUrl = $storedValue
                                ? (str_starts_with($storedValue, 'http') ? $storedValue : asset($storedValue))
                                : null;
                        @endphp
                        <div class="repeater-item">
                            <div class="repeater-header">
                                <strong>Ảnh #{{ $index + 1 }}</strong>
                                <button type="button" class="btn-link" data-remove data-item=".repeater-item">Xóa</button>
                            </div>
                            <input type="hidden" name="images[{{ $index }}][id]" value="{{ $image['id'] ?? null }}">
                            
                            <div class="grid-2">
                                <div>
                                    <label>Tiêu đề (Title)</label>
                                    <input type="text" class="form-control" name="images[{{ $index }}][title]" value="{{ $image['title'] ?? '' }}">
                                </div>
                                <div>
                                    <label>Thẻ Alt</label>
                                    <input type="text" class="form-control" name="images[{{ $index }}][alt]" value="{{ $image['alt'] ?? '' }}">
                                </div>
                                <div>
                                    <label>Ghi chú</label>
                                    <input type="text" class="form-control" name="images[{{ $index }}][notes]" value="{{ $image['notes'] ?? '' }}">
                                </div>
                                <div>
                                    <label>Thứ tự</label>
                                    <input type="number" class="form-control" name="images[{{ $index }}][order]" value="{{ $image['order'] ?? $index }}">
                                </div>
                            </div>

                            <div class="mt-3">
                                <label>File ảnh</label>
                                <input type="hidden" id="image-path-{{ $index }}" name="images[{{ $index }}][existing_path]" value="{{ $storedValue }}">
                                <div class="image-preview" id="image-preview-{{ $index }}">
                                    @if($storedUrl)
                                        <img src="{{ $storedUrl }}" alt="">
                                    @endif
                                </div>
                                <div class="product-image-picker-actions">
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-product-media-open>
                                        <i class="fa-solid fa-photo-film me-1"></i> Mở thư viện ảnh
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-product-media-clear>
                                        Bỏ chọn
                                    </button>
                                </div>
                                <div class="product-image-picker-status {{ $storedValue ? '' : 'is-empty' }}" data-product-media-status>
                                    {{ $storedValue ? 'Đang chọn: ' . $storedValue : 'Chưa chọn ảnh từ thư viện.' }}
                                </div>
                            </div>

                            <div class="mt-2">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input me-1" name="images[{{ $index }}][is_primary]" value="1" {{ !empty($image['is_primary']) ? 'checked' : '' }}>
                                    Đặt làm ảnh chính
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Biến thể & Phân loại --}}
            <div class="card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fa-solid fa-cubes text-primary me-2"></i>Biến thể sản phẩm</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary fw-bold" data-add="#variant-list" data-template="#variant-template">
                        + Thêm biến thể
                    </button>
                </div>
                <p class="text-muted small mb-3">
                    Nếu sản phẩm có nhiều quy cách, kích cỡ, phân loại hoặc tùy chọn, hãy thêm các biến thể bên dưới.
                </p>

                <div class="repeater-list" id="variant-list">
                    @foreach($existingVariants as $index => $variant)
                        <div class="repeater-item">
                            <div class="repeater-header">
                                <strong>Biến thể #{{ $index + 1 }}</strong>
                                <button type="button" class="btn-link" data-remove data-item=".repeater-item">Xóa</button>
                            </div>
                            <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant['id'] ?? null }}">

                            <div class="grid-2">
                                <div>
                                    <label>Tên biến thể <span class="text-danger">*</span> (VD: 1m có chậu, Combo 3)</label>
                                    <input type="text" class="form-control" name="variants[{{ $index }}][name]" value="{{ $variant['name'] ?? '' }}" required>
                                </div>
                                <div>
                                    <label>Mã SKU biến thể</label>
                                    <input type="text" class="form-control" name="variants[{{ $index }}][sku]" value="{{ $variant['sku'] ?? '' }}">
                                </div>
                                <div>
                                    <label>Giá bán <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="variants[{{ $index }}][price]" value="{{ $variant['price'] ?? '' }}" required min="0">
                                </div>
                                <div>
                                    <label>Giá khuyến mãi</label>
                                    <input type="number" class="form-control" name="variants[{{ $index }}][sale_price]" value="{{ $variant['sale_price'] ?? '' }}" min="0">
                                </div>
                                <div>
                                    <label>Tồn kho</label>
                                    <input type="number" class="form-control" name="variants[{{ $index }}][stock_quantity]" value="{{ $variant['stock_quantity'] ?? '' }}" min="0">
                                </div>
                                <div>
                                    <label>Trạng thái</label>
                                    <select class="form-control" name="variants[{{ $index }}][is_active]">
                                        <option value="1" {{ !empty($variant['is_active']) ? 'selected' : '' }}>Kích hoạt</option>
                                        <option value="0" {{ empty($variant['is_active']) ? 'selected' : '' }}>Tắt</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-2">
                                <label>Ghi chú</label>
                                <input type="text" class="form-control" name="variants[{{ $index }}][note]" value="{{ $variant['note'] ?? '' }}" placeholder="Thông tin bổ sung cho biến thể này...">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Cấu hình SEO Meta --}}
            @php
                $metaKeywordsValue = old('meta_keywords');
                if (is_null($metaKeywordsValue)) {
                    $metaKeywordsValue = is_array($product->meta_keywords ?? null)
                        ? implode(', ', $product->meta_keywords)
                        : ($product->meta_keywords ?? '');
                }
            @endphp
            <div class="card">
                <h5><i class="fa-solid fa-magnifying-glass-chart text-primary me-2"></i>Cấu hình SEO Meta</h5>
                <div class="mb-3">
                    <label>Meta Title</label>
                    <input type="text" class="form-control" name="meta_title" value="{{ old('meta_title', $product->meta_title) }}" placeholder="Tiêu đề hiển thị trên kết quả tìm kiếm Google...">
                </div>
                <div class="mb-3">
                    <label>Meta Description</label>
                    <textarea class="form-control" rows="3" name="meta_description" placeholder="Đoạn văn ngắn mô tả sản phẩm trên Google...">{{ old('meta_description', $product->meta_description) }}</textarea>
                </div>
                <div class="mb-3">
                    <label>Meta Keywords</label>
                    <input type="text" class="form-control" name="meta_keywords" value="{{ $metaKeywordsValue }}" placeholder="từ khóa 1, từ khóa 2, từ khóa 3">
                </div>
            </div>
        </div>

        <!-- Cột phải: Thông tin quan trọng (Sticky) -->
        <div class="col-lg-4">
            {{-- Hộp Hành động & Lưu --}}
            <div class="card">
                <h5><i class="fa-solid fa-floppy-disk text-primary me-2"></i>Hành động</h5>
                
                <div class="mb-3">
                    <label>Trạng thái bán hàng</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ old('is_active', $product->is_active ?? true) ? 'selected' : '' }}>🟢 Đang bán</option>
                        <option value="0" {{ old('is_active', $product->is_active ?? true) ? '' : 'selected' }}>⚪ Tạm ẩn</option>
                    </select>
                </div>

                <div class="mb-3 form-check form-switch">
                    <input type="hidden" name="is_featured" value="0">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeaturedSwitch" {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="isFeaturedSwitch">⭐ Sản phẩm nổi bật</label>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold">
                        <i class="fa-solid fa-check me-1"></i> {{ $isEdit ? 'Cập nhật sản phẩm' : 'Đăng sản phẩm' }}
                    </button>
                    @if($isEdit && $product->slug)
                        <a href="{{ url('/' . $product->slug) }}" target="_blank" class="btn btn-outline-secondary">
                            <i class="fa-regular fa-eye me-1"></i> Xem ngoài website
                        </a>
                    @endif
                    <a href="{{ route('admin.products.index') }}" class="btn btn-link text-muted text-center">
                        Quay lại danh sách
                    </a>
                </div>
            </div>

            {{-- Hộp Giá & Tồn kho --}}
            <div class="card">
                <h5><i class="fa-solid fa-coins text-primary me-2"></i>Giá & Kho hàng</h5>

                <div class="mb-3">
                    <label>Mã SKU <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="sku" value="{{ old('sku', $product->sku) }}" required placeholder="SP-12345">
                </div>

                <div class="mb-3">
                    <label>Giá bán <span class="text-danger">*</span></label>
                    <input type="number" class="form-control fw-bold" name="price" value="{{ old('price', $product->price ?? 0) }}" min="0" step="1" required placeholder="0">
                </div>

                <div class="mb-3">
                    <label>Giá khuyến mãi</label>
                    <input type="number" class="form-control text-danger fw-bold" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" min="0" step="1" placeholder="Để trống nếu không giảm">
                </div>

                <div class="mb-3">
                    <label>Giá vốn (Cost price)</label>
                    <input type="number" class="form-control" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" min="0" step="1" placeholder="0">
                </div>

                <div class="mb-3">
                    <label>Số lượng tồn kho <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" min="0" required placeholder="0">
                </div>
            </div>

            {{-- Hộp Phân loại & Tags --}}
            <div class="card">
                <h5><i class="fa-solid fa-folder-tree text-primary me-2"></i>Danh mục & Hãng</h5>

                <div class="mb-3">
                    <label>Danh mục chính</label>
                    <select class="form-select" id="primary-category" name="primary_category_id">
                        <option value="">-- Chọn danh mục chính --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (string) old('primary_category_id', $product->primary_category_id) === (string) $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label>Danh mục phụ (Nhiều)</label>
                    <select class="form-select" id="extra-categories" name="category_ids[]" multiple>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ in_array($category->id, old('category_ids', $product->category_ids ?? [])) ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label>Hãng / Thương hiệu</label>
                    <select class="form-select" id="brand-select" name="brand_id">
                        <option value="">-- Chọn hãng --</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ (string) old('brand_id', $product->brand_id) === (string) $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label>Thẻ Tags</label>
                    <select name="tag_ids[]" id="tagSelect" class="form-select" multiple>
                        @foreach($tags as $tag)
                            <option value="{{ $tag->id }}" selected>
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted d-block mt-1">Tìm kiếm hoặc gõ tag từ hệ thống</small>

                    <div class="mt-2">
                        <label class="small text-muted">Hoặc thêm tags mới:</label>
                        <input type="text" name="tag_names" class="form-control" placeholder="Tag 1, Tag 2, Tag 3" value="{{ $tagNamesInput }}">
                        <small class="text-muted">Phân cách bằng dấu phẩy</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Templates cho Repeater --}}
<template id="image-template">
    <div class="repeater-item">
        <div class="repeater-header">
            <strong>Ảnh mới</strong>
            <button type="button" class="btn-link" data-remove data-item=".repeater-item">Xóa</button>
        </div>
        <input type="hidden" name="images[__INDEX__][id]">

        <div class="grid-2">
            <div>
                <label>Tiêu đề (Title)</label>
                <input type="text" class="form-control" name="images[__INDEX__][title]">
            </div>
            <div>
                <label>Thẻ Alt</label>
                <input type="text" class="form-control" name="images[__INDEX__][alt]">
            </div>
            <div>
                <label>Ghi chú</label>
                <input type="text" class="form-control" name="images[__INDEX__][notes]">
            </div>
            <div>
                <label>Thứ tự</label>
                <input type="number" class="form-control" name="images[__INDEX__][order]" value="__INDEX__">
            </div>
        </div>

        <div class="mt-3">
            <label>File ảnh</label>
            <input type="hidden" id="image-path-__INDEX__" name="images[__INDEX__][existing_path]">
            <div class="image-preview" id="image-preview-__INDEX__"></div>
            <div class="product-image-picker-actions">
                <button type="button" class="btn btn-outline-primary btn-sm" data-product-media-open>
                    <i class="fa-solid fa-photo-film me-1"></i> Mở thư viện ảnh
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" data-product-media-clear>
                    Bỏ chọn
                </button>
            </div>
            <div class="product-image-picker-status is-empty" data-product-media-status>
                Chưa chọn ảnh từ thư viện.
            </div>
        </div>

        <div class="mt-2">
            <label class="form-check-label">
                <input type="checkbox" class="form-check-input me-1" name="images[__INDEX__][is_primary]" value="1">
                Đặt làm ảnh chính
            </label>
        </div>
    </div>
</template>

<template id="variant-template">
    <div class="repeater-item">
        <div class="repeater-header">
            <strong>Biến thể mới</strong>
            <button type="button" class="btn-link" data-remove data-item=".repeater-item">Xóa</button>
        </div>
        <input type="hidden" name="variants[__INDEX__][id]">

        <div class="grid-2">
            <div>
                <label>Tên biến thể <span class="text-danger">*</span> (VD: 1m có chậu)</label>
                <input type="text" class="form-control" name="variants[__INDEX__][name]" required>
            </div>
            <div>
                <label>Mã SKU biến thể</label>
                <input type="text" class="form-control" name="variants[__INDEX__][sku]">
            </div>
            <div>
                <label>Giá bán <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="variants[__INDEX__][price]" required min="0" value="0">
            </div>
            <div>
                <label>Giá khuyến mãi</label>
                <input type="number" class="form-control" name="variants[__INDEX__][sale_price]" min="0">
            </div>
            <div>
                <label>Tồn kho</label>
                <input type="number" class="form-control" name="variants[__INDEX__][stock_quantity]" min="0" value="0">
            </div>
            <div>
                <label>Trạng thái</label>
                <select class="form-control" name="variants[__INDEX__][is_active]">
                    <option value="1" selected>Kích hoạt</option>
                    <option value="0">Tắt</option>
                </select>
            </div>
        </div>

        <div class="mt-2">
            <label>Ghi chú</label>
            <input type="text" class="form-control" name="variants[__INDEX__][note]" placeholder="Thông tin bổ sung...">
        </div>
    </div>
</template>

@endsection

@push('scripts')
@include('admins.partials.media-library-modal')
<script src="{{ asset('admins/js/media-library.js') }}?v={{ env('APP_VERSION', '1.0') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    window.mediaManagerConfig = {
        csrfToken: @json(csrf_token()),
        fallbackImage: @json(asset('clients/assets/no-image.webp')),
        limits: {
            upload: {
                maxFilesPerRequest: 20,
                maxBatchBytes: '16M',
                appMaxSingleFileKb: 5120,
                batchSafetyRatio: 0.9,
            },
            delete: { maxItemsPerRequest: 200 }
        },
        routes: {
            search: @json(route('admin.media.search')),
            upload: @json(route('admin.media.upload')),
            updateBase: @json(url('/admin/media/update')),
            assign: @json(route('admin.media.assign')),
            bulkDelete: @json(route('admin.media.bulk-delete')),
            targets: @json(route('admin.media.targets')),
        },
        initialState: {
            items: [],
            meta: @json($productMediaInitialPagination),
            stats: @json($productMediaInitialStats),
        },
    };

    window.productMediaPickerConfig = {
        defaultFolder: 'clothes',
        modalTitleBase: 'Chọn ảnh cho sản phẩm',
    };
</script>
<script src="{{ asset('admins/js/media-manager.js') }}?v={{ env('APP_VERSION', '1.0') }}"></script>
<script src="{{ asset('admins/js/product-media-picker.js') }}?v={{ env('APP_VERSION', '1.0') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // TomSelect
    if (document.getElementById('primary-category')) {
        new TomSelect('#primary-category', { create: false, allowEmptyOption: true });
    }
    if (document.getElementById('brand-select')) {
        new TomSelect('#brand-select', { create: false, allowEmptyOption: true });
    }
    if (document.getElementById('extra-categories')) {
        new TomSelect('#extra-categories', { plugins: ['remove_button'], persist: false });
    }
    if (document.getElementById('tagSelect')) {
        new TomSelect('#tagSelect', {
            plugins: ['remove_button'],
            persist: false,
            create: false,
            load: function(query, callback) {
                if (!query.length) return callback();
                fetch('{{ route("admin.products.search-tags") }}?keyword=' + encodeURIComponent(query))
                    .then(r => r.json())
                    .then(data => callback(data.data || []))
                    .catch(() => callback());
            }
        });
    }

    // Tự sinh slug từ tên
    const nameInput = document.getElementById('product-name');
    const slugInput = document.getElementById('product-slug');
    if (nameInput && slugInput && !slugInput.value) {
        nameInput.addEventListener('input', function () {
            slugInput.value = nameInput.value
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[đĐ]/g, 'd')
                .replace(/([^0-9a-z-\s])/g, '')
                .replace(/(\s+)/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-+|-+$/g, '');
        });
    }

    // Repeater Add / Remove
    const counters = {
        '#image-list': document.querySelectorAll('#image-list .repeater-item').length,
        '#variant-list': document.querySelectorAll('#variant-list .repeater-item').length,
    };

    document.querySelectorAll('[data-add]').forEach(btn => {
        btn.addEventListener('click', function () {
            const targetSel = this.dataset.add;
            const templateSel = this.dataset.template;
            const container = document.querySelector(targetSel);
            const template = document.querySelector(templateSel);

            if (!container || !template) return;

            const idx = counters[targetSel]++;
            const html = template.innerHTML.replace(/__INDEX__/g, idx);
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html.trim();
            container.appendChild(tempDiv.firstElementChild);
        });
    });

    document.addEventListener('click', function (e) {
        if (e.target.matches('[data-remove]')) {
            const item = e.target.closest(e.target.dataset.item || '.repeater-item');
            if (item) item.remove();
        }
    });

    // Release Editing Lock on exit
    @if($isEdit)
        window.addEventListener('beforeunload', function () {
            navigator.sendBeacon('{{ route("admin.products.release-lock", $product) }}', new FormData());
        });
    @endif
});
</script>
@endpush
