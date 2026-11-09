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

        /* High-End Industrial Gears Animation */
        .illustration-box {
            position: relative;
            width: 220px;
            height: 175px;
            margin: 0 auto 28px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .illustration-glow {
            position: absolute;
            width: 140px;
            height: 140px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.15) 0%, rgba(245, 158, 11, 0.08) 50%, transparent 70%);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            filter: blur(14px);
            pointer-events: none;
            animation: pulse-glow 3.5s ease-in-out infinite alternate;
        }

        .maintenance-svg {
            width: 100%;
            height: 100%;
            overflow: visible;
            animation: float-gentle 4s ease-in-out infinite;
        }

        .gear-rotate-cw {
            transform-origin: 0px 0px;
            animation: spin-cw 16s linear infinite;
        }

        .gear-rotate-ccw {
            transform-origin: 0px 0px;
            animation: spin-ccw 12s linear infinite;
        }

        .gear-rotate-ccw-fast {
            transform-origin: 0px 0px;
            animation: spin-ccw 12s linear infinite;
        }

        @keyframes spin-cw {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes spin-ccw {
            from { transform: rotate(0deg); }
            to { transform: rotate(-360deg); }
        }

        @keyframes float-gentle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        @keyframes pulse-glow {
            0% { transform: translate(-50%, -50%) scale(0.9); opacity: 0.6; }
            100% { transform: translate(-50%, -50%) scale(1.15); opacity: 1; }
        }

        .orbit-ring {
            transform-origin: 80px 85px;
            animation: spin-cw 40s linear infinite;
        }

        .sparkle {
            animation: sparkle-twinkle 2.5s ease-in-out infinite alternate;
        }
        .sparkle-2 {
            animation-delay: 1.2s;
        }

        @keyframes sparkle-twinkle {
            0% { opacity: 0.2; transform: scale(0.7); }
            100% { opacity: 1; transform: scale(1.1); }
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

        <!-- Animation Hệ Thống Bánh Răng Cơ Khí Tự Động Hóa Cao Cấp (Bảo Tín JSC) -->
        <div class="illustration-box" aria-hidden="true">
            <div class="illustration-glow"></div>
            <svg class="maintenance-svg" viewBox="0 0 220 170" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <!-- Gradient Xanh Dương Bảo Tín -->
                    <linearGradient id="blueGearGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#3b82f6" />
                        <stop offset="45%" stop-color="#2563eb" />
                        <stop offset="100%" stop-color="#1d4ed8" />
                    </linearGradient>

                    <!-- Gradient Cam Hổ Phách Năng Động -->
                    <linearGradient id="amberGearGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#fbbf24" />
                        <stop offset="45%" stop-color="#f59e0b" />
                        <stop offset="100%" stop-color="#d97706" />
                    </linearGradient>

                    <!-- Gradient Xám Titan Bạc Công Nghiệp -->
                    <linearGradient id="silverGearGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#e2e8f0" />
                        <stop offset="50%" stop-color="#94a3b8" />
                        <stop offset="100%" stop-color="#64748b" />
                    </linearGradient>

                    <!-- Đổ bóng 3D mềm mại chuẩn cơ khí -->
                    <filter id="gearShadow" x="-25%" y="-25%" width="150%" height="150%">
                        <feDropShadow dx="0" dy="6" stdDeviation="5" flood-color="#0f172a" flood-opacity="0.16" />
                    </filter>

                    <!-- Răng bánh lớn (chuẩn đối xứng) -->
                    <g id="bigTooth">
                        <rect x="-6.5" y="-47" width="13" height="15" rx="3" fill="url(#blueGearGrad)" />
                    </g>
                    <!-- Răng bánh cam -->
                    <g id="amberTooth">
                        <rect x="-5" y="-33" width="10" height="11" rx="2.5" fill="url(#amberGearGrad)" />
                    </g>
                    <!-- Răng bánh bạc -->
                    <g id="silverTooth">
                        <rect x="-4" y="-26" width="8" height="9" rx="2" fill="url(#silverGearGrad)" />
                    </g>
                </defs>

                <!-- Vòng quỹ đạo kỹ thuật chấm bi xoay nhẹ -->
                <circle cx="80" cy="85" r="70" stroke="#cbd5e1" stroke-width="1.2" stroke-dasharray="3 4" opacity="0.6" class="orbit-ring" />
                <circle cx="80" cy="85" r="58" stroke="#e2e8f0" stroke-width="1" opacity="0.5" />

                <!-- 1. BÁNH RĂNG LỚN (XANH DƯƠNG BẢO TÍN - 8 RĂNG) -->
                <g transform="translate(80, 85)">
                    <g class="gear-rotate-cw" filter="url(#gearShadow)">
                        <!-- 8 Răng chuẩn tuyệt đối 45 độ -->
                        <use href="#bigTooth" />
                        <use href="#bigTooth" transform="rotate(45)" />
                        <use href="#bigTooth" transform="rotate(90)" />
                        <use href="#bigTooth" transform="rotate(135)" />
                        <use href="#bigTooth" transform="rotate(180)" />
                        <use href="#bigTooth" transform="rotate(225)" />
                        <use href="#bigTooth" transform="rotate(270)" />
                        <use href="#bigTooth" transform="rotate(315)" />

                        <!-- Vành đĩa bánh răng -->
                        <circle r="39" fill="url(#blueGearGrad)" />
                        <circle r="32" fill="none" stroke="#93c5fd" stroke-width="2" opacity="0.75" />

                        <!-- 4 Lỗ thoát tải cơ khí công nghiệp -->
                        <circle cx="0" cy="-21" r="5.5" fill="#ffffff" stroke="#bfdbfe" stroke-width="1.2" />
                        <circle cx="21" cy="0" r="5.5" fill="#ffffff" stroke="#bfdbfe" stroke-width="1.2" />
                        <circle cx="0" cy="21" r="5.5" fill="#ffffff" stroke="#bfdbfe" stroke-width="1.2" />
                        <circle cx="-21" cy="0" r="5.5" fill="#ffffff" stroke="#bfdbfe" stroke-width="1.2" />

                        <!-- Trục tâm và ổ bi mạ bạc -->
                        <circle r="14" fill="#1e3a8a" />
                        <circle r="9" fill="#2563eb" />
                        <circle r="5" fill="#ffffff" />
                        <circle r="2.5" fill="#1d4ed8" />
                    </g>
                </g>

                <!-- 2. BÁNH RĂNG PHỤ ĂN KHỚP (CAM HỔ PHÁCH - 6 RĂNG) -->
                <g transform="translate(144, 114)">
                    <g class="gear-rotate-ccw" filter="url(#gearShadow)">
                        <use href="#amberTooth" />
                        <use href="#amberTooth" transform="rotate(60)" />
                        <use href="#amberTooth" transform="rotate(120)" />
                        <use href="#amberTooth" transform="rotate(180)" />
                        <use href="#amberTooth" transform="rotate(240)" />
                        <use href="#amberTooth" transform="rotate(300)" />

                        <circle r="27" fill="url(#amberGearGrad)" />
                        <circle r="21" fill="none" stroke="#fef08a" stroke-width="1.8" opacity="0.8" />

                        <!-- 3 Lỗ cơ khí giảm trọng lượng -->
                        <circle cx="0" cy="-12" r="3.8" fill="#ffffff" stroke="#fde047" stroke-width="1" />
                        <circle cx="10.4" cy="6" r="3.8" fill="#ffffff" stroke="#fde047" stroke-width="1" />
                        <circle cx="-10.4" cy="6" r="3.8" fill="#ffffff" stroke="#fde047" stroke-width="1" />

                        <!-- Trục tâm cam -->
                        <circle r="9" fill="#9a3412" />
                        <circle r="5" fill="#ffffff" />
                        <circle r="2" fill="#d97706" />
                    </g>
                </g>

                <!-- 3. BÁNH RĂNG NHỎ (BẠC TITAN - 6 RĂNG) -->
                <g transform="translate(138, 48)">
                    <g class="gear-rotate-ccw-fast" filter="url(#gearShadow)">
                        <use href="#silverTooth" />
                        <use href="#silverTooth" transform="rotate(60)" />
                        <use href="#silverTooth" transform="rotate(120)" />
                        <use href="#silverTooth" transform="rotate(180)" />
                        <use href="#silverTooth" transform="rotate(240)" />
                        <use href="#silverTooth" transform="rotate(300)" />

                        <circle r="21" fill="url(#silverGearGrad)" />
                        <circle r="15" fill="none" stroke="#f1f5f9" stroke-width="1.5" opacity="0.8" />
                        <circle r="7" fill="#334155" />
                        <circle r="3.5" fill="#ffffff" />
                    </g>
                </g>

                <!-- Tia sáng lấp lánh nhẹ (Sparkles) -->
                <g class="sparkle sparkle-1" transform="translate(36, 42)">
                    <path d="M0 -7 Q0 0 7 0 Q0 0 0 7 Q0 0 -7 0 Q0 0 0 -7Z" fill="#38bdf8" />
                </g>
                <g class="sparkle sparkle-2" transform="translate(186, 75)">
                    <path d="M0 -6 Q0 0 6 0 Q0 0 0 6 Q0 0 -6 0 Q0 0 0 -6Z" fill="#f59e0b" />
                </g>
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