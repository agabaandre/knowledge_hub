@extends(admin_layout())

@section('content')
<div class="page-header">
    <h1 class="page-title">RSS Feeds</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('admin') }}">Admin</a></li>
            <li class="breadcrumb-item active">RSS Feeds</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h3 class="card-title mb-0">Manage RSS Feeds</h3>
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-outline-primary btn-sm js-fetch-all" title="Fetch all active feeds now">
                        <i class="fa fa-refresh"></i> Fetch all now
                    </button>
                    <a href="{{ route('admin.rss_feeds.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add Feed</a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>URL</th>
                                <th>Active</th>
                                <th>Last fetched</th>
                                <th>Status</th>
                                <th width="180">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($feeds as $idx => $feed)
                            <tr>
                                <td>{{ $feeds->firstItem() + $idx }}</td>
                                <td>{{ $feed->name }}</td>
                                <td><a href="{{ $feed->url }}" target="_blank" rel="noopener">{{ \Illuminate\Support\Str::limit($feed->url, 50) }}</a></td>
                                <td>{{ $feed->is_active ? 'Yes' : 'No' }}</td>
                                <td>{{ $feed->last_fetched_at ? $feed->last_fetched_at->format('M d, Y H:i') : '-' }}</td>
                                <td><span class="badge badge-{{ $feed->last_fetch_status === 'success' ? 'success' : ($feed->last_fetch_status === 'error' ? 'danger' : 'secondary') }}">{{ $feed->last_fetch_status ?? '-' }}</span></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-secondary mr-1 js-fetch-one" data-feed-id="{{ $feed->id }}" title="Fetch this feed now"><i class="fa fa-refresh"></i></button>
                                    <a href="{{ route('admin.rss_feeds.edit', $feed->id) }}" class="btn btn-sm btn-outline-primary mr-1"><i class="fa fa-edit"></i></a>
                                    <form action="{{ route('admin.rss_feeds.destroy', $feed->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this feed and its staging items?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No RSS feeds yet. <a href="{{ route('admin.rss_feeds.create') }}">Add one</a>.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $feeds->links() }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Fetch progress --}}
<div class="modal fade" id="rssFetchProgressModal" tabindex="-1" aria-labelledby="rssFetchProgressModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rssFetchProgressModalLabel">Fetching RSS feeds</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="rssFetchProgressCloseBtn" style="display:none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-2 text-muted" id="rssFetchProgressMessage">Starting...</p>
                <div class="progress" style="height: 24px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" id="rssFetchProgressBar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                </div>
                <p class="mt-2 small text-muted mb-0" id="rssFetchProgressResult"></p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    var fetchNowUrl = @json(route('admin.rss_feeds.fetchNow'));
    var fetchProgressUrl = @json(route('admin.rss_feeds.fetchProgress', ['runId' => '__RUN_ID__']));
    var pollInterval = null;

    function showModal() {
        $('#rssFetchProgressModal').modal('show');
        $('#rssFetchProgressCloseBtn').hide();
        $('#rssFetchProgressResult').text('').hide();
        $('#rssFetchProgressBar').css('width', '0%').text('0%').removeClass('bg-success bg-danger').addClass('progress-bar-animated progress-bar-striped');
        $('#rssFetchProgressMessage').text('Starting...');
    }

    function updateProgress(data) {
        var pct = Math.min(100, parseInt(data.progress, 10) || 0);
        $('#rssFetchProgressBar').css('width', pct + '%').attr('aria-valuenow', pct).text(pct + '%');
        $('#rssFetchProgressMessage').text(data.message || '');
        if (data.created !== null && data.skipped !== null) {
            $('#rssFetchProgressResult').text('Created: ' + data.created + ', Skipped: ' + data.skipped).show();
        }
        if (data.status === 'completed') {
            $('#rssFetchProgressBar').removeClass('progress-bar-animated progress-bar-striped').addClass('bg-success');
            $('#rssFetchProgressCloseBtn').show();
            if (pollInterval) { clearInterval(pollInterval); pollInterval = null; }
        }
        if (data.status === 'error') {
            $('#rssFetchProgressBar').removeClass('progress-bar-animated progress-bar-striped').addClass('bg-danger');
            $('#rssFetchProgressCloseBtn').show();
            if (pollInterval) { clearInterval(pollInterval); pollInterval = null; }
        }
    }

    function startFetch(feedId) {
        showModal();
        var payload = feedId ? { feed_id: feedId } : {};
        $.ajax({
            url: fetchNowUrl,
            method: 'POST',
            data: JSON.stringify(payload),
            contentType: 'application/json',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Accept': 'application/json' }
        }).done(function(res) {
            var runId = res.run_id;
            var url = fetchProgressUrl.replace('__RUN_ID__', runId);
            function poll() {
                $.get(url).done(function(data) {
                    updateProgress(data);
                    if (data.status !== 'completed' && data.status !== 'error') {
                        pollInterval = setTimeout(poll, 1000);
                    }
                }).fail(function() {
                    $('#rssFetchProgressMessage').text('Could not get progress.');
                    $('#rssFetchProgressCloseBtn').show();
                    if (pollInterval) clearInterval(pollInterval);
                });
            }
            poll();
        }).fail(function() {
            $('#rssFetchProgressMessage').text('Failed to start fetch.');
            $('#rssFetchProgressCloseBtn').show();
        });
    }

    $(document).on('click', '.js-fetch-all', function() {
        startFetch(null);
    });
    $(document).on('click', '.js-fetch-one', function() {
        var id = $(this).data('feed-id');
        if (id) startFetch(id);
    });
})();
</script>
@endsection
