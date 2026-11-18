<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Banner Positions
    |--------------------------------------------------------------------------
    |
    | Danh sách các vị trí hiển thị banner trên website
    |
    */

    'positions' => [
        '1' => 'Trang chủ - Banner chính (Hero Slider 16:9)',
        '2' => 'Trang chủ - Banner phụ 1',
        '3' => 'Trang chủ - Banner phụ 2',
        'homepage_banner_parent' => 'Trang chủ - Banner chính (Slider)',
        'homepage_banner_children' => 'Trang chủ - Banner phụ (Bên phải)',
        'homepage' => 'Trang chủ (Tổng quát)',
        'sidebar' => 'Sidebar',
        'footer' => 'Footer',
        'header' => 'Header',
        'category' => 'Danh mục',
        'product' => 'Trang sản phẩm',
        'post' => 'Trang bài viết',
    ],

    /*
    |--------------------------------------------------------------------------
    | Position Badges
    |--------------------------------------------------------------------------
    |
    | Màu sắc badge cho từng vị trí
    |
    */

    'position_badges' => [
        '1' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
        '2' => ['bg' => '#bfdbfe', 'text' => '#1e3a8a'],
        '3' => ['bg' => '#bfdbfe', 'text' => '#1e3a8a'],
        'homepage_banner_parent' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
        'homepage_banner_children' => ['bg' => '#bfdbfe', 'text' => '#1e3a8a'],
        'homepage' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
        'sidebar' => ['bg' => '#fef3c7', 'text' => '#92400e'],
        'footer' => ['bg' => '#e0e7ff', 'text' => '#3730a3'],
        'header' => ['bg' => '#fce7f3', 'text' => '#9f1239'],
        'category' => ['bg' => '#dcfce7', 'text' => '#166534'],
        'product' => ['bg' => '#fef2f2', 'text' => '#991b1b'],
        'post' => ['bg' => '#f0fdf4', 'text' => '#14532d'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Settings
    |--------------------------------------------------------------------------
    |
    | Cấu hình cho việc upload và lưu trữ hình ảnh banner
    |
    */

    'image' => [
        'path' => 'clients/assets/img/banners',
        'allowed_types' => ['jpg', 'jpeg', 'png', 'webp'],
        'max_size' => 5120, // KB (5MB)
        'desktop' => [
            // Không resize/crop, giữ nguyên kích thước gốc khi upload
            'width' => 1920,
            'height' => 1080,
        ],
        'mobile' => [
            // Không resize/crop, giữ nguyên kích thước gốc khi upload
            'width' => 768,
            'height' => 432,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    |
    | Các giá trị mặc định cho banner
    |
    */

    'defaults' => [
        'target' => '_blank',
        'is_active' => true,
        'order' => 0,
    ],
];
