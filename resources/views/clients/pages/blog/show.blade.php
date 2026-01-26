@extends('clients.layouts.master')

@section('title', $pageTitle ?? ($post->title ?? 'Blog') . ' - ' . ($settings->site_name ?? 'Bảo Tín JSC'))

@section('head')
    <link rel="stylesheet" href="{{ asset('clients/assets/css/blog-single.css?v='.time()) }}">
    
    @php
        $canonicalUrl = $canonicalUrl ?? route('client.blog.show', $post);
        $coverImage = $coverAsset ?? ($post->coverImagePath() ? asset($post->coverImagePath()) : asset('clients/assets/img/posts/no-image.webp'));
    @endphp

    <!-- Meta Tags -->
    <meta name="description" content="{{ $pageDescription ?? $post->excerpt ?? '' }}">
    <meta name="keywords" content="{{ $pageKeywords ?? '' }}">
    <meta name="robots" content="index, follow, max-snippet:-1, max-video-preview:-1, max-image-preview:large" />
    
    <!-- 📅 Date -->
    <meta http-equiv="date" content="{{ ($post->published_at ?? $post->created_at)->format('d/m/Y') }}" />
    
    <!-- 🌐 Open Graph -->
    <meta property="og:title" content="{{ $pageTitle ?? $post->title }}">
    <meta property="og:description" content="{{ $pageDescription ?? $post->excerpt ?? '' }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $coverImage }}">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="{{ $settings->site_name ?? $settings->subname ?? 'Bảo Tín JSC' }}">
    <meta property="og:locale" content="vi_VN">
    <meta property="article:published_time" content="{{ ($post->published_at ?? $post->created_at)->toIso8601String() }}">
    <meta property="article:modified_time" content="{{ $post->updated_at->toIso8601String() }}">
    @if($post->category)
        <meta property="article:section" content="{{ $post->category->name }}">
    @endif

    <!-- 🐦 Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle ?? $post->title }}">
    <meta name="twitter:description" content="{{ $pageDescription ?? $post->excerpt ?? '' }}">
    <meta name="twitter:image" content="{{ $coverImage }}">

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
<main class="single_blog_main">
    <div class="single_blog_container">
        <div class="single_blog_layout">
            <!-- Content Area -->
            <div class="single_blog_content_wrapper">
                <!-- Breadcrumb -->
                <nav class="single_blog_breadcrumb">
                    <a href="{{ route('client.home.index') }}">Trang chủ</a>
                    <span>/</span>
                    <a href="{{ route('client.blog.index') }}">Tự động hóa</a>
                    @if($post->category)
                        <span>/</span>
                        <a href="{{ route('client.blog.index', ['category' => $post->category->slug]) }}">{{ $post->category->name }}</a>
                    @endif
                    <span>/</span>
                    <span>{{ Str::limit($post->title, 50) }}</span>
                </nav>

                <!-- Article Header -->
                <article class="single_blog_article">
                    <header class="single_blog_header">
                        <h1 class="single_blog_title">{{ $post->title }}</h1>
                        <div class="single_blog_meta">
                            <span class="single_blog_author">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M8 8a3 3 0 100-6 3 3 0 000 6zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                                {{ $post->author->name ?? $post->creator->name ?? 'Admin' }}
                            </span>
                            <span class="single_blog_date">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M11 1v2H5V1H3v2H2a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2h-1V1h-2zm3 14H2V6h12v9z"/>
                                </svg>
                                {{ ($post->published_at ?? $post->created_at)->format('d/m/Y') }}
                            </span>
                            <span class="single_blog_views">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M8 4C4.5 4 1.5 6.5 0 9c1.5 2.5 4.5 5 8 5s6.5-2.5 8-5c-1.5-2.5-4.5-5-8-5zm0 8c-1.7 0-3-1.3-3-3s1.3-3 3-3 3 1.3 3 3-1.3 3-3 3zm0-5c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                                {{ number_format($post->views ?? 0) }} lượt xem
                            </span>
                        </div>
                    </header>

                    <!-- Featured Image -->
                    @if($post->coverImagePath())
                        <div class="single_blog_featured_image">
                            <img src="{{ $coverImage }}" alt="{{ $post->title }}">
                        </div>
                    @endif

                    <!-- Table of Contents -->
                    @if(isset($toc) && $toc->isNotEmpty())
                        <div class="single_blog_toc">
                            <h2 class="single_blog_toc_title">Mục Lục</h2>
                            <ul class="single_blog_toc_list">
                                @foreach($toc as $item)
                                    <li>
                                        <a href="#{{ $item['id'] }}">{{ $item['label'] }}</a>
                                        @if(isset($item['children']) && count($item['children']) > 0)
                                            <ul>
                                                @foreach($item['children'] as $child)
                                                    <li><a href="#{{ $child['id'] }}">{{ $child['label'] }}</a></li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Article Content -->
                    <div class="single_blog_content">
                        {!! $contentWithAnchors ?? $post->content !!}
                    </div>

                    <!-- Tags -->
                    @if(isset($tags) && $tags->isNotEmpty())
                        <div class="single_blog_tags">
                            <span class="single_blog_tag_label">Tags:</span>
                            @foreach($tags as $tag)
                                <a href="{{ route('client.blog.index', ['tag' => $tag->slug]) }}" class="single_blog_tag">
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <!-- Share Buttons -->
                    <div class="single_blog_share">
                        <span class="single_blog_share_label">Chia sẻ:</span>
                        @php
                            $shareUrl = urlencode($canonicalUrl);
                            $shareTitle = urlencode($post->title);
                        @endphp
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" 
                           target="_blank"
                           class="single_blog_share_btn single_blog_share_facebook">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M7.5 6.5h-2v3h2v10h4v-10h2.5l.5-3h-3V5c0-.825.125-1 1-1h2V1h-3c-2.5 0-4 1.5-4 4v1.5z"/>
                            </svg>
                            Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}" 
                           target="_blank"
                           class="single_blog_share_btn single_blog_share_twitter">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M18 4.5c-.6.3-1.2.4-1.9.5.7-.4 1.2-1 1.4-1.8-.6.4-1.3.6-2.1.8-.6-.6-1.5-1-2.4-1-1.7 0-3.2 1.5-3.2 3.3 0 .3 0 .5.1.7-2.7-.1-5.2-1.4-6.8-3.4-.3.5-.4 1-.4 1.7 0 1.1.6 2.1 1.5 2.7-.5 0-1-.2-1.5-.4 0 1.6 1.1 2.9 2.6 3.2-.3.1-.6.1-.9.1-.2 0-.4 0-.6-.1.4 1.3 1.6 2.3 3.1 2.3-1.1.9-2.5 1.4-4.1 1.4H2c1.5.9 3.2 1.5 5 1.5 6 0 9.3-5 9.3-9.3v-.4c.7-.5 1.3-1.1 1.7-1.8z"/>
                            </svg>
                            Twitter
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" 
                           target="_blank"
                           class="single_blog_share_btn single_blog_share_linkedin">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M5 3c0 1.1-.7 2-2 2-1.2 0-2-.9-2-1.9C1 2 1.8 1 3 1s2 .9 2 2zM1 19h4V6H1v13zm6 0h4v-7c0-.4 0-.8.1-1 .3-.8.9-1.6 2-1.6 1.4 0 2 1.1 2 2.6v7h4v-7.4c0-3.7-2-5.4-4.5-5.4-2.1 0-3 1.2-3.5 2h.1V6H7c.1.8 0 13 0 13z"/>
                            </svg>
                            LinkedIn
                        </a>
                        <a href="#" 
                           class="single_blog_share_btn single_blog_share_copy"
                           data-url="{{ $canonicalUrl }}">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13 7h-2v2h2V7zm0 4h-2v2h2v-2zm4-8H7c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 14H7V5h10v12z"/>
                            </svg>
                            Copy Link
                        </a>
                    </div>
                </article>
            </div>

            <!-- Sidebar Right - Related Posts -->
            @if(isset($relatedPosts) && $relatedPosts->isNotEmpty())
                <aside class="single_blog_sidebar_right">
                    <div class="single_blog_widget">
                        <h3 class="single_blog_widget_title">Bài Viết Liên Quan</h3>
                        <div class="single_blog_related_sidebar">
                            @foreach($relatedPosts as $relatedPost)
                                @php
                                    $relatedImage = $relatedPost->coverImagePath() 
                                        ? asset($relatedPost->coverImagePath()) 
                                        : asset('clients/assets/img/posts/no-image.webp');
                                    $relatedDate = $relatedPost->published_at ?? $relatedPost->created_at;
                                @endphp
                                <article class="single_blog_related_sidebar_item">
                                    <a href="{{ route('client.blog.show', $relatedPost) }}">
                                        <div class="single_blog_related_sidebar_image">
                                            <img src="{{ $relatedImage }}" alt="{{ $relatedPost->title }}">
                                        </div>
                                        <div class="single_blog_related_sidebar_content">
                                            <h4>{{ Str::limit($relatedPost->title, 50) }}</h4>
                                            <span class="single_blog_related_sidebar_date">{{ $relatedDate->format('d/m/Y') }}</span>
                                        </div>
                                    </a>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </aside>
            @endif
        </div>
    </div>
</main>

<script src="{{ asset('clients/assets/js/blog-single.js?v='.time()) }}"></script>
@endsection
