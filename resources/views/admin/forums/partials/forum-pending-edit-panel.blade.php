{{-- Title/body editor for forums awaiting approval (admin moderation). Expects $forum. --}}
<div class="card border mb-0" style="background:#f8fafc;">
    <div class="card-body py-3">
        <h6 class="card-title mb-2">Edit before approval</h6>
        <p class="small text-muted mb-3">Adjust the title or body, then save. Use AI assist only for grammar, spelling, and punctuation—the meaning and structure should stay the same.</p>
        <div class="form-group">
            <label class="font-weight-bold small" for="mod_title_{{ $forum->id }}">Title</label>
            <input type="text" class="form-control" id="mod_title_{{ $forum->id }}" value="{{ $forum->forum_title }}" maxlength="500">
        </div>
        <div class="form-group mb-2">
            <label class="font-weight-bold small" for="mod_body_{{ $forum->id }}">Post body</label>
            {{-- Single {{ }} escape only; never {{ e() }} (double-escapes and breaks WYSIWYG). --}}
            <textarea id="mod_body_{{ $forum->id }}" class="form-control forum-mod-editor" rows="10" data-forum-id="{{ $forum->id }}">{{ forum_body_for_wysiwyg_editor($forum->forum_description ?? '') }}</textarea>
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
