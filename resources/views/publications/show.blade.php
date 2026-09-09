@extends('layouts.app')

@php
    $siteName = settings()->site_name ?? 'Africa CDC Knowledge Hub';
    $siteUrl = rtrim((string) config('app.url'), '/');
    $pubTitle = clean_unicode($publication->title);

    $seoTagLabels = $publication->tags
        ->map(fn ($pt) => clean_unicode(optional($pt->tag)->tag_text ?? ''))
        ->filter()
        ->unique()
        ->values();

    $themeDesc = clean_unicode(optional($publication->theme)->description ?? '');
    $subThemeDesc = clean_unicode(optional($publication->sub_theme)->description ?? '');
    $categoryName = clean_unicode(optional($publication->data_category)->category_name ?? optional($publication->category)->category_name ?? '');

    $pageKeywords = collect([
        $seoTagLabels->implode(', '),
        $themeDesc,
        $subThemeDesc,
        $categoryName,
        'public health',
        'Africa CDC',
        settings()->seo_keywords ?? '',
    ])->filter()->implode(', ');
    $pageKeywords = Str::limit(trim(preg_replace('/\s*,\s*,+/', ', ', preg_replace('/\s+/', ' ', $pageKeywords))), 300);

    $descPlain = trim(preg_replace('/\s+/u', ' ', strip_tags(clean_unicode($publication->description ?? ''))));
    $pageDescription = Str::limit($descPlain, 160);
    if ($pageDescription === '') {
        $by = clean_unicode(optional($publication->author)->name ?? '');
        $fallbackBits = array_filter([
            trim(clean_unicode($publication->author_affiliation ?? '')) ?: null,
            $categoryName ?: null,
            $subThemeDesc ?: null,
            $publication->year_published ? 'Year '.$publication->year_published : null,
            $by ? 'Source: '.$by : null,
        ]);
        $pageDescription = Str::limit(
            $pubTitle.' — '.($fallbackBits ? implode(' — ', $fallbackBits).' — ' : '').$siteName,
            160
        );
    }

    $pageTitle = $pubTitle.' — '.$siteName;

    $rawCover = $publication->cover ?? $publication->image_url ?? null;
    $pageImage = $rawCover
        ? (filter_var($rawCover, FILTER_VALIDATE_URL) ? $rawCover : asset(ltrim($rawCover, '/')))
        : asset('assets/images/cover.png');
    if (! filter_var($pageImage, FILTER_VALIDATE_URL)) {
        $pageImage = asset('assets/images/cover.png');
    }

    $canonicalUrl = publication_url($publication);
    $ogType = 'article';
    
    $publishDate = $publication->created_at ? $publication->created_at->toIso8601String() : now()->toIso8601String();
    $contentUpdated = publication_content_updated_at($publication);
    $modifiedDate = $contentUpdated
        ? (\Carbon\Carbon::parse($contentUpdated)->toIso8601String())
        : $publishDate;
    $articlePublishedTime = $publishDate;
    $articleModifiedTime = $modifiedDate;
    
    $authors = [];
    if (! empty($publication->associated_authors)) {
        $authors = array_map('trim', explode(',', (string) $publication->associated_authors));
    }
    if ($publication->author) {
        $authors[] = $publication->author->name;
    }
    $authors = array_values(array_unique(array_filter($authors)));

    $pageAuthor = $authors[0] ?? clean_unicode(optional($publication->author)->name ?? $siteName);

    $logoRaw = settings()->logo ?? '';
    $logoAbsolute = $logoRaw && filter_var($logoRaw, FILTER_VALIDATE_URL)
        ? $logoRaw
        : ($logoRaw ? asset(ltrim($logoRaw, '/')) : asset('assets/images/logo.png'));

    $authorLd = [];
    foreach ($authors as $name) {
        if ($name !== '' && $name !== null) {
            $authorLd[] = ['@type' => 'Person', 'name' => $name];
        }
    }
    if ($authorLd === []) {
        $authorLd[] = ['@type' => 'Organization', 'name' => $siteName];
    }

    $scholarlyArticle = [
        '@context' => 'https://schema.org',
        '@type' => 'ScholarlyArticle',
        'headline' => $pubTitle,
        'description' => Str::limit($descPlain !== '' ? $descPlain : $pageDescription, 320),
        'image' => $pageImage,
        'datePublished' => $publishDate,
        'dateModified' => $modifiedDate,
        'author' => count($authorLd) === 1 ? $authorLd[0] : $authorLd,
        'publisher' => [
            '@type' => 'Organization',
            'name' => $siteName,
            'url' => $siteUrl ?: url('/'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $logoAbsolute,
            ],
        ],
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => $canonicalUrl,
        ],
        'inLanguage' => 'en',
        'url' => $canonicalUrl,
        'isAccessibleForFree' => true,
    ];

    if ($pageKeywords !== '') {
        $scholarlyArticle['keywords'] = $pageKeywords;
    }
    if ($categoryName !== '') {
        $scholarlyArticle['articleSection'] = $categoryName;
    }
    if (! empty($publication->doi)) {
        $scholarlyArticle['identifier'] = [
            '@type' => 'PropertyValue',
            'propertyID' => 'DOI',
            'value' => $publication->doi,
        ];
    }
    if (! empty($publication->issn)) {
        $scholarlyArticle['issn'] = $publication->issn;
    }
    if (! empty($publication->isbn)) {
        $scholarlyArticle['isbn'] = $publication->isbn;
    }
    if ($publication->license && ! empty($publication->license->url ?? null)) {
        $scholarlyArticle['license'] = $publication->license->url;
    }
    if (! empty($publication->year_published)) {
        $scholarlyArticle['copyrightYear'] = (int) $publication->year_published;
    }
    if (! empty($publication->funder)) {
        $scholarlyArticle['funder'] = [
            '@type' => 'Organization',
            'name' => $publication->funder,
        ];
    }
    if (! empty($publication->journal_name)) {
        $isPartOf = [
            '@type' => 'Periodical',
            'name' => $publication->journal_name,
        ];
        if (! empty($publication->journal_volume)) {
            $isPartOf['volumeNumber'] = $publication->journal_volume;
        }
        if (! empty($publication->journal_issue)) {
            $isPartOf['issueNumber'] = $publication->journal_issue;
        }
        if (! empty($publication->journal_pages)) {
            $isPartOf['pagination'] = $publication->journal_pages;
        }
        $scholarlyArticle['isPartOf'] = $isPartOf;
    }

    $publicationJsonLd = \App\Support\PublicationSeo::structuredDataGraph(
        $publication,
        $related_publications ?? null
    );
    $jsonLdFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE;

    $publicationCitationTags = \App\Support\PublicationCitationMeta::highwireTags($publication);
    $publicationCitationLinks = \App\Support\PublicationCitationMeta::citationLinks($publication);
@endphp

@section('structured_data')
<script type="application/ld+json">{!! json_encode($publicationJsonLd, $jsonLdFlags) !!}</script>
@endsection

@section('styles')
{{-- Summernote CSS loaded via partial in scripts to match forums --}}
<style>
    /* Title and description: display as normal text, not as links */
    .publication-page-title { text-decoration: none !important; color: #1e293b !important; }
    .publication-page-description { color: #334155; }
    .publication-page-description a { color: var(--theme-color-primary, #119A48); text-decoration: underline; }
    /* Global border-radius override - all elements use 0.25rem */
    * {
        --border-radius-default: 0.25rem;
    }
    
    /* Override all common elements to use 0.25rem border-radius */
    .card, .card-md, .card-body, .card-header, .card-footer,
    .btn, .btn-sm, .btn-lg, .btn-group,
    .badge, .badge-au,
    .modal-content, .modal-header, .modal-footer,
    .list-group-item,
    .form-control, .form-select,
    .alert,
    .dropdown-menu,
    .input-group-text,
    .comment-box {
        border-radius: 0.25rem !important;
    }
    
    body {
        background: #f4f6f9;
    }
    .section-heading {
        font-size: 1.25rem;
        font-weight: 600;
        color: #911C39;
        margin-bottom: 1rem;
    }
    .card-md {
        background: #fff;
        border-radius: 0.25rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    .badge-au {
        background-color: #119A48;
        color: #fff;
        font-size: 0.85rem;
        border-radius: 0.25rem;
        padding: 0.35rem 0.75rem;
        margin-right: 0.5rem;
    }
    .btn-au {
        background-color: #119A48;
        color: #fff;
    }
    .btn-au:hover {
        background-color: #0e7a3a;
        color: #fff;
    }
    .comment-box {
        background: #f1f1f1;
        border-radius: 0.25rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .meta-label {
        font-weight: 500;
        color: #5F5F5F;
        font-size: 0.9rem;
    }
    .meta-value {
        display: block;
        margin-bottom: 1rem;
        font-size: 0.95rem;
    }
    .publication-tags-list {
        gap: 0.5rem 0.75rem;
        margin-right: 0.5rem;
    }
    .publication-tag-pill {
        background-color: #6c757d !important;
        color: #ffffff !important;
        padding: 0.35em 0.65em;
        font-size: 0.8em;
        text-decoration: none;
        margin-right: 0;
        margin-bottom: 0;
    }
    .publication-tag-pill:hover {
        color: #ffffff;
        text-decoration: none;
    }
    /* Full-width hero video inside the title card (16:9) */
    .publication-hero-video {
        width: 100%;
        aspect-ratio: 16 / 9;
        background: #000;
    }
    .publication-hero-video iframe,
    .publication-hero-video video {
        width: 100%;
        height: 100%;
        border: 0;
        display: block;
    }
    .publication-hero-video video {
        object-fit: contain;
    }

    @include('publications.partials.preview_modal_styles')

    .preview-attachment {
        transition: all 0.2s ease;
    }

    .preview-attachment:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .d-flex.gap-2 > * + * {
        margin-left: 0.5rem;
    }
</style>
@endsection

@section('content')
@php $likes = count($publication->favourited); @endphp
<article itemscope itemtype="https://schema.org/ScholarlyArticle">


<section class="py-4">
    <div class="container">
        <nav class="mb-3" aria-label="Breadcrumb">
            <ol class="breadcrumb mb-0" style="background: transparent; padding: 0; font-size: 0.875rem;">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('records/search') }}">Browse resources</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($pubTitle, 80) }}</li>
            </ol>
        </nav>
        <div class="row">
            <!-- Left Column: Title/Info Card and Description -->
            <div class="col-lg-8">
                <!-- Title and Info Section with Cover Image -->
                <div class="card-md mb-3">
                            @php
                                // Get image with proper fallback logic like top_searches
                                $image_link = $publication->cover ?? $publication->image_url ?? null;
                                // Default image is cover.png from public assets/images
                                $default_image = asset('assets/images/cover.png');
                                
                                // Check if image_link is valid URL or path
                                if (empty($image_link) || $image_link === null) {
                                    $image_link = $default_image;
                                } elseif (!filter_var($image_link, FILTER_VALIDATE_URL)) {
                                    // If it's a relative path, try to make it full URL
                                    if (strpos($image_link, 'storage/') !== false || strpos($image_link, 'uploads/') !== false) {
                                        $image_link = asset($image_link);
                                    } elseif (strpos($image_link, '/') === 0) {
                                        $image_link = url($image_link);
                                    } else {
                                        $image_link = $default_image;
                                    }
                                }
                            @endphp
                            @php
                                $pubUrl = trim((string) ($publication->publication ?? ''));
                                $embedUrl = get_video_embed_url($pubUrl);
                                $directVideo = is_direct_video_file_url($pubUrl);
                                $isVideoLink = is_video_platform_url($pubUrl);
                                $showFullWidthVideo = ($publication->is_video || $isVideoLink) && $pubUrl !== '' && ($embedUrl || $directVideo);
                            @endphp
                            @if ($showFullWidthVideo)
                                @if ($embedUrl)
                                    <div class="publication-hero-video shadow rounded overflow-hidden mb-3">
                                        <iframe
                                            src="{{ $embedUrl }}"
                                            title="{{ $publication->title }} - Video Preview"
                                            frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share; fullscreen"
                                            referrerpolicy="strict-origin-when-cross-origin"
                                            allowfullscreen></iframe>
                                    </div>
                                @elseif ($directVideo)
                                    <div class="publication-hero-video shadow rounded overflow-hidden mb-3">
                                        <video controls playsinline webkit-playsinline preload="metadata">
                                            <source src="{{ $pubUrl }}">
                                            Your browser does not support HTML5 video.
                                        </video>
                                    </div>
                                @endif
                            @endif
                    <div class="row">
                        <!-- Cover Image - Inside Card -->
                        <div class="col-md-4 text-center mb-3 mb-md-0">
                            @unless ($showFullWidthVideo)
                            <img src="{{ $image_link }}" class="img-fluid shadow rounded" alt="{{ $publication->title }} - Cover Image" itemprop="image" style="max-height: 250px; width: auto;" onerror="this.onerror=null; this.src='{{ $default_image }}';">
                            @endunless
                            
                            <!-- Source, Views, Year, Comments below image -->
                            <div class="mt-3 text-left" style="font-size: 0.9rem;">
                                <div class="mb-2 ml-2">
                                    <span class="badge" style="background-color: #6c757d; color: #ffffff; padding: 0.35em 0.65em; font-size: 0.875em;">
                                        <i class="fa fa-eye mr-1"></i>Views: {{ format_view_count($publication->visits ?? 0) }}
                                    </span>
                                </div>
                                <div class="mb-2 ml-2">
                                    <span class="badge" style="background-color: #6c757d; color: #ffffff; padding: 0.35em 0.65em; font-size: 0.875em;">
                                        <i class="fa fa-calendar mr-1"></i>Updated: {{ publication_content_updated_ago($publication) }}
                                    </span>
                                </div>
                                @if(publication_last_visited_at($publication))
                                <div class="mb-2 ml-2">
                                    <span class="badge" style="background-color: #6c757d; color: #ffffff; padding: 0.35em 0.65em; font-size: 0.875em;">
                                        <i class="fa fa-history mr-1"></i>Last visit: {{ time_ago(publication_last_visited_at($publication)) }}
                                    </span>
                                </div>
                                @endif
                                @if(!empty($publication->year_published))
                                <div class="mb-2 ml-2">
                                    <span class="badge" style="background-color: #6c757d; color: #ffffff; padding: 0.35em 0.65em; font-size: 0.875em;">
                                        <i class="fa fa-calendar mr-1"></i>Year: {{ $publication->year_published }}
                                    </span>
                                </div>
                                @endif
                                <div class="mb-2 ml-2">
                                    <span class="badge" style="background-color: #6c757d; color: #ffffff; padding: 0.35em 0.65em; font-size: 0.875em;">
                                        <i class="fa fa-comments mr-1"></i>Comments: {{ count($publication->comments) }}
                                    </span>
                                </div>
                            </div>
            </div>
                        
                        <!-- Title and Info Content -->
                        <div class="col-md-8">
                            <h1 itemprop="headline" class="font-weight-bold mb-2 publication-page-title" style="font-size: 1.75rem;">{{ clean_unicode($publication->title) }}</h1>
                            <meta itemprop="name" content="{{ clean_unicode($publication->title) }}">
                            <div class="d-flex align-items-center flex-wrap mb-3" style="gap: 8px;">
                                <p class="text-muted mb-0" itemprop="about">{{ clean_unicode($publication->theme->description ?? '') }}</p>
                                @auth
                                    @if ($publication->user_id == current_user()->id || is_admin())
                                        <a href="{{ route('account.publications.edit') }}?id={{ $publication->id }}"
                                           class="btn btn-sm btn-outline-secondary"
                                           title="Edit this publication">
                                            <i class="fa fa-edit mr-1"></i>Edit publication
                                        </a>
                                    @endif
                                    @if (is_admin() || auth()->user()->can('view_publications'))
                                        <a href="{{ url('admin/publications/details') }}?id={{ $publication->id }}"
                                           class="btn btn-sm btn-outline-secondary"
                                           title="View this publication in Admin">
                                            <i class="fa fa-eye mr-1"></i>View as admin
                                        </a>
                                    @endif
                                @endauth
                            </div>
                <div class="d-flex flex-wrap mb-3">
                    <span class="badge badge-au">
                        {{ !$publication->is_version ? clean_unicode($publication->sub_theme->description ?? '') : 'Version ' . $publication->version_no }}
                    </span>
                    @if($likes)
                                    <span class="badge" style="background-color: #000000; color: #ffffff; padding: 0.35em 0.65em;">
                            <i class="lni lni-heart"></i> {{ $likes }} like{{ $likes > 1 ? 's' : '' }}
                        </span>
                    @endif
                </div>
                            
                            {{-- Category and Sub Category --}}
                            <div class="mb-3" style="font-size: 0.95rem;">
                                @if(@$publication->data_category)
                                <div class="mb-2">
                                    <strong style="color: #5F5F5F;">Category:</strong>
                                    <span style="color: #0f172a;">{{ $publication->data_category->category_name }}</span>
                                </div>
                                @endif
                                @if($publication->sub_category)
                                <div class="mb-2">
                                    <strong style="color: #5F5F5F;">Sub Category:</strong>
                                    <span style="color: #0f172a;">{{ $publication->sub_category->category_name }}</span>
                                </div>
                                @endif
                            </div>
                            
                            {{-- Associated Authors, affiliation, and contributing source --}}
                            @php
                                $authorAffiliation = trim(clean_unicode($publication->author_affiliation ?? ''));
                            @endphp
                            @if(!empty($publication->associated_authors) || $authorAffiliation !== '' || $publication->author)
                            <div class="mb-3" style="font-size: 0.95rem;">
                                @if(!empty($publication->associated_authors))
                                <div class="mb-2">
                                    <strong style="color: #5F5F5F;">Associated Authors:</strong>
                                    <span class="notranslate" translate="no" style="color: #0f172a;">{{ clean_unicode($publication->associated_authors) }}</span>
                                </div>
                                @endif
                                @if($authorAffiliation !== '')
                                <div class="mb-2">
                                    <strong style="color: #5F5F5F;">Author Affiliation/Institution:</strong>
                                    <span class="notranslate" translate="no" style="color: #0f172a;">{{ $authorAffiliation }}</span>
                                </div>
                                @endif
                                @if($publication->author)
                                <div class="mb-2">
                                    <strong style="color: #5F5F5F;">Affiliation/Source:</strong>
                                    <span style="color: #0f172a;">
                                        <a href="{{ author_publications_url($publication->author) }}" title="View contributor profile for {{ clean_unicode($publication->author->name) }}" class="notranslate" translate="no" style="color: #0f172a; text-decoration: none;">
                                            {{ clean_unicode($publication->author->name) }}
                                        </a>
                                        @if(!empty($publication->author->orcid))
                                            <a href="https://orcid.org/{{ $publication->author->orcid }}" target="_blank" rel="noopener noreferrer" title="View ORCID profile" class="ms-1" style="color: #64748b;">
                                                <i class="fa fa-external-link-alt" style="font-size: 0.75rem;"></i>
                                            </a>
                                        @endif
                                    </span>
                                </div>
                                @endif
                                
                                <!-- Rate and Share Section -->
                                <div class="d-flex align-items-center flex-wrap" style="gap: 15px; margin-top: 15px;">
                                    <!-- Rate this Resource -->
                                    <div class="d-flex align-items-center" style="gap: 8px;">
                                        <strong style="color: #5F5F5F; font-size: 0.9rem;">Rate this Resource:</strong>
                                        @include('partials.general.rating')
                                    </div>
                                    
                                    <!-- Share This Resource -->
                                    <div class="d-flex align-items-center" style="gap: 8px;">
                                        <strong style="color: #5F5F5F; font-size: 0.9rem;">Share This Resource:</strong>
                                        <div class="btn-group" role="group" aria-label="Share">
                                            @php 
                                                $shareUrl = publication_url($publication); 
                                                $shareText = urlencode(strip_tags($publication->title)); 
                                            @endphp
                                            <a class="btn btn-sm btn-outline-secondary" target="_blank" href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareUrl) }}" title="Share on LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                                            <a class="btn btn-sm btn-outline-secondary" target="_blank" href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ $shareText }}" title="Share on X"><i class="fab fa-x-twitter"></i></a>
                                            <a class="btn btn-sm btn-outline-secondary" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" title="Share on Facebook"><i class="fab fa-facebook-f"></i></a>
                                            <a class="btn btn-sm btn-outline-secondary" target="_blank" href="https://api.whatsapp.com/send?text={{ $shareText }}%20{{ urlencode($shareUrl) }}" title="Share on WhatsApp"><i class="fab fa-whatsapp"></i></a>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyPublicationLink('{{ $shareUrl }}')" title="Copy link"><i class="fa fa-link"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    @php
                        $pdfSourcesList = $publication->pdf_sources;
                        $pdfSourceCount = count($pdfSourcesList);
                        $khubAiConfig = $publication->defaultKhubAiConfig();
                        $defaultAssistantMode = $khubAiConfig['assistant_mode'];
                        $defaultAssistantAttachmentId = $khubAiConfig['attachment_id'];
                        $defaultPdfSourceKeys = $khubAiConfig['pdf_source_keys'] ?? [];
                    @endphp
                    <!-- Action Buttons - Favorite, Khub AI (ChatPDF; multi-PDF selectable in assistant) -->
                    <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-2 flex-wrap align-items-center">
                    @include('common.favourites_btn',['row'=>$publication])
                @auth
                    <button type="button" class="btn btn-au btn-sm js-open-pdf-chat"
                            data-publication-id="{{ $publication->id }}"
                            data-attachment-id="{{ $defaultAssistantAttachmentId !== null ? $defaultAssistantAttachmentId : '' }}"
                            data-assistant-mode="{{ $defaultAssistantMode }}"
                            data-doc-title="{{ e(Str::limit(strip_tags($publication->title ?? 'Document'), 200)) }}"
                            title="{{ $pdfSourceCount > 1 ? 'Chat with one or more PDFs (choose documents in the assistant)' : '' }}"
                            onclick="typeof openPdfChat === 'function' && openPdfChat({{ $publication->id }}, @json($defaultAssistantAttachmentId), @json(Str::limit(strip_tags($publication->title ?? 'Document'), 200)), @json($defaultAssistantMode))">
                        <i class="fa-solid fa-microchip"></i> Khub AI
                    </button>
                @else
                    <a href="{{ url('login') }}?redirect={{ urlencode(publication_url($publication)) }}" class="btn btn-au btn-sm">
                        <i class="fa-solid fa-microchip"></i> Khub AI <small>(login required)</small>
                    </a>
                @endauth
            </div>
                        <div class="d-flex gap-2">
                @auth
                            @if((!isset($publication->is_version) || $publication->is_version == 0) && (settings()->enable_version_submission ?? true))
                            <a href="{{ route('account.newversion') }}?id={{ $publication->id }}" class="btn btn-outline-danger btn-sm" style="margin-right:4px !important;">
                        <i class="fa fa-plus"></i> Submit Version
                    </a>
                            @endif
                            <a href="{{ route('account.summarize') }}?id={{ $publication->id }}" class="btn btn-outline-danger btn-sm">
                        <i class="fa fa-file"></i> Submit Summary
                    </a>
                @endauth
            </div>
        </div>
    </div>

                <!-- Resources & Attachments - Mobile/Tablet View (shown only on mobile/tablet) -->
                @if ($publication->publication || $publication->has_attachments)
                    <div class="card-md mb-3 d-lg-none">
                        <h5 class="section-heading">Resources & Attachments</h5>
                        
                        @if ($publication->publication)
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                                        <strong><i class="fa fa-link mr-2 text-success"></i>External Resource</strong>
                                        <p class="mb-0 text-muted" style="font-size: 0.9rem; word-break: break-all;">
                                            {{ Str::limit($publication->publication, 80) }}
                                        </p>
                                    </div>
                                    <a href="{{ $publication->publication }}" target="_blank" class="btn btn-outline-success btn-sm">
                                            <i class="fa fa-external-link-alt mr-1"></i> Open
                                        </a>
                    </div>
                </div>
                        @endif

                @if ($publication->has_attachments)
                            <div>
                                <h6 class="mb-3" style="font-weight: 600; color: #5F5F5F;">
                                    <i class="fa fa-paperclip mr-2"></i>Downloadable Files ({{ count($publication->attachments) }})
                                </h6>
                        <ul class="list-group">
                            @foreach ($publication->attachments as $i => $file)
                                        @php
                                            $url = $file->file;
                                            $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION)) ?: 'pdf';
                                            $ext = normalize_publication_attachment_extension($ext);
                                            $office = in_array($ext, ['ppt','pptx','doc','docx','xls','xlsx']) ? 1 : 0;
                                            $downloadFilename = $file->download_filename;
                                            $humanReadable = trim((string) ($file->original_filename ?? $file->description ?? ''));
                                            if ($humanReadable === '') {
                                                $humanReadable = (string) $downloadFilename;
                                            }
                                            $humanReadable = str_replace('_', ' ', $humanReadable);
                                            $attachmentNameMax = 70;
                                            $displayName = Str::limit($humanReadable, $attachmentNameMax);
                                            
                                            // Determine icon based on file extension
                                            $fileIcon = 'fa-file';
                                            $iconColor = 'text-muted';
                                            
                                            if (in_array($ext, ['pdf'])) {
                                                $fileIcon = 'fa-file-pdf';
                                                $iconColor = 'text-danger';
                                            } elseif (in_array($ext, ['doc', 'docx'])) {
                                                $fileIcon = 'fa-file-word';
                                                $iconColor = 'text-primary';
                                            } elseif (in_array($ext, ['xls', 'xlsx'])) {
                                                $fileIcon = 'fa-file-excel';
                                                $iconColor = 'text-success';
                                            } elseif (in_array($ext, ['ppt', 'pptx'])) {
                                                $fileIcon = 'fa-file-powerpoint';
                                                $iconColor = 'text-warning';
                                            } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                                                $fileIcon = 'fa-file-image';
                                                $iconColor = 'text-info';
                                            } elseif (in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'])) {
                                                $fileIcon = 'fa-file-archive';
                                                $iconColor = 'text-secondary';
                                            } elseif (in_array($ext, ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv'])) {
                                                $fileIcon = 'fa-file-video';
                                                $iconColor = 'text-danger';
                                            } elseif (in_array($ext, ['mp3', 'wav', 'ogg', 'flac', 'aac'])) {
                                                $fileIcon = 'fa-file-audio';
                                                $iconColor = 'text-primary';
                                            } elseif (in_array($ext, ['txt', 'csv'])) {
                                                $fileIcon = 'fa-file-alt';
                                                $iconColor = 'text-muted';
                                            }
                                        @endphp
                                <li class="list-group-item">
                                            <div class="d-flex flex-column">
                                                <div class="mb-2">
                                                    <i class="fa {{ $fileIcon }} {{ $iconColor }} mr-2" style="font-size: 1.1rem;"></i> 
                                                    <strong class="d-inline-block" style="max-width: 100%; word-break: break-word;" title="{{ e($humanReadable) }}">{{ $displayName }}</strong>
                                                    <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">
                                                        {{ strtoupper($ext) }} file
                                                    </small>
                                                </div>
                                                <div class="d-flex gap-2 flex-wrap">
                                                    <button type="button" class="btn btn-au btn-sm preview-attachment"
                                                            data-file-url="{{ $url }}" data-file-ext="{{ $ext }}" data-file-office="{{ $office }}"
                                                            title="Preview file"
                                                            onclick="window.previewAttachmentClick(event, this); return false;">
                                                        <i class="fa fa-eye mr-1"></i> Preview
                                                    </button>
                                                    <a href="{{ $url }}" target="_blank" class="btn btn-au btn-sm" title="Download: {{ e($humanReadable) }}" download="{{ e($downloadFilename) }}">
                                                        <i class="fa fa-download mr-1"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                </li>
                            @endforeach
                        </ul>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Description Card -->
                <div class="card-md">
                    @if ($pubUrl !== '')
                        @if (!($publication->is_video || $isVideoLink) && $publication->is_embedded)
                        <div class="responsive-iframe-container mb-4">
                                <iframe src="{{ $pubUrl }}" allowfullscreen></iframe>
                        </div>
                        @endif
                    @endif
                    <h2 class="section-heading">Description</h2>
                    <div itemprop="articleBody" class="publication-page-description">
                    @php
                        // Process publication description to detect and embed video links and convert URLs to clickable links
                        $processedDescription = detect_and_embed_video_links(clean_unicode(publication_description_for_list($publication->description ?? '')), 180, 180);
                        $processedDescription = sanitize_rich_text_for_display($processedDescription);
                    @endphp
                    <p>{!! $processedDescription !!}</p>
                    </div>
                </div>

                {{-- Related Resources Carousel --}}
                @if(isset($related_publications) && $related_publications->count() > 0)
                <style>
                    .related-resources-strip{margin:16px auto 8px;border:1px solid #e2e8f0;border-radius:0.25rem;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,.06);overflow:hidden}
                    .related-resources-strip .head{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-bottom:1px solid #eef2f7}
                    .related-resources-strip .head h5{margin:0;font-weight:700;color:#0f172a}
                    .related-resources-track{display:flex;overflow-x:auto;scroll-snap-type:x mandatory;gap:12px;padding:12px;scroll-behavior:smooth}
                    .related-resources-track::-webkit-scrollbar{height:8px}
                    .related-resources-track::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:0.25rem}
                    .related-resource-card{min-width:520px;max-width:560px;flex:0 0 auto;border:1px solid #e2e8f0;border-radius:0.25rem;scroll-snap-align:start;background:#fff;display:flex;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s}
                    .related-resource-card:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,.1);cursor:pointer}
                    .related-resource-cover{width:42%;min-width:42%;height:170px;background:#f8fafc;border-right:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;overflow:hidden}
                    .related-resource-cover img{width:100%;height:100%;object-fit:cover}
                    .related-resource-body{padding:12px;flex:1}
                    .related-resource-title{font-weight:700;color:#0f172a;margin:0 0 8px;font-size:1.08rem;line-height:1.25}
                    .related-resource-meta{font-size:.86rem;color:#475569;margin-bottom:4px}
                    .related-resource-desc{font-size:.85rem;color:#334155;margin-top:6px;max-height:3.2em;overflow:hidden}
                    .related-resource-actions{display:flex;gap:8px;margin-top:10px}
                    .related-resource-actions .btn{padding:6px 10px;border-radius:0.25rem}
                    @media (max-width:768px){.related-resource-card{min-width:320px;max-width:360px}.related-resource-cover{height:140px;width:45%;min-width:45%}}
                </style>
                
                <div class="related-resources-strip mt-3">
                    <div class="head">
                        <h5>Related Resources</h5>
                        <a href="{{ url('records') }}" class="btn btn-au btn-sm">
                            View All
                        </a>
                            </div>
                    <div id="relatedResourcesTrack" class="related-resources-track">
                        @foreach($related_publications as $relatedPub)
                        @php
                            // Publication::getCoverAttribute already resolves local covers to full URLs.
                            $cover = $relatedPub->cover ?? $relatedPub->image_url ?? asset('assets/images/cover.png');
                            $detailsUrl = publication_url($relatedPub);
                        @endphp
                        <div class="related-resource-card" onclick="window.open('{{ $detailsUrl }}','_blank')">
                            <div class="related-resource-cover">
                                <img src="{{ $cover }}" alt="{{ $relatedPub->title }}" onerror="this.onerror=null;this.src='{{ asset('assets/images/cover.png') }}'"/>
                            </div>
                            <div class="related-resource-body">
                                <div class="related-resource-title">{{ Str::limit(strip_tags($relatedPub->title), 70) }}</div>
                                @if($relatedPub->author)
                                <div class="related-resource-meta">
                                    <i class="fa fa-user mr-1"></i>
                                    @if(!empty($relatedPub->author->orcid))
                                        <a href="https://orcid.org/{{ $relatedPub->author->orcid }}" target="_blank" rel="noopener noreferrer" title="View {{ $relatedPub->author->name }}'s ORCID profile" class="notranslate" translate="no" style="color: inherit; text-decoration: none;" onclick="event.stopPropagation();">
                                            {{ Str::limit($relatedPub->author->name, 40) }}
                                            <i class="fa fa-external-link-alt" style="font-size: 0.65rem; margin-left: 2px;"></i>
                                        </a>
                                    @else
                                        <span class="notranslate" translate="no">{{ Str::limit($relatedPub->author->name, 40) }}</span>
                        @endif
                                </div>
                                @endif
                                @if($relatedPub->created_at)
                                <div class="related-resource-meta">
                                    <i class="fa fa-calendar mr-1"></i>{{ $relatedPub->created_at->format('M Y') }}
                                </div>
                                @endif
                                @if(!empty($relatedPub->description))
                                <div class="related-resource-desc">{{ Str::limit(strip_tags(clean_unicode(publication_description_for_list($relatedPub->description))), 140) }}</div>
                        @endif
                                <div class="related-resource-actions">
                                    <a href="{{ $detailsUrl }}" target="_blank" class="btn btn-au btn-sm" onclick="event.stopPropagation();">
                                        <i class="fa fa-eye mr-1"></i>View
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                            </div>
                </div>

                <script>
                    // Smooth right-to-left auto scroll for related resources
                    (function(){
                        var track = document.getElementById('relatedResourcesTrack');
                        if(!track) return;
                        var step = 1;
                        var gap = 12; // matches CSS gap
                        function cardWidth(){
                            var card = track.querySelector('.related-resource-card');
                            if(!card) return 300;
                            var style = window.getComputedStyle(card);
                            return card.getBoundingClientRect().width + gap;
                        }
                        // Start at the far right
                        function toEnd(){ track.scrollLeft = track.scrollWidth; }
                        toEnd();
                        window.relatedResourcesSlide = function(dir){
                            var delta = cardWidth();
                            // dir: 1 means move right->left, -1 left->right
                            track.scrollLeft -= (dir * delta);
                            if(track.scrollLeft <= 0){ toEnd(); }
                            if(track.scrollLeft >= track.scrollWidth - track.clientWidth){ track.scrollLeft = 0; }
                        };
                        setInterval(function(){ relatedResourcesSlide(1); }, 5000);
                    })();
                </script>
                @endif
            </div>

            <!-- Right Column: Resources & Attachments, Resource Details, and Comments -->
            <div class="col-lg-4">
                @if ($publication->publication || $publication->has_attachments)
                    <div class="card-md mb-3 d-none d-lg-block">
                        <h5 class="section-heading">Resources & Attachments</h5>
                        
                        @if ($publication->publication)
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                                        <strong><i class="fa fa-link mr-2 text-success"></i>External Resource</strong>
                                        <p class="mb-0 text-muted" style="font-size: 0.9rem; word-break: break-all;">
                                            {{ Str::limit($publication->publication, 80) }}
                                        </p>
                    </div>
                                    <a href="{{ $publication->publication }}" target="_blank" class="btn btn-outline-success btn-sm">
                                            <i class="fa fa-external-link-alt mr-1"></i> Open
                                        </a>
                </div>
                </div>
                        @endif

                @if ($publication->has_attachments)
                            <div>
                                <h6 class="mb-3" style="font-weight: 600; color: #5F5F5F;">
                                    <i class="fa fa-paperclip mr-2"></i>Downloadable Files ({{ count($publication->attachments) }})
                                </h6>
                        <ul class="list-group">
                            @foreach ($publication->attachments as $i => $file)
                                        @php
                                            $url = $file->file;
                                            $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION)) ?: 'pdf';
                                            $ext = normalize_publication_attachment_extension($ext);
                                            $office = in_array($ext, ['ppt','pptx','doc','docx','xls','xlsx']) ? 1 : 0;
                                            $downloadFilename = $file->download_filename;
                                            $humanReadable = trim((string) ($file->original_filename ?? $file->description ?? ''));
                                            if ($humanReadable === '') {
                                                $humanReadable = (string) $downloadFilename;
                                            }
                                            $humanReadable = str_replace('_', ' ', $humanReadable);
                                            $attachmentNameMax = 70;
                                            $displayName = Str::limit($humanReadable, $attachmentNameMax);
                                            
                                            // Determine icon based on file extension
                                            $fileIcon = 'fa-file';
                                            $iconColor = 'text-muted';
                                            
                                            if (in_array($ext, ['pdf'])) {
                                                $fileIcon = 'fa-file-pdf';
                                                $iconColor = 'text-danger';
                                            } elseif (in_array($ext, ['doc', 'docx'])) {
                                                $fileIcon = 'fa-file-word';
                                                $iconColor = 'text-primary';
                                            } elseif (in_array($ext, ['xls', 'xlsx'])) {
                                                $fileIcon = 'fa-file-excel';
                                                $iconColor = 'text-success';
                                            } elseif (in_array($ext, ['ppt', 'pptx'])) {
                                                $fileIcon = 'fa-file-powerpoint';
                                                $iconColor = 'text-warning';
                                            } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                                                $fileIcon = 'fa-file-image';
                                                $iconColor = 'text-info';
                                            } elseif (in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'])) {
                                                $fileIcon = 'fa-file-archive';
                                                $iconColor = 'text-secondary';
                                            } elseif (in_array($ext, ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv'])) {
                                                $fileIcon = 'fa-file-video';
                                                $iconColor = 'text-danger';
                                            } elseif (in_array($ext, ['mp3', 'wav', 'ogg', 'flac', 'aac'])) {
                                                $fileIcon = 'fa-file-audio';
                                                $iconColor = 'text-primary';
                                            } elseif (in_array($ext, ['txt', 'csv'])) {
                                                $fileIcon = 'fa-file-alt';
                                                $iconColor = 'text-muted';
                                            }
                                        @endphp
                                <li class="list-group-item">
                                            <div class="d-flex flex-column">
                                                <div class="mb-2">
                                                    <i class="fa {{ $fileIcon }} {{ $iconColor }} mr-2" style="font-size: 1.1rem;"></i> 
                                                    <strong class="d-inline-block" style="max-width: 100%; word-break: break-word;" title="{{ e($humanReadable) }}">{{ $displayName }}</strong>
                                                    <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">
                                                        {{ strtoupper($ext) }} file
                                                    </small>
                                                </div>
                                                <div class="d-flex gap-2 flex-wrap">
                                                    <button type="button" class="btn btn-au btn-sm preview-attachment"
                                                            data-file-url="{{ $url }}" data-file-ext="{{ $ext }}" data-file-office="{{ $office }}"
                                                            title="Preview file"
                                                            onclick="window.previewAttachmentClick(event, this); return false;">
                                                        <i class="fa fa-eye mr-1"></i> Preview
                                                    </button>
                                                    <a href="{{ $url }}" target="_blank" class="btn btn-au btn-sm" title="Download: {{ e($humanReadable) }}" download="{{ e($downloadFilename) }}">
                                                        <i class="fa fa-download mr-1"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                </li>
                            @endforeach
                        </ul>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Versions Card - Moved below Resources & Attachments --}}
                @if (count($publication->versioning) || $publication->parent_id > 0)
                    <div class="card-md mb-3">
                        <h5 class="section-heading">Versions</h5>
                        <ul class="list-group">
                            @foreach ($publication->versioning as $version)
                                <li class="list-group-item" style="word-wrap: break-word; overflow-wrap: break-word;">
                                    <a href="{{ publication_url($version)}}" style="text-decoration: none; color: inherit; display: block;">
                                        <span style="word-break: break-word; white-space: normal;">
                                            {{ Str::limit(strip_tags($version->title ?? 'Untitled'), 60) }} 
                                            <span class="text-muted">(Version {{ $version->version_no }})</span>
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                            @if ($publication->parent_id > 0)
                                <li class="list-group-item" style="word-wrap: break-word; overflow-wrap: break-word;">
                                    <a href="{{ publication_url($publication->parent_id) }}" style="text-decoration: none; color: inherit; display: block;">
                                        <span style="word-break: break-word; white-space: normal;">
                                        <i class="fa fa-link"></i> Original Version
                                        </span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                @endif

                <!-- Resource Details Card -->
                <div class="card-md mb-3">
                    <h5 class="section-heading">Resource Details</h5>
                    <div>
                        <label class="meta-label">Theme</label><span class="meta-value">{!! $publication->theme->description ?? '' !!}</span>
                        <label class="meta-label">Sub-Theme</label><span class="meta-value">{!! nl2br($publication->sub_theme->description ?? '') !!}</span>
                        <label class="meta-label">Associated Authors</label><span class="meta-value notranslate" translate="no">{{ $publication->associated_authors ?? 'N/A' }}</span>
                        
                        {{-- Tags --}}
                        @if($publication->tags && $publication->tags->count() > 0)
                        <label class="meta-label">Tags</label>
                        <span class="meta-value">
                            <div class="d-flex flex-wrap publication-tags-list mt-1 mb-3">
                                @foreach($publication->tags as $pubTag)
                                    @if($pubTag->tag)
                                        <a href="{{ tag_records_url($pubTag->tag_id) }}" class="badge badge-secondary publication-tag-pill">{{ $pubTag->tag->tag_text }}</a>
                                    @endif
                                @endforeach
                            </div>
                        </span>
                        @endif
                        
                        {{-- Publication Metadata --}}
                        @if(!empty($publication->doi))
                        <label class="meta-label">DOI</label>
                        <span class="meta-value">
                            <a href="https://doi.org/{{ $publication->doi }}" target="_blank" rel="noopener noreferrer" style="color: #911C39; text-decoration: none;">
                                {{ $publication->doi }} <i class="fa fa-external-link-alt" style="font-size: 0.75rem;"></i>
                            </a>
                        </span>
                        @endif

                        @if(!empty($publicationCitationLinks))
                        <label class="meta-label">Track citations</label>
                        <span class="meta-value">
                            <span class="text-muted d-block mb-1" style="font-size: 0.85rem;">Look up this work in external citation indexes (opens in a new tab).</span>
                            <div class="d-flex flex-wrap gap-2 mt-1">
                                @foreach($publicationCitationLinks as $citeLink)
                                    <a href="{{ $citeLink['url'] }}" target="_blank" rel="noopener noreferrer" class="badge badge-secondary" style="text-decoration: none;">
                                        {{ $citeLink['label'] }} <i class="fa fa-external-link-alt" style="font-size: 0.7rem;"></i>
                                    </a>
                                @endforeach
                            </div>
                        </span>
                        @endif
                        
                        @if(!empty($publication->issn))
                        <label class="meta-label">ISSN</label><span class="meta-value">{{ $publication->issn }}</span>
                        @endif
                        
                        @if(!empty($publication->isbn))
                        <label class="meta-label">ISBN</label><span class="meta-value">{{ $publication->isbn }}</span>
                        @endif
                        
                        @if(!empty($publication->publisher))
                        <label class="meta-label">Publisher</label><span class="meta-value">{{ $publication->publisher }}</span>
                        @endif
                        
                        @if($publication->license)
                        <label class="meta-label">License</label>
                        <span class="meta-value">
                            @if($publication->license->url)
                                <a href="{{ $publication->license->url }}" target="_blank" rel="noopener noreferrer" style="color: #911C39; text-decoration: none;">
                                    {{ $publication->license->name }}@if($publication->license->short_name) ({{ $publication->license->short_name }})@endif <i class="fa fa-external-link-alt" style="font-size: 0.75rem;"></i>
                                </a>
                            @else
                                {{ $publication->license->name }}@if($publication->license->short_name) ({{ $publication->license->short_name }})@endif
                            @endif
                        </span>
                        @endif
                        
                        @if(!empty($publication->copyright_info))
                        <label class="meta-label">Copyright Information</label><span class="meta-value">{!! nl2br(e($publication->copyright_info)) !!}</span>
                        @endif
                        
                        @if(!empty($publication->funder))
                        <label class="meta-label">Funder</label><span class="meta-value">{{ $publication->funder }}</span>
                        @endif
                        
                        {{-- Journal Information (if journal article) --}}
                        @if(!empty($publication->journal_name) || !empty($publication->journal_volume) || !empty($publication->journal_issue) || !empty($publication->journal_pages))
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                            <label class="meta-label" style="color: #911C39; font-weight: 600;">Journal Information</label>
                            @if(!empty($publication->journal_name))
                            <label class="meta-label" style="margin-top: 0.5rem;">Journal Name</label><span class="meta-value">{{ $publication->journal_name }}</span>
                            @endif
                            @if(!empty($publication->journal_volume) || !empty($publication->journal_issue) || !empty($publication->journal_pages))
                            <div style="display: flex; flex-wrap: wrap; gap: 1rem; margin-top: 0.5rem;">
                                @if(!empty($publication->journal_volume))
                                <div style="flex: 1 1 auto;">
                                    <label class="meta-label">Volume</label><span class="meta-value">{{ $publication->journal_volume }}</span>
                                </div>
                                @endif
                                @if(!empty($publication->journal_issue))
                                <div style="flex: 1 1 auto;">
                                    <label class="meta-label">Issue</label><span class="meta-value">{{ $publication->journal_issue }}</span>
                                </div>
                                @endif
                                @if(!empty($publication->journal_pages))
                                <div style="flex: 1 1 auto;">
                                    <label class="meta-label">Pages</label><span class="meta-value">{{ $publication->journal_pages }}</span>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Comments Card -->
                <div class="card-md mb-3">
                    <h2 class="section-heading">Comments ({{ count($publication->comments) }})</h2>
                    @auth
                        <form id="commentForm" action="{{ url('records/comment') }}" method="post" class="mb-3">
                            @csrf
                            <input type="hidden" name="publication_id" value="{{ $publication->id }}">
                            <input type="hidden" name="user_id" value="{{ current_user()->user_id }}">
                            <div class="form-group">
                                <label class="mb-2">Write a comment</label>
                                <textarea name="comment" id="publicationCommentEditor" class="form-control" cols="30" rows="6" placeholder="Type your comment...."></textarea>
                            </div>
                            <div class="form-group">
                                @php
                                    $recaptchaSiteKey = config('recaptcha.api_site_key');
                                    $isLocalhost = in_array(request()->getHost(), ['localhost', '127.0.0.1']) || 
                                                  app()->environment('local', 'testing');
                                    $showRecaptcha = $recaptchaSiteKey && !empty($recaptchaSiteKey) && !$isLocalhost;
                                @endphp
                                @if($showRecaptcha)
                                    <div class="py-2">
                                        {!! \Biscolab\ReCaptcha\Facades\ReCaptcha::htmlFormSnippet() !!}
                                        @error('g-recaptcha-response')
                                            <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-au btn-sm">Post</button>
                            </div>
                        </form>
                    @else
                        <p class="text-muted">Login to comment.</p>
                        <a href="{{ url('/login') }}" class="btn btn-outline-primary btn-sm">Login</a>
                    @endauth

                    <div id="commentsList">
                        @foreach ($publication->comments as $comment)
                            <div class="comment-box">
                                <strong class="notranslate" translate="no">{{ $comment->user->name ?? 'Anonymous' }}</strong>
                                <small class="text-muted d-block">{{ time_ago($comment->created_at) }}</small>
                                @php
                                    $rawComment = (string) ($comment->comment ?? '');
                                    $hasHtml = preg_match('/<[^>]+>/', $rawComment) === 1;
                                    $safeHtmlComment = sanitize_rich_text_for_display($rawComment);
                                @endphp
                                <div class="mb-0">{!! $hasHtml ? $safeHtmlComment : nl2br(e($rawComment)) !!}</div>
                </div>
                        @endforeach
                    </div>
                </div>

                @if (count($publication->summaries))
                    <div class="card-md">
                        <h5 class="section-heading">Summaries & Abstracts</h5>
                        <ul class="list-group">
                            @foreach ($publication->summaries as $summary)
                                @if ($comment->is_approved == 1)
                                    <li class="list-group-item">
                                        <a href="{{ url('records/shortened') }}?id={{ $summary->id }}">
                                            {{ truncate($summary->title, 100) }} by <span class="notranslate" translate="no">{{ $summary->author->name ?? '' }}</span>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
</article>
@auth
@include('common.pdf-chat-modal')
<script>
  var pdfChatPublicationId = {{ $publication->id }};
  var pdfChatAttachmentId = @json($defaultAssistantAttachmentId ?? null);
  var pdfChatDocumentTitle = @json(Str::limit(strip_tags($publication->title ?? 'Document'), 200));
  window.pdfChatAssistantMode = @json($defaultAssistantMode ?? 'publication');
  window.pdfChatPdfSources = @json($pdfSourcesList ?? []);
  window.pdfChatPdfSourceKeys = @json($defaultPdfSourceKeys ?? []);
</script>
@include('common.pdf-chat-js')
@endauth
@include('common.attachment_js')
@include('publications.partials.preview_modal')

@endsection
{{-- <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script> --}}
@section('scripts')
@include('partials.general.summernote')


<script>

// Function to copy publication link - Must be global for onclick handler
function copyPublicationLink(url){
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(function(){
            alert('Link copied to clipboard');
        });
    } else {
        const el = document.createElement('textarea');
        el.value = url; 
        document.body.appendChild(el); 
        el.select();
        try { 
            document.execCommand('copy'); 
            alert('Link copied to clipboard'); 
        } finally { 
            document.body.removeChild(el); 
        }
    }
}

// Function to download/save file (keeping for backward compatibility, but using direct link now)
function downloadFile(url, filename) {
    // Open file in new tab for download
    window.open(url, '_blank');
}

// Wait for both jQuery and Summernote to be loaded
(function() {
    function initScripts() {
        // Check if jQuery is available
        if (typeof jQuery === 'undefined') {
            console.error('jQuery is not loaded');
            return;
        }
        
        var $ = jQuery;
        
        // Initialize Summernote for comment textarea using unique ID
        function initSummernote() {
            var $editor = $('#publicationCommentEditor');
            if ($editor.length && typeof $.fn.summernote !== 'undefined') {
                // Check if already initialized
                if ($editor.data('summernote')) {
                    console.log('Summernote already initialized on comment editor');
                    return;
                }
                
                try {
                    $editor.summernote({
                        placeholder: 'Type your comment....',
                        height: 150,
                        toolbar: [
                            ['style', ['bold','italic','underline']],
                            ['para', ['ul','ol']],
                            ['insert', ['picture']]
                        ],
                        callbacks: {
                            onImageUpload: function(files){
                                if(!files || !files.length) return;
                                var file = files[0];
                                if (file.size > 1024*1024) { // 1MB
                                    alert('Please upload images up to 1MB.');
                                    return;
                                }
                                var data = new FormData();
                                data.append('file', file);
                                data.append('_token', '{{ csrf_token() }}');
                                $.ajax({
                                    url: '{{ route('image.upload') }}',
                                    type: 'POST',
                                    data: data,
                                    cache: false,
                                    contentType: false,
                                    processData: false
                                }).done(function(resp){
                                    var imageUrl = resp.url || resp;
                                    $editor.summernote('insertImage', imageUrl);
                                }).fail(function(){
                                    alert('Image upload failed.');
                                });
                            }
                        }
                    });
                    console.log('Summernote initialized successfully on comment editor');
                } catch(e) {
                    console.error('Summernote initialization failed:', e);
                }
            } else if ($editor.length && typeof $.fn.summernote === 'undefined') {
                // Retry after a delay if Summernote isn't loaded yet
                console.log('Summernote not loaded yet, retrying...');
                setTimeout(initSummernote, 300);
            } else if (!$editor.length) {
                console.log('Comment editor not found');
            }
        }
        
        // Initialize when document is ready
        $(document).ready(function() {
            setTimeout(function() {
                initSummernote();
            }, 500);
        });
        
        // Also try on window load
        $(window).on('load', function() {
            setTimeout(function() {
                initSummernote();
            }, 800);
        });
        
        // Comment form handler
        $('#commentForm').on('submit', function(e){
            e.preventDefault();
            var html;
            var $editor = $('#publicationCommentEditor');
            if ($editor.length && $editor.data('summernote')) {
                html = $editor.summernote('code');
                // ensure textarea has the html value for serialize
                $editor.val(html);
            } else {
                html = $editor.val();
            }
            // simple guard: empty or only tags
            if (!html || $('<div>').html(html).text().trim().length === 0) {
                alert('Please write a comment.');
                return;
            }
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize()
            }).done(function(resp){
                // optimistic render new comment at top
                var nowText = 'just now';
                var name = '{{ current_user()->name ?? "You" }}';
                var safeHtml = html; // server sanitization should also occur
                var item = '<div class="comment-box">'
                    + '<strong>'+ name +'</strong>'
                    + '<small class="text-muted d-block">'+ nowText +'</small>'
                    + '<div class="mb-0">'+ safeHtml +'</div>'
                    + '</div>';
                $('#commentsList').prepend(item);
                // clear editor
                if ($editor.length && $editor.data('summernote')) {
                    $editor.summernote('reset');
                    $editor.summernote('code', '');
                } else {
                    $editor.val('');
                }
            }).fail(function(xhr){
                alert('Failed to post comment.');
            });
        });
    }
    
    // Check if jQuery is already loaded
    if (typeof jQuery !== 'undefined') {
        initScripts();
    } else {
        // Wait for jQuery to load
        var checkJQuery = setInterval(function() {
            if (typeof jQuery !== 'undefined') {
                clearInterval(checkJQuery);
                initScripts();
            }
        }, 100);
        
        // Timeout after 5 seconds
        setTimeout(function() {
            clearInterval(checkJQuery);
            if (typeof jQuery === 'undefined') {
                console.error('jQuery failed to load after 5 seconds');
            }
        }, 5000);
    }
})();

// Preview: use window.previewAttachmentClick (defined above with #previewModal) — attached via onclick on .preview-attachment buttons.
</script>
@endsection
