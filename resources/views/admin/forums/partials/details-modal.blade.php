<div class="modal" id="details{{$forum->id}}">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="max-height: 100vh;">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">Forum Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
            </div>
            <div class="modal-body" style="overflow-y: auto;">
                @php
                    $forumIsRejected = (int) ($forum->is_rejected ?? 0) === 1;
                    $forumPendingModeration = ! $forumIsRejected && (int) $forum->is_approved === 0 && (int) $forum->status === 0;
                @endphp
                @if($forumIsRejected && !empty($forum->rejected_reason))
                <div class="alert alert-light border mb-3">
                    <strong class="small text-uppercase text-muted">Rejection reason</strong>
                    <p class="mb-0 small">{{ e($forum->rejected_reason) }}</p>
                </div>
                @endif
                @if($forumPendingModeration)
                <div class="card border mb-3" style="background:#f8fafc;">
                    <div class="card-body py-3">
                        <h6 class="card-title mb-2">Edit before approval</h6>
                        <p class="small text-muted mb-3">Adjust the title or body, then save. Use AI assist only for grammar, spelling, and punctuation—the meaning and structure should stay the same.</p>
                        <div class="form-group">
                            <label class="font-weight-bold small" for="mod_title_{{ $forum->id }}">Title</label>
                            <input type="text" class="form-control" id="mod_title_{{ $forum->id }}" value="{{ e($forum->forum_title) }}" maxlength="500">
                        </div>
                        <div class="form-group mb-2">
                            <label class="font-weight-bold small" for="mod_body_{{ $forum->id }}">Post body</label>
                            <textarea id="mod_body_{{ $forum->id }}" class="form-control forum-mod-editor" rows="10" data-forum-id="{{ $forum->id }}">{{ e($forum->forum_description) }}</textarea>
                        </div>
                        <div class="d-flex flex-wrap align-items-center" style="gap:8px;">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-forum-ai-grammar" data-forum-id="{{ $forum->id }}">
                                <i class="fa fa-magic mr-1"></i> AI: Fix grammar only
                            </button>
                            <button type="button" class="btn btn-sm btn-success btn-forum-save-pending" data-forum-id="{{ $forum->id }}">
                                <i class="fa fa-save mr-1"></i> Save changes
                            </button>
                            <span class="small text-muted forum-mod-status" id="mod_status_{{ $forum->id }}" style="display:none;"></span>
                        </div>
                    </div>
                </div>
                @endif
               @include('forums.partials.forum_details',['no_comments'=>true])
            </div>
            <div class="modal-footer d-flex flex-wrap align-items-center justify-content-between" style="gap:8px;">
                @if($forumPendingModeration)
                    <div class="d-flex flex-wrap" style="gap:8px;">
                        <a href="{{ url('admin/forums/approve') }}?id={{$forum->id}}" class="btn btn-success">
                            <i class="fa fa-check mr-1"></i> Approve &amp; post
                        </a>
                        <button type="button" class="btn btn-outline-danger" data-toggle="modal" data-target="#rejectForum{{ $forum->id }}">
                            <i class="fa fa-times mr-1"></i> Reject post…
                        </button>
                    </div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                @elseif((int) $forum->is_approved === 0 && ! $forumIsRejected)
                    <a href="{{ url('admin/forums/approve') }}?id={{$forum->id}}" class="btn btn-outline-success">Approve &amp; post</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                @else
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                @endif
            </div>
        </div>
    </div>
</div>

@include('admin.forums.partials.reject-forum-modal', ['forum' => $forum])
