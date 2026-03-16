@extends(admin_layout())

@section('content')
<div class="page-header">
    <h1 class="page-title">RSS Staging (Pending from feeds)</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('admin') }}">Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.rss_feeds.index') }}">RSS Feeds</a></li>
            <li class="breadcrumb-item active">Staging</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">Items from RSS (edit & approve or reject)</h3>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form method="GET" action="{{ route('admin.rss_staging.index') }}" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Feed</label>
                            <select name="feed_id" class="form-control">
                                <option value="">All feeds</option>
                                @foreach($feeds as $f)
                                    <option value="{{ $f->id }}" {{ request('feed_id') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="pending" {{ request('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Feed</th>
                                <th>Title</th>
                                <th>Link</th>
                                <th>Fetched</th>
                                @if(request('status') != 'pending')
                                    <th>Processed</th>
                                @endif
                                <th width="220">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($staging as $idx => $item)
                            <tr>
                                <td>{{ $staging->firstItem() + $idx }}</td>
                                <td>{{ $item->feed->name ?? '-' }}</td>
                                <td>{{ \Illuminate\Support\Str::limit(strip_tags($item->title), 60) }}</td>
                                <td><a href="{{ $item->rss_link }}" target="_blank" rel="noopener">{{ \Illuminate\Support\Str::limit($item->rss_link, 40) }}</a></td>
                                <td>{{ $item->created_at->format('M d, Y') }}</td>
                                @if(request('status') != 'pending')
                                    <td>{{ $item->processed_at ? $item->processed_at->format('M d, Y') : '-' }} {{ $item->processed_status }}</td>
                                @endif
                                <td>
                                    @if($item->processed_status === 'pending')
                                        <a href="{{ route('admin.rss_staging.edit', $item->id) }}" class="btn btn-sm btn-success mr-1"><i class="fa fa-check"></i> Edit & Approve</a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#rejectModal{{ $item->id }}"><i class="fa fa-times"></i> Reject</button>
                                        <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('admin.rss_staging.reject', $item->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Reject RSS item</h5>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($item->title), 80) }}</p>
                                                            <label for="rejection_reason{{ $item->id }}">Reason (optional)</label>
                                                            <textarea name="rejection_reason" id="rejection_reason{{ $item->id }}" class="form-control" rows="3" placeholder="Why this item is not approved"></textarea>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">Reject</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        @if($item->publication_id)
                                            <a href="{{ url('admin/publications/details?id=' . $item->publication_id) }}" class="btn btn-sm btn-outline-primary">View Publication</a>
                                        @else
                                            <span class="text-muted">Rejected</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ request('status') != 'pending' ? 7 : 6 }}" class="text-center text-muted">No items.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $staging->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
