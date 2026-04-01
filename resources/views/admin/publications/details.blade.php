@extends(admin_layout())

@section('styles')
    <style>
        .ap-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px}
        .ap-card-header{padding:12px 16px;border-bottom:1px solid #e2e8f0;background:#f8fafc}
        .ap-card-body{padding:16px}
        .ap-meta label{display:block;font-size:.75rem;color:#64748b;margin-bottom:2px}
        .ap-meta .value{font-weight:600;color:#0f172a}
        .ap-cover{border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;background:#f8fafc}
        .ap-list{list-style:none;padding-left:0;margin:0}
        .ap-list li{padding:6px 0;border-bottom:1px dashed #e5e7eb}
        .ap-list li:last-child{border-bottom:none}
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Publication Details</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Publish</a></li>
                <li class="breadcrumb-item active" aria-current="page">Publication Details</li>
            </ol>
        </div>
    </div>

    @php
        // Derive image link with robust fallback like Top Searches
        $image_link = $publication->cover ?? $publication->image_url ?? null;
        $default_image = asset('assets/images/cover.png');
        $pubUrl = trim((string) ($publication->publication ?? ''));
        $embedUrl = get_video_embed_url($pubUrl);
        $directVideo = is_direct_video_file_url($pubUrl);
        $isVideoLink = is_video_platform_url($pubUrl);
        if (empty($image_link)) {
            $image_link = $default_image;
        } elseif (!filter_var($image_link, FILTER_VALIDATE_URL)) {
            if (strpos($image_link, 'storage/') !== false || strpos($image_link, 'uploads/') !== false) {
                $image_link = asset($image_link);
            } elseif (strpos($image_link, '/') === 0) {
                $image_link = url($image_link);
            } else {
                $image_link = $default_image;
            }
        }
    @endphp

    <div class="">
            @include('layouts.partials.alerts')
        <div class="ap-card mb-3" style="width:100%;">
            <div class="ap-card-header d-flex align-items-center justify-content-between" style="width:100%;">
                <div class="d-flex align-items-center">
                    <div class="mr-3" style="width:48px;height:48px;overflow:hidden;border-radius:8px;background:#f3f4f6;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;">
                        <img src="{{ $image_link }}" alt="Cover" style="width:100%;height:100%;object-fit:cover;" onerror="this.onerror=null; this.src='{{ asset('assets/images/cover.png') }}';">
                            </div>
                    <div>
                        <h4 class="mb-0" style="font-weight:700;color:#0f172a;">{!! $publication->title !!}</h4>
                        <div class="text-muted" style="font-size:.9rem;">{!! $publication->theme->description ?? '' !!}</div>
                        @if(!$publication->is_version)
                            <div class="text-muted" style="font-size:.8rem;">{!! $publication->sub_theme->description ?? '' !!}</div>
                        @else
                            <span class="badge badge-success">Version {{ $publication->version_no }}</span>
                                @endif
                        <div class="mt-2 d-flex flex-wrap" style="gap:8px;">
                            @if($publication->author)
                                <span class="badge badge-light" title="Source"><i class="fa fa-user mr-1"></i>{{ $publication->author->name }}</span>
                                        @endif
                            @if(!empty($publication->year_published))
                                <span class="badge badge-light" title="Year"><i class="fa fa-calendar mr-1"></i>{{ $publication->year_published }}</span>
                                @endif
                            @if(@$publication->data_category)
                                <span class="badge badge-light" title="Category"><i class="fa fa-folder-open mr-1"></i>{{ @$publication->data_category->category_name }}</span>
                                        @endif
                            <span class="badge badge-light" title="Views"><i class="fa fa-eye mr-1"></i>{{ $publication->visits }}</span>
                            <span class="badge badge-light" title="Likes"><i class="fa fa-heart mr-1"></i>{{ count($publication->favourited ?? []) }}</span>
                            <span class="badge badge-light" title="Comments"><i class="fa fa-comments mr-1"></i>{{ count($publication->comments) }}</span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    @php
                        $statusText = $publication->is_approved ? 'Approved' : ($publication->is_rejected ? 'Rejected' : 'Pending');
                        $statusClass = $publication->is_approved ? 'badge-success' : ($publication->is_rejected ? 'badge-danger' : 'badge-secondary');
                    @endphp
                    <span class="badge {{ $statusClass }} mr-2">{{ $statusText }}</span>
                    <a href="{{ url('admin/publications/edit') }}?id={{ $publication->id }}"
                       class="btn btn-outline-dark btn-sm ml-1">
                        <i class="fa fa-edit mr-1"></i>Edit
                    </a>
                    <a href="{{ url('records/resource') }}?id={{ $publication->id }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="btn btn-outline-success btn-sm ml-1">
                        <i class="fa fa-external-link-alt mr-1"></i>Preview Public
                    </a>
                    {{-- External link moved to Resources & Attachments card below --}}
                    @if ($publication->is_approved == 0)
                        <a href="#approval-modal" data-toggle="modal" class="btn btn-success btn-sm ml-2"><i class="fa fa-check-circle mr-1"></i>{{ $publication->is_rejected == 0 ? 'Approve' : 'Reconsider' }}</a>
                    @endif
                    @if (($publication->is_rejected == 0 && $publication->is_approved == 1) || ($publication->is_rejected == 0 && $publication->is_approved == 0))
                        <a href="#reject-modal" data-toggle="modal" class="btn btn-danger btn-sm ml-2"><i class="fa fa-times-circle mr-1"></i>{{ $publication->is_approved == 1 ? 'Recall' : 'Reject' }}</a>
                    @endif
                </div>
            </div>
        </div>
        @include('admin.publications.partials.approval-modal', [ 'action' => url('admin/publications/approval'), 'record' => $publication ])
        @include('admin.publications.partials.reject-modal', [ 'action' => url('admin/publications/approval'), 'record' => $publication ])
    </div>
    <!-- ======================= Publication Info ======================== -->

    <!-- ============================ Publication Details Start ================================== -->
    <section class="py-5  bg-white">
        <div class="container">
            <!-- Two Column Layout: Cover Image (Left) + Additional Info & Attachments (Right) -->
            <div class="row mb-4">
                <!-- Left Column: Cover Image -->
                <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-4">
                    <div class="ap-card">
                        <div class="ap-card-header">
                            <h5 class="mb-0" style="font-weight:600;color:#0f172a;">Cover Image</h5>
                        </div>
                        <div class="ap-card-body text-center">
                            @if (($publication->is_video || $isVideoLink) && !empty($pubUrl))
                                @if ($embedUrl)
                                    <div class="ap-cover">
                                        <iframe
                                            width="100%"
                                            height="240"
                                            src="{{ $embedUrl }}"
                                            title="Video preview"
                                            frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share; fullscreen"
                                            referrerpolicy="strict-origin-when-cross-origin"
                                            allowfullscreen></iframe>
                                    </div>
                                @elseif ($directVideo)
                                    <div class="ap-cover" style="padding: 8px;">
                                        <video controls playsinline webkit-playsinline preload="metadata" style="width:100%;max-height:240px;background:#000;border-radius:8px;">
                                            <source src="{{ $pubUrl }}">
                                            Your browser does not support HTML5 video.
                                        </video>
                                    </div>
                                @else
                                    <div class="ap-cover" style="max-height:400px;overflow:hidden;">
                                        <img src="{{ $image_link }}" alt="Cover" style="width:100%;height:auto;max-height:400px;object-fit:contain;border-radius:8px;" onerror="this.onerror=null; this.src='{{ asset('assets/images/cover.png') }}';">
                                    </div>
                                @endif
                            @else
                                <div class="ap-cover" style="max-height:400px;overflow:hidden;">
                                    <img src="{{ $image_link }}" alt="Cover" style="width:100%;height:auto;max-height:400px;object-fit:contain;border-radius:8px;" onerror="this.onerror=null; this.src='{{ asset('assets/images/cover.png') }}';">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column: Additional Info & Attachments -->
                <div class="col-xl-8 col-lg-8 col-md-7 col-sm-12">
                    <!-- Additional Information Card -->
                    <div class="ap-card mb-3">
                        <div class="ap-card-header">
                            <h5 class="mb-0" style="font-weight:600;color:#0f172a;">Additional Information</h5>
                        </div>
                        <div class="ap-card-body">
                            <ul class="ap-list">
                                <li>
                                    <label class="meta-label">Source</label>
                                    <span class="value">{{ $publication->author->name ?? 'N/A' }}</span>
                                </li>
                                <li>
                                    <label class="meta-label">Category</label>
                                    <span class="value">{{ @$publication->data_category->category_name ?? 'N/A' }}</span>
                                </li>
                                <li>
                                    <label class="meta-label">Sub Category</label>
                                    <span class="value">{{ $publication->sub_category->category_name ?? 'N/A' }}</span>
                                </li>
                                <li>
                                    <label class="meta-label">Theme</label>
                                    <span class="value">{!! $publication->theme->description ?? 'N/A' !!}</span>
                                </li>
                                <li>
                                    <label class="meta-label">Sub-Theme</label>
                                    <span class="value">{!! $publication->sub_theme->description ?? 'N/A' !!}</span>
                                </li>
                                @if(!empty($publication->doi))
                                <li>
                                    <label class="meta-label">DOI</label>
                                    <span class="value">
                                        <a href="https://doi.org/{{ $publication->doi }}" target="_blank" rel="noopener noreferrer" style="color: #119A48; text-decoration: none;">
                                            {{ $publication->doi }} <i class="fa fa-external-link-alt" style="font-size: 0.75rem;"></i>
                                        </a>
                                    </span>
                                </li>
                                @endif
                                @if(!empty($publication->issn))
                                <li>
                                    <label class="meta-label">ISSN</label>
                                    <span class="value">{{ $publication->issn }}</span>
                                </li>
                                @endif
                                @if(!empty($publication->isbn))
                                <li>
                                    <label class="meta-label">ISBN</label>
                                    <span class="value">{{ $publication->isbn }}</span>
                                </li>
                                @endif
                                @if(!empty($publication->publisher))
                                <li>
                                    <label class="meta-label">Publisher</label>
                                    <span class="value">{{ $publication->publisher }}</span>
                                </li>
                                @endif
                                @if(!empty($publication->year_published))
                                <li>
                                    <label class="meta-label">Year Published</label>
                                    <span class="value">{{ $publication->year_published }}</span>
                                </li>
                                @endif
                                <li>
                                    <label class="meta-label">Visits</label>
                                    <span class="value">{{ $publication->visits }}</span>
                                </li>
                                <li>
                                    <label class="meta-label">Likes</label>
                                    <span class="value">{{ count($publication->favourited ?? []) }}</span>
                                </li>
                                <li>
                                    <label class="meta-label">Comments</label>
                                    <span class="value">{{ count($publication->comments) }}</span>
                                </li>
                                <li>
                                    <label class="meta-label">Associated Authors</label>
                                    <span class="value">{{ $publication->associated_authors ?? 'N/A' }}</span>
                                </li>
                                @if($publication->license)
                                <li>
                                    <label class="meta-label">License</label>
                                    <span class="value">
                                        @if($publication->license->url)
                                            <a href="{{ $publication->license->url }}" target="_blank" rel="noopener noreferrer" style="color: #119A48; text-decoration: none;">
                                                {{ $publication->license->name }}@if($publication->license->short_name) ({{ $publication->license->short_name }})@endif <i class="fa fa-external-link-alt" style="font-size: 0.75rem;"></i>
                                            </a>
                                        @else
                                            {{ $publication->license->name }}@if($publication->license->short_name) ({{ $publication->license->short_name }})@endif
                                        @endif
                                    </span>
                                </li>
                                @endif
                                @if(!empty($publication->copyright_info))
                                <li>
                                    <label class="meta-label">Copyright Information</label>
                                    <span class="value" style="white-space: pre-wrap;">{{ $publication->copyright_info }}</span>
                                </li>
                                @endif
                                @if(!empty($publication->funder))
                                <li>
                                    <label class="meta-label">Funder</label>
                                    <span class="value">{{ $publication->funder }}</span>
                                </li>
                                @endif
                            </ul>
                            
                            {{-- Journal Information Section --}}
                            @if(!empty($publication->journal_name) || !empty($publication->journal_volume) || !empty($publication->journal_issue) || !empty($publication->journal_pages))
                            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid #e2e8f0;">
                                <h6 style="font-weight:600;color:#0f172a;margin-bottom:1rem;">Journal Information</h6>
                                <ul class="ap-list">
                                    @if(!empty($publication->journal_name))
                                    <li>
                                        <label class="meta-label">Journal Name</label>
                                        <span class="value">{{ $publication->journal_name }}</span>
                                    </li>
                                    @endif
                                    @if(!empty($publication->journal_volume))
                                    <li>
                                        <label class="meta-label">Volume</label>
                                        <span class="value">{{ $publication->journal_volume }}</span>
                                    </li>
                                    @endif
                                    @if(!empty($publication->journal_issue))
                                    <li>
                                        <label class="meta-label">Issue</label>
                                        <span class="value">{{ $publication->journal_issue }}</span>
                                    </li>
                                    @endif
                                    @if(!empty($publication->journal_pages))
                                    <li>
                                        <label class="meta-label">Pages</label>
                                        <span class="value">{{ $publication->journal_pages }}</span>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resources & Attachments Card - Full Width -->
            @if (!empty($publication->publication) || $publication->has_attachments)
            <div class="row mb-4">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="ap-card">
                        <div class="ap-card-header">
                            <h5 class="mb-0" style="font-weight:600;color:#0f172a;">Resources & Attachments</h5>
                        </div>
                        <div class="ap-card-body">
                            <ul class="ap-list">
                                @if (!empty($publication->publication))
                                <li>
                                    <a href="{{ $publication->publication }}" target="_blank" rel="noopener noreferrer" style="color: #119A48; text-decoration: none; font-weight: 500;">
                                        <i class="fa fa-external-link-alt mr-2"></i>
                                        External Resource Link
                                        <small class="text-muted d-block mt-1" style="font-weight: normal; color: #64748b;">{{ $publication->publication }}</small>
                                    </a>
                                </li>
                                @endif
                                
                                @if ($publication->has_attachments)
                                    @php
                                        $count = 1;
                                    @endphp
                                    @foreach ($publication->attachments as $pub_file)
                                        <li>
                                            <a href="{{ $pub_file->file }}" target="_blank" style="color: #119A48; text-decoration: none; font-weight: 500;">
                                                <i class="fa fa-download mr-2"></i>
                                                {{ Str::limit($pub_file->original_filename ?? $pub_file->description ?? ('Attachment ' . $count), 90) }}
                                            </a>
                                        </li>
                                        @php
                                            $count++;
                                        @endphp
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Main Content: Description and Comments -->
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="rounded mb-4">
                        <div class="jbd-01 pr-3">

                            <div class="jbd-details mb-4">
                                <h5 class="ft-medium fs-md text-success">Description</h5>
                                <div class="other-details">
                                    <p>{!! sanitize_rich_text_for_display($publication->description) !!}</p>
                                </div>
                            </div>

                        </div>

                        <div class="article_detail_wrapss single_article_wrap format-standard">

                            @if (count($publication->summaries) > 0 || count($publication->versioning) > 0 || $publication->parent_id > 0)
                                <div class="jb-apply-form bg-white shadow rounded py-3 px-4 box-static">


                                    @if (count($publication->versioning) > 0)
                                        <h4 class="ft-medium fs-md mb-3">Resource Versions</h4>
                                        <ul class="list-group mb-3 pl-2">
                                            @foreach ($publication->versioning as $version)
                                                <li>
                                                    <h5 class="text-muted"><a
                                                            href="{{ url('admin/publications/details') }}?id={{ $version->id }}">Version
                                                            {{ $version->version_no }}</a></h5>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @elseif($publication->parent_id > 0)
                                        <h5 class=" mb-3"><a
                                                class=" col-lg-12 text-center btn btn-sm btn-outline-success rounded"
                                                href="{{ url('admin/publications/details') }}?id={{ $publication->parent_id }}"><i
                                                    class="fa fa-link"></i> Original Version</a></h5>
                                    @endif

                                    @if (count($publication->summaries) > 0)
                                        <h6 class="ft-medium fs-sm mb-3">Summaries and Abstracts</h6>
                                        <ul class="list-group mb-3">
                                            @foreach ($publication->summaries as $summary)
                                                <li>
                                                    <h6 class="text-muted"><a
                                                            href="{{ url('admin/publications/summary') }}?id={{ $summary->id }}">{{ truncate($summary->title, 100) }}
                                                            by {{ $summary->author->name }}</a></h6>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif


                            @endif
                        </div>

                    </div>

                    <!-- Blog Comment -->
                    <div class="article_detail_wrapss single_article_wrap format-standard">

                        <div class="comment-area">
                            <div class="all-comments">
                                <h3 class="comments-title mt-3">{{ count($publication->comments) }} Comment(s)</h3>
                                <div class="comment-list">
                                    <ul>

                                        @foreach ($publication->comments as $comment)
                                            <li class="article_comments_wrap">

                                                <article>
                                                    <div class="comment-details app-comment">
                                                        <div class="comment-meta">
                                                            <div class="comment-left-meta">
                                                                <h5 class="author-name">
                                                                    {{ $comment->user ? $comment->user->name : 'Anonymous' }}
                                                                </h5>
                                                                <div class="comment-date">
                                                                    {{ time_ago($comment->created_at) }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="comment-text">
                                                            <p>{{ nl2br($comment->comment) }}</p>
                                                        </div>

                                                    </div>


                                                </article>

                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>


                </div>
            </div>
    </section>

@endsection
