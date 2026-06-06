@extends(admin_layout())

@php
    $forumIsRejected = (int) ($forum->is_rejected ?? 0) === 1;
    $forumPendingModeration = ! $forumIsRejected && (int) ($forum->is_approved ?? 0) === 0 && (int) ($forum->status ?? 0) === 0;
@endphp

@section('styles')
<link href="{{ asset('assets/plugins/summernote/dist/summernote.min.css') }}" rel="stylesheet">
@php
    $primaryColor = settings()->primary_color ?? '#119A48';
    $secondaryColor = settings()->secondary_color ?? '#0e7a3a';
    $auGold = settings()->au_gold ?? '#B4A269';
@endphp
<style>
    .af-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; }
    .af-card-header { padding:16px 20px; border-bottom:1px solid #e2e8f0; background:#f8fafc; }
    .af-card-body { padding:20px; }
    .af-meta label { display:block; font-size:.75rem; color:#64748b; margin-bottom:2px; }
    .af-meta .value { font-weight:600; color:#0f172a; }
    .af-badge { padding:2px 8px; border-radius:999px; font-size:.75rem; }
    .af-badge-success { background:#dcfce7; color:#166534; }
    .af-badge-danger { background:#fee2e2; color:#991b1b; }
    .af-badge-muted { background:#e2e8f0; color:#334155; }
    .af-actions .btn { margin-left:8px; }
    .af-cover { border:1px solid #e2e8f0; border-radius:10px; overflow:hidden; background:#f8fafc; }
    .af-section-title { font-size:1rem; font-weight:700; color:#0f172a; margin-bottom:8px; }
    .af-list { list-style:none; padding-left:0; margin-bottom:0; }
    .af-list li { padding:6px 0; border-bottom:1px dashed #e5e7eb; }
    .af-list li:last-child { border-bottom:none; }
    
    /* Sidebar Styling - matching public forums page */
    .sidebar-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    
    .sidebar-card-title {
        font-size: 1rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .forum-sidebar-item {
        display: flex;
        gap: 0.75rem;
        padding: 0.75rem;
        border-radius: 4px;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
        cursor: pointer;
        text-decoration: none;
        border: 1px solid transparent;
    }
    
    .forum-sidebar-item:hover {
        background: rgba(17, 154, 72, 0.05);
        border-color: {{ $primaryColor }};
        text-decoration: none;
        transform: translateX(4px);
    }
    
    .forum-sidebar-item:last-child {
        margin-bottom: 0;
    }
    
    .forum-sidebar-img {
        width: 60px;
        height: 60px;
        border-radius: 4px;
        object-fit: cover;
        flex-shrink: 0;
        border: 1px solid #e2e8f0;
    }
    
    .forum-sidebar-content {
        flex: 1;
        min-width: 0;
    }
    
    .forum-sidebar-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.25rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .forum-sidebar-time {
        font-size: 0.75rem;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .forum-sidebar-time i {
        color: {{ $auGold }};
    }
    
    .tag-item {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        background: rgba(180, 162, 105, 0.1);
        color: {{ $auGold }};
        border: 1px solid {{ $auGold }};
        border-radius: 4px;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .tag-item:hover {
        background: {{ $auGold }};
        color: #ffffff;
        text-decoration: none;
    }
</style>
@endsection

@section('content')

	@php
		// Use the same image handling as frontend
		$image_link = null;
		if (!empty($forum->forum_image)) {
			$forumImageUrl = $forum->forum_image; // This goes through the accessor which applies storage_link
			$baseUrl = url('/');
			// Process URL like frontend does
			if (strpos($forumImageUrl, 'http://') === 0 || strpos($forumImageUrl, 'https://') === 0) {
				// Already a full URL
				$image_link = $forumImageUrl;
			} elseif (strpos($forumImageUrl, $baseUrl) !== false) {
				// Already contains base URL
				$image_link = $forumImageUrl;
			} elseif (strpos($forumImageUrl, '/storage/') === 0) {
				$image_link = $baseUrl . $forumImageUrl;
			} elseif (strpos($forumImageUrl, 'storage/') === 0) {
				$image_link = $baseUrl . '/' . $forumImageUrl;
			} else {
				$image_link = $forumImageUrl;
			}
		}
	@endphp

    <div class="container">
        @include('layouts.partials.alerts')

        <div class="af-card mb-3">
            <div class="af-card-header d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted mb-1" style="font-size:.8rem;">Forum</div>
                    <h4 class="mb-0" style="font-weight:700; color:#0f172a;">
                        {!! $forum->forum_title !!}
                        @include('admin.forums.partials.resubmission-badge', ['forum' => $forum, 'class' => 'align-middle ml-2'])
                    </h4>
                    <div class="text-muted" style="font-size:.9rem;">By {{ $forum->user->name }} · {{ time_ago($forum->created_at) }}</div>
                    @if($forumIsRejected && !empty($forum->rejected_reason))
                    <div class="alert alert-light border py-2 px-3 small mb-0 mt-2" style="max-width:40rem;">
                        <strong class="text-uppercase text-muted" style="font-size:.7rem;">Rejection reason</strong>
                        <p class="mb-0 mt-1">{{ e($forum->rejected_reason) }}</p>
                    </div>
                    @endif
                    @if((int) ($forum->is_resubmission_pending ?? 0) === 1 && ! $forumIsRejected && (int) ($forum->is_approved ?? 0) === 0)
                    <div class="alert alert-warning py-2 px-3 small mb-0 mt-2" style="max-width:40rem;">
                        <strong>Resubmission.</strong> This discussion was rejected earlier; the author resubmitted it and it is awaiting your review.
                    </div>
                    @endif
                </div>
                <div class="af-actions">
                    @php
                        $statusBadge = $forum->is_approved ? 'af-badge-success' : ($forum->is_rejected ? 'af-badge-danger' : 'af-badge-muted');
                        $statusText  = $forum->is_approved ? 'Approved' : ($forum->is_rejected ? 'Rejected' : 'Pending');
                    @endphp
                    <span class="af-badge {{ $statusBadge }}">{{ $statusText }}</span>
                    @if($forum->is_approved == 0)
                        <a href="#approval-modal" data-toggle="modal" class="btn btn-success btn-sm"><i class="fa fa-check-circle mr-1"></i>{{ ($forum->is_rejected== 0)?'Approve':'Reconsider'}}</a>
                    @endif
                    @if(($forum->is_rejected== 0 && $forum->is_approved==1) || ($forum->is_rejected== 0 && $forum->is_approved==0))
                        <a href="#reject-modal" data-toggle="modal" class="btn btn-danger btn-sm"><i class="fa fa-times-circle mr-1"></i>{{ ($forum->is_approved==1)?'Recall':'Reject'}}</a>
                    @endif
                </div>
            </div>
            @if($forumPendingModeration)
            <div class="px-3 pt-3 border-bottom" style="background:#fafbfc;">
                <p class="small text-muted mb-2 mb-md-3"><strong>Edit forum</strong> — update title and body before approving (same as Review / Edit on the pending list).</p>
                <div id="details{{ $forum->id }}">
                    @include('admin.forums.partials.forum-pending-edit-panel', ['forum' => $forum])
                </div>
            </div>
            @endif
            <div class="af-card-body">
                <div class="row">
                    <div class="col-lg-8">
                        @if($image_link)
                        <div class="af-cover mb-3">
                            <img src="{{ $image_link }}" 
                                 alt="{{ $forum->forum_title ?? 'Forum Cover' }}" 
                                 style="width:100%; height:auto; display:block; max-height: 400px; object-fit: contain; background: #f8fafc;"
                                 onerror="this.onerror=null; this.src='{{ asset('assets/images/cover.png') }}';">
                        </div>
                        @endif
                        <div class="mb-4">
                            <div class="af-section-title">Description</div>
                            <div class="text-body">{!! sanitize_rich_text_for_display($forum->forum_description) !!}</div>
                        </div>

                        @if(isset($forum->attachments) && count($forum->attachments))
                        <div class="mb-4">
                            <div class="af-section-title">Attachments</div>
                            <ul class="af-list">
                                @foreach($forum->attachments as $att)
                                    <li>
                                        <a href="{{ forum_comment_attachment_effective_href($att) }}" target="_blank" rel="noopener noreferrer">
                                            <i class="fa fa-paperclip mr-1"></i>{{ forum_attachment_display_name($att) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="mb-2">
                            <div class="af-section-title">Comments ({{ count($forum->comments) }})</div>
                        </div>
                        <div class="article_detail_wrapss single_article_wrap format-standard">
                            <div class="comment-area">
                                <div class="comment-list">
                                    <ul class="af-list">
                                        @foreach ($forum->comments as $comment)
                                            @php
                                                $commentStatus = strtolower((string) ($comment->status ?? ''));
                                                $commentNeedsModeration = ! in_array($commentStatus, ['approved', 'rejected'], true);
                                            @endphp
                                            <li>
                                                <div class="app-comment">
                                                    <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:8px;">
                                                        <div>
                                                            <div style="font-weight:600;">{{ ($comment->user) ? $comment->user->name : 'Anonymous'}}</div>
                                                            <div class="text-muted" style="font-size:.85rem;">{{ time_ago($comment->created_at)}}</div>
                                                        </div>
                                                        <div class="d-flex align-items-center flex-wrap" style="gap:8px;">
                                                            <span class="af-badge af-badge-muted">{{ $comment->status ? ucwords($comment->status) : 'Pending' }}</span>
                                                            @can('moderate_forum')
                                                                @if($commentNeedsModeration)
                                                                    <a href="{{ url('admin/forums/approve-comment') }}?id={{ $comment->id }}" class="btn btn-sm btn-success">Approve</a>
                                                                    <a href="{{ url('admin/forums/reject-comment') }}?id={{ $comment->id }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Reject this comment? The author will receive an email if their account has an email address.');">Reject</a>
                                                                @endif
                                                            @endcan
                                                        </div>
                                                    </div>
                                                    <div class="mt-2">{!! sanitize_rich_text_for_display($comment->comment) !!}</div>
                                                    @if($comment->attachments && $comment->attachments->count() > 0)
                                                        <div class="mt-2 small">
                                                            <span class="text-muted">Attachments:</span>
                                                            <ul class="list-unstyled mb-0 mt-1">
                                                                @foreach($comment->attachments as $catt)
                                                                    <li>
                                                                        <a href="{{ forum_comment_attachment_effective_href($catt) }}" target="_blank" rel="noopener noreferrer">
                                                                            <i class="fa fa-paperclip mr-1"></i>{{ forum_attachment_display_name($catt) }}
                                                                        </a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <!-- Recent Forums -->
                        @if(isset($forums) && $forums->count() > 0)
                        <div class="sidebar-card">
                            <h5 class="sidebar-card-title">
                                <i class="fa fa-comments me-2" style="color: {{ $primaryColor }};"></i>Recent Forums
                            </h5>
                            @foreach ($forums as $other)
                            <a href="{{ url('admin/forums/details') }}?id={{ $other->id }}" class="forum-sidebar-item">
                                <div>
                                    @if (is_image($other->forum_image))
                                        <img class="forum-sidebar-img" src="{{ $other->forum_image }}" alt="{{ $other->forum_title ?? 'Forum Discussion' }}" loading="lazy">
                                    @else
                                        <div class="forum-sidebar-img" style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }}); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                                            <i class="fa fa-comments"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="forum-sidebar-content">
                                    <div class="forum-sidebar-title">{!! strip_tags($other->forum_title) !!}</div>
                                    <div class="forum-sidebar-time">
                                        <i class="fa fa-clock"></i>
                                        {{ time_ago($other->created_at) }}
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                        @endif

                        <!-- Tags -->
                        @if(isset($forum->tags) && count($forum->tags) > 0)
                        <div class="sidebar-card">
                            <h5 class="sidebar-card-title">
                                <i class="fa fa-tags me-2" style="color: {{ $auGold }};"></i>Tags
                            </h5>
                            <div>
                                @foreach($forum->tags as $tag)
                                    <a href="{{ url('admin/forums') }}?tag={{ $tag->tag ?? $tag->tag_text ?? '' }}" class="tag-item">{{ $tag->tag ?? $tag->tag_text ?? '' }}</a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Metadata -->
                        <div class="sidebar-card">
                            <h5 class="sidebar-card-title">
                                <i class="fa fa-info-circle me-2" style="color: {{ $primaryColor }};"></i>Metadata
                            </h5>
                            <div class="af-meta mb-3">
                                <label>Author</label>
                                <div class="value">{{ $forum->user->name }}</div>
                            </div>
                            <div class="af-meta mb-3">
                                <label>Created</label>
                                <div class="value">{{ $forum->created_at }}</div>
                            </div>
                            <div class="af-meta mb-3">
                                <label>Approved By</label>
                                <div class="value">
                                    @php $ab = isset($forum->approved_by) ? (\App\Models\User::find($forum->approved_by)->name ?? '-') : '-'; @endphp
                                    {{ $ab }}
                                </div>
                            </div>
                            <div class="af-meta mb-3">
                                <label>Rejected By</label>
                                <div class="value">
                                    @php $rb = isset($forum->rejected_by) ? (\App\Models\User::find($forum->rejected_by)->name ?? '-') : '-'; @endphp
                                    {{ $rb }}
                                </div>
                            </div>

                            @if(isset($forum->communities) && count($forum->communities))
                            <div class="af-meta mb-2">
                                <label>Communities</label>
                                <ul class="af-list">
                                    @foreach($forum->communities as $c)
                                        <li>{{ $c->community->name ?? '' }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                @include('admin.publications.partials.approval-modal',[ 'action'=>url('admin/forums/approve'), 'record'=>$forum])
                @include('admin.publications.partials.reject-modal',[ 'action'=>url('admin/forums/reject'), 'record'=>$forum])

                @include('admin.forums.partials.approval_trail', ['approvalTrail' => $approvalTrail ?? collect()])
            </div>
        </div>
    </div>

    <!-- spacer -->
    <section class="py-2 bg-transparent"></section>

    @endsection

@if($forumPendingModeration)
@section('scripts')
    @include('admin.forums.partials.moderation-forum-editor-scripts')
    <script>
        $(function () {
            if (window.forumAdminInitPendingEditor) {
                window.forumAdminInitPendingEditor($('#details{{ $forum->id }}'));
            }
        });
    </script>
@endsection
@endif