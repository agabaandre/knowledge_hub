@extends('layouts.app')

@php
    // SEO Meta Tags for Publication Page
    $pageTitle = $publication->title . ' - ' . (settings()->site_name ?? 'Africa CDC Knowledge Hub');
    $pageDescription = Str::limit(strip_tags($publication->description ?? ''), 160) ?: ($publication->title . ' - Published by ' . ($publication->author->name ?? 'Africa CDC'));
    $pageKeywords = $publication->tags->pluck('tag_text')->implode(', ') . ', ' . ($publication->theme->description ?? '') . ', ' . ($publication->sub_theme->description ?? '');
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
    "headline": "{{ addslashes($publication->title) }}",
    "description": "{{ addslashes(Str::limit(strip_tags($publication->description ?? ''), 300)) }}",
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
        border-radius: 0.75rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    .badge-au {
        background-color: #119A48;
        color: #fff;
        font-size: 0.85rem;
        border-radius: 50px;
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
        border-radius: 0.5rem;
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
</style>
@endsection

@section('content')
@php $likes = count($publication->favourited); @endphp
<article itemscope itemtype="https://schema.org/ScholarlyArticle">
<section class="py-5" style="background: #fff;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-3 text-center mb-3">
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
                <img src="{{ $image_link }}" class="img-fluid shadow rounded" alt="{{ $publication->title }} - Cover Image" itemprop="image" style="max-height: 300px; width: auto;" onerror="this.onerror=null; this.src='{{ $default_image }}';">
            </div>
            <div class="col-md-6">
                <h1 itemprop="headline" class="font-weight-bold mb-2">{{ $publication->title }}</h1>
                <meta itemprop="name" content="{{ $publication->title }}">
                <p class="text-muted" itemprop="about">{{ $publication->theme->description ?? '' }}</p>
                <div class="d-flex flex-wrap mb-3">
                    <span class="badge badge-au">
                        {{ !$publication->is_version ? $publication->sub_theme->description ?? '' : 'Version ' . $publication->version_no }}
                    </span>
                    @if($likes)
                        <span class="badge badge-dark">
                            <i class="lni lni-heart"></i> {{ $likes }} like{{ $likes > 1 ? 's' : '' }}
                        </span>
                    @endif
                </div>
                
                {{-- Associated Authors and Affiliation/Source --}}
                @if(!empty($publication->associated_authors) || $publication->author)
                <div class="mb-3" style="font-size: 0.95rem;">
                    @if(!empty($publication->associated_authors))
                    <div class="mb-2">
                        <strong style="color: #5F5F5F;">Associated Authors:</strong>
                        <span style="color: #0f172a;">{{ $publication->associated_authors }}</span>
                    </div>
                    @endif
                    @if($publication->author)
                    <div>
                        <strong style="color: #5F5F5F;">Affiliation/Source:</strong>
                        <span style="color: #0f172a;">
                            @if(!empty($publication->author->orcid))
                                <a href="https://orcid.org/{{ $publication->author->orcid }}" target="_blank" rel="noopener noreferrer" title="View {{ $publication->author->name }}'s ORCID profile" style="color: #0f172a; text-decoration: none;">
                                    {{ $publication->author->name }}
                                    <i class="fa fa-external-link-alt" style="font-size: 0.75rem; margin-left: 3px;"></i>
                                </a>
                            @else
                                {{ $publication->author->name }}
                            @endif
                        </span>
                    </div>
                    @endif
                </div>
                @endif
                
                <button onclick="summarise({{ $publication->id }})" class="btn btn-au btn-sm">
                    <i class="fa-solid fa-microchip"></i> AI Processing (Summarizer)
                </button>
            </div>
            <div class="col-md-3 mt-3 mt-md-0">
                @if ($publication->publication)
                    <a href="{{ $publication->publication }}" target="_blank" class="btn btn-outline-success btn-block mb-2">
                        <i class="fa fa-eye"></i> Browse Resource
                    </a>
                @endif
                @auth
                    <a href="{{ route('account.newversion') }}?id={{ $publication->id }}" class="btn btn-outline-danger btn-block mb-2">
                        <i class="fa fa-plus"></i> Submit Version
                    </a>
                    <a href="{{ route('account.summarize') }}?id={{ $publication->id }}" class="btn btn-outline-danger btn-block">
                        <i class="fa fa-file"></i> Submit Summary
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>

<section class="py-4">
    <div class="container">
        <div class="row">
            <!-- Left -->
            <div class="col-lg-8">
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
                        <p>{!! $publication->description !!}</p>
                    </div>
                </div>

                <div class="card-md">
                    <h2 class="section-heading">Comments ({{ count($publication->comments) }})</h2>
                    @auth
                        <form id="commentForm" action="{{ url('records/comment') }}" method="post" class="mb-3">
                            @csrf
                            <input type="hidden" name="publication_id" value="{{ $publication->id }}">
                            <input type="hidden" name="user_id" value="{{ current_user()->user_id }}">
                            <div class="form-group">
                                <label class="mb-2">Write a comment</label>
                                <textarea name="comment" class="form-control summernote-sm" cols="30" rows="6" placeholder="Type your comment...."></textarea>
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
            </div>

            <!-- Right -->
            <div class="col-lg-4">
                <div class="card-md">
                    <h5 class="section-heading">Resource Details</h5>
                    <div>
                        <label class="meta-label">Source</label>
                        <span class="meta-value">
                            @if(!empty($publication->author->orcid))
                                <a href="https://orcid.org/{{ $publication->author->orcid }}" target="_blank" rel="noopener noreferrer" title="View {{ $publication->author->name }}'s ORCID profile" style="color: inherit; text-decoration: none;">
                                    {{ $publication->author->name }}
                                    <i class="fa fa-external-link-alt" style="font-size: 0.75rem; margin-left: 3px;"></i>
                                </a>
                            @else
                                {{ $publication->author->name }}
                            @endif
                        </span>
                        <label class="meta-label">Visits</label><span class="meta-value">{{ $publication->visits }}</span>
                        @if(!empty($publication->year_published))
                        <label class="meta-label">Year</label><span class="meta-value">{{ $publication->year_published }}</span>
                        @endif
                        <label class="meta-label">Likes</label><span class="meta-value">{{ $likes }}</span>
                        <label class="meta-label">Comments</label><span class="meta-value">{{ count($publication->comments) }}</span>
                        <label class="meta-label">Category</label><span class="meta-value">{{ @$publication->data_category->category_name }}</span>
                        <label class="meta-label">Sub Category</label><span class="meta-value">{{ $publication->sub_category->category_name ?? '' }}</span>
                        <label class="meta-label">Theme</label><span class="meta-value">{!! $publication->theme->description ?? '' !!}</span>
                        <label class="meta-label">Sub-Theme</label><span class="meta-value">{!! nl2br($publication->sub_theme->description ?? '') !!}</span>
                        <label class="meta-label">Associated Authors</label><span class="meta-value">{{ $publication->associated_authors ?? 'N/A' }}</span>
                        
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

                @if ($publication->has_attachments)
                    <div class="card-md">
                        <h5 class="section-heading">Attachments</h5>
                        <ul class="list-group">
                            @foreach ($publication->attachments as $i => $file)
                                @php
                                    $url = $file->file;
                                    $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
                                    $office = in_array($ext, ['ppt','pptx','doc','docx','xls','xlsx']) ? 1 : 0;
                                @endphp
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fa fa-paperclip text-muted mr-2"></i> {{ $file->description ?? 'Attachment ' . ($i + 1) }}</span>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-secondary preview-attachment"
                                                data-file-url="{{ $url }}" data-file-ext="{{ $ext }}" data-file-office="{{ $office }}">
                                            <i class="fa fa-eye"></i> Preview
                                        </button>
                                        <a class="btn btn-outline-primary" href="{{ $url }}" target="_blank">
                                            <i class="fa fa-download"></i>
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (count($publication->versioning) || $publication->parent_id > 0)
                    <div class="card-md">
                        <h5 class="section-heading">Versions</h5>
                        <ul class="list-group">
                            @foreach ($publication->versioning as $version)
                                <li class="list-group-item">
                                    <a href="{{ url('records/resource') }}?id={{ $version->id }}">Version {{ $version->version_no }}</a>
                                </li>
                            @endforeach
                            @if ($publication->parent_id > 0)
                                <li class="list-group-item">
                                    <a href="{{ url('records/resource') }}?id={{ $publication->parent_id }}">
                                        <i class="fa fa-link"></i> Original Version
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                @endif

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

                <div class="card-md">
                    <h5 class="section-heading">Rate this Resource</h5>
                    @include('partials.general.rating')
                </div>

                <div class="card-md">
                    <h5 class="section-heading">Share This Resource</h5>
                    {{ share_buttons(url('records/resource') . '?id=' . $publication->id) }}
                </div>
            </div>
        </div>
    </div>
</section>
</article>
@include('common.ai-summary')
<!-- Modal for preview -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width:95%">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="previewModalLabel">Attachment Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="previewModalBody" style="min-height:70vh;display:flex;align-items:center;justify-content:center;background:#f8fafc;">
        <div class="text-center w-100">Loading preview...</div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
@include('partials.general.summernote')
<script>
// Wait for both jQuery and Summernote to be loaded
(function() {
    function initScripts() {
        // Check if jQuery is available
        if (typeof jQuery === 'undefined') {
            console.error('jQuery is not loaded');
            return;
        }
        
        var $ = jQuery;
        
        $(document).on('click', '.preview-attachment', function() {
            var fileUrl = $(this).data('file-url');
            var ext = ($(this).data('file-ext') || '').toString();
            var isOffice = ($(this).data('file-office') || '0').toString() === '1';
            var modalBody = $('#previewModalBody');
            var content = '';
            if(['jpg','jpeg','png','gif','webp'].includes(ext)) {
                content = '<img src="'+fileUrl+'" class="img-fluid" style="max-height:75vh;max-width:100%;margin:auto;display:block;">';
            } else if(ext === 'pdf') {
                content = '<iframe src="'+fileUrl+'#toolbar=1&navpanes=0&scrollbar=1" style="width:100%;height:75vh;border:none;"></iframe>';
            } else if(isOffice) {
                var gdocs = 'https://docs.google.com/viewer?url='+encodeURIComponent(fileUrl)+'&embedded=true';
                content = '<iframe src="'+gdocs+'" style="width:100%;height:75vh;border:none;"></iframe>';
            } else {
                content = '<div class="alert alert-info">Preview not available. <a href="'+fileUrl+'" target="_blank">Download/Open file</a></div>';
            }
            modalBody.html(content);
            var modal = new bootstrap.Modal(document.getElementById('previewModal'));
            modal.show();
        });

        // Initialize Summernote for comment textarea - wait for document ready
        $(document).ready(function() {
            // Wait a bit more to ensure Summernote is fully loaded
            setTimeout(function() {
                var $editor = $('textarea.summernote-sm');
                if ($editor.length && !$editor.data('summernote')) {
                    // Check if Summernote is available
                    if (typeof $.fn.summernote === 'undefined') {
                        console.error('Summernote plugin is not loaded');
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
                                    // Use centralized upload endpoint
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
                    } catch(e) {
                        console.error('Summernote initialization failed:', e);
                    }
                }

                $('#commentForm').on('submit', function(e){
                    e.preventDefault();
                    var html;
                    var $editor = $('textarea.summernote-sm');
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
            }, 100);
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
</script>
@endsection
