@php
    $isEdit = $post?->exists;
    $seoScore = $isEdit ? ($seoInsights ?? ['score' => 0, 'issues' => [], 'suggestions' => []]) : ['score' => 0, 'issues' => [], 'suggestions' => []];
    $mediaPicker = $mediaPicker ?? [
        'title' => 'Thư viện ảnh',
        'scope' => 'client',
        'folder' => 'posts',
        'per_page' => 100,
        'list_url' => route('admin.media.list'),
        'upload_url' => route('admin.media.upload'),
    ];
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tiêu đề *</label>
                    <input type="text" name="title" class="form-control form-control-lg" value="{{ old('title', $post->title ?? '') }}" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Slug</label>
                        <input type="text" name="slug" class="form-control" value="{{ old('slug', $post->slug ?? '') }}" placeholder="Tự tạo nếu để trống">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Danh mục</label>
                        <select name="category_id" class="form-select">
                            <option value="">-- Chọn danh mục --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $post->category_id ?? '') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Trạng thái</label>
                        <select name="status" class="form-select">
                            @foreach(['draft'=>'Bản nháp','pending'=>'Chờ duyệt','published'=>'Xuất bản','archived'=>'Lưu trữ'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $post->status ?? 'draft') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Thời gian xuất bản</label>
                        <input type="datetime-local" name="published_at" class="form-control"
                               value="{{ old('published_at', isset($post->published_at) ? $post->published_at->format('Y-m-d\TH:i') : '') }}">
                    </div>
                </div>
                <div class="mb-3 form-check form-switch">
                    <input type="hidden" name="is_featured" value="0">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeaturedSwitch"
                           @checked(old('is_featured', $post->is_featured ?? false))>
                    <label class="form-check-label" for="isFeaturedSwitch">Đặt làm bài viết nổi bật</label>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tóm tắt</label>
                    <textarea name="excerpt" class="form-control" rows="3">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tags</label>
                    
                    <!-- Dropdown để chọn tags có sẵn (autocomplete) -->
                    <div class="mb-2">
                        <label class="form-label small text-muted">Gõ để tìm và chọn tags (autocomplete):</label>
                        <select name="tag_ids[]" id="tagSelect" class="form-select" multiple>
                            @php
                                // Lấy tag IDs từ relationship nếu có post, hoặc từ old input
                                $selectedTagIds = old('tag_ids', []);
                                if (empty($selectedTagIds) && isset($post) && $post->exists) {
                                    // Ưu tiên lấy từ tag_ids JSON (đảm bảo đầy đủ)
                                    if (!empty($post->tag_ids) && is_array($post->tag_ids)) {
                                        $selectedTagIds = array_values(array_unique($post->tag_ids));
                                    } else {
                                        // Fallback: lấy từ relationship
                                        $selectedTagIds = $post->tags()->pluck('id')->toArray();
                                    }
                                }
                                // Đảm bảo selectedTagIds là array và loại bỏ duplicate
                                $selectedTagIds = is_array($selectedTagIds) ? array_values(array_unique(array_filter($selectedTagIds))) : [];
                            @endphp
                            @foreach($tags as $tag)
                                <option value="{{ $tag->id }}" @selected(in_array($tag->id, $selectedTagIds)) data-name="{{ $tag->name }}">
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1">Gõ từ khóa để tìm tags. Chỉ hiển thị tags chứa từ khóa đúng hoặc gần đúng.</small>
                    </div>
                    
                    <!-- Input để thêm tags mới -->
                    <div>
                        <label class="form-label small text-muted">Hoặc thêm tags mới (phân cách bằng dấu phẩy):</label>
                        <input type="text" 
                               name="tag_names" 
                               id="tagNamesInput" 
                               class="form-control" 
                               placeholder="Ví dụ: Fashion, Style, Trend"
                               value="{{ old('tag_names', '') }}">
                        <small class="text-muted">Nhập tên tags mới, phân cách bằng dấu phẩy. Tags mới sẽ được tạo tự động.</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nội dung</label>
                    <textarea id="post-content-editor" name="content" class="form-control" rows="15">{{ old('content', $post->content ?? '') }}</textarea>
                    <small class="text-muted" id="autosave-status">Autosave sẽ hiển thị sau khi bạn chỉnh sửa.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="fw-bold d-flex justify-content-between align-items-center">
                    SEO Score
                    <span class="badge rounded-pill bg-primary" id="seo-score-badge">{{ $seoScore['score'] }}</span>
                </h5>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Meta title</label>
                    <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $post->meta_title ?? '') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Meta description</label>
                    <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Meta keywords</label>
                    <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $post->meta_keywords ?? '') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Canonical URL</label>
                    <input type="text" name="meta_canonical" class="form-control" value="{{ old('meta_canonical', $post->meta_canonical ?? '') }}" placeholder="/tu-dong-hoa/slug hoặc https://example.com/tu-dong-hoa/slug">
                    <small class="text-muted">Có thể nhập relative URL (ví dụ: /tu-dong-hoa/slug) hoặc absolute URL (ví dụ: https://example.com/tu-dong-hoa/slug)</small>
                </div>
                <button type="button" class="btn btn-outline-primary w-100" id="seo-analyze-btn">Phân tích SEO</button>
                <ul class="list-unstyled mt-3 text-muted small" id="seo-warning-list">
                    @foreach($seoScore['issues'] ?? [] as $issue)
                        <li>• {{ $issue }}</li>
                    @endforeach
                    @foreach($seoScore['suggestions'] ?? [] as $suggestion)
                        <li class="text-info">💡 {{ $suggestion }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Hình ảnh đại diện</h5>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên ảnh đại diện (vd: anh-bai-viet.webp)</label>
                    <div class="input-group">
                        <input type="text" name="thumbnail" id="thumbnail-input" class="form-control" placeholder="ten-anh.webp" value="{{ old('thumbnail', $post->thumbnail ?? '') }}">
                        <button type="button" class="btn btn-outline-secondary" onclick="openThumbnailPicker()">Chọn ảnh</button>
                    </div>
                    <small class="text-muted">Chọn từ thư viện ảnh hoặc nhập tên file. Ảnh được lưu trong thư mục posts.</small>
                    <div id="thumbnail-preview" class="mt-2">
                        @php
                            $thumbVal = old('thumbnail', $post->thumbnail ?? '');
                            $adminThumb = $thumbVal ? basename($thumbVal) : null;
                        @endphp
                        @if(!empty($adminThumb))
                            <img src="{{ asset('clients/assets/img/posts/' . $adminThumb) }}" 
                                 class="img-fluid rounded shadow-sm" 
                                 style="max-height: 180px; object-fit: cover;" 
                                 alt="preview"
                                 onerror="this.onerror=null; this.src='{{ asset('clients/assets/img/no-image.webp') }}';">
                        @endif
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Alt text cho ảnh</label>
                    <input type="text" name="thumbnail_alt_text" class="form-control" value="{{ old('thumbnail_alt_text', $post->thumbnail_alt_text ?? '') }}" placeholder="Nhập mô tả ảnh chuẩn SEO">
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Gallery ảnh phụ (Tùy chọn)</h5>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Hình ảnh</label>
                    <div id="image-gallery" class="d-flex flex-wrap gap-2 mb-2">
                        @php
                            $imageIds = old('image_ids', $post->image_ids ?? []);
                            if (!is_array($imageIds)) {
                                $imageIds = [];
                            }
                            // Load images nếu có
                            if (!empty($imageIds) && isset($post) && $post->exists) {
                                \App\Models\Post::preloadImages([$post]);
                            }
                        @endphp
                        @foreach($imageIds as $index => $imageId)
                            @php
                                $image = null;
                                $displayValue = $imageId;
                                if (is_numeric($imageId) && isset($post) && $post->exists) {
                                    $image = $post->images->firstWhere('id', $imageId);
                                    if ($image) {
                                        $displayValue = $image->id; // Gửi ID nếu là Image record
                                    }
                                } elseif (isset($post) && $post->exists && !is_numeric($imageId)) {
                                    // Nếu là tên file, tìm Image record
                                    $image = \App\Models\Image::where('url', $imageId)->first();
                                    if ($image) {
                                        $displayValue = $image->id;
                                    } else {
                                        $displayValue = $imageId; // Giữ nguyên tên file nếu chưa có Image record
                                    }
                                }
                            @endphp
                            <div class="position-relative image-item" style="width: 100px; height: 100px;" data-index="{{ $index }}">
                                @if($image)
                                    <img src="{{ asset('clients/assets/img/posts/' . $image->url) }}" 
                                         class="img-fluid rounded border" style="width: 100%; height: 100%; object-fit: cover;" alt="{{ $image->alt ?? '' }}">
                                @elseif(!empty($imageId) && !is_numeric($imageId))
                                    <img src="{{ asset('clients/assets/img/posts/' . $imageId) }}" 
                                         class="img-fluid rounded border" style="width: 100%; height: 100%; object-fit: cover;" alt="preview"
                                         onerror="this.parentElement.querySelector('.fallback').style.display='flex'; this.style.display='none';">
                                    <div class="fallback d-none align-items-center justify-content-center h-100 bg-light rounded border position-absolute top-0 start-0 w-100">
                                        <small class="text-muted">Ảnh {{ $index + 1 }}</small>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100 bg-light rounded border">
                                        <small class="text-muted">Ảnh {{ $index + 1 }}</small>
                                    </div>
                                @endif
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" 
                                        onclick="removeImage(this)" style="transform: translate(50%, -50%); padding: 2px 6px;">×</button>
                                <input type="hidden" name="image_ids[]" value="{{ $displayValue }}">
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addImageToGallery()">+ Thêm ảnh (cũ)</button>
                        <button type="button" class="btn btn-sm btn-primary" id="post-media-picker-btn">📚 Chọn từ thư viện (mới)</button>
                        <label for="post-direct-upload" class="btn btn-sm btn-success mb-0" style="cursor: pointer;">📤 Upload trực tiếp</label>
                        <input type="file" id="post-direct-upload" accept="image/*" multiple style="display: none;" onchange="handleDirectUpload(event)">
                    </div>
                    <small class="text-muted d-block mt-1">Chọn ảnh từ thư viện, upload trực tiếp hoặc nhập tên file (ví dụ: banner.jpg)</small>
                </div>
            </div>
        </div>

        @if($isEdit)
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <h5 class="fw-bold mb-0 flex-grow-1">Lịch sử bản thảo</h5>
                        <button class="btn btn-sm btn-outline-secondary" type="button" id="refresh-revision-btn">Refresh</button>
                    </div>
                    <div class="timeline" id="revision-list" style="max-height: 260px; overflow-y:auto;">
                        @forelse($post->revisions as $revision)
                            <div class="border rounded p-2 mb-2">
                                <div class="small text-muted">{{ $revision->created_at->diffForHumans() }}</div>
                                <div class="fw-semibold">{{ $revision->editor?->name ?? 'Unknown' }}</div>
                                <div class="badge bg-light text-dark">{{ $revision->is_autosave ? 'Autosave' : 'Manual' }}</div>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary w-100 mt-2"
                                    data-restore-url="{{ route('admin.posts.restore-revision', [$post, $revision->id]) }}"
                                    onclick="restoreRevision(this)"
                                >
                                    Khôi phục
                                </button>
                            </div>
                        @empty
                            <p class="text-muted">Chưa có lịch sử.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    @include('admins.partials.media-library-modal')
    <script src="{{ asset('admins/js/media-library.js') }}?v={{ file_exists(public_path('admins/js/media-library.js')) ? filemtime(public_path('admins/js/media-library.js')) : time() }}"></script>
    <script>
        function openThumbnailPicker() {
            if (!window.postMediaLibrary) {
                window.postMediaLibrary = new MediaLibrary({
                    context: 'post',
                    insertMode: 'single',
                    apiUrl: '{{ route('admin.media.library.index') }}',
                    onInsert: (selected) => {
                        const file = Array.isArray(selected) ? selected[0] : selected;
                        if (file) {
                            const fileName = file.url || file.name || '';
                            const cleanName = fileName.replace(/^.*[\\\/]/, '');
                            const inputEl = document.getElementById('thumbnail-input');
                            if (inputEl) inputEl.value = cleanName;
                            
                            const preview = document.getElementById('thumbnail-preview');
                            if (preview) {
                                preview.innerHTML = `<img src="/clients/assets/img/posts/${cleanName}" class="img-fluid rounded shadow-sm" style="max-height: 180px; object-fit: cover;" alt="${file.alt || ''}">`;
                            }
                            
                            const altInput = document.querySelector('input[name="thumbnail_alt_text"]');
                            if (altInput && !altInput.value && file.alt) {
                                altInput.value = file.alt;
                            }
                        }
                    }
                });
            } else {
                window.postMediaLibrary.context = 'post';
            }
            window.postMediaLibrary.open();
        }

        const autosaveUrl = "{{ $isEdit ? route('admin.posts.autosave', $post) : '' }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let autosaveTimer;

        const mediaPickerConfig = window.mediaPickerConfig = @json($mediaPicker);
        const restoreRevisionUrlTemplate = "{{ $isEdit ? route('admin.posts.restore-revision', [$post, '__REVISION_ID__']) : '' }}";
        const escapeHtml = (value) => {
            const div = document.createElement('div');
            div.textContent = value ?? '';
            return div.innerHTML;
        };

        if (window.tinymce && document.getElementById('post-content-editor')) {
            tinymce.init({
                selector: '#post-content-editor',
                menubar: true,
                height: 500,
                plugins: 'code lists link image table media autoresize fullscreen codesample wordcount preview',
                toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media baotinjsc_gallery | table codesample | code | fullscreen preview',
                skin: 'oxide',
                statusbar: true,
                relative_urls: false,
                remove_script_host: false,
                convert_urls: true,
                content_style: `
                    body {
                        max-height: 500px;
                        overflow-y: scroll !important;
                    }
                `,
                automatic_uploads: false,
                file_picker_types: 'image media',
                file_picker_callback: (callback, value, meta) => {
                    if (meta.filetype !== 'image') {
                        return;
                    }
                    if (typeof window.openMediaPicker !== 'function') {
                        alert('Không tải được popup thư viện ảnh. Vui lòng F5.');
                        return;
                    }
                    window.openMediaPicker({
                        mode: 'single',
                        scope: 'client',
                        folder: 'posts',
                        onSelect: (file) => {
                            if (!file) return;
                            const alt = file.alt || file.title || file.filename || file.name || '';
                            callback(file.url, { alt });
                        }
                    });
                },
                images_upload_handler: () => Promise.reject('Upload bị vô hiệu, hãy chọn ảnh từ thư viện'),
                setup: function (editor) {
                    editor.ui.registry.addButton('baotinjsc_gallery', {
                        text: '🖼 Thư viện',
                        tooltip: 'Chèn ảnh từ thư viện assets',
                        onAction: function () {
                            if (typeof window.openMediaPicker !== 'function') {
                                alert('Không tải được popup thư viện ảnh. Vui lòng F5.');
                                return;
                            }
                            window.openMediaPicker({
                                mode: 'single',
                                scope: 'client',
                                folder: 'posts',
                                onSelect: (file) => {
                                    if (!file) return;
                                    const alt = file.alt || file.title || file.filename || file.name || '';
                                    editor.insertContent(`<img src="${file.url}" alt="${escapeHtml(alt)}">`);
                                }
                            });
                        },
                    });

                    editor.on('input', scheduleAutosave);
                    editor.on('change', scheduleAutosave);
                }
            });
        }

        document.querySelectorAll('input[name="title"], textarea[name="excerpt"], input[name="meta_title"], textarea[name="meta_description"]').forEach(el => {
            el.addEventListener('input', scheduleAutosave);
        });

        // Media picker (popup mới) cho bài viết
        document.getElementById('post-media-picker-btn')?.addEventListener('click', () => {
            if (typeof window.openMediaPicker !== 'function') return;
            window.openMediaPicker({
                mode: 'multiple',
                scope: 'client',
                folder: 'posts',
                onSelect: (files) => {
                    const gallery = document.getElementById('image-gallery');
                    if (!gallery) return;
                    const arr = Array.isArray(files) ? files : [files];
                    arr.forEach(file => appendGalleryItem(file, gallery));
                }
            });
        });

        function appendGalleryItem(file, gallery) {
            const idx = gallery.querySelectorAll('.image-item').length;
            const filename = file.url ? file.url.split('/').pop() : file.filename;
            const wrapper = document.createElement('div');
            wrapper.className = 'position-relative image-item';
            wrapper.style.width = '100px';
            wrapper.style.height = '100px';
            wrapper.dataset.index = idx;
            wrapper.innerHTML = `
                <img src="${file.url}" class="img-fluid rounded border" style="width: 100%; height: 100%; object-fit: cover;" alt="${file.alt || ''}">
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" 
                        onclick="this.parentElement.remove()" style="transform: translate(50%, -50%); padding: 2px 6px;">×</button>
                <input type="hidden" name="image_ids[]" value="${filename}">
            `;
            gallery.appendChild(wrapper);
        }

        function handleDirectUpload(event) {
            const files = event.target.files;
            if (!files || files.length === 0) {
                return;
            }

            const gallery = document.getElementById('image-gallery');
            if (!gallery) {
                return;
            }

            // Upload từng file
            Array.from(files).forEach(file => {
                const formData = new FormData();
                formData.append('image', file);

                fetch('{{ route("admin.posts.upload-image") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Thêm vào gallery
                        const idx = gallery.querySelectorAll('.image-item').length;
                        const div = document.createElement('div');
                        div.className = 'position-relative image-item';
                        div.style.cssText = 'width: 100px; height: 100px;';
                        div.setAttribute('data-index', idx);
                        div.innerHTML = `
                            <img src="${data.url}" class="img-fluid rounded border" style="width: 100%; height: 100%; object-fit: cover;" alt="${data.image.alt || ''}">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" 
                                    onclick="removeImage(this)" style="transform: translate(50%, -50%); padding: 2px 6px;">×</button>
                            <input type="hidden" name="image_ids[]" value="${data.filename}">
                        `;
                        gallery.appendChild(div);
                    } else {
                        alert('Upload thất bại: ' + (data.message || 'Lỗi không xác định'));
                    }
                })
                .catch(error => {
                    console.error('Upload error', error);
                    alert('Upload ảnh thất bại.');
                });
            });

            // Reset input
            event.target.value = '';
        }

        function scheduleAutosave() {
            if (!autosaveUrl) return;
            clearTimeout(autosaveTimer);
            autosaveTimer = setTimeout(runAutosave, 4000);
        }

        function runAutosave() {
            const statusEl = document.getElementById('autosave-status');
            statusEl.textContent = 'Đang lưu bản nháp...';
            fetch(autosaveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    title: document.querySelector('input[name="title"]').value,
                    excerpt: document.querySelector('textarea[name="excerpt"]').value,
                    content: window.tinymce ? tinymce.get('post-content-editor').getContent() : document.getElementById('post-content-editor').value,
                    meta_title: document.querySelector('input[name="meta_title"]').value,
                    meta_description: document.querySelector('textarea[name="meta_description"]').value,
                    meta_keywords: document.querySelector('input[name="meta_keywords"]').value,
                })
            }).then(res => res.json())
                .then(() => {
                    statusEl.textContent = 'Đã autosave lúc ' + new Date().toLocaleTimeString();
                })
                .catch(() => statusEl.textContent = 'Autosave lỗi. Vui lòng kiểm tra kết nối.');
        }

        document.getElementById('seo-analyze-btn')?.addEventListener('click', function () {
            const btn = this;
            btn.disabled = true;
            btn.textContent = 'Đang phân tích...';
            
            // Tính toán SEO score từ dữ liệu hiện tại
            const title = document.querySelector('input[name="title"]').value;
            const content = window.tinymce ? tinymce.get('post-content-editor').getContent() : document.getElementById('post-content-editor').value;
            const excerpt = document.querySelector('textarea[name="excerpt"]').value;
            const metaTitle = document.querySelector('input[name="meta_title"]').value;
            const metaDescription = document.querySelector('textarea[name="meta_description"]').value;
            const tagIds = Array.from(document.querySelectorAll('select[name="tag_ids[]"] option:checked')).map(opt => opt.value);
            
            // Tính toán score đơn giản (client-side)
            let score = 0;
            const issues = [];
            const suggestions = [];
            
            if (title.length >= 30 && title.length <= 60) score += 20;
            else if (title.length > 0) {
                score += 10;
                if (title.length < 30) issues.push('Tiêu đề quá ngắn (nên từ 30-60 ký tự)');
                else if (title.length > 60) issues.push('Tiêu đề quá dài (nên từ 30-60 ký tự)');
            } else issues.push('Thiếu tiêu đề');
            
            if (metaTitle.length >= 30 && metaTitle.length <= 60) score += 15;
            else if (metaTitle.length > 0) {
                score += 7;
                if (metaTitle.length < 30) issues.push('Meta title quá ngắn');
                else if (metaTitle.length > 60) issues.push('Meta title quá dài');
            } else suggestions.push('Nên thêm meta title');
            
            if (metaDescription.length >= 120 && metaDescription.length <= 160) score += 15;
            else if (metaDescription.length > 0) {
                score += 7;
                if (metaDescription.length < 120) issues.push('Meta description quá ngắn');
                else if (metaDescription.length > 160) issues.push('Meta description quá dài');
            } else suggestions.push('Nên thêm meta description');
            
            const contentText = content.replace(/<[^>]*>/g, '');
            if (contentText.length >= 300) score += 20;
            else if (contentText.length >= 150) {
                score += 10;
                suggestions.push('Nội dung nên dài hơn 300 ký tự');
            } else issues.push('Nội dung quá ngắn');
            
            if (excerpt.length >= 120 && excerpt.length <= 200) score += 10;
            else if (excerpt.length > 0) {
                score += 5;
                suggestions.push('Tóm tắt nên từ 120-200 ký tự');
            } else suggestions.push('Nên thêm tóm tắt');
            
            if (tagIds.length > 0) score += 10;
            else suggestions.push('Nên thêm tags');
            
            document.getElementById('seo-score-badge').textContent = score;
            const list = document.getElementById('seo-warning-list');
            list.innerHTML = '';
            issues.forEach(issue => {
                const li = document.createElement('li');
                li.textContent = '• ' + issue;
                list.appendChild(li);
            });
            suggestions.forEach(suggestion => {
                const li = document.createElement('li');
                li.className = 'text-info';
                li.textContent = '💡 ' + suggestion;
                list.appendChild(li);
            });
            
            btn.disabled = false;
            btn.textContent = 'Phân tích SEO';
        });

        document.getElementById('refresh-revision-btn')?.addEventListener('click', function () {
            if (!autosaveUrl) return;
            const btn = this;
            btn.disabled = true;
            fetch("{{ $isEdit ? route('admin.posts.revisions', $post) : '' }}")
                .then(res => res.json())
                .then(res => {
                    const list = document.getElementById('revision-list');
                    list.innerHTML = '';
                    if (res.data && res.data.length > 0) {
                        res.data.forEach(revision => {
                            const actionUrl = restoreRevisionUrlTemplate.replace('__REVISION_ID__', revision.id);
                            const div = document.createElement('div');
                            div.className = 'border rounded p-2 mb-2';
                            div.innerHTML = `
                                <div class="small text-muted">${new Date(revision.created_at).toLocaleString()}</div>
                                <div class="fw-semibold">${revision.editor?.name ?? 'Unknown'}</div>
                                <div class="badge bg-light text-dark">${revision.is_autosave ? 'Autosave' : 'Manual'}</div>
                                <button type="button" class="btn btn-sm btn-outline-primary w-100 mt-2"
                                        data-restore-url="${actionUrl}"
                                        onclick="restoreRevision(this)">
                                    Khôi phục
                                </button>
                            `;
                            list.appendChild(div);
                        });
                    } else {
                        list.innerHTML = '<p class="text-muted">Chưa có lịch sử.</p>';
                    }
                    btn.disabled = false;
                })
                .catch(() => {
                    btn.disabled = false;
                    alert('Không thể tải lịch sử.');
                });
        });

        function addImageToGallery() {
            if (typeof window.openMediaPicker !== 'function') return;
            window.openMediaPicker({
                mode: 'single',
                scope: 'client',
                folder: 'posts',
                onSelect: function (file) {
                const fileName = file.filename || file.name || (file.url ? file.url.split('/').pop() : '');
                const gallery = document.getElementById('image-gallery');
                const index = gallery.children.length;
                
                const div = document.createElement('div');
                div.className = 'position-relative image-item';
                div.style.cssText = 'width: 100px; height: 100px;';
                div.setAttribute('data-index', index);
                div.innerHTML = `
                    <img src="${file.url}" class="img-fluid rounded border" style="width: 100%; height: 100%; object-fit: cover;" alt="${file.filename || file.name}">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" 
                            onclick="removeImage(this)" style="transform: translate(50%, -50%); padding: 2px 6px;">×</button>
                    <input type="hidden" name="image_ids[]" value="${fileName}">
                `;
                gallery.appendChild(div);
            });
        }

        function removeImage(button) {
            button.closest('.image-item').remove();
        }

        function restoreRevision(button) {
            const url = button.dataset.restoreUrl;
            if (!url) {
                return;
            }
            if (!confirm('Khôi phục bản thảo này?')) {
                return;
            }
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = csrfToken;
            form.appendChild(tokenInput);
            document.body.appendChild(form);
            form.submit();
        }



    </script>
@endpush

