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
                @if($forumPendingModeration && (int) ($forum->is_resubmission_pending ?? 0) === 1)
                <div class="alert alert-warning border-warning mb-3">
                    <strong><i class="fa fa-redo mr-1"></i>Resubmission</strong>
                    <p class="mb-0 small">This thread was previously rejected. The author revised it and submitted it again for review.</p>
                </div>
                @endif
                @if($forumPendingModeration)
                    <div class="mb-3">
                        @include('admin.forums.partials.forum-pending-edit-panel', ['forum' => $forum])
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
