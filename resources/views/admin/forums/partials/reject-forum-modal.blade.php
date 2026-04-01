{{-- Reject pending forum with reason (author receives email). Pair with Review modal id="details{{ $forum->id }}". --}}
@if((int) ($forum->is_rejected ?? 0) === 0 && (int) $forum->is_approved === 0 && (int) $forum->status === 0)
<div class="modal fade" id="rejectForum{{ $forum->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectForumTitle{{ $forum->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="post" action="{{ url('admin/forums/reject') }}">
                @csrf
                <input type="hidden" name="id" value="{{ $forum->id }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectForumTitle{{ $forum->id }}">Reject forum post</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-left">
                    <p class="small text-muted mb-2">The author will be emailed this explanation. Be clear and constructive (minimum 10 characters).</p>
                    <div class="form-group mb-0">
                        <label for="rejected_reason_{{ $forum->id }}" class="font-weight-bold small">Reason for rejection</label>
                        <textarea
                            id="rejected_reason_{{ $forum->id }}"
                            name="rejected_reason"
                            class="form-control"
                            rows="5"
                            required
                            minlength="10"
                            maxlength="5000"
                            placeholder="e.g. The post does not meet community guidelines because… or Please revise spelling and resubmit."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm">Reject post</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
