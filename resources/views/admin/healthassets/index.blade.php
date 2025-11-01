@extends('admin.layouts.main')

@section('styles')
 @include('common.table')
 <style>
    .card { border: 1px solid #e2e8f0; border-radius: 0; margin-bottom: 1.5rem; }
    .card-header { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.5rem; }
    .card-body { padding: 1.5rem; }
 </style>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Health Assets Management</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Health Assets</li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Filters Card -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="card-title mb-0">Filter Health Assets</h3>
                    <small class="text-muted">Search and filter health assets</small>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ url('admin/healthassets') }}" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <input type="text" name="term" id="filterTitle" class="form-control" placeholder="Search by name, description, or URL..." value="{{ @$search->term ?? ''}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <select name="asset_type_id" id="asset_type_id" class="form-control">
                                    <option value="">All Asset Types</option>
                                    @foreach($asset_types ?? [] as $type)
                                        <option value="{{ $type->id }}" {{ (@$search->asset_type_id == $type->id) ? 'selected' : '' }}>
                                            {{ $type->type_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex">
                                <button type="submit" id="filterButton" class="btn btn-primary btn-sm mr-2">Filter</button>
                                <a href="{{ url('admin/healthassets') }}" class="btn btn-secondary btn-sm">Clear</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Health Assets Table Card -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Health Assets</h3>
                </div>
            </div>
            <div class="card-body">
                @if(session('message'))
                    <div class="alert alert-{{ session('status') == 'success' ? 'success' : 'danger' }}">
                        {{ session('message') }}
                    </div>
                @endif

                @if(count($assets) > 0)
                    <div class="table-responsive">
                        <table id="healthAssetsTable" class="table table-striped table-bordered table-hover" style="border-radius: 0;">
                            <thead>
                                <tr>
                                    <th width="60px">#</th>
                                    <th>Asset Name</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>URL</th>
                                    <th width="150px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assets as $idx => $row)
                                    <tr>
                                        <td><span class="text-muted">{{ $assets->firstItem() + $idx }}</span></td>
                                        <td><strong>{{ $row->asset_name ?? 'N/A' }}</strong></td>
                                        <td>{{ $row->type->type_name ?? 'N/A' }}</td>
                                        <td>{{ Str::limit(strip_tags($row->asset_desc ?? '-'), 80) }}</td>
                                        <td>
                                            @if($row->url)
                                                <a href="{{ $row->url }}" target="_blank" class="text-primary">
                                                    <i class="fa fa-external-link mr-1"></i> View
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ url('admin/healthassets/detail') }}?id={{ $row->id }}" class="btn btn-sm btn-outline-info mr-1" title="View Details">
                                                <i class="fa fa-eye mr-1"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $assets->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <p class="text-muted">No health assets found</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2 for asset type filter if available
        if ($.fn.select2) {
            $('#asset_type_id').select2({
                placeholder: 'All Asset Types',
                allowClear: true
            });
        }
    });
</script>
@endsection

