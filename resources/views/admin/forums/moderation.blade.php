@extends(admin_layout())

@section('styles')
<link href="{{ asset('assets/plugins/summernote/dist/summernote.min.css') }}" rel="stylesheet">
 @include('common.table')
 <style>
    .filter-card { background:#fff; border:1px solid #e2e8f0; border-radius:10px; }
    .filter-card .card-header { background:#f8fafc; border-bottom:1px solid #e2e8f0; }
    .btn-soft { border:1px solid #cbd5e1; background:#ffffff; }
    .btn-soft:hover { background:#f8fafc; }
    .form-label-sm { font-size:.875rem; font-weight:600; color:#334155; }
 </style>
@endsection

@section('content')
<div class="row">
	<div class="card col-lg-12">
		<div class="filter-card">
			<div class="card-header d-flex align-items-center justify-content-between">
				<div>
					<strong>{{ $title ?? 'Pending Forums' }}</strong>
					<small class="text-muted d-block">Review, approve or reject threads</small>
				</div>
			</div>
			<div class="card-body">
				<form class="container-fluid">
					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								<label class="form-label-sm" for="title">Keyword</label>
								<input type="text" name="term" id="filterTitle" class="form-control" placeholder="Filter by title or description" value="{{ @$search->term ?? ''}}">
							</div>
						</div>
						<div class="col-md-4 d-flex align-items-end justify-content-end" style="gap:8px;">
							<button type="submit" id="filterButton" class="btn btn-dark btn-sm"><i class="fa fa-filter mr-1"></i> Apply</button>
							<button type="button" id="reset" class="btn btn-soft btn-sm"><i class="fa fa-rotate-left mr-1"></i> Reset</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="card-body text-left">
			<div class="table-responsive">
			<table id="publicationTable" class="table table-striped table-hover table-bordered">
				<thead class="thead-light">
					<tr>
						<th style="width:60px;">#</th>
						<th>Forum Title</th>
						<th>Description</th>
						<th>Author</th>
						<th width="14%">Submitted</th>
						<th width="22%">Actions</th>
					</tr>
				</thead>
				<tbody>

					@foreach($forums as $idx => $row)
						<tr>
							<td><span class="text-muted">{{ $forums->firstItem() + $idx }}</span></td>
							<td>
								{{ $row->forum_title }}
								@include('admin.forums.partials.resubmission-badge', ['forum' => $row, 'class' => 'ml-1 align-middle'])
							</td>
							<td>{!! truncate(strip_tags($row->forum_description), 100) !!}</td>
							<td>{!! truncate($row->user->name,100) !!}</td>
							<td>{!! time_ago($row->created_at) !!}</td>
							<td>
								<a class="btn btn-sm btn-outline-primary mr-1" href="#details{{$row->id}}" data-toggle="modal"><i class="fa fa-eye mr-1"></i> Review</a>
								<a class="btn btn-sm btn-outline-secondary" target="_blank" href="{{ url('forums/thread')}}?id={{$row->id}}"><i class="fa fa-external-link mr-1"></i> Preview</a>
							</td>
						</tr>

						@include('admin.forums.partials.details-modal',['forum'=>$row])

					@endforeach
				</tbody>
			</table>
			</div>

			<div class="py-2"> {{$forums->links() }}</div>

		</div>

	</div>

	<!-- Include delete-modal.php -->
	@include('admin.forums.partials.delete-modal')

    @endsection

@section('scripts')
<script src="{{ asset('assets/plugins/summernote/dist/summernote.min.js') }}"></script>
<script>
(function () {
    var grammarUrl = @json(route('admin.forums.moderation.grammar-assist'));
    var saveUrl = @json(route('admin.forums.moderation.update-pending'));
    var csrf = @json(csrf_token());

    function initForumModEditor($modal) {
        $modal.find('textarea.forum-mod-editor').each(function () {
            var $ta = $(this);
            if ($ta.data('forumModSummernote')) {
                return;
            }
            $ta.summernote({
                placeholder: 'Post body',
                tabsize: 2,
                height: 280,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview']]
                ]
            });
            $ta.data('forumModSummernote', true);
        });
    }

    $(document).on('shown.bs.modal', '.modal[id^="details"]', function () {
        initForumModEditor($(this));
    });

    function setStatus(forumId, msg, isError) {
        var $s = $('#mod_status_' + forumId);
        if (!$s.length) return;
        $s.text(msg).css('color', isError ? '#c0392b' : '#119A48').show();
        if (!isError && msg) {
            setTimeout(function () { $s.fadeOut(); }, 4000);
        }
    }

    $(document).on('click', '.btn-forum-ai-grammar', function () {
        var forumId = $(this).data('forum-id');
        var $modal = $('#details' + forumId);
        var $ta = $modal.find('#mod_body_' + forumId);
        if (!$ta.length || !$ta.data('forumModSummernote')) {
            alert('Open the editor first (click Review).');
            return;
        }
        var html = $ta.summernote('code');
        if (!html || !String(html).trim()) {
            alert('Nothing to proofread.');
            return;
        }
        var $btn = $(this);
        $btn.prop('disabled', true);
        setStatus(forumId, 'AI is proofreading…', false);
        $.ajax({
            url: grammarUrl,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            data: { _token: csrf, html: html },
            success: function (res) {
                if (res && res.ok && res.html) {
                    $ta.summernote('code', res.html);
                    setStatus(forumId, 'Grammar suggestions applied. Review before saving.', false);
                } else {
                    setStatus(forumId, (res && res.error) ? res.error : 'AI request failed.', true);
                }
            },
            error: function (xhr) {
                var err = 'Request failed.';
                try {
                    var j = xhr.responseJSON;
                    if (j && j.error) err = j.error;
                    else if (j && j.message) err = j.message;
                } catch (e) {}
                setStatus(forumId, err, true);
            },
            complete: function () {
                $btn.prop('disabled', false);
            }
        });
    });

    $(document).on('click', '.btn-forum-save-pending', function () {
        var forumId = $(this).data('forum-id');
        var $modal = $('#details' + forumId);
        var title = ($modal.find('#mod_title_' + forumId).val() || '').trim();
        var $ta = $modal.find('#mod_body_' + forumId);
        if (!$ta.length || !$ta.data('forumModSummernote')) {
            alert('Editor not ready.');
            return;
        }
        var body = $ta.summernote('code');
        if (!title) {
            alert('Title is required.');
            return;
        }
        var $btn = $(this);
        $btn.prop('disabled', true);
        setStatus(forumId, 'Saving…', false);
        $.ajax({
            url: saveUrl,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            data: {
                _token: csrf,
                id: forumId,
                forum_title: title,
                forum_description: body
            },
            success: function () {
                setStatus(forumId, 'Saved. Reloading…', false);
                window.location.reload();
            },
            error: function (xhr) {
                var err = 'Save failed.';
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    err = Object.values(xhr.responseJSON.errors).flat().join(' ');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    err = xhr.responseJSON.message;
                }
                setStatus(forumId, err, true);
                $btn.prop('disabled', false);
            }
        });
    });
})();
</script>
@endsection