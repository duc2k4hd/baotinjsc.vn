@extends('clients.layouts.master')

@section('title', $pageTitle ?? 'Blog - ' . ($settings->site_name ?? 'Bảo Tín JSC'))

@section('head')
    <link rel="stylesheet" href="{{ asset('clients/assets/css/blog.css?v='.time()) }}">
    
    @php
        $canonicalUrl = route('client.blog.index');
        if ($activeCategory) {
            $canonicalUrl = route('client.blog.index', ['category' => $activeCategory->slug]);
        } elseif ($activeTag) {
            $canonicalUrl = route('client.blog.index', ['tag' => $activeTag->slug]);
        } elseif ($searchTerm) {
            $canonicalUrl = route('client.blog.index', ['q' => $searchTerm]);
        }
    @endphp

    <!-- Meta Tags -->
    <meta name="description" content="{{ $pageDescription ?? 'Blog chia sẻ kiến thức kỹ thuật tự động hóa' }}">
    <meta name="keywords" content="{{ $pageKeywords ?? 'blog, tin tức, tự động hóa' }}">
    @if($shouldNoindex ?? false)
        <meta name="robots" content="noindex, follow" />
    @else
        <meta name="robots" content="index, follow, max-snippet:-1, max-video-preview:-1, max-image-preview:large" />
    @endif
    
    <!-- 📅 Date -->
    <meta http-equiv="date" content="{{ now()->format('d/m/Y') }}" />
    
    <!-- 🌐 Open Graph -->
    <meta property="og:title" content="{{ $pageTitle ?? 'Blog - ' . ($settings->site_name ?? 'Bảo Tín JSC') }}">
    <meta property="og:description" content="{{ $pageDescription ?? 'Blog chia sẻ kiến thức kỹ thuật tự động hóa' }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $settings->site_name ?? $settings->subname ?? 'Bảo Tín JSC' }}">
    <meta property="og:locale" content="vi_VN">

    <!-- 🐦 Twitter -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $pageTitle ?? 'Blog - ' . ($settings->site_name ?? 'Bảo Tín JSC') }}">
    <meta name="twitter:description" content="{{ $pageDescription ?? 'Blog chia sẻ kiến thức kỹ thuật tự động hóa' }}">

    <!-- 🔗 Canonical & hreflang -->
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="alternate" hreflang="vi" href="{{ $canonicalUrl }}">
    <link rel="alternate" hreflang="x-default" href="{{ $canonicalUrl }}">
@endsection

@section('schema')
@if(isset($schemaData) && is_array($schemaData))
    @foreach($schemaData as $schema)
        <script type="application/ld+json">
        {!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>
    @endforeach
@endif
@endsection

@section('content')
<!-- MAIN -->
<main class="blog_main">
    <!-- Breadcrumb -->
    <div class="blog_container">
        <div class="blog_breadcrumb">
            <a href="{{ route('client.home.index') }}">Trang chủ</a>
            <span>/</span>
            <span>Tin tức</span>
            @if($activeCategory ?? null)
                <span>/</span>
                <span>{{ $activeCategory->name }}</span>
            @endif
            @if($activeTag ?? null)
                <span>/</span>
                <span>#{{ $activeTag->name }}</span>
            @endif
            @if($searchTerm ?? null)
                <span>/</span>
                <span>Tìm kiếm: {{ $searchTerm }}</span>
            @endif
        </div>
    </div>

    <!-- Hero Section -->
    @if(isset($featuredPosts) && $featuredPosts->isNotEmpty())
        <section class="blog_hero">
            <div class="blog_container">
                <div class="blog_hero_content">
                    @php
                        $heroPost = $featuredPosts->first();
                        $heroImage = $heroPost->coverImagePath() 
                            ? asset($heroPost->coverImagePath()) 
                            : asset('clients/assets/img/posts/no-image.webp');
                    @endphp
                    <div class="blog_hero_left">
                        <h1 class="blog_hero_title">{{ $heroPost->title }}</h1>
                        <p class="blog_hero_desc">
                            {!! $heroPost->meta_description ?? $heroPost->excerpt ?? '' !!}
                        </p>
                        <a href="{{ route('client.blog.show', $heroPost) }}" class="blog_read_more">ĐỌC TIẾP →</a>
                        <div class="blog_hero_pagination">
                            @foreach($featuredPosts->take(3) as $index => $post)
                                <span class="blog_pagination_line {{ $index === 0 ? 'blog_pagination_line_active' : '' }}" 
                                      data-slide="{{ $index }}"
                                      data-post-id="{{ $post->id }}"></span>
                            @endforeach
                        </div>
                    </div>
                    <div class="blog_hero_right">
                        <a href="{{ route('client.blog.show', $heroPost) }}">
                            <img src="{{ $heroImage }}" alt="{{ $heroPost->title }}" class="blog_hero_image">
                        </a>
                        <div class="blog_hero_caption">
                            <h2>{{ $heroPost->title }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Latest Posts -->
    @if(isset($posts) && $posts->count() > 0)
        <section class="blog_latest">
            <div class="blog_container">
                <div class="blog_section_header">
                    <h2 class="blog_section_title">Bài viết mới nhất</h2>
                    <div class="blog_nav_arrows">
                        <button class="blog_arrow_btn blog_arrow_prev">‹</button>
                        <button class="blog_arrow_btn blog_arrow_next">›</button>
                    </div>
                </div>
                <div class="blog_posts_grid" id="latestPosts">
                    @foreach($posts->take(4) as $post)
                        @php
                            $postImage = $post->coverImagePath() 
                                ? asset($post->coverImagePath()) 
                                : asset('clients/assets/img/posts/no-image.webp');
                            $postDate = $post->published_at ?? $post->created_at;
                        @endphp
                        <article class="blog_post_card">
                            <a href="{{ route('client.blog.show', $post) }}">
                                <div class="blog_post_image">
                                    <img src="{{ $postImage }}" alt="{{ $post->title }}">
                                </div>
                                <h3 class="blog_post_title">{{ $post->title }}</h3>
                                <div class="blog_post_date">
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <circle cx="7" cy="7" r="6" stroke="currentColor"/>
                                        <path d="M7 3V7L9 9" stroke="currentColor" stroke-linecap="round"/>
                                    </svg>
                                    <span>{{ $postDate->format('d/m/Y') }}</span>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- All Posts -->
    <section class="blog_all_posts">
        <div class="blog_container">
            <h2 class="blog_section_title">{{ $heroHeading ?? 'Danh mục bài viết' }}</h2>
            @if($heroSubheading ?? null)
                <p style="color: #666; margin-bottom: 30px; font-size: 15px;">{{ $heroSubheading }}</p>
            @endif
            
            @if(isset($sidebarCategories) && $sidebarCategories->isNotEmpty())
                <div class="blog_filter_tabs">
                    <a href="{{ route('client.blog.index') }}" 
                       class="blog_tab_btn {{ !($activeCategory ?? null) ? 'blog_tab_btn_active' : '' }}">
                        Tất cả
                    </a>
                    @foreach($sidebarCategories as $category)
                        <a href="{{ route('client.blog.index', ['category' => $category->slug]) }}" 
                           class="blog_tab_btn {{ ($activeCategory->id ?? null) === $category->id ? 'blog_tab_btn_active' : '' }}">
                            {{ $category->name }} ({{ $category->posts_count ?? 0 }})
                        </a>
                    @endforeach
                </div>
            @endif
            
            @if(isset($posts) && $posts->count() > 0)
                <div class="blog_posts_grid" id="allPosts">
                    @foreach($posts as $post)
                        @php
                            $postImage = $post->coverImagePath() 
                                ? asset($post->coverImagePath()) 
                                : asset('clients/assets/img/posts/no-image.webp');
                            $postDate = $post->published_at ?? $post->created_at;
                        @endphp
                        <article class="blog_post_card">
                            <a href="{{ route('client.blog.show', $post) }}">
                                <div class="blog_post_image">
                                    <img src="{{ $postImage }}" alt="{{ $post->title }}">
                                </div>
                                <h3 class="blog_post_title">{{ $post->title }}</h3>
                                <div class="blog_post_date">
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <circle cx="7" cy="7" r="6" stroke="currentColor"/>
                                        <path d="M7 3V7L9 9" stroke="currentColor" stroke-linecap="round"/>
                                    </svg>
                                    <span>{{ $postDate->format('d/m/Y') }}</span>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($posts->hasPages())
                    <div class="blog_pagination_wrapper" style="margin-top: 50px; display: flex; justify-content: center;">
                        {{ $posts->links('pagination.custom') }}
                    </div>
                @endif
            @else
                <div class="blog_no_posts" style="text-align: center; padding: 60px 20px; color: #666;">
                    <p style="font-size: 18px; margin-bottom: 10px;">Không tìm thấy bài viết nào</p>
                    <p style="font-size: 14px;">Vui lòng thử lại với từ khóa hoặc danh mục khác</p>
                </div>
            @endif
        </div>
    </section>
</main>

<script src="{{ asset('clients/assets/js/blog.js?v='.time()) }}"></script>
@endsection
