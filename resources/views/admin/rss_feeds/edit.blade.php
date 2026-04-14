@extends(admin_layout())

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit RSS Feed</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('admin') }}">Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.rss_feeds.index') }}">RSS Feeds</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title mb-0">Edit Feed</h3></div>
            <div class="card-body">
                <form action="{{ route('admin.rss_feeds.update', $feed->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $feed->name) }}" required>
                        @error('name')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="url">Feed URL <span class="text-danger">*</span></label>
                        <input type="url" name="url" id="url" class="form-control" value="{{ old('url', $feed->url) }}" required>
                        @error('url')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="is_active" id="is_active" value="1" {{ old('is_active', $feed->is_active) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Active</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('admin.rss_feeds.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
