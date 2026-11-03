<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') | {{ $settings->site_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('admins/css/custom.css') }}">
    @stack('styles')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f7fa;
            color: #333;
            display: flex;
            min-height: 100vh;
        }
        
        /* Modern Sidebar Design (Chuẩn 100% nobifashion.vn) */
        .sidebar {
            width: 260px;
            background: #ffffff;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.04);
            z-index: 1000;
            border-right: 1px solid #e2e8f0;
            transition: transform 0.25s ease, width 0.25s ease;
        }
        .sidebar.collapsed {
            transform: translateX(-100%);
        }
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 4px;
        }
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }
        .sidebar-header {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 70px;
        }
        .sidebar-header img {
            max-width: 170px;
            max-height: 46px;
            height: auto;
            object-fit: contain;
        }
        .sidebar-header h1 {
            font-size: 16px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 4px;
            color: #1e293b;
        }
        .sidebar-header p {
            font-size: 11.5px;
            color: #64748b;
        }
        .sidebar-menu {
            padding: 8px 0 40px 0;
        }
        .menu-section {
            padding: 18px 18px 6px 18px;
            font-size: 11px;
            text-transform: uppercase;
            color: #94a3b8;
            font-weight: 700;
            letter-spacing: 0.07em;
            user-select: none;
        }
        .menu-group {
            margin-bottom: 2px;
        }
        .menu-group-header {
            display: flex;
            align-items: center;
            margin: 2px 10px;
            padding: 8.5px 12px;
            border-radius: 8px;
            color: #475569;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
            font-size: 13.5px;
            font-weight: 500;
            user-select: none;
        }
        .menu-group-header:hover {
            background: #f8fafc;
            color: #0f172a;
        }
        .menu-group-header.expanded {
            background: #f8fafc;
            color: #0f172a;
            font-weight: 600;
        }
        .menu-group-header .menu-arrow {
            margin-left: auto;
            color: #94a3b8;
            transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), color 0.15s ease;
            flex-shrink: 0;
        }
        .menu-group-header.expanded .menu-arrow {
            transform: rotate(90deg);
            color: #2563eb;
        }
        .menu-group-items {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.28s cubic-bezier(0.4, 0, 0.2, 1);
            margin: 2px 10px 4px 22px;
            padding-left: 12px;
            border-left: 1.5px solid #e2e8f0;
        }
        .menu-group-items.expanded {
            max-height: 1000px;
        }
        .menu-sub-item {
            position: relative;
            display: flex;
            align-items: center;
            padding: 6.5px 10px 6.5px 14px;
            margin: 2px 0;
            font-size: 13px;
            color: #64748b;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.15s ease;
            font-weight: 400;
        }
        .menu-sub-item::before {
            content: '';
            position: absolute;
            left: 2px;
            width: 4.5px;
            height: 4.5px;
            border-radius: 50%;
            background: #cbd5e1;
            transition: all 0.15s ease;
        }
        .menu-sub-item:hover {
            color: #0f172a;
            background: #f8fafc;
        }
        .menu-sub-item:hover::before {
            background: #64748b;
            transform: scale(1.3);
        }
        .menu-sub-item.active {
            color: #2563eb;
            background: #eff6ff;
            font-weight: 600;
        }
        .menu-sub-item.active::before {
            background: #2563eb;
            transform: scale(1.3);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
        }
        .menu-item {
            display: flex;
            align-items: center;
            margin: 2px 10px;
            padding: 8.5px 12px;
            border-radius: 8px;
            color: #475569;
            text-decoration: none;
            transition: all 0.15s ease;
            font-size: 13.5px;
            font-weight: 500;
        }
        .menu-item:hover {
            background: #f8fafc;
            color: #0f172a;
        }
        .menu-item.active {
            background: #eff6ff;
            color: #2563eb;
            font-weight: 600;
        }
        .menu-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            margin-right: 11px;
            flex-shrink: 0;
            color: #64748b;
            transition: color 0.15s ease;
        }
        .menu-item:hover .menu-icon,
        .menu-group-header:hover .menu-icon {
            color: #0f172a;
        }
        .menu-item.active .menu-icon,
        .menu-group-header.expanded .menu-icon {
            color: #2563eb;
        }
        .menu-badge {
            font-size: 11px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 9999px;
            margin-left: auto;
            line-height: 1.2;
            flex-shrink: 0;
        }
        .menu-badge-warning {
            background: #fef3c7;
            color: #b45309;
        }
        .menu-badge-danger {
            background: #fee2e2;
            color: #b91c1c;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 14px 20px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        .main-content.sidebar-collapsed {
            margin-left: 0;
        }
        
        /* Modern Compact Top Bar */
        .top-bar {
            background: #ffffff;
            padding: 7px 16px;
            border-radius: 8px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
            border: 1px solid #e2e8f0;
            margin-bottom: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 46px;
        }
        .top-bar-title {
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
            letter-spacing: -0.01em;
        }
        .top-bar-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .menu-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
            width: 32px;
            height: 32px;
            padding: 0;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.15s ease;
            margin-right: 12px;
            flex-shrink: 0;
        }
        .menu-toggle:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #cbd5e1;
        }
        .notification-bell {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #64748b;
            transition: all 0.15s ease;
            text-decoration: none;
        }
        .notification-bell:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #cbd5e1;
        }
        .notification-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #ef4444;
            color: #ffffff;
            border-radius: 9999px;
            min-width: 16px;
            height: 16px;
            font-size: 10px;
            font-weight: 700;
            padding: 0 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            border: 1.5px solid #ffffff;
        }
        .user-info {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 3px 10px 3px 4px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
        }
        .user-avatar {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 600;
            font-size: 11px;
            flex-shrink: 0;
        }
        .user-name {
            font-size: 13px;
            font-weight: 500;
            color: #334155;
            line-height: 1.2;
            max-width: 140px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .topbar-logout-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            transition: all 0.15s ease;
            padding: 0;
            margin-left: 2px;
        }
        .topbar-logout-btn:hover {
            background: #fee2e2;
            color: #ef4444;
        }
        
        /* Content Area */
        .content-area {
            background: transparent;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0 !important;
            }
            .main-content.sidebar-collapsed {
                margin-left: 0 !important;
            }
        }
        .menu-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f3f5;
            color: #495057;
            border: 1px solid #dee2e6;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.2s;
            margin-right: 15px;
        }
        .menu-toggle:hover {
            background: #e9ecef;
            border-color: #ced4da;
        }
        
        /* Pagination Container */
        nav[role="navigation"] {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        /* Mobile pagination buttons */
        nav[role="navigation"] > div:first-child {
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }
        
        nav[role="navigation"] > div:first-child a,
        nav[role="navigation"] > div:first-child span {
            padding: 10px 20px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
            color: #475569;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
        }
        
        nav[role="navigation"] > div:first-child a:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #334155;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        nav[role="navigation"] > div:first-child span.cursor-default {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f8f9fa;
        }
        
        /* Desktop pagination container */
        nav[role="navigation"] > div:last-child {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        /* Showing text */
        nav[role="navigation"] p.text-sm {
            color: #64748b;
            font-size: 14px;
            font-weight: 400;
            margin: 0;
        }
        
        nav[role="navigation"] p.text-sm .font-medium {
            color: #334155;
            font-weight: 600;
        }
        
        /* Pagination buttons container - Target by multiple attributes */
        nav[role="navigation"] span.relative.z-0 {
            display: inline-flex;
            align-items: center;
            gap: 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        /* All pagination buttons */
        nav[role="navigation"] span.relative.z-0 > * {
            margin: 0;
        }
        
        nav[role="navigation"] span.relative.z-0 a,
        nav[role="navigation"] span.relative.z-0 span {
            padding: 10px 16px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #475569;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            border-right: none;
        }
        
        nav[role="navigation"] span.relative.z-0 > *:first-child {
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }
        
        nav[role="navigation"] span.relative.z-0 > *:last-child {
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
            border-right: 1px solid #e2e8f0;
        }
        
        /* Hover states */
        nav[role="navigation"] span.relative.z-0 a:hover {
            background: #f1f5f9;
            color: #334155;
            z-index: 1;
            border-color: #cbd5e1;
        }
        
        /* Active page - Beautiful gradient */
        nav[role="navigation"] span[aria-current="page"] span,
        nav[role="navigation"] span.cursor-default:not([aria-disabled]) {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
            color: #fff !important;
            border-color: #6366f1 !important;
            font-weight: 600;
            z-index: 2;
            box-shadow: 0 2px 4px rgba(99, 102, 241, 0.3);
        }
        
        /* Disabled buttons */
        nav[role="navigation"] span[aria-disabled="true"] span {
            opacity: 0.4;
            cursor: not-allowed;
            background: #f8f9fa !important;
            color: #94a3b8 !important;
        }
        
        /* SVG icons in buttons */
        nav[role="navigation"] svg {
            width: 20px;
            height: 20px;
        }
        
        /* Focus states for accessibility */
        nav[role="navigation"] a:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            z-index: 1;
        }

        .tox-tinymce {
            min-height: 500px;
        }

        /* CKEditor 5 Styles & Disable Browser Spellcheck Red Squiggly Lines */
        ::spelling-error {
            text-decoration: none !important;
            -webkit-text-decoration-line: none !important;
        }
        ::grammar-error {
            text-decoration: none !important;
            -webkit-text-decoration-line: none !important;
        }
        .ck-editor__main > .ck-editor__editable,
        .ck-editor__editable_inline:not(.ck-editor__nested-editable) {
            min-height: 450px;
            outline: none !important;
        }
        .ck-editor__editable *,
        .ck-editor__editable_inline *,
        .ck-content * {
            text-decoration-line: none;
        }
        .ck-editor__editable a,
        .ck-editor__editable_inline a,
        .ck-content a {
            text-decoration: underline;
        }
        
        /* Fit content cho figcaption của ảnh trong CKEditor 5 */
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
        
        /* Notification Bell */
        .notification-bell {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            transition: all 0.2s;
            text-decoration: none;
        }
        .notification-bell:hover {
            background: #e9ecef;
            border-color: #ced4da;
            transform: scale(1.05);
        }
        .notification-badge {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }
        
        /* Responsive design */
        @media (max-width: 640px) {
            nav[role="navigation"] > div:last-child {
                flex-direction: column;
                align-items: stretch;
            }
            
            nav[role="navigation"] span.relative.z-0 {
                justify-content: center;
                flex-wrap: wrap;
            }
            
            nav[role="navigation"] p.text-sm {
                text-align: center;
                width: 100%;
            }
        }
    </style>
    <style>
        .media-picker-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 8px;
            min-height: 360px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        .media-picker-card {
            background: #fff;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: all .15s ease;
            position: relative;
        }
        .media-picker-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 2px 8px rgba(59,130,246,0.15);
        }
        .media-picker-card.selected {
            border-color: #3b82f6;
            background: #eff6ff;
        }
        .media-picker-card {
            position: relative;
        }
        .media-picker-checkbox-wrapper {
            position: absolute;
            top: 8px;
            left: 8px;
            z-index: 10;
            background: rgba(255,255,255,0.9);
            border-radius: 4px;
            padding: 2px;
        }
        .media-picker-context-menu {
            position: fixed;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            min-width: 180px;
            padding: 4px 0;
            display: none;
        }
        .media-picker-context-menu-item {
            padding: 8px 16px;
            cursor: pointer;
            font-size: 14px;
            color: #334155;
            transition: background 0.15s;
        }
        .media-picker-context-menu-item:hover {
            background: #f1f5f9;
        }
        .media-picker-context-menu-divider {
            height: 1px;
            background: #e2e8f0;
            margin: 4px 0;
        }
        .media-picker-context-menu-item[data-action="delete"] {
            color: #dc2626;
        }
        .media-picker-context-menu-item[data-action="delete"]:hover {
            background: #fee2e2;
        }
        .media-picker-thumb {
            width: 100%;
            height: 100px;
            position: relative;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .media-picker-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .media-picker-meta {
            padding: 6px 8px;
        }
        .media-picker-name {
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.3;
        }
        .media-picker-size {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }
        .media-picker-toolbar input {
            min-width: 220px;
        }
    </style>
    @stack('head')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div><img src="{{ asset('clients/assets/img/business/'. $settings->site_logo) }}" alt="{{ $settings->site_name }}" style="width: 100%; height: 100%;"></div>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-section">Tổng Quan</div>
            <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="menu-icon">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="14" width="7" height="7" rx="1.5"></rect><rect x="3" y="14" width="7" height="7" rx="1.5"></rect></svg>
                </span>
                <span>Dashboard</span>
            </a>
            
            <div class="menu-section">Sản Phẩm</div>
            <div class="menu-group">
                <div class="menu-group-header {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.featured-products.*') ? 'expanded' : '' }}" data-group="products">
                    <span class="menu-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"></path><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path><path d="m3.3 7 8.7 5 8.7-5"></path><path d="M12 22V12"></path></svg>
                    </span>
                    <span>Sản Phẩm</span>
                    <svg class="menu-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
                <div class="menu-group-items {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.featured-products.*') ? 'expanded' : '' }}" id="products-group">
                    <a href="{{ route('admin.products.index') }}" class="menu-sub-item {{ request()->routeIs('admin.products.index') && !request('status') ? 'active' : '' }}">
                        Tất cả sản phẩm
                    </a>
                    <a href="{{ route('admin.products.index', ['status' => 'active']) }}" class="menu-sub-item {{ request()->routeIs('admin.products.index') && request('status') === 'active' ? 'active' : '' }}">
                        Đang bán
                    </a>
                    <a href="{{ route('admin.products.index', ['status' => 'inactive']) }}" class="menu-sub-item {{ request()->routeIs('admin.products.index') && request('status') === 'inactive' ? 'active' : '' }}">
                        Tạm ẩn
                    </a>
                    @if(Route::has('admin.featured-products.index'))
                        <a href="{{ route('admin.featured-products.index') }}" class="menu-sub-item {{ request()->routeIs('admin.featured-products*') ? 'active' : '' }}">
                            Sản phẩm phổ biến
                        </a>
                    @endif
                </div>
            </div>
            @if(Route::has('admin.categories.index'))
                <a href="{{ route('admin.categories.index') }}" class="menu-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                    </span>
                    <span>Danh Mục</span>
                </a>
            @endif
            @if(Route::has('admin.brands.index'))
                <a href="{{ route('admin.brands.index') }}" class="menu-item {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"></circle><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"></path></svg>
                    </span>
                    <span>Hãng</span>
                </a>
            @endif
            @if(Route::has('admin.support-staff.index'))
                <a href="{{ route('admin.support-staff.index') }}" class="menu-item {{ request()->routeIs('admin.support-staff.*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                    </span>
                    <span>CSKH</span>
                </a>
            @endif
            @if(Route::has('admin.popup-contents.index'))
                <a href="{{ route('admin.popup-contents.index') }}" class="menu-item {{ request()->routeIs('admin.popup-contents.*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                    </span>
                    <span>Popup</span>
                </a>
            @endif
            
            <div class="menu-section">Nội Dung</div>
            @if(Route::has('admin.posts.index'))
                <div class="menu-group">
                    <div class="menu-group-header {{ request()->routeIs('admin.posts.*') ? 'expanded' : '' }}" data-group="posts">
                        <span class="menu-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        </span>
                        <span>Bài viết</span>
                        <svg class="menu-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                    <div class="menu-group-items {{ request()->routeIs('admin.posts.*') ? 'expanded' : '' }}" id="posts-group">
                        <a href="{{ route('admin.posts.index') }}" class="menu-sub-item {{ request()->routeIs('admin.posts.index') ? 'active' : '' }}">
                            Tất cả bài viết
                        </a>
                        <a href="{{ route('admin.posts.create') }}" class="menu-sub-item {{ request()->routeIs('admin.posts.create') ? 'active' : '' }}">
                            Thêm bài viết mới
                        </a>
                    </div>
                </div>
            @endif
            @if(Route::has('admin.comments.index'))
                <a href="{{ route('admin.comments.index') }}" class="menu-item {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    </span>
                    <span>Bình luận</span>
                </a>
            @endif
            @if(Route::has('admin.tags.index'))
                <a href="{{ route('admin.tags.index') }}" class="menu-item {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="9" x2="20" y2="9"></line><line x1="4" y1="15" x2="20" y2="15"></line><line x1="10" y1="3" x2="8" y2="21"></line><line x1="16" y1="3" x2="14" y2="21"></line></svg>
                    </span>
                    <span>Thẻ (Tags)</span>
                </a>
            @endif
            @if(Route::has('admin.banners.index'))
                <a href="{{ route('admin.banners.index') }}" class="menu-item {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg>
                    </span>
                    <span>Banner</span>
                </a>
            @endif
            @if(Route::has('admin.media.index'))
                <a href="{{ route('admin.media.index') }}" class="menu-item {{ request()->routeIs('admin.media.*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"></path></svg>
                    </span>
                    <span>Media</span>
                </a>
            @endif
            @if(Route::has('admin.sitemap.index'))
                <a href="{{ route('admin.sitemap.index') }}" class="menu-item {{ request()->routeIs('admin.sitemap.*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="18" r="3"></circle><circle cx="6" cy="6" r="3"></circle><circle cx="18" cy="18" r="3"></circle><path d="M6 9v3a3 3 0 0 0 3 3h6"></path></svg>
                    </span>
                    <span>Sitemap</span>
                </a>
            @endif
            
            <div class="menu-section">Đơn Hàng</div>
            @if(Route::has('admin.orders.index'))
                <div class="menu-group">
                    <div class="menu-group-header {{ request()->routeIs('admin.orders.*') || request()->routeIs('admin.quotes.*') ? 'expanded' : '' }}" data-group="orders">
                        <span class="menu-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"></rect><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><path d="M12 11h4"></path><path d="M12 16h4"></path><path d="M8 11h.01"></path><path d="M8 16h.01"></path></svg>
                        </span>
                        <span>Đơn Hàng</span>
                        <svg class="menu-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                    <div class="menu-group-items {{ request()->routeIs('admin.orders.*') || request()->routeIs('admin.quotes.*') ? 'expanded' : '' }}" id="orders-group">
                        <a href="{{ route('admin.orders.index') }}" class="menu-sub-item {{ request()->routeIs('admin.orders.index') && !request('status') && !request('delivery_status') ? 'active' : '' }}">
                            Tất cả đơn hàng
                        </a>
                        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="menu-sub-item {{ request()->routeIs('admin.orders.*') && request('status') === 'pending' ? 'active' : '' }}">
                            Chờ xử lý
                        </a>
                        <a href="{{ route('admin.orders.index', ['delivery_status' => 'shipped']) }}" class="menu-sub-item {{ request()->routeIs('admin.orders.*') && request('delivery_status') === 'shipped' ? 'active' : '' }}">
                            Đang giao hàng
                        </a>
                        <a href="{{ route('admin.orders.index', ['status' => 'completed']) }}" class="menu-sub-item {{ request()->routeIs('admin.orders.*') && request('status') === 'completed' ? 'active' : '' }}">
                            Hoàn thành
                        </a>
                        @if(Route::has('admin.quotes.index'))
                            <a href="{{ route('admin.quotes.index') }}" class="menu-sub-item {{ request()->routeIs('admin.quotes.*') ? 'active' : '' }}">
                                Yêu cầu báo giá
                            </a>
                        @endif
                    </div>
                </div>
            @endif
            @if(Route::has('admin.carts.index'))
                <a href="{{ route('admin.carts.index') }}" class="menu-item {{ request()->routeIs('admin.carts.index') || (request()->routeIs('admin.carts.show') || request()->routeIs('admin.carts.edit')) ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>
                    </span>
                    <span>Giỏ Hàng</span>
                </a>
            @endif
            @if(Route::has('admin.carts.create-order.index'))
                <a href="{{ route('admin.carts.create-order.index') }}" class="menu-item {{ request()->routeIs('admin.carts.create-order.*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                    </span>
                    <span>Lên Đơn Hàng</span>
                </a>
            @endif
            
            <div class="menu-section">Khách Hàng</div>
            @if(Route::has('admin.accounts.index'))
                <a href="{{ route('admin.accounts.index') }}" class="menu-item {{ request()->routeIs('admin.accounts.*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </span>
                    <span>Tài khoản</span>
                </a>
            @endif
            @if(Route::has('admin.newsletters.index'))
                <a href="{{ route('admin.newsletters.index') }}" class="menu-item {{ request()->routeIs('admin.newsletters.*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    </span>
                    <span>Newsletter</span>
                    @php
                        try {
                            $pendingNewsletterCount = \App\Models\Newsletter::where('is_verified', false)->count();
                        } catch (\Exception $e) {
                            $pendingNewsletterCount = 0;
                        }
                    @endphp
                    @if($pendingNewsletterCount > 0)
                        <span class="menu-badge menu-badge-warning">{{ $pendingNewsletterCount }}</span>
                    @endif
                </a>
            @endif
            @if(Route::has('admin.quick-consultation-leads.index'))
                <a href="{{ route('admin.quick-consultation-leads.index') }}" class="menu-item {{ request()->routeIs('admin.quick-consultation-leads.*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                    </span>
                    <span>Leads Tư vấn</span>
                    @php
                        try {
                            $newLeadCount = \App\Models\QuickConsultationLead::where('is_contacted', false)->count();
                        } catch (\Exception $e) {
                            $newLeadCount = 0;
                        }
                    @endphp
                    @if($newLeadCount > 0)
                        <span class="menu-badge menu-badge-danger">{{ $newLeadCount }}</span>
                    @endif
                </a>
            @endif
            @if(Route::has('admin.contacts.index'))
                <a href="{{ route('admin.contacts.index') }}" class="menu-item {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    </span>
                    <span>Liên Hệ</span>
                    @php
                        try {
                            $newContactCount = \App\Models\Contact::where('status', 'new')->count();
                        } catch (\Exception $e) {
                            $newContactCount = 0;
                        }
                    @endphp
                    @if($newContactCount > 0)
                        <span class="menu-badge menu-badge-danger">{{ $newContactCount }}</span>
                    @endif
                </a>
            @endif
            @if(Route::has('admin.notifications.index'))
                <a href="{{ route('admin.notifications.index') }}" class="menu-item {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                    </span>
                    <span>Thông báo</span>
                </a>
            @endif
            @if(Route::has('admin.addresses.index'))
                <a href="{{ route('admin.addresses.index') }}" class="menu-item {{ request()->routeIs('admin.addresses.*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    </span>
                    <span>Địa Chỉ Giao Hàng</span>
                </a>
            @endif
            
            <div class="menu-section">Khuyến Mãi</div>
            @if(Route::has('admin.vouchers.index'))
                <div class="menu-group">
                    <div class="menu-group-header {{ request()->routeIs('admin.vouchers.*') ? 'expanded' : '' }}" data-group="vouchers">
                        <span class="menu-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="12" x="2" y="6" rx="2"></rect><circle cx="12" cy="12" r="2"></circle><path d="M6 12h.01M18 12h.01"></path></svg>
                        </span>
                        <span>Voucher</span>
                        <svg class="menu-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                    <div class="menu-group-items {{ request()->routeIs('admin.vouchers.*') ? 'expanded' : '' }}" id="vouchers-group">
                        <a href="{{ route('admin.vouchers.index') }}" class="menu-sub-item {{ request()->routeIs('admin.vouchers.index') ? 'active' : '' }}">
                            Tất cả voucher
                        </a>
                        <a href="{{ route('admin.vouchers.create') }}" class="menu-sub-item {{ request()->routeIs('admin.vouchers.create') ? 'active' : '' }}">
                            Tạo voucher mới
                        </a>
                        @if(Route::has('admin.vouchers.analytics'))
                            <a href="{{ route('admin.vouchers.analytics') }}" class="menu-sub-item {{ request()->routeIs('admin.vouchers.analytics*') ? 'active' : '' }}">
                                Analytics & Báo cáo
                            </a>
                        @endif
                    </div>
                </div>
            @endif
            
            <div class="menu-section">Báo Cáo & Phân Tích</div>
            @if(Route::has('admin.reports.index'))
                <a href="{{ route('admin.reports.index') }}" class="menu-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                    </span>
                    <span>Báo Cáo</span>
                </a>
            @endif
            
            <div class="menu-section">Hệ Thống</div>
            @if(Route::has('admin.email-templates.index'))
                <a href="{{ route('admin.email-templates.index') }}" class="menu-item {{ request()->routeIs('admin.email-templates.*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>
                    </span>
                    <span>Email Templates</span>
                </a>
            @endif
            @if(Route::has('admin.backups.index'))
                <a href="{{ route('admin.backups.index') }}" class="menu-item {{ request()->routeIs('admin.backups.*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="12" x2="2" y2="12"></line><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path><line x1="6" y1="16" x2="6.01" y2="16"></line><line x1="10" y1="16" x2="10.01" y2="16"></line></svg>
                    </span>
                    <span>Backup & Restore</span>
                </a>
            @endif
            @if(Route::has('admin.settings.index'))
                <a href="{{ route('admin.settings.index') }}" class="menu-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    </span>
                    <span>Cài Đặt</span>
                </a>
            @endif
            @if(Route::has('admin.tools.index'))
                <div class="menu-group">
                    <div class="menu-group-header {{ request()->routeIs('admin.tools.*') ? 'expanded' : '' }}" data-group="tools">
                        <span class="menu-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
                        </span>
                        <span>Tools Website</span>
                        <svg class="menu-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                    <div class="menu-group-items {{ request()->routeIs('admin.tools.*') ? 'expanded' : '' }}" id="tools-group">
                        <a href="{{ route('admin.tools.index') }}" class="menu-sub-item {{ request()->routeIs('admin.tools.index') && !request()->has('tool') && !request()->has('scroll') ? 'active' : '' }}">
                            Tất cả Tools
                        </a>
                        <a href="{{ route('admin.tools.index') }}?scroll=images#images-tool" class="menu-sub-item">
                            Xóa Ảnh Không Dùng
                        </a>
                        <a href="{{ route('admin.tools.index') }}?scroll=tags#tags-tool" class="menu-sub-item">
                            Xóa Tags Không Dùng
                        </a>
                    </div>
                </div>
            @endif
            @if(Route::has('admin.trash.index'))
                <a href="{{ route('admin.trash.index') }}" class="menu-item {{ request()->routeIs('admin.trash.*') ? 'active' : '' }}">
                    <span class="menu-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg>
                    </span>
                    <span>Thùng Rác</span>
                </a>
            @endif
            <a href="{{ url('/') }}" class="menu-item" target="_blank">
                <span class="menu-icon">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                </span>
                <span>Về Trang Chủ</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div style="display: flex; align-items: center; min-width: 0;">
                <button class="menu-toggle" id="sidebarToggle" onclick="toggleSidebar()" title="Đóng/Mở menu">
                    <span id="toggleIcon" style="font-size: 14px; line-height: 1;">☰</span>
                </button>
                <span class="top-bar-title text-truncate">@yield('page-title', 'Dashboard')</span>
            </div>
            <div class="top-bar-actions">
                {{-- Nút xem trang chủ ngoài --}}
                <a href="{{ url('/') }}" target="_blank" class="notification-bell" title="Về xem trang chủ ngoài">
                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                </a>

                {{-- Notification Bell --}}
                <div style="position: relative;">
                    <a href="{{ route('admin.notifications.index') }}" class="notification-bell" id="notificationBell" title="Thông báo">
                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                        <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
                    </a>
                </div>
                
                {{-- User Info Pill --}}
                @php
                    $user = auth('web')->user() ?? auth()->user();
                    $userInitial = $user ? strtoupper(substr($user->name ?? 'A', 0, 1)) : 'A';
                    $userName = $user ? ($user->name ?? 'Admin') : 'Admin';
                @endphp
                <div class="user-info">
                    <div class="user-avatar">{{ $userInitial }}</div>
                    <span class="user-name" title="{{ $userName }}">{{ $userName }}</span>
                    @if(Route::has('admin.logout'))
                        <form action="{{ route('admin.logout') }}" method="POST" style="display: inline; margin: 0;">
                            @csrf
                            <button type="submit" class="topbar-logout-btn" title="Đăng xuất" onclick="return confirm('Bạn có chắc chắn muốn đăng xuất?')">
                                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            @yield('content')
        </div>
    </main>
    
    <div id="custom-toast-container" class="custom-toast-container"></div>

    <style>
        .custom-toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            z-index: 9999;
            cursor: pointer;
        }

        .custom-toast {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 16px;
            color: #fff;
            max-width: 320px;
            opacity: 0;
            transform: translateX(100%);
            transition: transform 0.4s ease, opacity 0.3s ease;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .custom-toast.show {
            opacity: 1;
            transform: translateX(0);
        }

        .custom-toast.success {
            background-color: #1c9a4a;
            /* xanh lá */
        }

        .custom-toast.error {
            background-color: #ef4444;
            /* đỏ */
        }

        .custom-toast.warning {
            background-color: #f59e0b;
            /* cam */
        }

        .custom-toast.info {
            background-color: #3b82f6;
            /* xanh dương */
        }

        .custom-toast-icon {
            font-size: 18px;
        }
    </style>

    <script>
        function showCustomToast(
            message = "Thông báo!",
            type = "info",
            duration = 5000
        ) {
            const container = document.getElementById("custom-toast-container");
            const toast = document.createElement("div");
            const icon = document.createElement("span");

            toast.className = `custom-toast ${type}`;
            icon.className = "custom-toast-icon";

            // Gán biểu tượng theo loại
            const icons = {
                success: "✅",
                error: "❌",
                warning: "⚠️",
                info: "💬",
            };
            icon.textContent = icons[type] || "🔔";

            toast.appendChild(icon);
            toast.appendChild(document.createTextNode(message));
            container.appendChild(toast);

            // Kích hoạt animation
            setTimeout(() => toast.classList.add("show"), 100);

            toast.addEventListener("click", () => {
                toast.classList.remove("show");
                setTimeout(() => {
                    container.removeChild(toast);
                }, 300);
                return;
            });

            // Gỡ thông báo sau duration
            setTimeout(() => {
                toast.classList.remove("show");
                setTimeout(() => {
                    container.removeChild(toast);
                }, 300);
                return;
            }, duration);
        }
        @php
            $alerts = [
                'success' => session('success'),
                'error'   => session('error'),
                'warning' => session('warning'),
                'info'    => session('info'),
            ];
        @endphp
        document.addEventListener("DOMContentLoaded", function() {
            let alerts = [];
            @foreach ($alerts as $type => $message)
                @if ($message)
                    alerts.push({type: '{{ $type }}', message: @json($message)});
                @endif
            @endforeach

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    alerts.push({type: 'error', message: @json($error)});
                @endforeach
            @endif

            @if(request()->cookie('updated_account_success'))
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        showCustomToast(
                            "Cập nhật tài khoản thành công. Vui lòng đăng nhập lại.",
                            "success"
                        );
                    });
                </script>
            @endif

            alerts.forEach(a => showCustomToast(a.message, a.type));
        });
    </script>

    @php
        Cookie::queue(Cookie::forget('updated_account_success'));
    @endphp

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script>
        // Load and update notification count - tối ưu để tránh tốn tài nguyên
        function loadNotificationCount() {
            // Chỉ fetch nếu tab đang visible
            if (document.hidden) {
                return;
            }
            
            fetch('{{ route("admin.notifications.unread-count") }}')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const badge = document.getElementById('notificationBadge');
                    if (badge) {
                        if (data.count > 0) {
                            badge.textContent = data.count > 99 ? '99+' : data.count;
                            badge.style.display = 'flex';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                })
                .catch(error => {
                    // Chỉ log error trong development, không spam console
                    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                        console.error('Error loading notification count:', error);
                    }
                });
        }

        // Load notification count on page load
        let notificationInterval = null;
        document.addEventListener('DOMContentLoaded', function() {
            // Chỉ load notification count một lần khi trang load
            loadNotificationCount();
            
            // Chỉ auto refresh khi user đang ở trang notifications hoặc dashboard
            // Để tránh tốn tài nguyên khi không cần thiết
            const currentRoute = window.location.pathname;
            const shouldAutoRefresh = currentRoute.includes('/notifications') || 
                                     currentRoute.includes('/dashboard') ||
                                     currentRoute === '/admin' ||
                                     currentRoute === '/admin/';
            
            function startNotificationInterval() {
                // Clear interval cũ nếu có
                if (notificationInterval) {
                    clearInterval(notificationInterval);
                }
                
                // Chỉ refresh nếu tab đang visible và ở trang cần thiết
                if (!document.hidden && shouldAutoRefresh) {
                    notificationInterval = setInterval(function() {
                        // Double check khi interval chạy
                        if (!document.hidden) {
                            loadNotificationCount();
                        }
                    }, 30000);
                }
            }
            
            if (shouldAutoRefresh) {
                startNotificationInterval();
                
                // Pause khi tab không visible, resume khi visible
                document.addEventListener('visibilitychange', function() {
                    if (document.hidden) {
                        // Tab không visible - dừng refresh
                        if (notificationInterval) {
                            clearInterval(notificationInterval);
                            notificationInterval = null;
                        }
                    } else {
                        // Tab visible - resume refresh
                        startNotificationInterval();
                    }
                });
            }
            
            // Cleanup khi rời khỏi trang
            window.addEventListener('beforeunload', function() {
                if (notificationInterval) {
                    clearInterval(notificationInterval);
                }
            });
        });

        // Load sidebar state from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            const toggleIcon = document.getElementById('toggleIcon');
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('sidebar-collapsed');
                toggleIcon.textContent = '☰';
            } else {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('sidebar-collapsed');
                toggleIcon.textContent = '✕';
            }
            
            // Initialize mobile sidebar state
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('open');
            }
        });

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            const toggleIcon = document.getElementById('toggleIcon');
            const isMobile = window.innerWidth <= 768;
            
            if (isMobile) {
                // Mobile: toggle open class
                sidebar.classList.toggle('open');
            } else {
                // Desktop: toggle collapsed class
                const isCollapsed = sidebar.classList.contains('collapsed');
                
                if (isCollapsed) {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('sidebar-collapsed');
                    toggleIcon.textContent = '✕';
                    localStorage.setItem('sidebarCollapsed', 'false');
                } else {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('sidebar-collapsed');
                    toggleIcon.textContent = '☰';
                    localStorage.setItem('sidebarCollapsed', 'true');
                }
            }
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.querySelector('.menu-toggle');
            if (window.innerWidth <= 768 && sidebar.classList.contains('open')) {
                if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                    sidebar.classList.remove('open');
                }
            }
        });

        // Menu accordion functionality
        document.addEventListener('DOMContentLoaded', function() {
            const menuGroups = document.querySelectorAll('.menu-group-header');
            
            menuGroups.forEach(header => {
                header.addEventListener('click', function(e) {
                    e.preventDefault();
                    const groupId = this.getAttribute('data-group');
                    const items = document.getElementById(groupId + '-group');
                    const isExpanded = items.classList.contains('expanded');
                    
                    // Close all other groups
                    document.querySelectorAll('.menu-group-items').forEach(item => {
                        item.classList.remove('expanded');
                    });
                    document.querySelectorAll('.menu-group-header').forEach(h => {
                        h.classList.remove('expanded');
                    });
                    
                    // Toggle current group
                    if (!isExpanded) {
                        items.classList.add('expanded');
                        this.classList.add('expanded');
                    }
                });
            });

            // Auto-expand groups with active items
            document.querySelectorAll('.menu-item.active, .menu-sub-item.active').forEach(activeItem => {
                const group = activeItem.closest('.menu-group-items');
                if (group) {
                    group.classList.add('expanded');
                    const header = group.previousElementSibling;
                    if (header) {
                        header.classList.add('expanded');
                    }
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.tiny.cloud/1/{{ env('APP_KEY_TINYMCE') }}/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>

    <!-- CKEditor 5 (Giống 100% nobifashion.vn) -->
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/47.4.0/ckeditor5.css" crossorigin>
    <script src="https://cdn.ckeditor.com/ckeditor5/47.4.0/ckeditor5.umd.js" crossorigin></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/47.4.0/translations/vi.umd.js" crossorigin></script>
    <script src="{{ asset('admins/js/ckeditor-init.js') }}?v={{ file_exists(public_path('admins/js/ckeditor-init.js')) ? filemtime(public_path('admins/js/ckeditor-init.js')) : time() }}"></script>

    {{-- Popup media picker dùng chung --}}
    @include('admins.media.modal-picker')

    @stack('scripts')

    <!-- Back to Top Button -->
    <button id="backToTop" class="back-to-top" aria-label="Lên đầu trang">
        <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 640 640"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M270.8 111.8C266.3 122.5 264 134 264 145.6L264 246L112 360L112 312C112 298.7 101.3 288 88 288C74.7 288 64 298.7 64 312L64 456C64 469.3 74.7 480 88 480C101.3 480 112 469.3 112 456L112 448L264 448L264 502.4L198 555.2C194.2 558.2 192 562.8 192 567.7L192 592C192 600.8 199.2 608 208 608L296 608L296 568C296 554.7 306.7 544 320 544C333.3 544 344 554.7 344 568L344 608L432 608C440.8 608 448 600.8 448 592L448 567.7C448 562.8 445.8 558.2 442 555.2L376 502.4L376 448L528 448L528 456C528 469.3 538.7 480 552 480C565.3 480 576 469.3 576 456L576 312C576 298.7 565.3 288 552 288C538.7 288 528 298.7 528 312L528 360L376 246L376 145.6C376 134 373.7 122.5 369.2 111.8L342.1 46.8C338.4 37.8 329.7 32 320 32C310.3 32 301.6 37.8 297.8 46.8L270.7 111.8z"/></svg>
    </button>

    <style>
        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 9999;
            padding: 0;
        }

        .back-to-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .back-to-top:active {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .back-to-top.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .back-to-top svg {
            width: 24px;
            height: 24px;
            transition: transform 0.3s ease;
            fill: rgb(255, 255, 255);
        }

        .back-to-top:hover svg {
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .back-to-top {
                width: 45px;
                height: 45px;
                bottom: 15px;
                right: 15px;
            }

            .back-to-top svg {
                width: 20px;
                height: 20px;
            }
        }
    </style>

    <script>
        (function() {
            const backToTopButton = document.getElementById('backToTop');
            
            if (!backToTopButton) return;

            // Show/hide button based on scroll position
            function toggleBackToTop() {
                if (window.pageYOffset > 300) {
                    backToTopButton.classList.add('show');
                } else {
                    backToTopButton.classList.remove('show');
                }
            }

            // Scroll to top smoothly
            function scrollToTop() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }

            // Event listeners
            window.addEventListener('scroll', toggleBackToTop);
            backToTopButton.addEventListener('click', scrollToTop);

            // Initial check
            toggleBackToTop();
        })();
    </script>
</body>
</html>

