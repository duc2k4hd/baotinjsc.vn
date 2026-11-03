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

    // Dữ liệu ảnh đại diện (ảnh đầu tiên)
    $primaryImage = null;
    if (old('primary_image')) {
        $primaryImage = old('primary_image');
    } elseif ($product->exists && $product->images && $product->images->isNotEmpty()) {
        $firstImg = $product->images->first();
        $primaryImage = [
            'id' => $firstImg->id,
            'existing_path' => $firstImg->path ?? $firstImg->url,
            'url' => $firstImg->url ?? '',
            'title' => $firstImg->name ?? $firstImg->title ?? '',
            'alt' => $firstImg->alt ?? $firstImg->name ?? '',
        ];
    }

    // Dữ liệu thư viện ảnh phụ (từ ảnh thứ 2 trở đi)
    $existingImages = [];
    if (old('images')) {
        $existingImages = old('images');
    } elseif ($product->exists && $product->images && $product->images->count() > 1) {
        $existingImages = $product->images->slice(1)->values()->map(function ($img, $idx) {
            return [
                'id' => $img->id,
                'title' => $img->name ?? $img->title ?? '',
                'notes' => $img->context ?? '',
                'alt' => $img->alt ?? $img->name ?? '',
                'order' => $idx + 1,
                'existing_path' => $img->path ?? $img->url,
                'is_primary' => 0,
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" integrity="sha512-cyzxRvewl+FOKTtpBzYjW6x6IAYDX23wfwwzs2UmQxF9sj4r2CKvV3QUF1Ssub24DWqy8/TUvScuLFZYnxdrJA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush

@push('styles')
<style>
    /* Cropper 1:1 Modal Styling */
    .cropper-modal-wrapper {
        width: 100%;
        height: 440px;
        max-height: 55vh;
        background-color: #0f172a;
        border-radius: 6px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .cropper-modal-wrapper img {
        max-width: 100%;
        max-height: 100%;
        display: block;
    }
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
        margin-top: 5px;
    }

    .image-preview img {
        max-width: 140px;
        max-height: 100px;
        object-fit: contain;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        background: #fff;
    }

    .primary-image-preview {
        min-height: 200px;
        max-height: 280px;
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        cursor: pointer;
        position: relative;
        transition: all 0.2s ease-in-out;
        user-select: none;
    }

    .primary-image-preview:hover {
        border-color: #3b82f6;
        background-color: #f1f5f9;
    }

    .primary-image-preview.drag-over {
        border-color: #2563eb !important;
        background-color: #eff6ff !important;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15) !important;
        transform: scale(1.01);
    }

    .gallery-dropzone {
        border: 2px dashed #cbd5e1;
        border-radius: 8px;
        background: #f8fafc;
        cursor: pointer;
        min-height: 90px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease-in-out;
        user-select: none;
    }

    .gallery-dropzone:hover {
        border-color: #3b82f6;
        background-color: #f1f5f9;
    }

    .gallery-dropzone.drag-over {
        border-color: #2563eb !important;
        background-color: #eff6ff !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
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

    /* CKEditor 5 Styles: Mô tả chi tiết min-height 1000px, cuộn nếu nhiều content */
    .product-detail-editor-wrap .ck-editor__main > .ck-editor__editable,
    .product-detail-editor-wrap .ck-editor__editable_inline:not(.ck-editor__nested-editable) {
        min-height: 1000px !important;
        max-height: 1000px !important;
        height: 1000px !important;
        overflow-y: auto !important;
        padding: 18px 24px !important;
    }

    /* CKEditor 5 Styles: Mô tả ngắn gọn gàng, cuộn nếu nhiều content */
    .product-short-editor-wrap .ck-editor__main > .ck-editor__editable,
    .product-short-editor-wrap .ck-editor__editable_inline:not(.ck-editor__nested-editable) {
        min-height: 220px !important;
        max-height: 350px !important;
        height: auto !important;
        overflow-y: auto !important;
    }

    /* figcaption của ảnh luôn fit-content, không bị ăn min-height của editor chính và hỗ trợ click sửa chữ */
    .ck-content .image > figcaption,
    .ck-editor__editable figcaption,
    figcaption.ck-editor__nested-editable,
    figcaption.ck-editor__editable {
        min-height: 24px !important;
        height: auto !important;
        max-height: none !important;
        width: fit-content !important;
        max-width: 100% !important;
        margin: 6px auto 0 !important;
        padding: 4px 10px !important;
        display: block !important;
        caption-side: bottom !important;
        font-size: 13px !important;
        color: #475569 !important;
        text-align: center !important;
        cursor: text !important;
        pointer-events: auto !important;
        user-select: text !important;
        -webkit-user-select: text !important;
        outline: none !important;
        border-radius: 4px !important;
        transition: background-color 0.15s ease;
    }

    /* Hover và Focus trên figcaption để người dùng biết có thể bấm vào sửa */
    figcaption.ck-editor__nested-editable:hover,
    figcaption.ck-editor__nested-editable:focus,
    .ck-content .image > figcaption:hover,
    .ck-content .image > figcaption:focus {
        background-color: #f1f5f9 !important;
        box-shadow: 0 0 0 1px #cbd5e1 !important;
    }

    /* Đảm bảo nếu có thẻ figure bao ngoài cũng không làm vỡ giao diện */
    .ck-content figure:has(> figure.image) {
        display: contents !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
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

            {{-- Thư viện ảnh phụ (Gallery) --}}
            <div class="card">
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0"><i class="fa-solid fa-images text-primary me-2"></i>Thư viện ảnh phụ</h5>
                        <small class="text-muted">Các ảnh phụ bổ sung (hiển thị từ ảnh thứ 2 trở đi trong trang chi tiết sản phẩm).</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-primary fw-bold" id="btn-add-multiple-gallery">
                            <i class="fa-solid fa-photo-film me-1"></i> + Chọn nhiều từ thư viện
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary fw-bold" data-add="#image-list" data-template="#image-template">
                            + Thêm 1 ảnh phụ
                        </button>
                    </div>
                </div>

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
                                <strong>Ảnh phụ #{{ $index + 1 }}</strong>
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
                                    <input type="number" class="form-control" name="images[{{ $index }}][order]" value="{{ $image['order'] ?? ($index + 1) }}">
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="form-label fw-semibold">File ảnh</label>
                                <input type="hidden" class="gallery-image-path" name="images[{{ $index }}][existing_path]" value="{{ $storedValue }}">
                                <input type="file" class="d-none gallery-image-file" name="images[{{ $index }}][file]" accept="image/*">
                                
                                <div class="gallery-dropzone p-2 mb-2 text-center" title="Kéo thả ảnh vào đây hoặc bấm để chọn">
                                    <div class="gallery-preview image-preview">
                                        @if($storedUrl)
                                            <img src="{{ $storedUrl }}" alt="">
                                        @else
                                            <div class="text-muted py-2">
                                                <i class="fa-solid fa-cloud-arrow-up fa-2x mb-1 d-block text-secondary"></i>
                                                <span class="small">Kéo thả ảnh vào đây hoặc bấm chọn</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="product-image-picker-actions">
                                    <button type="button" class="btn btn-outline-primary btn-sm btn-pick-gallery-media">
                                        <i class="fa-solid fa-photo-film me-1"></i> Chọn từ thư viện
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm btn-upload-gallery-file">
                                        <i class="fa-solid fa-upload me-1"></i> Tải từ máy tính
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm btn-clear-gallery-item {{ $storedValue ? '' : 'd-none' }}">
                                        Bỏ chọn
                                    </button>
                                </div>
                                <div class="product-image-picker-status {{ $storedValue ? '' : 'is-empty' }}">
                                    {{ $storedValue ? 'Đang chọn: ' . $storedValue : 'Chưa chọn ảnh.' }}
                                </div>
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

            {{-- Hộp Ảnh đại diện sản phẩm --}}
            <div class="card" id="primary-image-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0"><i class="fa-solid fa-image text-primary me-2"></i>Ảnh đại diện <span class="text-danger">*</span></h5>
                </div>
                <p class="text-muted small mb-3">
                    Ảnh đại diện chính hiển thị trên danh sách sản phẩm và đầu trang chi tiết.
                </p>

                <div class="primary-image-wrapper text-center">
                    @php
                        $primaryImgValue = old('primary_image.existing_path', $primaryImage['existing_path'] ?? null);
                        $primaryImgId = old('primary_image.id', $primaryImage['id'] ?? null);
                        $primaryImgUrl = $primaryImgValue
                            ? (str_starts_with($primaryImgValue, 'http') ? $primaryImgValue : asset($primaryImgValue))
                            : null;
                    @endphp
                    <input type="hidden" id="primary-image-id" name="primary_image[id]" value="{{ $primaryImgId }}">
                    <input type="hidden" id="primary-image-path" name="primary_image[existing_path]" value="{{ $primaryImgValue }}">
                    <input type="file" id="primary-image-file" name="primary_image[file]" accept="image/*" class="d-none">

                    <div class="primary-image-preview mb-3" id="primary-image-preview" title="Bấm để chọn ảnh từ thư viện hoặc kéo thả file từ máy tính">
                        @if($primaryImgUrl)
                            <img src="{{ $primaryImgUrl }}" alt="Ảnh đại diện" style="max-width: 100%; max-height: 260px; object-fit: contain;">
                        @else
                            <div class="drop-zone-placeholder text-muted p-4">
                                <i class="fa-solid fa-cloud-arrow-up fa-3x mb-2 d-block text-primary"></i>
                                <span class="fw-bold d-block text-dark">Kéo & thả ảnh vào đây</span>
                                <small class="text-muted d-block mt-1">hoặc bấm vào đây để chọn ảnh</small>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <button type="button" class="btn btn-primary btn-sm fw-bold" id="btn-pick-primary-media">
                            <i class="fa-solid fa-photo-film me-1"></i> Chọn từ thư viện
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm fw-bold" id="btn-upload-primary-file">
                            <i class="fa-solid fa-upload me-1"></i> Tải từ máy tính
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm {{ ($primaryImgValue || $primaryImgId) ? '' : 'd-none' }}" id="btn-clear-primary-image">
                            <i class="fa-solid fa-trash me-1"></i> Xóa
                        </button>
                    </div>
                    <div id="primary-image-source-info" class="small text-muted mt-2 {{ $primaryImgValue ? '' : 'd-none' }}" style="font-size: 11.5px; word-break: break-all;">
                        Đang chọn: <span id="primary-image-source-text" class="fw-semibold text-primary">{{ $primaryImgValue }}</span>
                    </div>
                    <small class="text-muted d-block mt-2" style="font-size: 11px;">
                        <i class="fa-solid fa-circle-info me-1"></i>Bắt buộc có ảnh đại diện. Nếu để trống, hệ thống sẽ tự lấy ảnh đầu tiên từ Thư viện ảnh sản phẩm.
                    </small>
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
            <label class="form-label fw-semibold">File ảnh</label>
            <input type="hidden" class="gallery-image-path" name="images[__INDEX__][existing_path]">
            <input type="file" class="d-none gallery-image-file" name="images[__INDEX__][file]" accept="image/*">
            
            <div class="gallery-dropzone p-2 mb-2 text-center" title="Kéo thả ảnh vào đây hoặc bấm để chọn">
                <div class="gallery-preview image-preview">
                    <div class="text-muted py-2">
                        <i class="fa-solid fa-cloud-arrow-up fa-2x mb-1 d-block text-secondary"></i>
                        <span class="small">Kéo thả ảnh vào đây hoặc bấm chọn</span>
                    </div>
                </div>
            </div>

            <div class="product-image-picker-actions">
                <button type="button" class="btn btn-outline-primary btn-sm btn-pick-gallery-media">
                    <i class="fa-solid fa-photo-film me-1"></i> Chọn từ thư viện
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm btn-upload-gallery-file">
                    <i class="fa-solid fa-upload me-1"></i> Tải từ máy tính
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm btn-clear-gallery-item d-none">
                    Bỏ chọn
                </button>
            </div>
            <div class="product-image-picker-status is-empty">
                Chưa chọn ảnh.
            </div>
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

{{-- Modal Bắt buộc cắt ảnh vuông 1:1 khi upload --}}
<div class="modal fade" id="cropImageModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="cropImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-2 px-3">
                <h6 class="modal-title d-flex align-items-center gap-2 mb-0" id="cropImageModalLabel">
                    <i class="fa-solid fa-crop-simple text-warning"></i>
                    <span>Cắt ảnh sản phẩm (Bắt buộc tỷ lệ vuông 1:1)</span>
                </h6>
                <span class="badge bg-warning text-dark fw-bold ms-auto">Chuẩn tỷ lệ 1:1</span>
            </div>
            <div class="modal-body p-3">
                <div class="alert alert-warning py-2 px-3 small mb-2 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation fs-5 text-warning"></i>
                    <div>
                        Ảnh bạn vừa tải lên có kích thước <strong id="cropDimText">...</strong> (chưa phải hình vuông).
                        Hệ thống yêu cầu <strong>căn chỉnh và cắt thành hình vuông 1:1</strong> để ảnh hiển thị chuẩn đẹp trên website.
                    </div>
                </div>

                <div class="cropper-modal-wrapper">
                    <img id="cropperTargetImg" src="" alt="Cắt ảnh sản phẩm">
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" id="btnCropperZoomIn" title="Phóng to">
                            <i class="fa-solid fa-magnifying-glass-plus"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnCropperZoomOut" title="Thu nhỏ">
                            <i class="fa-solid fa-magnifying-glass-minus"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnCropperRotateLeft" title="Xoay trái 90°">
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnCropperRotateRight" title="Xoay phải 90°">
                            <i class="fa-solid fa-rotate-right"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnCropperReset" title="Đặt lại góc cắt">
                            <i class="fa-solid fa-arrows-rotate"></i> Đặt lại
                        </button>
                    </div>
                    <span class="text-muted small">
                        <i class="fa-solid fa-hand-pointer me-1"></i> Kéo các góc khung lưới để căn chỉnh vùng ảnh đẹp nhất.
                    </span>
                </div>
            </div>
            <div class="modal-footer py-2 px-3 bg-light d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3" id="btnCropperCancel">
                    <i class="fa-solid fa-xmark me-1"></i> Hủy tải lên
                </button>
                <button type="button" class="btn btn-primary btn-sm px-4 fw-bold" id="btnCropperApply">
                    <i class="fa-solid fa-check me-1"></i> Cắt ảnh & Sử dụng
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js" integrity="sha512-6lplKLe1tT/Qgqt1ezde4M45+Z51x5eS5e57147b19895c102a9e96f13bcf04051b72a39281e7d0251" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

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

    // Repeater Add / Remove Counters
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

    // ========================================================
    // 1. Quản lý Ảnh đại diện (Primary Image)
    // ========================================================
    const primaryPreview = document.getElementById('primary-image-preview');
    const primaryFileInput = document.getElementById('primary-image-file');
    const primaryPathInput = document.getElementById('primary-image-path');
    const primaryIdInput = document.getElementById('primary-image-id');
    const btnPickPrimaryMedia = document.getElementById('btn-pick-primary-media');
    const btnUploadPrimaryFile = document.getElementById('btn-upload-primary-file');
    const btnClearPrimary = document.getElementById('btn-clear-primary-image');
    const primarySourceInfo = document.getElementById('primary-image-source-info');
    const primarySourceText = document.getElementById('primary-image-source-text');

    function renderPrimaryPreview(imgUrl, sourceLabel) {
        if (!primaryPreview) return;
        primaryPreview.innerHTML = `<img src="${imgUrl}" alt="Ảnh đại diện" style="max-width: 100%; max-height: 260px; object-fit: contain;">`;
        if (btnClearPrimary) btnClearPrimary.classList.remove('d-none');
        if (sourceLabel && primarySourceInfo && primarySourceText) {
            primarySourceInfo.classList.remove('d-none');
            primarySourceText.textContent = sourceLabel;
        }
    }

    function clearPrimaryImage() {
        if (primaryFileInput) primaryFileInput.value = '';
        if (primaryPathInput) primaryPathInput.value = '';
        if (primaryIdInput) primaryIdInput.value = '';
        if (primaryPreview) {
            primaryPreview.innerHTML = `
                <div class="drop-zone-placeholder text-muted p-4">
                    <i class="fa-solid fa-cloud-arrow-up fa-3x mb-2 d-block text-primary"></i>
                    <span class="fw-bold d-block text-dark">Kéo & thả ảnh vào đây</span>
                    <small class="text-muted d-block mt-1">hoặc bấm vào đây để chọn ảnh</small>
                </div>`;
        }
        if (btnClearPrimary) btnClearPrimary.classList.add('d-none');
        if (primarySourceInfo) primarySourceInfo.classList.add('d-none');
        if (primarySourceText) primarySourceText.textContent = '';
    }

    // ========================================================
    // CROPPER 1:1 CONTROLLER (Bắt buộc tỷ lệ vuông 1:1 khi upload ảnh)
    // ========================================================
    let currentCropper = null;
    let cropperResolve = null;
    let cropperReject = null;
    const cropModalEl = document.getElementById('cropImageModal');
    const cropperTargetImg = document.getElementById('cropperTargetImg');
    const cropDimText = document.getElementById('cropDimText');
    let cropModalInstance = null;

    if (cropModalEl && typeof bootstrap !== 'undefined') {
        cropModalInstance = new bootstrap.Modal(cropModalEl, {
            backdrop: 'static',
            keyboard: false
        });
    }

    function checkAndCropImage(file) {
        return new Promise((resolve, reject) => {
            if (!file || !file.type || !file.type.startsWith('image/')) {
                return resolve(file);
            }

            const img = new Image();
            const objectUrl = URL.createObjectURL(file);

            img.onload = function () {
                const w = img.naturalWidth;
                const h = img.naturalHeight;
                const diff = Math.abs(w - h);

                // Cho phép sai lệch tối đa 2px
                if (diff <= 2) {
                    URL.revokeObjectURL(objectUrl);
                    return resolve(file);
                }

                // Không đúng hình vuông -> Buộc mở Cropper popup
                openCropperPopup(objectUrl, file, w, h)
                    .then(croppedFile => {
                        URL.revokeObjectURL(objectUrl);
                        resolve(croppedFile);
                    })
                    .catch(err => {
                        URL.revokeObjectURL(objectUrl);
                        reject(err);
                    });
            };

            img.onerror = function () {
                URL.revokeObjectURL(objectUrl);
                resolve(file);
            };

            img.src = objectUrl;
        });
    }

    function openCropperPopup(imageSrc, file, width, height) {
        return new Promise((resolve, reject) => {
            cropperResolve = resolve;
            cropperReject = reject;

            if (cropDimText) {
                cropDimText.textContent = `${width} × ${height}px`;
            }

            if (cropperTargetImg) {
                cropperTargetImg.src = imageSrc;
                cropperTargetImg.alt = file.name || 'product_image';
            }

            if (cropModalInstance) {
                cropModalInstance.show();
            }

            const onShown = function () {
                cropModalEl.removeEventListener('shown.bs.modal', onShown);
                if (currentCropper) {
                    currentCropper.destroy();
                }
                if (typeof Cropper !== 'undefined' && cropperTargetImg) {
                    currentCropper = new Cropper(cropperTargetImg, {
                        aspectRatio: 1, // BẮT BUỘC TỶ LỆ VUÔNG 1:1
                        viewMode: 1,
                        autoCropArea: 0.95,
                        responsive: true,
                        restore: false,
                        guides: true,
                        center: true,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                    });
                }
            };
            cropModalEl.addEventListener('shown.bs.modal', onShown);
        });
    }

    document.getElementById('btnCropperZoomIn')?.addEventListener('click', () => currentCropper?.zoom(0.1));
    document.getElementById('btnCropperZoomOut')?.addEventListener('click', () => currentCropper?.zoom(-0.1));
    document.getElementById('btnCropperRotateLeft')?.addEventListener('click', () => currentCropper?.rotate(-90));
    document.getElementById('btnCropperRotateRight')?.addEventListener('click', () => currentCropper?.rotate(90));
    document.getElementById('btnCropperReset')?.addEventListener('click', () => currentCropper?.reset());

    document.getElementById('btnCropperApply')?.addEventListener('click', () => {
        if (!currentCropper) return;
        const canvas = currentCropper.getCroppedCanvas({
            maxWidth: 2000,
            maxHeight: 2000,
            fillColor: '#ffffff',
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high'
        });

        if (!canvas) {
            alert('Không thể cắt ảnh. Vui lòng thử lại.');
            return;
        }

        canvas.toBlob((blob) => {
            if (!blob) {
                alert('Lỗi tạo dữ liệu sau khi cắt ảnh.');
                return;
            }
            const origName = cropperTargetImg?.alt || 'product_image.jpg';
            const fileName = origName.replace(/\.[^.]+$/, '') + '.jpg';
            const croppedFile = new File([blob], fileName, { type: 'image/jpeg', lastModified: Date.now() });

            if (cropModalInstance) cropModalInstance.hide();
            if (currentCropper) {
                currentCropper.destroy();
                currentCropper = null;
            }
            if (typeof cropperResolve === 'function') {
                cropperResolve(croppedFile);
                cropperResolve = null;
            }
        }, 'image/jpeg', 0.92);
    });

    document.getElementById('btnCropperCancel')?.addEventListener('click', () => {
        if (cropModalInstance) cropModalInstance.hide();
        if (currentCropper) {
            currentCropper.destroy();
            currentCropper = null;
        }
        if (typeof cropperReject === 'function') {
            cropperReject(new Error('Hủy cắt ảnh'));
            cropperReject = null;
        }
    });

    function handlePrimaryFile(file) {
        if (!file) return;
        if (!file.type || !file.type.startsWith('image/')) {
            alert('Vui lòng chọn hoặc kéo thả file định dạng hình ảnh (JPG, PNG, WEBP, GIF...).');
            return;
        }

        checkAndCropImage(file).then(validFile => {
            try {
                const dt = new DataTransfer();
                dt.items.add(validFile);
                primaryFileInput.files = dt.files;
            } catch (err) {
                console.warn('DataTransfer assignment not supported:', err);
            }

            if (primaryPathInput) primaryPathInput.value = '';
            if (primaryIdInput) primaryIdInput.value = '';

            const reader = new FileReader();
            reader.onload = function (e) {
                renderPrimaryPreview(e.target.result, `File từ máy: ${validFile.name} (${(validFile.size / 1024).toFixed(1)} KB) - Chuẩn 1:1`);
            };
            reader.readAsDataURL(validFile);
        }).catch(err => {
            if (primaryFileInput) primaryFileInput.value = '';
            console.log('Hủy cắt ảnh đại diện.');
        });
    }

    function openPrimaryMediaPicker() {
        if (typeof window.openMediaPicker === 'function') {
            window.openMediaPicker({
                mode: 'single',
                scope: 'client',
                folder: 'clothes',
                onSelect: (file) => {
                    if (file) {
                        if (primaryFileInput) primaryFileInput.value = '';
                        const chosenPath = file.relative_path || file.path || file.filename || file.url;
                        if (primaryPathInput) primaryPathInput.value = chosenPath;
                        if (primaryIdInput) primaryIdInput.value = file.id || '';
                        const previewUrl = file.url || (chosenPath.startsWith('http') ? chosenPath : ('/' + chosenPath.replace(/^\/+/, '')));
                        renderPrimaryPreview(previewUrl, `Thư viện: ${file.filename || chosenPath}`);
                    }
                }
            });
        } else {
            alert('Popup thư viện ảnh chưa sẵn sàng. Vui lòng tải lại trang.');
        }
    }

    if (btnPickPrimaryMedia) {
        btnPickPrimaryMedia.addEventListener('click', openPrimaryMediaPicker);
    }
    if (btnUploadPrimaryFile) {
        btnUploadPrimaryFile.addEventListener('click', () => {
            if (primaryFileInput) primaryFileInput.click();
        });
    }
    if (primaryFileInput) {
        primaryFileInput.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                handlePrimaryFile(this.files[0]);
            }
        });
    }
    if (btnClearPrimary) {
        btnClearPrimary.addEventListener('click', clearPrimaryImage);
    }

    if (primaryPreview) {
        primaryPreview.addEventListener('click', function (e) {
            if (e.target.closest('button')) return;
            const hasImg = (primaryPathInput && primaryPathInput.value) || (primaryFileInput && primaryFileInput.files && primaryFileInput.files.length);
            if (!hasImg) {
                openPrimaryMediaPicker();
            }
        });

        ['dragenter', 'dragover'].forEach(evt => {
            primaryPreview.addEventListener(evt, (e) => {
                e.preventDefault();
                e.stopPropagation();
                primaryPreview.classList.add('drag-over');
            });
        });

        ['dragleave', 'dragend'].forEach(evt => {
            primaryPreview.addEventListener(evt, (e) => {
                e.preventDefault();
                e.stopPropagation();
                primaryPreview.classList.remove('drag-over');
            });
        });

        primaryPreview.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            primaryPreview.classList.remove('drag-over');
            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                handlePrimaryFile(e.dataTransfer.files[0]);
            }
        });
    }

    // ========================================================
    // 2. Quản lý Thư viện ảnh phụ (Gallery Repeater)
    // ========================================================
    const imageListContainer = document.getElementById('image-list');
    const imageTemplate = document.getElementById('image-template');
    const btnAddMultipleGallery = document.getElementById('btn-add-multiple-gallery');

    function createGalleryItemWithData(fileData) {
        if (!imageListContainer || !imageTemplate) return;
        const idx = counters['#image-list']++;
        const html = imageTemplate.innerHTML.replace(/__INDEX__/g, idx);
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html.trim();
        const itemEl = tempDiv.firstElementChild;
        imageListContainer.appendChild(itemEl);

        if (fileData) {
            const pathInput = itemEl.querySelector('.gallery-image-path');
            const previewWrap = itemEl.querySelector('.gallery-preview');
            const statusEl = itemEl.querySelector('.product-image-picker-status');
            const btnClear = itemEl.querySelector('.btn-clear-gallery-item');
            const titleInput = itemEl.querySelector('input[name*="[title]"]');
            const altInput = itemEl.querySelector('input[name*="[alt]"]');

            const chosenPath = fileData.relative_path || fileData.path || fileData.filename || fileData.url;
            const previewUrl = fileData.url || (chosenPath.startsWith('http') ? chosenPath : ('/' + chosenPath.replace(/^\/+/, '')));

            if (pathInput) pathInput.value = chosenPath;
            if (previewWrap) {
                previewWrap.innerHTML = `<img src="${previewUrl}" alt="" style="max-height: 100px; max-width: 180px; object-fit: contain; border-radius: 4px;">`;
            }
            if (statusEl) {
                statusEl.classList.remove('is-empty');
                statusEl.textContent = 'Đang chọn: ' + (fileData.filename || chosenPath);
            }
            if (btnClear) btnClear.classList.remove('d-none');
            if (titleInput && !titleInput.value && fileData.title) titleInput.value = fileData.title;
            if (altInput && !altInput.value && fileData.alt) altInput.value = fileData.alt;
        }
        return itemEl;
    }

    if (btnAddMultipleGallery) {
        btnAddMultipleGallery.addEventListener('click', function () {
            if (typeof window.openMediaPicker === 'function') {
                window.openMediaPicker({
                    mode: 'multiple',
                    scope: 'client',
                    folder: 'clothes',
                    onSelect: (files) => {
                        const selectedList = Array.isArray(files) ? files : [files];
                        selectedList.forEach(file => {
                            if (file) createGalleryItemWithData(file);
                        });
                    }
                });
            } else {
                alert('Popup thư viện ảnh chưa sẵn sàng. Vui lòng tải lại trang.');
            }
        });
    }

    function updateGalleryRowWithFile(row, file) {
        if (!row || !file || !file.type.startsWith('image/')) return;
        const fileInput = row.querySelector('.gallery-image-file');
        const pathInput = row.querySelector('.gallery-image-path');
        const previewWrap = row.querySelector('.gallery-preview');
        const statusEl = row.querySelector('.product-image-picker-status');
        const btnClear = row.querySelector('.btn-clear-gallery-item');

        checkAndCropImage(file).then(validFile => {
            try {
                const dt = new DataTransfer();
                dt.items.add(validFile);
                if (fileInput) fileInput.files = dt.files;
            } catch (e) {
                console.warn(e);
            }

            if (pathInput) pathInput.value = '';

            const reader = new FileReader();
            reader.onload = function (e) {
                if (previewWrap) {
                    previewWrap.innerHTML = `<img src="${e.target.result}" alt="" style="max-height: 100px; max-width: 180px; object-fit: contain; border-radius: 4px;">`;
                }
                if (statusEl) {
                    statusEl.classList.remove('is-empty');
                    statusEl.textContent = `File từ máy: ${validFile.name} (${(validFile.size / 1024).toFixed(1)} KB) - Chuẩn 1:1`;
                }
                if (btnClear) btnClear.classList.remove('d-none');
            };
            reader.readAsDataURL(validFile);
        }).catch(err => {
            if (fileInput) fileInput.value = '';
            console.log('Hủy cắt ảnh gallery.');
        });
    }

    function clearGalleryRow(row) {
        if (!row) return;
        const fileInput = row.querySelector('.gallery-image-file');
        const pathInput = row.querySelector('.gallery-image-path');
        const previewWrap = row.querySelector('.gallery-preview');
        const statusEl = row.querySelector('.product-image-picker-status');
        const btnClear = row.querySelector('.btn-clear-gallery-item');

        if (fileInput) fileInput.value = '';
        if (pathInput) pathInput.value = '';
        if (previewWrap) {
            previewWrap.innerHTML = `
                <div class="text-muted py-2">
                    <i class="fa-solid fa-cloud-arrow-up fa-2x mb-1 d-block text-secondary"></i>
                    <span class="small">Kéo thả ảnh vào đây hoặc bấm chọn</span>
                </div>`;
        }
        if (statusEl) {
            statusEl.classList.add('is-empty');
            statusEl.textContent = 'Chưa chọn ảnh.';
        }
        if (btnClear) btnClear.classList.add('d-none');
    }

    // Event delegation cho các hàng ảnh phụ
    document.addEventListener('click', function (e) {
        const btnPickMedia = e.target.closest('.btn-pick-gallery-media');
        if (btnPickMedia) {
            const row = btnPickMedia.closest('.repeater-item');
            if (row && typeof window.openMediaPicker === 'function') {
                window.openMediaPicker({
                    mode: 'single',
                    scope: 'client',
                    folder: 'clothes',
                    onSelect: (file) => {
                        if (file) {
                            const fileInput = row.querySelector('.gallery-image-file');
                            const pathInput = row.querySelector('.gallery-image-path');
                            const previewWrap = row.querySelector('.gallery-preview');
                            const statusEl = row.querySelector('.product-image-picker-status');
                            const btnClear = row.querySelector('.btn-clear-gallery-item');
                            const titleInput = row.querySelector('input[name*="[title]"]');
                            const altInput = row.querySelector('input[name*="[alt]"]');

                            if (fileInput) fileInput.value = '';
                            const chosenPath = file.relative_path || file.path || file.filename || file.url;
                            if (pathInput) pathInput.value = chosenPath;

                            const previewUrl = file.url || (chosenPath.startsWith('http') ? chosenPath : ('/' + chosenPath.replace(/^\/+/, '')));
                            if (previewWrap) {
                                previewWrap.innerHTML = `<img src="${previewUrl}" alt="" style="max-height: 100px; max-width: 180px; object-fit: contain; border-radius: 4px;">`;
                            }
                            if (statusEl) {
                                statusEl.classList.remove('is-empty');
                                statusEl.textContent = 'Đang chọn: ' + (file.filename || chosenPath);
                            }
                            if (btnClear) btnClear.classList.remove('d-none');
                            if (titleInput && !titleInput.value && file.title) titleInput.value = file.title;
                            if (altInput && !altInput.value && file.alt) altInput.value = file.alt;
                        }
                    }
                });
            }
            return;
        }

        const btnUploadFile = e.target.closest('.btn-upload-gallery-file');
        if (btnUploadFile) {
            const row = btnUploadFile.closest('.repeater-item');
            const fileInput = row?.querySelector('.gallery-image-file');
            if (fileInput) fileInput.click();
            return;
        }

        const dropzone = e.target.closest('.gallery-dropzone');
        if (dropzone && !e.target.closest('button')) {
            const row = dropzone.closest('.repeater-item');
            const pathInput = row?.querySelector('.gallery-image-path');
            const fileInput = row?.querySelector('.gallery-image-file');
            if (!pathInput?.value && (!fileInput?.files || !fileInput?.files.length)) {
                row?.querySelector('.btn-pick-gallery-media')?.click();
            }
            return;
        }

        const btnClear = e.target.closest('.btn-clear-gallery-item');
        if (btnClear) {
            clearGalleryRow(btnClear.closest('.repeater-item'));
            return;
        }
    });

    document.addEventListener('change', function (e) {
        if (e.target.matches('.gallery-image-file')) {
            const row = e.target.closest('.repeater-item');
            if (row && e.target.files && e.target.files.length > 0) {
                updateGalleryRowWithFile(row, e.target.files[0]);
            }
        }
    });

    document.addEventListener('dragover', function (e) {
        const dropzone = e.target.closest('.gallery-dropzone');
        if (dropzone) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.add('drag-over');
        }
    });

    document.addEventListener('dragleave', function (e) {
        const dropzone = e.target.closest('.gallery-dropzone');
        if (dropzone) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.remove('drag-over');
        }
    });

    document.addEventListener('drop', function (e) {
        const dropzone = e.target.closest('.gallery-dropzone');
        if (dropzone) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.remove('drag-over');
            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                const row = dropzone.closest('.repeater-item');
                if (row) {
                    updateGalleryRowWithFile(row, e.dataTransfer.files[0]);
                }
            }
        }
    });

    // ========================================================
    // 3. Form Submit Validation & Primary Image Fallback
    // ========================================================
    const productForm = document.getElementById('product-form');
    if (productForm) {
        productForm.addEventListener('submit', function (e) {
            const primaryPath = document.getElementById('primary-image-path')?.value?.trim();
            const primaryId = document.getElementById('primary-image-id')?.value?.trim();
            const primaryHasFile = document.getElementById('primary-image-file')?.files?.length > 0;

            if (!primaryPath && !primaryId && !primaryHasFile) {
                const galleryItems = document.querySelectorAll('#image-list .repeater-item');
                let foundPath = '';
                let foundId = '';
                let foundFile = null;
                let foundItem = null;

                for (let item of galleryItems) {
                    const pathInput = item.querySelector('.gallery-image-path');
                    const fileInput = item.querySelector('.gallery-image-file');
                    const idInput = item.querySelector('input[name*="[id]"]');

                    if (fileInput && fileInput.files && fileInput.files.length > 0) {
                        foundFile = fileInput.files[0];
                        foundItem = item;
                        break;
                    }
                    if (pathInput && pathInput.value && pathInput.value.trim()) {
                        foundPath = pathInput.value.trim();
                        foundId = idInput?.value || '';
                        foundItem = item;
                        break;
                    }
                }

                if (foundFile) {
                    try {
                        const dt = new DataTransfer();
                        dt.items.add(foundFile);
                        document.getElementById('primary-image-file').files = dt.files;
                    } catch (err) {
                        console.warn(err);
                    }
                    if (foundItem) foundItem.remove();
                } else if (foundPath || foundId) {
                    if (document.getElementById('primary-image-path')) {
                        document.getElementById('primary-image-path').value = foundPath;
                    }
                    if (document.getElementById('primary-image-id')) {
                        document.getElementById('primary-image-id').value = foundId;
                    }
                    if (foundItem) foundItem.remove();
                } else {
                    e.preventDefault();
                    alert('Bắt buộc phải chọn ảnh đại diện cho sản phẩm (hoặc thêm ít nhất một ảnh vào thư viện ảnh).');
                    const card = document.getElementById('primary-image-card');
                    if (card) {
                        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        card.style.border = '2px solid #ef4444';
                        setTimeout(() => card.style.border = '', 3000);
                    }
                    return false;
                }
            }
        });
    }

    // Release Editing Lock on exit
    @if($isEdit)
        window.addEventListener('beforeunload', function () {
            navigator.sendBeacon('{{ route("admin.products.release-lock", $product) }}', new FormData());
        });
    @endif
});
</script>
@endpush
