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
                                    <th>Linked Data Categories</th>
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
                                    <td>{{ $row->linkedDataCategories->count() }}</td>
                                    <td>
                                        <a href="#edit-subcategory-modal" data-toggle="modal"
                                           data-id="{{ $row->id }}"
                                           data-category_name="{{ e($row->category_name) }}"
                                           data-category_desc="{{ e($row->category_desc ?? '') }}"
                                           data-linked_categories='@json($row->linkedDataCategories->pluck("id")->values())'
                                           class="btn btn-sm btn-outline-primary">Edit</a>
                                        @can('delete_publication_metadata')
                                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger" onclick='openDeleteModal({{ (int) $row->id }}, @json((string) $row->category_name))'>Delete</a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No categories yet. <a href="#add-subcategory-modal" data-toggle="modal">Add one</a>.</td>
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
    var toDeleteName = '';
    function openDeleteModal(id, categoryName) {
        toDeleteId = id;
        toDeleteName = categoryName || '';
        $('#delete-subcategory-modal').modal('show');
        var nameEl = document.getElementById('deleteCategoryName');
        if (nameEl) {
            nameEl.textContent = toDeleteName || ('ID ' + toDeleteId);
        }
        var replacementSelect = document.getElementById('replacement_category_id');
        if (replacementSelect) {
            replacementSelect.value = '';
            Array.from(replacementSelect.options).forEach(function (opt) {
                if (String(opt.value) === String(toDeleteId)) {
                    opt.disabled = true;
                } else if (opt.value !== '') {
                    opt.disabled = false;
                }
            });
        }
    }
    function showDeleteNotice(message, type = 'info', onClose = null) {
        if (typeof swal === 'function') {
            var result = swal(type === 'success' ? 'Success' : 'Notice', message, type);
            if (result && typeof result.then === 'function') {
                result.then(function () { if (typeof onClose === 'function') onClose(); });
            } else if (typeof onClose === 'function') {
                setTimeout(onClose, 300);
            }
            return;
        }
        alert(message);
        if (typeof onClose === 'function') onClose();
    }
    function confirmDelete() {
        if (!toDeleteId) return;
        var replacementCategoryId = (document.getElementById('replacement_category_id') || {}).value;
        if (!replacementCategoryId) {
            showDeleteNotice('Please select the category to map data to.', 'warning');
            return;
        }
        if (String(replacementCategoryId) === String(toDeleteId)) {
            showDeleteNotice('Please select a different category.', 'warning');
            return;
        }
        var url = "{{ route('admin.subcategories.destroy') }}" + "?id=" + encodeURIComponent(toDeleteId) + "&replacement_category_id=" + encodeURIComponent(replacementCategoryId);
        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function (res) { return res.json(); })
            .then(function (res) {
                if (res.status !== 'success') {
                    showDeleteNotice(res.message || 'Failed to delete category.', 'error');
                    return;
                }
                $('#delete-subcategory-modal').modal('hide');
                var movedSubs = res?.data?.moved_subcategories ?? 0;
                var movedPubs = res?.data?.moved_publications ?? 0;
                showDeleteNotice((res.message || 'Category deleted.') + ' Mapped ' + movedSubs + ' subcategories and ' + movedPubs + ' publications.', 'success', function () {
                    window.location.reload();
                });
            })
            .catch(function () { showDeleteNotice('Failed to delete category.', 'error'); });
    }
</script>
@endsection
