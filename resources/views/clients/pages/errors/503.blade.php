<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ Thống Đang Nâng Cấp & Bảo Trì | {{ $settings->site_name ?? 'Bảo Tín JSC' }}</title>
    
    <!-- Bảo vệ SEO: không index khi đang bảo trì tạm thời -->
    <meta name="robots" content="noindex, nofollow" />
    <meta http-equiv="refresh" content="120">
    
    <link rel="shortcut icon" href="{{ asset('clients/assets/img/favicon.ico') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #1e3a8a;
            --primary-dark: #172554;
            --accent: #2563eb;
            --warning: #f59e0b;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --bg-page: #f8fafc;
            --card-bg: #ffffff;
            --border: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-page);
            background-image: 
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(30, 58, 138, 0.06) 0px, transparent 50%),
                linear-gradient(to right, #f1f5f9 1px, transparent 1px),
                linear-gradient(to bottom, #f1f5f9 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 32px 32px, 32px 32px;
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            line-height: 1.5;
        }

        .maintenance-wrapper {
            width: 100%;
            max-width: 680px;
            margin: 0 auto;
        }

        .maintenance-card {
            background: var(--card-bg);
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.08), 0 0 1px 1px rgba(15, 23, 42, 0.02);
            padding: 48px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        @media (max-width: 640px) {
            .maintenance-card {
                padding: 32px 20px;
                border-radius: 16px;
            }
        }

        .brand-header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 28px;
        }

        .brand-logo {
            max-height: 60px;
            max-width: 220px;
            object-fit: contain;
        }

        .brand-name-fallback {
            font-size: 24px;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }

        /* SVG Gears Animation */
        .illustration-box {
            position: relative;
            width: 130px;
            height: 130px;
            margin: 0 auto 28px;
        }

        .gear {
            position: absolute;
            transform-origin: center center;
        }

        .gear-primary {
            width: 86px;
            height: 86px;
            top: 10px;
            left: 10px;
            fill: #2563eb;
            animation: spin 9s linear infinite;
        }

        .gear-secondary {
            width: 60px;
            height: 60px;
            bottom: 10px;
            right: 12px;
            fill: #f59e0b;
            animation: spin-reverse 6s linear infinite;
        }

        .gear-inner {
            fill: #ffffff;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes spin-reverse {
            from { transform: rotate(0deg); }
            to { transform: rotate(-360deg); }
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fef3c7;
            color: #92400e;
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 16px;
            border: 1px solid #fde68a;
        }

        .badge-status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #d97706;
            animation: pulse-dot 1.5s infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        .title {
            font-size: 26px;
            font-weight: 800;
            color: var(--primary-dark);
            letter-spacing: -0.5px;
            margin-bottom: 12px;
        }

        @media (max-width: 640px) {
            .title {
                font-size: 22px;
            }
        }

        .description {
            font-size: 15px;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 28px;
            max-width: 520px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Information cards */
        .info-grid {
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 28px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            text-align: left;
        }

        @media (max-width: 640px) {
            .info-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .info-label {
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 600;
            color: #94a3b8;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: #334155;
        }

        /* Buttons & Actions */
        .action-group {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
            margin-bottom: 24px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 22px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary-action {
            background: #2563eb;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
        }

        .btn-primary-action:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .btn-call-action {
            background: #16a34a;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(22, 163, 74, 0.25);
        }

        .btn-call-action:hover {
            background: #15803d;
            transform: translateY(-1px);
        }

        .btn-outline-action {
            background: #ffffff;
            color: #334155;
            border: 1px solid var(--border);
        }

        .btn-outline-action:hover {
            background: #f1f5f9;
        }

        /* Countdown & Auto check */
        .timer-box {
            font-size: 13px;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .timer-highlight {
            font-weight: 700;
            color: var(--primary);
        }

        /* Footer */
        .footer-note {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px dashed var(--border);
            font-size: 13px;
            color: #94a3b8;
        }

        .footer-note strong {
            color: #475569;
        }

        @if(isset($isPreview) && $isPreview)
        .preview-banner {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        @endif
    </style>
</head>
<body>

<div class="maintenance-wrapper">
    <div class="maintenance-card">

        @if(isset($isPreview) && $isPreview)
        <div class="preview-banner">
            <span>👁️</span> Chế độ xem trước (Giao diện khách hàng khi bảo trì)
        </div>
        @endif

        <!-- Logo Thương Hiệu Bảo Tín JSC -->
        <div class="brand-header">
            @php
                $logoFile = $settings->site_logo ?? 'site-logo-1769418909.jpg';
                $logoPath = 'clients/assets/img/business/' . $logoFile;
            @endphp
            @if(file_exists(public_path($logoPath)))
                <img src="{{ asset($logoPath) }}" alt="{{ $settings->site_name ?? 'Bảo Tín JSC' }}" class="brand-logo">
            @else
                <span class="brand-name-fallback">{{ $settings->site_name ?? 'BẢO TÍN JSC' }}</span>
            @endif
        </div>

        <!-- Animation Bánh Răng Công Nghiệp Chuyển Động Thuần CSS -->
        <div class="illustration-box" aria-hidden="true">
            <!-- Bánh răng lớn chính -->
            <svg class="gear gear-primary" viewBox="0 0 100 100">
                <path d="M50 15c-1.5 0-3 .1-4.5.3L43.8 6.8c-.5-1.5-2-2.5-3.6-2.4l-7.3.7c-1.6.2-2.9 1.4-3.2 3L28.3 16c-2.7 1-5.3 2.3-7.7 3.9l-7.2-4.9c-1.3-.9-3.1-.7-4.2.5l-5.1 5.1c-1.1 1.1-1.3 2.9-.5 4.2l4.9 7.2c-1.6 2.4-2.9 5-3.9 7.7l-7.9 1.4c-1.6.3-2.8 1.6-3 3.2l-.7 7.3c-.2 1.6.8 3.1 2.4 3.6l8.5 1.7c.2 1.5.5 3 .9 4.4l-5.6 6.7c-1 1.2-1 3 .1 4.1l5.2 5.2c1.1 1.1 2.9 1.2 4.1.2l6.7-5.6c1.4.5 2.9.8 4.4.9l1.7 8.5c.3 1.6 1.7 2.7 3.3 2.7l7.3-.2c1.6 0 3-1.1 3.4-2.7l1.9-8.5c2.6-.7 5.1-1.8 7.4-3.1l6.9 5.3c1.3 1 3.1.9 4.3-.2l5.1-5.1c1.1-1.1 1.3-2.9.4-4.2l-5.3-6.9c1.3-2.3 2.4-4.8 3.1-7.4l8.5-1.9c1.6-.4 2.7-1.8 2.7-3.4l-.2-7.3c0-1.6-1.1-3-2.7-3.3l-8.5-1.7c-.1-1.5-.4-3-.9-4.4l5.6-6.7c1-1.2 1-3-.1-4.1l-5.2-5.2c-1.1-1.1-2.9-1.2-4.1-.2l-6.7 5.6c-1.4-.5-2.9-.8-4.4-.9L53.4 15c-.2 0-.3 0-.5 0h-2.9zM50 32c9.9 0 18 8.1 18 18s-8.1 18-18 18-18-8.1-18-18 8.1-18 18-18z"/>
                <circle class="gear-inner" cx="50" cy="50" r="11"/>
            </svg>
            <!-- Bánh răng phụ nhỏ ăn khớp -->
            <svg class="gear gear-secondary" viewBox="0 0 100 100">
                <path d="M50 18c-1.3 0-2.6.1-3.8.3L44 8.7c-.5-1.4-1.8-2.3-3.3-2.2l-6.5.6c-1.5.1-2.7 1.2-3 2.7l-1.3 7.5c-2.4.9-4.7 2.1-6.8 3.5l-6.4-4.4c-1.2-.8-2.8-.7-3.8.4l-4.6 4.6c-1 1-1.2 2.6-.4 3.8l4.4 6.4c-1.4 2.1-2.6 4.4-3.5 6.8l-7.5 1.3c-1.5.3-2.5 1.5-2.7 3l-.6 6.5c-.1 1.5.8 2.8 2.2 3.3l7.6 1.5c.2 1.3.5 2.6.8 3.9l-5 6c-.9 1.1-.9 2.7.1 3.7l4.6 4.6c1 1 2.6 1.1 3.7.2l6-5c1.3.3 2.6.6 3.9.8l1.5 7.6c.3 1.4 1.5 2.4 3 2.4l6.5-.2c1.4 0 2.7-1 3-2.4l1.7-7.6c2.3-.6 4.5-1.6 6.6-2.8l6.1 4.7c1.2.9 2.8.8 3.8-.2l4.6-4.6c1-1 1.1-2.6.3-3.8l-4.7-6.1c1.2-2.1 2.2-4.3 2.8-6.6l7.6-1.7c1.4-.3 2.4-1.6 2.4-3l-.2-6.5c0-1.4-1-2.7-2.4-3l-7.6-1.5c-.1-1.3-.4-2.6-.8-3.9l5-6c.9-1.1.9-2.7-.1-3.7l-4.6-4.6c-1-1-2.6-1.1-3.7-.2l-6 5c-1.3-.3-2.6-.6-3.9-.8L53 18c-.2 0-.3 0-.4 0h-2.6zM50 35c8.3 0 15 6.7 15 15s-6.7 15-15 15-15-6.7-15-15 6.7-15 15-15z"/>
                <circle class="gear-inner" cx="50" cy="50" r="9"/>
            </svg>
        </div>

        <div class="badge-status">
            <span class="badge-status-dot"></span>
            <span>BẢO TRÌ ĐỊNH KỲ HỆ THỐNG</span>
        </div>

        <h1 class="title">Hệ Thống Đang Nâng Cấp Kỹ Thuật</h1>
        
        <p class="description">
            Website của <strong>{{ $settings->site_name ?? 'Bảo Tín JSC' }}</strong> đang trong quá trình nâng cấp định kỳ nhằm tối ưu hóa tốc độ và nâng cao trải nghiệm dịch vụ. Quý khách vui lòng quay lại sau ít phút.
        </p>

        <!-- Bảng Thông Tin Tóm Tắt -->
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Trạng thái</span>
                <span class="info-value" style="color: #d97706;">Đang tối ưu & cập nhật</span>
            </div>
            <div class="info-item">
                <span class="info-label">Thời gian dự kiến</span>
                <span class="info-value">15 - 30 phút</span>
            </div>
            <div class="info-item">
                <span class="info-label">Hotline hỗ trợ 24/7</span>
                <span class="info-value">{{ $settings->contact_phone ?? '0985.214.595' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Email tiếp nhận</span>
                <span class="info-value">{{ $settings->contact_email ?? 'baotinjsc.vn@gmail.com' }}</span>
            </div>
        </div>

        <!-- Các Nút Hành Động -->
        <div class="action-group">
            <a href="tel:{{ preg_replace('/[^0-9]/', '', $settings->contact_phone ?? '0985214595') }}" class="btn-action btn-call-action">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/></svg>
                <span>Gọi Hotline: {{ $settings->contact_phone ?? '0985.214.595' }}</span>
            </a>

            @if(!empty($settings->contact_phone))
            <a href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', $settings->contact_phone) }}" target="_blank" rel="noopener" class="btn-action btn-primary-action">
                <span>💬 Nhắn Zalo tư vấn</span>
            </a>
            @endif

            <button type="button" onclick="window.location.reload();" class="btn-action btn-outline-action">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Tải lại trang</span>
            </button>
        </div>

        <!-- Bộ đếm tự động kiểm tra lại -->
        <div class="timer-box">
            <span>Hệ thống sẽ tự động thử kết nối lại sau</span>
            <span class="timer-highlight" id="countdown-val">60s</span>
        </div>

        <div class="footer-note">
            Cảm ơn Quý khách đã tin tưởng và đồng hành cùng <strong>{{ $settings->site_name ?? 'Công Ty Cổ Phần Công Nghiệp Bảo Tín' }}</strong>.
        </div>

    </div>
</div>

<script>
    (function() {
        let timeLeft = 60;
        const countdownEl = document.getElementById('countdown-val');
        
        const timer = setInterval(function() {
            timeLeft--;
            if (countdownEl) {
                countdownEl.textContent = timeLeft + 's';
            }
            if (timeLeft <= 0) {
                clearInterval(timer);
                window.location.reload();
            }
        }, 1000);
    })();
</script>

</body>
</html>