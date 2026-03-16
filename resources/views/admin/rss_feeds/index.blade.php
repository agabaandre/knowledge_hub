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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Manage RSS Feeds</h3>
                <a href="{{ route('admin.rss_feeds.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add Feed</a>
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
@endsection
