@extends('layouts.app')

@php
    // SEO Meta Tags for Publication Page
    $pageTitle = clean_unicode($publication->title) . ' - ' . (settings()->site_name ?? 'Africa CDC Knowledge Hub');
    $pageDescription = Str::limit(strip_tags(clean_unicode($publication->description ?? '')), 160) ?: (clean_unicode($publication->title) . ' - Published by ' . (clean_unicode($publication->author->name ?? 'Africa CDC')));
    $pageKeywords = $publication->tags->pluck('tag_text')->map(function($tag) { return clean_unicode($tag); })->implode(', ') . ', ' . clean_unicode($publication->theme->description ?? '') . ', ' . clean_unicode($publication->sub_theme->description ?? '');
    $pageImage = $publication->cover ?? $publication->image_url ?? asset('assets/images/cover.png');
    $pageImage = filter_var($pageImage, FILTER_VALIDATE_URL) ? $pageImage : asset($pageImage);
    $canonicalUrl = url('records/resource?id=' . $publication->id);
    $ogType = 'article';
    
    // Get publication date
    $publishDate = $publication->created_at ? $publication->created_at->toIso8601String() : now()->toIso8601String();
    $modifiedDate = $publication->updated_at ? $publication->updated_at->toIso8601String() : $publishDate;
    
    // Get authors
    $authors = [];
    if (!empty($publication->associated_authors)) {
        $authors = array_map('trim', explode(',', $publication->associated_authors));
    }
    if ($publication->author) {
        $authors[] = $publication->author->name;
    }
    $authors = array_unique($authors);
    
    // Get tags for article meta
    $tags = $publication->tags->pluck('tag_text')->toArray();
@endphp

@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "ScholarlyArticle",
    "headline": "{{ addslashes(clean_unicode($publication->title)) }}",
    "description": "{{ addslashes(Str::limit(strip_tags(clean_unicode($publication->description ?? '')), 300)) }}",
    "image": "{{ $pageImage }}",
    "datePublished": "{{ $publishDate }}",
    "dateModified": "{{ $modifiedDate }}",
    "author": [
        @foreach($authors as $index => $author)
        {
            "@type": "Person",
            "name": "{{ addslashes($author) }}"
        }@if(!$loop->last),@endif
        @endforeach
    ],
    @if($publication->author)
    "publisher": {
        "@type": "Organization",
        "name": "{{ addslashes($publication->author->name) }}",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ settings()->logo ?? asset('assets/images/logo.png') }}"
        }
    },
    @endif
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{{ $canonicalUrl }}"
    },
    @if($publication->doi)
    "identifier": {
        "@type": "PropertyValue",
        "propertyID": "DOI",
        "value": "{{ $publication->doi }}"
    },
    @endif
    @if($publication->issn)
    "issn": "{{ $publication->issn }}",
    @endif
    @if($publication->isbn)
    "isbn": "{{ $publication->isbn }}",
    @endif
    @if($publication->license)
    "license": "{{ $publication->license->url ?? '' }}",
    @endif
    @if($publication->year_published)
    "copyrightYear": "{{ $publication->year_published }}",
    @endif
    @if($publication->funder)
    "funder": {
        "@type": "Organization",
        "name": "{{ addslashes($publication->funder) }}"
    },
    @endif
    @if($publication->journal_name)
    "isPartOf": {
        "@type": "Periodical",
        "name": "{{ addslashes($publication->journal_name) }}",
        @if($publication->journal_volume)
        "volumeNumber": "{{ $publication->journal_volume }}",
        @endif
        @if($publication->journal_issue)
        "issueNumber": "{{ $publication->journal_issue }}",
        @endif
        @if($publication->journal_pages)
        "pagination": "{{ $publication->journal_pages }}"
        @endif
    },
    @endif
    "keywords": "{{ $pageKeywords }}",
    "inLanguage": "en",
    "url": "{{ $canonicalUrl }}",
    "breadcrumb": {
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Home",
                "item": "{{ url('/') }}"
            },
            {
                "@type": "ListItem",
                "position": 2,
                "name": "Publications",
                "item": "{{ url('records') }}"
            },
            {
                "@type": "ListItem",
                "position": 3,
                "name": "{{ addslashes(Str::limit($publication->title, 50)) }}",
                "item": "{{ $canonicalUrl }}"
            }
        ]
    }
}
</script>
@endsection

@section('styles')
{{-- Summernote CSS loaded via partial in scripts to match forums --}}
<style>
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
    
    /* Modal specific - no border-radius */
    #previewModal .modal-content,
    #previewModal .modal-header {
        border-radius: 0 !important;
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
    
    /* A4-like PDF Preview Modal Styles */
    #previewModal .modal-dialog {
        max-width: 100vw;
        width: 100vw;
        margin: 0;
        padding: 0;
        transition: all 0.3s ease;
    }
    
    /* Fullscreen modal */
    #previewModal.fullscreen .modal-dialog {
        max-width: 100vw;
        width: 100vw;
        height: 100vh;
        margin: 0;
        padding: 0;
    }
    
    #previewModal.fullscreen .modal-content {
        height: 100vh;
        border-radius: 0;
    }
    
    #previewModal.fullscreen .modal-body {
        max-height: calc(100vh - 60px);
        height: calc(100vh - 60px);
    }
    
    @media (min-width: 1200px) {
        #previewModal:not(.fullscreen) .modal-dialog {
            max-width: 100vw; /* Full width for better viewing */
            width: 100vw;
        }
    }
    
    #previewModal .modal-content {
        border-radius: 0;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        background: #ffffff;
        width: 100%;
        height: 100vh;
    }
    
    #previewModal .modal-header {
        background: linear-gradient(135deg, #119A48 0%, #0e7a3a 100%);
        color: white;
        border-radius: 0;
        border: none;
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
    }
    
    #previewModal .modal-header .header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex: 1;
    }
    
    #previewModal .modal-header .header-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    #previewModal .btn-fullscreen {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 0.4rem 0.6rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    #previewModal .btn-fullscreen:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
    }
    
    #previewModal .modal-title {
        font-weight: 600;
        font-size: 1.1rem;
        color: white;
        margin: 0;
        flex: 1;
    }
    
    #previewModal .close {
        color: white;
        opacity: 0.9;
        text-shadow: none;
        font-weight: 300;
        font-size: 1.5rem;
        padding: 0.5rem;
        line-height: 1;
    }
    
    #previewModal .close:hover {
        opacity: 1;
        color: white;
    }
    
    #previewModal .close:focus {
        outline: none;
    }
    
    #previewModal .modal-body {
        min-height: calc(100vh - 60px);
        height: calc(100vh - 60px);
        max-height: calc(100vh - 60px);
        overflow: hidden;
        padding: 0;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        width: 100%;
    }
    
    /* A4-like paper container */
    #previewModalBody {
        width: 100%;
        height: 100%;
        min-height: 500px;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        opacity: 1;
        transition: opacity 0.15s ease-in-out;
    }
    
    /* PDF iframe - full height */
    #previewModalBody iframe {
        width: 100%;
        height: 100%;
        min-height: calc(100vh - 60px);
        border: none;
        border-radius: 0;
        background: #ffffff;
    }
    
    /* Image preview - centered with full height constraints */
    #previewModalBody img {
        max-width: 100%;
        max-height: 100%;
        height: auto;
        border-radius: 0.25rem;
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        margin: auto;
        display: block;
    }
    
    /* Fullscreen image adjustments */
    #previewModal.fullscreen #previewModalBody img {
        max-height: calc(100vh - 120px);
    }
    
    #previewModal.fullscreen #previewModalBody iframe {
        height: calc(100vh - 120px);
    }
    
    /* Office documents preview */
    #previewModalBody .alert {
        margin: 2rem;
        text-align: center;
    }
    
    .preview-attachment {
        transition: all 0.2s ease;
    }
    
    .preview-attachment:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    /* Button spacing for Preview and Download */
    .d-flex.gap-2 > * + * {
        margin-left: 0.5rem;
    }
    
    /* Loading indicator */
    #previewModalBody .text-center {
        padding: 3rem;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        #previewModal .modal-dialog {
            max-width: 95vw;
            margin: 0.5rem auto;
        }
        
        #previewModal .modal-body {
            max-height: 85vh;
        }
        
        #previewModalBody iframe {
            height: calc(100vh * 0.70);
        }
    }
</style>
@endsection

@section('content')
@php $likes = count($publication->favourited); @endphp
<article itemscope itemtype="https://schema.org/ScholarlyArticle">


<section class="py-4">
    <div class="container">
        <div class="row">
            <!-- Left Column: Title/Info Card and Description -->
            <div class="col-lg-8">
                <!-- Title and Info Section with Cover Image -->
                <div class="card-md mb-3">
                    <div class="row">
                        <!-- Cover Image - Inside Card -->
                        <div class="col-md-4 text-center mb-3 mb-md-0">
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
                            <img src="{{ $image_link }}" class="img-fluid shadow rounded" alt="{{ $publication->title }} - Cover Image" itemprop="image" style="max-height: 250px; width: auto;" onerror="this.onerror=null; this.src='{{ $default_image }}';">
                            
                            <!-- Source, Visits, Year, Comments below image -->
                            <div class="mt-3 text-left" style="font-size: 0.9rem;">
                                <div class="mb-2 ml-2">
                                    <span class="badge" style="background-color: #6c757d; color: #ffffff; padding: 0.35em 0.65em; font-size: 0.875em;">
                                        <i class="fa fa-eye mr-1"></i>Visits: {{ $publication->visits }}
                                    </span>
            </div>
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
                            <h1 itemprop="headline" class="font-weight-bold mb-2" style="font-size: 1.75rem;">{{ clean_unicode($publication->title) }}</h1>
                            <meta itemprop="name" content="{{ clean_unicode($publication->title) }}">
                            <p class="text-muted mb-3" itemprop="about">{{ clean_unicode($publication->theme->description ?? '') }}</p>
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
                            
                            {{-- Associated Authors and Affiliation/Source --}}
                            @if(!empty($publication->associated_authors) || $publication->author)
                            <div class="mb-3" style="font-size: 0.95rem;">
                                @if(!empty($publication->associated_authors))
                                <div class="mb-2">
                                    <strong style="color: #5F5F5F;">Associated Authors:</strong>
                                    <span style="color: #0f172a;">{{ clean_unicode($publication->associated_authors) }}</span>
                                </div>
                                @endif
                                @if($publication->author)
                                <div class="mb-2">
                                    <strong style="color: #5F5F5F;">Affiliation/Source:</strong>
                                    <span style="color: #0f172a;">
                                        @if(!empty($publication->author->orcid))
                                            <a href="https://orcid.org/{{ $publication->author->orcid }}" target="_blank" rel="noopener noreferrer" title="View {{ clean_unicode($publication->author->name) }}'s ORCID profile" style="color: #0f172a; text-decoration: none;">
                                                {{ clean_unicode($publication->author->name) }}
                                                <i class="fa fa-external-link-alt" style="font-size: 0.75rem; margin-left: 3px;"></i>
                                            </a>
                                        @else
                                            {{ clean_unicode($publication->author->name) }}
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
                                                $shareUrl = url('records/resource') . '?id=' . $publication->id; 
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
                    
                    <!-- Action Buttons - Floated Right -->
                    <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                        <div>
                <button onclick="summarise({{ $publication->id }})" class="btn btn-au btn-sm">
                    <i class="fa-solid fa-microchip"></i> AI Processing (Summarizer)
                </button>
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
                                <div class="d-flex justify-content-between align-items-center">
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
                                            $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
                                            $office = in_array($ext, ['ppt','pptx','doc','docx','xls','xlsx']) ? 1 : 0;
                                            
                                            // Format attachment name: replace underscores with spaces and truncate to 20 characters
                                            $displayName = $file->description ?? pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_FILENAME);
                                            $displayName = str_replace('_', ' ', $displayName);
                                            $displayName = Str::limit($displayName, 20);
                                            
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
                                                    <strong>{{ $displayName }}</strong>
                                                    <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">
                                                        {{ strtoupper($ext) }} file
                                                    </small>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-au btn-sm preview-attachment"
                                                            data-file-url="{{ $url }}" data-file-ext="{{ $ext }}" data-file-office="{{ $office }}"
                                                            title="Preview file"
                                                            onclick="window.previewAttachmentClick(event, this); return false;">
                                                        <i class="fa fa-eye mr-1"></i> Preview
                                                    </button>
                                                    <a href="{{ $url }}" target="_blank" class="btn btn-au btn-sm" title="Download file" download>
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
                    @if ($publication->is_embedded)
                        <div class="responsive-iframe-container mb-4">
                            <iframe src="{{ $publication->publication }}" allowfullscreen></iframe>
                        </div>
                    @elseif ($publication->is_video)
                        <div class="mb-4">
                            <iframe width="100%" height="400" src="{{ $publication->publication }}"></iframe>
                        </div>
                    @endif
                    <h2 class="section-heading">Description</h2>
                    <div itemprop="articleBody">
                    @php
                        // Process publication description to detect and embed video links and convert URLs to clickable links
                        $processedDescription = detect_and_embed_video_links(clean_unicode($publication->description ?? ''), 180, 180);
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
                            $coverImage = $relatedPub->cover ?? null;
                            if ($coverImage) {
                                if ($relatedPub->cover_is_exteranl && filter_var($coverImage, FILTER_VALIDATE_URL)) {
                                    $cover = $coverImage;
                                } else {
                                    $cover = storage_link('uploads/publications/' . $coverImage);
                                }
                            } else {
                                $cover = asset('assets/images/cover.png');
                            }
                            $detailsUrl = url('records/resource?id=' . $relatedPub->id);
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
                                        <a href="https://orcid.org/{{ $relatedPub->author->orcid }}" target="_blank" rel="noopener noreferrer" title="View {{ $relatedPub->author->name }}'s ORCID profile" style="color: inherit; text-decoration: none;" onclick="event.stopPropagation();">
                                            {{ Str::limit($relatedPub->author->name, 40) }}
                                            <i class="fa fa-external-link-alt" style="font-size: 0.65rem; margin-left: 2px;"></i>
                                        </a>
                                    @else
                                        {{ Str::limit($relatedPub->author->name, 40) }}
                        @endif
                                </div>
                                @endif
                                @if($relatedPub->created_at)
                                <div class="related-resource-meta">
                                    <i class="fa fa-calendar mr-1"></i>{{ $relatedPub->created_at->format('M Y') }}
                                </div>
                                @endif
                                @if(!empty($relatedPub->description))
                                <div class="related-resource-desc">{{ Str::limit(strip_tags($relatedPub->description), 140) }}</div>
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
                                <div class="d-flex justify-content-between align-items-center">
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
                                            $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
                                            $office = in_array($ext, ['ppt','pptx','doc','docx','xls','xlsx']) ? 1 : 0;
                                            
                                            // Format attachment name: replace underscores with spaces and truncate to 20 characters
                                            $displayName = $file->description ?? pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_FILENAME);
                                            $displayName = str_replace('_', ' ', $displayName);
                                            $displayName = Str::limit($displayName, 20);
                                            
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
                                                    <strong>{{ $displayName }}</strong>
                                                    <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">
                                                        {{ strtoupper($ext) }} file
                                                    </small>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-au btn-sm preview-attachment"
                                                            data-file-url="{{ $url }}" data-file-ext="{{ $ext }}" data-file-office="{{ $office }}"
                                                            title="Preview file"
                                                            onclick="window.previewAttachmentClick(event, this); return false;">
                                                        <i class="fa fa-eye mr-1"></i> Preview
                                                    </button>
                                                    <a href="{{ $url }}" target="_blank" class="btn btn-au btn-sm" title="Download file" download>
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
                                    <a href="{{ url('records/resource') }}?id={{ $version->id }}" style="text-decoration: none; color: inherit; display: block;">
                                        <span style="word-break: break-word; white-space: normal;">
                                            {{ Str::limit(strip_tags($version->title ?? 'Untitled'), 60) }} 
                                            <span class="text-muted">(Version {{ $version->version_no }})</span>
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                            @if ($publication->parent_id > 0)
                                <li class="list-group-item" style="word-wrap: break-word; overflow-wrap: break-word;">
                                    <a href="{{ url('records/resource') }}?id={{ $publication->parent_id }}" style="text-decoration: none; color: inherit; display: block;">
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
                        <label class="meta-label">Associated Authors</label><span class="meta-value">{{ $publication->associated_authors ?? 'N/A' }}</span>
                        
                        {{-- Tags --}}
                        @if($publication->tags && $publication->tags->count() > 0)
                        <label class="meta-label">Tags</label>
                        <span class="meta-value">
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                @foreach($publication->tags as $pubTag)
                                    @if($pubTag->tag)
                                        <a href="{{ url('records?tag=' . $pubTag->tag_id) }}" class="badge badge-secondary" style="background-color: #6c757d; color: #ffffff; padding: 0.25em 0.5em; font-size: 0.8em; text-decoration: none;">
                                            {{ $pubTag->tag->tag_text }}
                                        </a>
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
                    @include('common.favourites_btn',['row'=>$publication])
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
                                <strong>{{ $comment->user->name ?? 'Anonymous' }}</strong>
                                <small class="text-muted d-block">{{ time_ago($comment->created_at) }}</small>
                                <div class="mb-0">{!! nl2br(e($comment->comment)) !!}</div>
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
                                            {{ truncate($summary->title, 100) }} by {{ $summary->author->name ?? '' }}
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
@include('common.ai-summary')
<!-- Modal for preview - Bootstrap 4 compatible -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 100vw; width: 100vw; margin: 0; padding: 0;">
    <div class="modal-content">
      <div class="modal-header">
        <div class="header-left">
          <button type="button" class="btn-fullscreen" id="toggleFullscreen" title="Toggle Fullscreen">
            <i class="fa fa-expand" id="fullscreenIcon"></i>
          </button>
          <h5 class="modal-title" id="previewModalLabel">
            <i class="fa fa-file-pdf mr-2"></i>Attachment Preview
          </h5>
        </div>
        <div class="header-actions">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="background: none; border: none; font-size: 1.5rem; opacity: 0.9; cursor: pointer; padding: 0.5rem; color: white;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      </div>
      <div class="modal-body" id="previewModalBody" style="padding: 0; min-height: calc(100vh - 60px); height: calc(100vh - 60px);">
        <div class="text-center w-100" style="padding: 3rem;">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading preview...</span>
          </div>
          <p class="mt-3 text-muted">Loading preview...</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Preview handler - defined immediately so it's available for onclick -->
<script>
// Define preview handler IMMEDIATELY so it's available for onclick
window.previewAttachmentClick = function(event, button) {
    event.preventDefault();
    event.stopPropagation();
    
    var fileUrl = button.getAttribute('data-file-url');
    var ext = (button.getAttribute('data-file-ext') || '').toLowerCase();
    var isOffice = (button.getAttribute('data-file-office') || '0') === '1';
    
    if (!fileUrl) {
        console.error('File URL not found on button');
        return false;
    }
    
    // Use jQuery if available
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.modal !== 'undefined') {
        var $ = jQuery;
        var modal = $('#previewModal');
        var modalBody = $('#previewModalBody');
        var modalTitle = $('#previewModalLabel');
        
        if (modal.length === 0) {
            console.error('Modal not found in DOM');
            return false;
        }
        
        // Reset fullscreen state when opening
        modal.removeClass('fullscreen');
        $('#fullscreenIcon').removeClass('fa-compress').addClass('fa-expand');
        
        // Update modal title based on file type BEFORE showing modal
        if(ext === 'pdf') {
            modalTitle.html('<i class="fa fa-file-pdf mr-2"></i>PDF Preview');
        } else if(['jpg','jpeg','png','gif','webp'].includes(ext)) {
            modalTitle.html('<i class="fa fa-file-image mr-2"></i>Image Preview');
        } else if(isOffice) {
            modalTitle.html('<i class="fa fa-file-alt mr-2"></i>Document Preview');
        } else {
            modalTitle.html('<i class="fa fa-file mr-2"></i>Attachment Preview');
        }
        
        // Generate content based on file type
        var content = '';
        if(['jpg','jpeg','png','gif','webp'].includes(ext)) {
            content = '<div style="text-align:center;padding:2rem;"><img src="'+fileUrl+'" class="img-fluid" style="max-height:calc(100vh - 120px);max-width:100%;margin:auto;display:block;" onerror="this.parentElement.innerHTML=\'<div class=\\\'alert alert-warning\\\' style=\\\'margin:2rem;\\\'>Failed to load image. <a href=\\\''+fileUrl+'\\\' target=\\\'_blank\\\'>Download file</a></div>\';"></div>';
        } else if(ext === 'pdf') {
            content = '<iframe src="'+fileUrl+'#toolbar=1&navpanes=0&scrollbar=1" style="width:100%;height:100%;min-height:calc(100vh - 60px);border:none;background:#ffffff;"></iframe>';
        } else if(isOffice) {
            var gdocs = 'https://docs.google.com/viewer?url='+encodeURIComponent(fileUrl)+'&embedded=true';
            content = '<iframe src="'+gdocs+'" style="width:100%;height:100%;min-height:calc(100vh - 60px);border:none;background:#ffffff;"></iframe>';
        } else {
            content = '<div class="alert alert-info" style="margin:2rem;text-align:center;"><i class="fa fa-info-circle mr-2"></i>Preview not available for this file type.<br><a href="'+fileUrl+'" target="_blank" class="btn btn-primary btn-sm mt-2"><i class="fa fa-download mr-1"></i>Download File</a></div>';
        }
        
        // Prepare content BEFORE showing modal to prevent flicker
        // Set loading state first
        modalBody.html('<div class="text-center w-100" style="padding: 3rem;"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading preview...</span></div><p class="mt-3 text-muted">Loading preview...</p></div>');
        
        // Show modal
        modal.modal('show');
        
        // Update content AFTER modal is fully visible (when animation completes)
        // Using 'shown.bs.modal' ensures modal is fully rendered before content swap
        modal.off('shown.bs.modal').one('shown.bs.modal', function() {
            // Fade out loading, then fade in content for smooth transition
            modalBody.css('opacity', '0.7');
            setTimeout(function() {
                modalBody.html(content);
                setTimeout(function() {
                    modalBody.css('opacity', '1');
                }, 10);
            }, 50);
        });
        
        return false;
    } else {
        // Fallback: try to show modal using pure JavaScript
        var modal = document.getElementById('previewModal');
        if (modal) {
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
            
            var modalBody = document.getElementById('previewModalBody');
            if (modalBody) {
                var content = '';
                if(['jpg','jpeg','png','gif','webp'].includes(ext)) {
                    content = '<div style="text-align:center;padding:2rem;"><img src="'+fileUrl+'" class="img-fluid" style="max-height:calc(100vh - 120px);max-width:100%;margin:auto;display:block;"></div>';
                } else if(ext === 'pdf') {
                    content = '<iframe src="'+fileUrl+'#toolbar=1&navpanes=0&scrollbar=1" style="width:100%;height:100%;min-height:calc(100vh - 60px);border:none;background:#ffffff;"></iframe>';
                } else if(isOffice) {
                    var gdocs = 'https://docs.google.com/viewer?url='+encodeURIComponent(fileUrl)+'&embedded=true';
                    content = '<iframe src="'+gdocs+'" style="width:100%;height:100%;min-height:calc(100vh - 60px);border:none;background:#ffffff;"></iframe>';
                } else {
                    content = '<div class="alert alert-info" style="margin:2rem;text-align:center;"><i class="fa fa-info-circle mr-2"></i>Preview not available for this file type.<br><a href="'+fileUrl+'" target="_blank" class="btn btn-primary btn-sm mt-2"><i class="fa fa-download mr-1"></i>Download File</a></div>';
                }
                modalBody.innerHTML = content;
            }
            return false;
        } else {
            console.error('Modal element not found in DOM');
            return false;
        }
    }
};

// Fullscreen toggle handler
(function() {
    function initFullscreenToggle() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initFullscreenToggle, 100);
            return;
        }
        
        var $ = jQuery;
        
        // Toggle fullscreen on button click
        $(document).on('click', '#toggleFullscreen', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var modal = $('#previewModal');
            var icon = $('#fullscreenIcon');
            
            if (modal.hasClass('fullscreen')) {
                // Exit fullscreen
                modal.removeClass('fullscreen');
                icon.removeClass('fa-compress').addClass('fa-expand');
            } else {
                // Enter fullscreen
                modal.addClass('fullscreen');
                icon.removeClass('fa-expand').addClass('fa-compress');
            }
            
            // Adjust iframe/image heights for fullscreen
            setTimeout(function() {
                if (modal.hasClass('fullscreen')) {
                    modal.find('iframe').css('height', 'calc(100vh - 120px)');
                    modal.find('img').css('max-height', 'calc(100vh - 120px)');
                } else {
                    modal.find('iframe').css('height', 'calc(100vh * 0.75)');
                    modal.find('img').css('max-height', 'calc(100vh * 0.75)');
                }
            }, 100);
        });
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFullscreenToggle);
    } else {
        initFullscreenToggle();
    }
})();
</script>

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
        
        // Preview attachment handler is now moved outside initScripts() to ensure it's always attached

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

// Preview attachment handler - Multiple approaches for maximum compatibility
// Approach 1: Simple direct attachment (immediate)
(function() {
    function attachHandler() {
        // Try multiple ways to attach the handler
        
        // Method 1: Using document.addEventListener (pure JS)
        var buttons = document.querySelectorAll('.preview-attachment');
        console.log('Found ' + buttons.length + ' preview buttons via querySelectorAll');
        
        buttons.forEach(function(button, index) {
            // Remove any existing listeners
            var newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            
            newButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                alert('Preview button clicked! (Method 1: addEventListener)');
                
                var fileUrl = this.getAttribute('data-file-url');
                var ext = (this.getAttribute('data-file-ext') || '').toLowerCase();
                var isOffice = (this.getAttribute('data-file-office') || '0') === '1';
                
                console.log('Click handler fired:', {fileUrl: fileUrl, ext: ext, isOffice: isOffice});
                
                if (!fileUrl) {
                    alert('File URL not found on button');
                    return false;
                }
                
                // Try to show modal using jQuery if available
                if (typeof jQuery !== 'undefined') {
                    var $ = jQuery;
                    var modal = $('#previewModal');
                    var modalBody = $('#previewModalBody');
                    var modalTitle = $('#previewModalLabel');
                    
                    if (modal.length === 0) {
                        alert('Modal not found in DOM');
                        return false;
                    }
                    
                    // Update modal title
                    if(ext === 'pdf') {
                        modalTitle.html('<i class="fa fa-file-pdf mr-2"></i>PDF Preview');
                    } else if(['jpg','jpeg','png','gif','webp'].includes(ext)) {
                        modalTitle.html('<i class="fa fa-file-image mr-2"></i>Image Preview');
                    } else if(isOffice) {
                        modalTitle.html('<i class="fa fa-file-alt mr-2"></i>Document Preview');
                    } else {
                        modalTitle.html('<i class="fa fa-file mr-2"></i>Attachment Preview');
                    }
                    
                    // Generate content
                    var content = '';
                    if(['jpg','jpeg','png','gif','webp'].includes(ext)) {
                        content = '<div style="text-align:center;padding:2rem;"><img src="'+fileUrl+'" class="img-fluid" style="max-height:calc(100vh * 0.75);max-width:100%;margin:auto;display:block;"></div>';
                    } else if(ext === 'pdf') {
                        content = '<iframe src="'+fileUrl+'#toolbar=1&navpanes=0&scrollbar=1" style="width:100%;height:calc(100vh * 0.75);min-height:500px;border:none;background:#ffffff;"></iframe>';
                    } else if(isOffice) {
                        var gdocs = 'https://docs.google.com/viewer?url='+encodeURIComponent(fileUrl)+'&embedded=true';
                        content = '<iframe src="'+gdocs+'" style="width:100%;height:calc(100vh * 0.75);min-height:500px;border:none;background:#ffffff;"></iframe>';
                    } else {
                        content = '<div class="alert alert-info" style="margin:2rem;text-align:center;"><i class="fa fa-info-circle mr-2"></i>Preview not available for this file type.<br><a href="'+fileUrl+'" target="_blank" class="btn btn-primary btn-sm mt-2"><i class="fa fa-download mr-1"></i>Download File</a></div>';
                    }
                    
                    modalBody.html(content);
                    
                    if (typeof $.fn.modal !== 'undefined') {
                        modal.modal('show');
                        alert('Modal shown using jQuery Bootstrap');
                    } else {
                        alert('jQuery modal plugin not available');
                    }
                } else {
                    alert('jQuery not available');
                }
                
                return false;
            });
        });
    }
    
    // Try multiple times to ensure buttons are found
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(attachHandler, 100);
            setTimeout(attachHandler, 500);
            setTimeout(attachHandler, 1000);
        });
    } else {
        attachHandler();
        setTimeout(attachHandler, 100);
        setTimeout(attachHandler, 500);
    }
})();

// Approach 2: jQuery-based handler (if jQuery is available)
(function() {
    function attachJQueryHandler() {
        if (typeof jQuery === 'undefined') {
            setTimeout(attachJQueryHandler, 100);
            return;
        }
        
        var $ = jQuery;
        
        // Remove existing handlers
        $(document).off('click', '.preview-attachment');
        
        // Attach new handler
        $(document).on('click', '.preview-attachment', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            alert('Preview button clicked! (Method 2: jQuery)');
            
            var $button = $(this);
            var fileUrl = $button.data('file-url');
            var ext = ($button.data('file-ext') || '').toString().toLowerCase();
            var isOffice = ($button.data('file-office') || '0').toString() === '1';
            
            console.log('jQuery handler fired:', {fileUrl: fileUrl, ext: ext, isOffice: isOffice});
            
            if (!fileUrl) {
                alert('File URL not found');
                return false;
            }
            
            var modal = $('#previewModal');
            var modalBody = $('#previewModalBody');
            var modalTitle = $('#previewModalLabel');
            
            if (modal.length === 0) {
                alert('Modal not found');
                return false;
            }
            
            // Update modal title
            if(ext === 'pdf') {
                modalTitle.html('<i class="fa fa-file-pdf mr-2"></i>PDF Preview');
            } else if(['jpg','jpeg','png','gif','webp'].includes(ext)) {
                modalTitle.html('<i class="fa fa-file-image mr-2"></i>Image Preview');
            } else if(isOffice) {
                modalTitle.html('<i class="fa fa-file-alt mr-2"></i>Document Preview');
            } else {
                modalTitle.html('<i class="fa fa-file mr-2"></i>Attachment Preview');
            }
            
            // Generate content
            var content = '';
            if(['jpg','jpeg','png','gif','webp'].includes(ext)) {
                content = '<div style="text-align:center;padding:2rem;"><img src="'+fileUrl+'" class="img-fluid" style="max-height:calc(100vh * 0.75);max-width:100%;margin:auto;display:block;"></div>';
            } else if(ext === 'pdf') {
                content = '<iframe src="'+fileUrl+'#toolbar=1&navpanes=0&scrollbar=1" style="width:100%;height:calc(100vh * 0.75);min-height:500px;border:none;background:#ffffff;"></iframe>';
            } else if(isOffice) {
                var gdocs = 'https://docs.google.com/viewer?url='+encodeURIComponent(fileUrl)+'&embedded=true';
                content = '<iframe src="'+gdocs+'" style="width:100%;height:calc(100vh * 0.75);min-height:500px;border:none;background:#ffffff;"></iframe>';
            } else {
                content = '<div class="alert alert-info" style="margin:2rem;text-align:center;"><i class="fa fa-info-circle mr-2"></i>Preview not available for this file type.<br><a href="'+fileUrl+'" target="_blank" class="btn btn-primary btn-sm mt-2"><i class="fa fa-download mr-1"></i>Download File</a></div>';
            }
            
            modalBody.html(content);
            
            if (typeof $.fn.modal !== 'undefined') {
                modal.modal('show');
            } else {
                alert('Bootstrap modal plugin not available');
            }
            
            return false;
        });
        
        console.log('jQuery handler attached. Found buttons:', $('.preview-attachment').length);
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(attachJQueryHandler, 200);
        });
    } else {
        setTimeout(attachJQueryHandler, 200);
    }
})();
</script>
@endsection
