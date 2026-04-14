@extends(admin_layout())

@section('styles')
    @include('common.table')
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Sub Categories</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin') }}">Admin</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)">Dropdown Lists</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sub Categories</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="card col-lg-12">
            <div class="card-header text-left d-flex justify-content-between align-items-center flex-wrap">
                <h3 class="card-title mb-0">Manage Categories</h3>
                <a href="#add-subcategory-modal" data-toggle="modal" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add Sub Category</a>
            </div>
            <div class="card-header">
                <form method="GET" action="{{ route('admin.subcategories.index') }}" class="container-fluid">
                    <div class="row align-items-end">
                        <div class="col-md-8">
                            <div class="form-group mb-0">
                                <label for="term">Search</label>
                                <input type="text" name="term" id="term" class="form-control" placeholder="Name or description..." value="{{ $search->term ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary btn-sm">Search</button>
                            <a href="{{ route('admin.subcategories.index') }}" class="btn btn-secondary btn-sm">Clear</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body text-left">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Sub categories</th>
                                <th width="140">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subcategories as $idx => $row)
                                <tr>
                                    <td>{{ $subcategories->firstItem() + $idx }}</td>
                                    <td>{{ $row->category_name }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($row->category_desc, 60) }}</td>
                                    <td>{{ $row->sub_categories_count ?? 0 }}</td>
                                    <td>
                                        <a href="#edit-subcategory-modal" data-toggle="modal"
                                           data-id="{{ $row->id }}"
                                           data-category_name="{{ e($row->category_name) }}"
                                           data-category_desc="{{ e($row->category_desc ?? '') }}"
                                           class="btn btn-sm btn-outline-primary">Edit</a>
                                        @can('delete_publication_metadata')
                                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger" onclick="openDeleteModal({{ $row->id }})">Delete</a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No categories yet. <a href="#add-subcategory-modal" data-toggle="modal">Add one</a>.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="py-2">{{ $subcategories->links() }}</div>
            </div>
        </div>
    </div>

    @include('admin.subcategories.partials.add-modal')
    @include('admin.subcategories.partials.edit-modal')
    @include('admin.subcategories.partials.delete-modal')
@endsection

@section('scripts')
<script>
    var toDeleteId = '';
    function openDeleteModal(id) {
        toDeleteId = id;
        $('#delete-subcategory-modal').modal('show');
    }
    function confirmDelete() {
        if (!toDeleteId) return;
        window.location.href = "{{ route('admin.subcategories.destroy') }}?id=" + toDeleteId;
    }
</script>
@endsection
