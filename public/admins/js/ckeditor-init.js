/**
 * CKEditor 5 Initialization for BAOTINJSC
 * Đồng bộ toàn bộ tính năng trình soạn thảo CKEditor 5 từ nobifashion.vn
 */

(function() {
    'use strict';

    // License key từ CKEditor 5 builder (đã cấu hình cho localhost/127.0.0.1)
    const LICENSE_KEY = 'eyJhbGciOiJFUzI1NiJ9.eyJleHAiOjE3Nzk0OTQzOTksImp0aSI6Ijc3Zjk5MzViLTRlYTgtNDM1MS05NmNmLWViNGE4OTRmMTgzNyIsInVzYWdlRW5kcG9pbnQiOiJodHRwczovL3Byb3h5LWV2ZW50LmNrZWRpdG9yLmNvbSIsImRpc3RyaWJ1dGlvbkNoYW5uZWwiOlsiY2xvdWQiLCJkcnVwYWwiXSwiZmVhdHVyZXMiOlsiRFJVUCIsIkUyUCIsIkUyVyJdLCJ2YyI6ImIzMDI0MDZmIn0.JWi5b64o2vzVfSMBEvCrXVvRquFklSgqxCxcjHYatP9SGI6AaonCt8tQHbh7Y399iwg6Tvbb0q883NyWjXHJ3Q';

    // Kiểm tra CKEditor 5 đã load chưa
    if (typeof window.CKEDITOR === 'undefined') {
        console.error('CKEditor 5 chưa được load. Vui lòng đảm bảo script CKEditor 5 đã được include.');
        return;
    }

    const {
        ClassicEditor,
        Essentials,
        Paragraph,
        Heading,
        Bold,
        Italic,
        Underline,
        Strikethrough,
        Link,
        AutoLink,
        List,
        Alignment,
        BlockQuote,
        CodeBlock,
        HorizontalLine,
        ImageBlock,
        ImageToolbar,
        ImageUpload,
        ImageInsertViaUrl,
        AutoImage,
        ImageTextAlternative,
        ImageCaption,
        ImageStyle,
        ImageUtils,
        ImageEditing,
        ImageResize,
        Table,
        TableToolbar,
        PlainTableOutput,
        TableCaption,
        SourceEditing,
        Fullscreen,
        GeneralHtmlSupport,
        Autoformat,
        TextTransformation,
        FontSize,
        FontFamily,
        FontColor,
        FontBackgroundColor,
        Highlight,
        Code,
        Subscript,
        Superscript,
        TodoList,
        Indent,
        IndentBlock,
        Style,
        ShowBlocks,
        BalloonToolbar,
        BlockToolbar,
        Plugin,
        ButtonView
    } = window.CKEDITOR;

    // Hàm chuẩn hóa HTML cho figure và figcaption:
    // 1. Gỡ bỏ figure cha bị lồng ngoài figure con
    // 2. Đưa figcaption mồ côi hoặc figcaption của figure cha vào đúng bên trong figure.image
    // 3. Đảm bảo mọi figure chứa ảnh đều có class="image" để CKEditor 5 nhận diện đúng ImageBlock + Caption
    function normalizeFigureHtml(html) {
        if (!html || typeof html !== 'string') return html;
        if (!/<figure|<figcaption/i.test(html)) return html;

        let cleaned = html;

        try {
            // Bước 1: Nếu trong môi trường trình duyệt có DOMParser, chuẩn hóa cây DOM thực tế
            if (typeof DOMParser !== 'undefined') {
                const parser = new DOMParser();
                const doc = parser.parseFromString(cleaned, 'text/html');
                let modified = false;

                // Xử lý figure lồng nhau: <figure> ... <figure class="image"> ... </figure> ... </figure>
                const allFigures = Array.from(doc.querySelectorAll('figure'));
                allFigures.forEach(fig => {
                    const parentFig = fig.parentElement ? fig.parentElement.closest('figure') : null;
                    if (parentFig) {
                        // Tìm figcaption ở figure cha nếu có
                        const parentCaption = parentFig.querySelector(':scope > figcaption');
                        if (parentCaption) {
                            const innerCaption = fig.querySelector('figcaption');
                            if (!innerCaption) {
                                fig.appendChild(parentCaption);
                            } else if (!innerCaption.textContent.trim() && parentCaption.textContent.trim()) {
                                innerCaption.textContent = parentCaption.textContent.trim();
                                parentCaption.remove();
                            } else {
                                parentCaption.remove();
                            }
                        }
                        if (!fig.classList.contains('image')) {
                            fig.classList.add('image');
                        }
                        parentFig.replaceWith(fig);
                        modified = true;
                    }
                });

                // Đảm bảo mọi figure chứa img đều có class="image"
                doc.querySelectorAll('figure').forEach(fig => {
                    if (fig.querySelector('img') && !fig.classList.contains('image')) {
                        fig.classList.add('image');
                        modified = true;
                    }
                });

                // Đưa figcaption nằm ngay sau figure.image vào trong figure
                doc.querySelectorAll('figure.image + figcaption').forEach(cap => {
                    const prev = cap.previousElementSibling;
                    if (prev && prev.tagName === 'FIGURE') {
                        const existing = prev.querySelector('figcaption');
                        if (!existing) {
                            prev.appendChild(cap);
                            modified = true;
                        } else {
                            if (!existing.textContent.trim() && cap.textContent.trim()) {
                                existing.textContent = cap.textContent.trim();
                            }
                            cap.remove();
                            modified = true;
                        }
                    }
                });

                if (modified) {
                    cleaned = doc.body.innerHTML;
                }
            }
        } catch (e) {
            console.warn('DOMParser normalize warning:', e);
        }

        // Bước 2: Regex dự phòng quét sạch mọi trường hợp lồng figure và orphan figcaption
        cleaned = cleaned.replace(
            /<figure[^>]*>\s*(<figure[\s\S]*?<\/figure>)\s*(<figcaption[\s\S]*?<\/figcaption>)?\s*<\/figure>/gi,
            (match, innerFigure, outerFigcaption) => {
                if (outerFigcaption && !innerFigure.includes('<figcaption')) {
                    return innerFigure.replace(/<\/figure>$/i, `${outerFigcaption}</figure>`);
                }
                return innerFigure;
            }
        );

        cleaned = cleaned.replace(
            /(<figure[^>]*class="[^"]*image[^"]*"[^>]*>[\s\S]*?)(<\/figure>)\s*(<figcaption[^>]*>[\s\S]*?<\/figcaption>)/gi,
            (match, figOpenAndBody, figClose, figcaption) => {
                if (!figOpenAndBody.includes('<figcaption')) {
                    return `${figOpenAndBody}${figcaption}${figClose}`;
                }
                return `${figOpenAndBody}${figClose}`;
            }
        );

        cleaned = cleaned.replace(
            /<figure(?:\s+class="([^"]*)")?([^>]*)>([\s\S]*?)<\/figure>/gi,
            (match, classAttr, otherAttrs, innerContent) => {
                if (/<img[\s>]/i.test(innerContent)) {
                    let classes = (classAttr || '').trim().split(/\s+/).filter(Boolean);
                    if (!classes.includes('image')) {
                        classes.push('image');
                    }
                    const newClass = classes.join(' ');
                    const cleanOther = (otherAttrs || '').replace(/\s*class="[^"]*"/gi, '');
                    return `<figure class="${newClass}"${cleanOther}>${innerContent}</figure>`;
                }
                return match;
            }
        );

        return cleaned;
    }

    // Hàm chèn ảnh trực tiếp vào CKEditor 5 model (chuẩn imageBlock + caption con)
    function insertImageToModel(editor, imageUrl, alt, caption) {
        if (!imageUrl) return;
        try {
            editor.model.change(writer => {
                const imageElement = writer.createElement('imageBlock', {
                    src: imageUrl,
                    alt: alt || ''
                });

                if (caption) {
                    const captionElement = writer.createElement('caption');
                    writer.appendText(caption, captionElement);
                    writer.append(captionElement, imageElement);
                }

                if (typeof editor.model.insertObject === 'function') {
                    editor.model.insertObject(imageElement, null, null, { setSelection: 'on' });
                } else {
                    editor.model.insertContent(imageElement);
                }
            });
        } catch (err) {
            console.error('Lỗi chèn imageBlock model, chuyển sang HTML fallback:', err);
            let html = `<figure class="image"><img src="${imageUrl}" alt="${(alt || '').replace(/"/g, '&quot;')}">`;
            if (caption) {
                html += `<figcaption>${caption}</figcaption>`;
            }
            html += `</figure>`;
            if (editor.data && editor.data.processor) {
                const viewFragment = editor.data.processor.toView(html);
                const modelFragment = editor.data.toModel(viewFragment);
                editor.model.insertContent(modelFragment);
            }
        }
    }

    // Custom Plugin: Media Library Button tích hợp sẵn trên thanh công cụ CKEditor 5
    class MediaLibraryPlugin extends Plugin {
        static get pluginName() {
            return 'MediaLibrary';
        }

        init() {
            const editor = this.editor;
            const context = editor.config.get('mediaLibraryContext') || 'product';

            if (editor.model.schema.isRegistered('imageBlock')) {
                editor.model.schema.extend('imageBlock', { allowAttributes: 'title' });
            }
            if (editor.model.schema.isRegistered('imageInline')) {
                editor.model.schema.extend('imageInline', { allowAttributes: 'title' });
            }

            editor.conversion.attributeToAttribute({ model: 'title', view: 'title' });

            editor.ui.componentFactory.add('mediaLibrary', locale => {
                const view = new ButtonView(locale);

                view.set({
                    label: 'Thư viện ảnh',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 503.746 503.746"><path style="fill:#2563eb;" d="M465.346,503.319h-358.4c-21.333,0-38.4-17.067-38.4-38.4v-358.4c0-21.333,17.067-38.4,38.4-38.4h358.4c21.333,0,38.4,17.067,38.4,38.4v358.4C503.746,486.253,486.679,503.319,465.346,503.319z"/><rect x="105.946" y="105.519" style="fill:#ECF4F7;" width="358.4" height="358.4"/><circle cx="208.346" cy="207.919" r="34.133" style="fill:#FFE079;"/><path style="fill:#50DD8E;" d="M379.012,368.346c14.507-4.267,31.573-6.827,51.2-6.827c0,0,17.067,0,34.133,8.533v93.867h-358.4c0,0,42.667-68.267,102.4-68.267s93.867,34.133,93.867,34.133S319.279,385.412,379.012,368.346z"/></svg>',
                    tooltip: true,
                    withText: false
                });

                view.on('execute', () => {
                    const folder = context === 'post' ? 'posts' : 'clothes';
                    if (window.mediaLibrary) {
                        window.mediaLibrary.open({
                            context: context,
                            onInsert: (result) => {
                                try {
                                    const items = Array.isArray(result) ? result : [result];
                                    if (!items.length) return;

                                    items.forEach(image => {
                                        const fileName = image.url || image.file_name || image.path || '';
                                        if (!fileName) return;

                                        const imageUrl = fileName.startsWith('http') 
                                            ? fileName 
                                            : (fileName.includes('/') ? `/${fileName.replace(/^\//, '')}` : `/clients/assets/img/${folder}/${fileName}`);
                                        const alt = image.alt || image.name || image.title || '';
                                        const caption = (image.description || image.title || '').trim();

                                        insertImageToModel(editor, imageUrl, alt, caption);
                                    });
                                } catch (err) {
                                    console.error('Lỗi khi chèn ảnh vào CKEditor:', err);
                                }
                            },
                            insertMode: 'single'
                        });
                    } else if (typeof window.openMediaPicker === 'function') {
                        window.openMediaPicker({
                            mode: 'single',
                            scope: 'client',
                            folder: folder,
                            onSelect: (file) => {
                                if (!file || !file.url) return;
                                const caption = (file.caption || file.description || file.title || '').trim();
                                insertImageToModel(editor, file.url, file.alt || file.title || '', caption);
                            }
                        });
                    } else {
                        alert('Thư viện ảnh chưa sẵn sàng.');
                    }
                });

                return view;
            });
        }
    }

    // Cấu hình editor cho Posts (Đầy đủ tính năng cao cấp)
    const postsEditorConfig = {
        licenseKey: LICENSE_KEY,
        language: 'vi',
        toolbar: {
            shouldNotGroupWhenFull: true,
            items: [
                'undo', 'redo', '|',
                'heading', '|',
                'bold', 'italic', 'underline', 'strikethrough', '|',
                'fontSize', 'fontColor', 'fontBackgroundColor', '|',
                'alignment', '|',
                'bulletedList', 'numberedList', 'todoList', '|',
                'link', 'mediaLibrary', 'insertTable', '|',
                'blockQuote', 'codeBlock', 'horizontalLine', '|',
                'sourceEditing', 'fullscreen'
            ]
        },
        plugins: [
            Essentials, Paragraph, Heading, Bold, Italic, Underline, Strikethrough,
            Link, AutoLink, List, Alignment, BlockQuote, CodeBlock, HorizontalLine,
            ImageBlock, ImageToolbar, ImageUpload, ImageInsertViaUrl, AutoImage,
            ImageTextAlternative, ImageCaption, ImageStyle, ImageUtils, ImageEditing, ImageResize,
            Table, TableToolbar, PlainTableOutput, TableCaption,
            SourceEditing, Fullscreen, GeneralHtmlSupport, Autoformat, TextTransformation,
            FontSize, FontFamily, FontColor, FontBackgroundColor, Highlight, Code,
            Subscript, Superscript, TodoList, Indent, IndentBlock, Style, ShowBlocks,
            BalloonToolbar, BlockToolbar, MediaLibraryPlugin
        ],
        heading: {
            options: [
                { model: 'paragraph', title: 'Đoạn văn', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Tiêu đề 1 (H1)', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Tiêu đề 2 (H2)', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Tiêu đề 3 (H3)', class: 'ck-heading_heading3' },
                { model: 'heading4', view: 'h4', title: 'Tiêu đề 4 (H4)', class: 'ck-heading_heading4' }
            ]
        },
        fontSize: {
            options: [10, 12, 14, 'default', 18, 20, 22, 26, 30],
            supportAllValues: true
        },
        fontFamily: {
            supportAllValues: true
        },
        htmlSupport: {
            allow: [
                {
                    name: /^.*$/,
                    styles: true,
                    attributes: true,
                    classes: true
                }
            ],
            disallow: [
                {
                    name: /^(figure|figcaption)$/
                }
            ]
        },
        image: {
            resizeOptions: [
                {
                    name: 'resizeImage:original',
                    label: 'Gốc (100%)',
                    value: null
                },
                {
                    name: 'resizeImage:25',
                    label: '25%',
                    value: '25'
                },
                {
                    name: 'resizeImage:50',
                    label: '50%',
                    value: '50'
                },
                {
                    name: 'resizeImage:75',
                    label: '75%',
                    value: '75'
                }
            ],
            toolbar: [
                'imageStyle:inline', 'imageStyle:wrapText', 'imageStyle:breakText', '|',
                'resizeImage', '|',
                'toggleImageCaption', 'imageTextAlternative'
            ]
        },
        link: {
            addTargetToExternalLinks: true,
            defaultProtocol: 'https://',
            decorators: {
                openInNewTab: {
                    mode: 'manual',
                    label: 'Mở trong tab mới',
                    attributes: {
                        target: '_blank',
                        rel: 'noopener noreferrer'
                    }
                }
            }
        },
        table: {
            contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
        },
        mediaLibraryContext: 'post'
    };

    // Cấu hình editor cho Products
    const productsEditorConfig = {
        licenseKey: LICENSE_KEY,
        language: 'vi',
        toolbar: {
            items: [
                'undo', 'redo', '|',
                'heading', '|',
                'bold', 'italic', 'underline', 'strikethrough', '|',
                'fontSize', 'fontColor', 'fontBackgroundColor', '|',
                'alignment', '|',
                'bulletedList', 'numberedList', '|',
                'link', 'mediaLibrary', 'insertTable', '|',
                'blockQuote', 'horizontalLine', '|',
                'sourceEditing', 'fullscreen'
            ],
            shouldNotGroupWhenFull: true
        },
        plugins: [
            Essentials, Paragraph, Heading, Bold, Italic, Underline, Strikethrough,
            Link, AutoLink, List, Alignment, BlockQuote, HorizontalLine,
            ImageBlock, ImageToolbar, ImageUpload, ImageInsertViaUrl, AutoImage,
            ImageTextAlternative, ImageCaption, ImageStyle, ImageUtils, ImageEditing, ImageResize,
            Table, TableToolbar, PlainTableOutput, TableCaption,
            SourceEditing, Fullscreen, GeneralHtmlSupport, Autoformat, TextTransformation,
            FontSize, FontFamily, FontColor, FontBackgroundColor, Highlight, Code,
            MediaLibraryPlugin
        ],
        heading: {
            options: [
                { model: 'paragraph', title: 'Đoạn văn', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Tiêu đề 1 (H1)', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Tiêu đề 2 (H2)', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Tiêu đề 3 (H3)', class: 'ck-heading_heading3' },
                { model: 'heading4', view: 'h4', title: 'Tiêu đề 4 (H4)', class: 'ck-heading_heading4' }
            ]
        },
        fontSize: {
            options: [10, 12, 14, 'default', 18, 20, 22, 26],
            supportAllValues: true
        },
        htmlSupport: {
            allow: [
                {
                    name: /^.*$/,
                    styles: true,
                    attributes: true,
                    classes: true
                }
            ],
            disallow: [
                {
                    name: /^(figure|figcaption)$/
                }
            ]
        },
        image: {
            resizeOptions: [
                {
                    name: 'resizeImage:original',
                    label: 'Gốc (100%)',
                    value: null
                },
                {
                    name: 'resizeImage:25',
                    label: '25%',
                    value: '25'
                },
                {
                    name: 'resizeImage:50',
                    label: '50%',
                    value: '50'
                },
                {
                    name: 'resizeImage:75',
                    label: '75%',
                    value: '75'
                }
            ],
            toolbar: [
                'imageStyle:inline', 'imageStyle:wrapText', 'imageStyle:breakText', '|',
                'resizeImage', '|',
                'toggleImageCaption', 'imageTextAlternative'
            ]
        },
        link: {
            addTargetToExternalLinks: true,
            defaultProtocol: 'https://',
            decorators: {
                openInNewTab: {
                    mode: 'manual',
                    label: 'Mở trong tab mới',
                    attributes: {
                        target: '_blank',
                        rel: 'noopener noreferrer'
                    }
                }
            }
        },
        table: {
            contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
        },
        mediaLibraryContext: 'product'
    };

    // Hàm tắt tính năng kiểm tra chính tả (bỏ gạch chân đỏ lượn sóng)
    function disableEditorSpellcheck(editor) {
        if (!editor) return;

        const applyAttributes = (el) => {
            if (!el) return;
            el.spellcheck = false;
            el.setAttribute('spellcheck', 'false');
            el.setAttribute('autocorrect', 'off');
            el.setAttribute('autocomplete', 'off');
            el.setAttribute('autocapitalize', 'off');
            if (el.querySelectorAll) {
                el.querySelectorAll('[contenteditable]').forEach(child => {
                    child.spellcheck = false;
                    child.setAttribute('spellcheck', 'false');
                });
            }
        };

        try {
            editor.editing.view.change(writer => {
                const root = editor.editing.view.document.getRoot();
                if (root) {
                    writer.setAttribute('spellcheck', 'false', root);
                    writer.setAttribute('autocorrect', 'off', root);
                    writer.setAttribute('autocomplete', 'off', root);
                }
                if (editor.editing.view.document.roots) {
                    for (const r of editor.editing.view.document.roots) {
                        writer.setAttribute('spellcheck', 'false', r);
                        writer.setAttribute('autocorrect', 'off', r);
                        writer.setAttribute('autocomplete', 'off', r);
                    }
                }
            });
        } catch (e) {}

        const updateDom = () => {
            try {
                if (editor.ui && editor.ui.view && editor.ui.view.editable) {
                    applyAttributes(editor.ui.view.editable.element);
                }
                if (editor.editing && editor.editing.view) {
                    applyAttributes(editor.editing.view.getDomRoot());
                }
            } catch (e) {}
        };

        updateDom();

        try {
            if (editor.editing && editor.editing.view && editor.editing.view.document) {
                editor.editing.view.document.on('render', updateDom);
            }
            if (editor.ui && editor.ui.focusTracker) {
                editor.ui.focusTracker.on('change:isFocused', (evt, name, isFocused) => {
                    if (isFocused) {
                        updateDom();
                    }
                });
            }
        } catch (e) {}
    }

    // Thiết lập hành vi cuộn mượt mà:
    // Khi để chuột trong trình soạn thảo và cuộn xuống:
    // 1. Nếu trình soạn thảo chưa dính hẳn lên top -> cuộn trang web xuống trước cho đến khi editor chạm đỉnh màn hình.
    // 2. Khi editor đã dính lên top -> cuộn tiếp nội dung bên trong editor.
    // 3. Khi cuộn hết nội dung bên trong editor -> cuộn tiếp trang web xuống dưới các phần còn lại.
    // 4. Khi cuộn lên và nội dung editor đã về đỉnh -> cuộn trang web lên trên để xem các phần phía trên.
    function setupEditorScrollChaining(editor) {
        if (!editor || !editor.ui || !editor.ui.view) return;
        const editable = editor.ui.view.editable && editor.ui.view.editable.element;
        const editorElement = editor.ui.view.element;
        if (!editable || !editorElement) return;

        if (editorElement.dataset.scrollChainingAttached === 'true') return;
        editorElement.dataset.scrollChainingAttached = 'true';

        editorElement.addEventListener('wheel', (e) => {
            // Bỏ qua chế độ fullscreen hoặc cuộn ngang
            if (document.body.classList.contains('ck-fullscreen') || 
                editorElement.classList.contains('ck-fullscreen') ||
                Math.abs(e.deltaY) < Math.abs(e.deltaX)) {
                return;
            }

            const rect = editorElement.getBoundingClientRect();
            const topThreshold = 10; // Khoảng cách tới mép trên viewport (px)

            // 1. CUỘN XUỐNG (deltaY > 0)
            if (e.deltaY > 0) {
                const distToTop = rect.top - topThreshold;

                // Nếu đỉnh editor chưa chạm top màn hình
                if (distToTop > 0) {
                    e.preventDefault();
                    const windowScroll = Math.min(e.deltaY, distToTop);
                    window.scrollBy({ top: windowScroll, behavior: 'auto' });

                    const remaining = e.deltaY - windowScroll;
                    if (remaining > 0) {
                        editable.scrollTop += remaining;
                    }
                    return;
                }

                // Khi editor ĐÃ chạm/dính lên top
                const maxScrollTop = editable.scrollHeight - editable.clientHeight;
                const remainingScroll = maxScrollTop - editable.scrollTop;

                if (remainingScroll <= 0) {
                    // Đã cuộn hết nội dung bên trong -> cuộn trang web xuống tiếp
                    e.preventDefault();
                    window.scrollBy({ top: e.deltaY, behavior: 'auto' });
                    return;
                }

                if (e.deltaY > remainingScroll) {
                    e.preventDefault();
                    editable.scrollTop = maxScrollTop;
                    const overflow = e.deltaY - remainingScroll;
                    window.scrollBy({ top: overflow, behavior: 'auto' });
                    return;
                }

                // Còn lại để trình duyệt cuộn nội dung bên trong editor
            }
            // 2. CUỘN LÊN (deltaY < 0)
            else if (e.deltaY < 0) {
                const deltaUp = Math.abs(e.deltaY);

                if (editable.scrollTop <= 0) {
                    // Nội dung editor đã ở đỉnh -> cuộn trang web lên trên
                    e.preventDefault();
                    window.scrollBy({ top: e.deltaY, behavior: 'auto' });
                    return;
                }

                if (deltaUp > editable.scrollTop) {
                    e.preventDefault();
                    const overflow = deltaUp - editable.scrollTop;
                    editable.scrollTop = 0;
                    window.scrollBy({ top: -overflow, behavior: 'auto' });
                    return;
                }

                // Còn lại để trình duyệt cuộn nội dung editor lên
            }
        }, { passive: false });
    }

    // Khởi tạo editor cho Posts
    function initPostsEditor() {
        const textarea = document.getElementById('post-content-editor');
        if (!textarea || textarea.dataset.ckeditorInitialized === 'true') return;

        textarea.setAttribute('spellcheck', 'false');
        const initialData = normalizeFigureHtml(textarea.value || '');

        ClassicEditor
            .create(textarea, {
                ...postsEditorConfig,
                initialData: initialData
            })
            .then(editor => {
                disableEditorSpellcheck(editor);
                setupEditorScrollChaining(editor);

                // Chuẩn hóa mọi HTML được chèn/dán vào editor (qua setData, clipboard paste, v.v.)
                if (editor.data && editor.data.processor && typeof editor.data.processor.toView === 'function') {
                    const originalToView = editor.data.processor.toView.bind(editor.data.processor);
                    editor.data.processor.toView = function(data) {
                        if (typeof data === 'string') {
                            data = normalizeFigureHtml(data);
                        }
                        return originalToView(data);
                    };
                }

                textarea.dataset.ckeditorInitialized = 'true';
                window.ckeditorInstances = window.ckeditorInstances || {};
                window.ckeditorInstances['post-content-editor'] = editor;

                const form = textarea.closest('form');
                if (form) {
                    form.addEventListener('submit', () => {
                        let htmlContent = editor.getData();
                        htmlContent = normalizeFigureHtml(htmlContent);
                        htmlContent = htmlContent.replace(/\s*data-list-item-id="[^"]*"/gi, '');
                        textarea.value = htmlContent;
                    });
                }

                editor.model.document.on('change:data', () => {
                    if (window.scheduleAutosave) {
                        window.scheduleAutosave();
                    }
                });
            })
            .catch(error => {
                console.error('Lỗi khởi tạo CKEditor 5 cho bài viết:', error);
            });
    }

    // Khởi tạo editor cho Products
    function initProductsEditors() {
        document.querySelectorAll('.tinymce-editor').forEach(textarea => {
            if (textarea.dataset.ckeditorInitialized === 'true') {
                return;
            }

            textarea.setAttribute('spellcheck', 'false');
            const initialData = normalizeFigureHtml(textarea.value || '');
            const editorId = textarea.id || 'ckeditor-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
            if (!textarea.id) {
                textarea.id = editorId;
            }

            const isProductForm = textarea.closest('form#product-form');
            const config = isProductForm ? productsEditorConfig : postsEditorConfig;

            ClassicEditor
                .create(textarea, {
                    ...config,
                    initialData: initialData
                })
                .then(editor => {
                    disableEditorSpellcheck(editor);
                    setupEditorScrollChaining(editor);

                    // Chuẩn hóa mọi HTML được chèn/dán vào editor (qua setData, clipboard paste, v.v.)
                    if (editor.data && editor.data.processor && typeof editor.data.processor.toView === 'function') {
                        const originalToView = editor.data.processor.toView.bind(editor.data.processor);
                        editor.data.processor.toView = function(data) {
                            if (typeof data === 'string') {
                                data = normalizeFigureHtml(data);
                            }
                            return originalToView(data);
                        };
                    }

                    textarea.dataset.ckeditorInitialized = 'true';
                    window.ckeditorInstances = window.ckeditorInstances || {};
                    window.ckeditorInstances[editorId] = editor;

                    const form = textarea.closest('form');
                    if (form) {
                        form.addEventListener('submit', () => {
                            let htmlContent = editor.getData();
                            htmlContent = normalizeFigureHtml(htmlContent);
                            htmlContent = htmlContent.replace(/\s*data-list-item-id="[^"]*"/gi, '');
                            textarea.value = htmlContent;
                        });
                    }
                })
                .catch(error => {
                    console.error('Lỗi khởi tạo CKEditor 5:', error);
                });
        });
    }

    // API tương thích toàn cầu
    window.CKEditor5API = {
        get: function(editorId) {
            return window.ckeditorInstances && window.ckeditorInstances[editorId] || null;
        },
        getContent: function(editorId) {
            const editor = this.get(editorId);
            if (!editor) return '';
            let htmlContent = editor.getData();
            htmlContent = normalizeFigureHtml(htmlContent);
            htmlContent = htmlContent.replace(/\s*data-list-item-id="[^"]*"/gi, '');
            return htmlContent;
        },
        setContent: function(editorId, content) {
            const editor = this.get(editorId);
            if (editor) {
                editor.setData(normalizeFigureHtml(content || ''));
            }
        }
    };

    // Auto-init khi DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initPostsEditor();
            initProductsEditors();
        });
    } else {
        initPostsEditor();
        initProductsEditors();
    }

    // Observer tự động bắt các textarea mới
    if (typeof MutationObserver !== 'undefined') {
        const editorObserver = new MutationObserver((mutations) => {
            let shouldInit = false;
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === 1) {
                        if (node.tagName === 'TEXTAREA' && 
                            (node.id === 'post-content-editor' || node.classList.contains('tinymce-editor'))) {
                            shouldInit = true;
                        } else if (node.querySelectorAll) {
                            const textareas = node.querySelectorAll('#post-content-editor, .tinymce-editor');
                            if (textareas.length > 0) {
                                shouldInit = true;
                            }
                        }
                    }
                });
            });
            if (shouldInit) {
                setTimeout(() => {
                    initPostsEditor();
                    initProductsEditors();
                }, 100);
            }
        });

        if (document.body) {
            editorObserver.observe(document.body, { childList: true, subtree: true });
        }
    }

    // Tắt kiểm tra chính tả toàn cục
    function enforceDisableSpellcheck() {
        document.querySelectorAll('.ck-editor__editable, .ck-editor__editable_inline, .ck-content, [contenteditable="true"], textarea, .tinymce-editor, form').forEach(el => {
            el.spellcheck = false;
            if (el.getAttribute('spellcheck') !== 'false') {
                el.setAttribute('spellcheck', 'false');
                el.setAttribute('autocorrect', 'off');
                el.setAttribute('autocomplete', 'off');
                el.setAttribute('autocapitalize', 'off');
            }
        });
    }

    document.addEventListener('focusin', enforceDisableSpellcheck, true);
    document.addEventListener('input', enforceDisableSpellcheck, true);
    document.addEventListener('DOMContentLoaded', enforceDisableSpellcheck);
    setTimeout(enforceDisableSpellcheck, 300);
    setTimeout(enforceDisableSpellcheck, 1000);
})();

