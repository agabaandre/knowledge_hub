@extends('admin.layouts.main')

@section('styles')
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
 </style>
@endsection

@section('content')

	@php
		if($forum->forum_image):
			$image_link = storage_link('uploads/forums/'.$forum->forum_image);
		else:
			$image_link = null;
		endif;
	@endphp

    <div class="container">
        @include('layouts.partials.alerts')

        <div class="af-card mb-3">
            <div class="af-card-header d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted mb-1" style="font-size:.8rem;">Forum</div>
                    <h4 class="mb-0" style="font-weight:700; color:#0f172a;">{!! $forum->forum_title !!}</h4>
                    <div class="text-muted" style="font-size:.9rem;">By {{ $forum->user->name }} · {{ time_ago($forum->created_at) }}</div>
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
            <div class="af-card-body">
                <div class="row">
                    <div class="col-lg-8">
                        @if($image_link)
                        <div class="af-cover mb-3">
                            <img src="{{ $image_link }}" alt="Cover" style="width:100%; height:auto; display:block;">
                        </div>
                        @endif
                        <div class="mb-4">
                            <div class="af-section-title">Description</div>
                            <div class="text-body">{!! $forum->forum_description !!}</div>
                        </div>

                        @if(isset($forum->attachments) && count($forum->attachments))
                        <div class="mb-4">
                            <div class="af-section-title">Attachments</div>
                            <ul class="af-list">
                                @foreach($forum->attachments as $att)
                                    <li>
                                        <a href="{{ storage_link('uploads/'.$att->path) }}" target="_blank">
                                            <i class="fa fa-paperclip mr-1"></i> {{ basename($att->path) }}
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
                                            <li>
                                                <div class="app-comment">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div>
                                                            <div style="font-weight:600;">{{ ($comment->user) ? $comment->user->name : 'Anonymous'}}</div>
                                                            <div class="text-muted" style="font-size:.85rem;">{{ time_ago($comment->created_at)}}</div>
                                                        </div>
                                                        <span class="af-badge af-badge-muted">{{ ucwords($comment->status) }}</span>
                                                    </div>
                                                    <div class="mt-2">{!! $comment->comment !!}</div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="af-card">
                            <div class="af-card-body">
                                <div class="af-section-title">Metadata</div>
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

                                @if(isset($forum->tags) && count($forum->tags))
                                <div class="af-meta mb-3">
                                    <label>Tags</label>
                                    <div>
                                        @foreach($forum->tags as $tg)
                                            <span class="af-badge af-badge-muted" style="margin-right:6px;">{{ $tg->tag ?? $tg->tag_text ?? '' }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

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
                </div>

                @include('admin.publications.partials.approval-modal',[ 'action'=>url('admin/forums/approve'), 'record'=>$forum])
                @include('admin.publications.partials.reject-modal',[ 'action'=>url('admin/forums/reject'), 'record'=>$forum])
            </div>
        </div>
    </div>

    <!-- spacer -->
    <section class="py-2 bg-transparent"></section>

    @endsection