@extends('admin.layouts.main')

@section('styles')
    @include('common.table')
@endsection


@section('content')
    <div class="page-header">
        <h1 class="page-title">Manage Public Health Resources</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Publish</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manage Public Health Resource</li>
            </ol>
        </div>
    </div>

    <div class="row">

        <div class="card col-lg-12">
            <div class="card-header text-left">

            </div>
            <!-- Card Header With Form Filters -->
            <div class="card-header">
                <form class="container-fluid">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" name="term" id="filterTitle" class="form-control"
                                    placeholder="Filter by Title" value="{{ @$search->term ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="source">Description</label>
                                <input type="text" name="description" id="filterDesc" class="form-control"
                                    value="{{ @$search->description ?? '' }}" placeholder="Filter by Description">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="source">Source / Author</label>
                                @include('partials.authors.dropdown', [
                                    'field' => 'author',
                                    'selected' => @$search->author,
                                ])
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="file_type">File Type</label>
                                @include('partials.publications.filetype_dropdown', [
                                    'field' => 'file_type',
                                    'selected' => @$search->file_type,
                                ])
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        @include('partials.search.search_fields')
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-right">
                            <!-- Export Button -->
                            <button type="submit" id="filterButton" class="btn btn-primary btn-sm">Filter Data</button>
                            <button type="button" id="reset" class="btn btn-secondary btn-sm">Reset</button>
                            <button type="button" id="exportButton" class="btn btn-success btn-sm">Export Data</button>

                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body text-left">
                <form id="bulk-actions-form" method="POST" action="">
                    @csrf
                    <input type="hidden" name="action" id="bulk-action-type" value="">
                    <div class="mb-2">
                        <button type="button" class="btn btn-warning btn-sm" onclick="submitBulkAction('inactive')">Inactive/Unpublish</button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="submitBulkAction('delete')">Delete</button>
                        <button type="button" class="btn btn-info btn-sm" onclick="submitBulkAction('featured')">Mark as Featured</button>
                    </div>
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <!-- Datatable -->
                    <table id="publicationTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>Title</th>
                                <th>Description</th>
                                <th width="10%">Author</th>
                                <th width="10%">Member State</th>
                                <th>Status</th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                            @php
                                $i = 1;
                            @endphp

                            @foreach ($publications as $publication)
                                <tr>
                                    <td><input type="checkbox" name="selected_ids[]" value="{{ $publication->id }}"></td>
                                    <td width="5%"><i class="fa {{ $publication->file_type->icon ?? 'fa-file' }} text-muted"></i></td>
                                    <td>
                                        <a href="{{ $publication->publication }}" target="_blank">
                                            {!! truncate($publication->title, 30) !!}
                                        </a>
                                    </td>
                                    <td>
                                        {!! truncate(html_to_text($publication->description), 50) !!}
                                    </td>
                                    <td>
                                        {{ $publication->author->name ?? '' }}
                                    </td>
                                    <td>
                                        {{ $publication->country->name ?? '' }}
                                    </td>
                                    <td>
                                        {{ get_publication_state($publication->is_approved, $publication->is_rejected) }}
                                    </td>
                                    <td>
                                        <a href="{{ url('admin/publications/details') }}?id={{ $publication->id }}"
                                            class="text-success">Details</a>

                                        @if ($publication->user_id == current_user()->id || is_admin())
                                            | <a href="{{ url('admin/publications/edit') }}?id={{ $publication->id }}"
                                                class="text-primary">Edit</a>
                                        @endif

                                        @can('delete_publications')
                                            | <a href="javascript:void(0);" onclick="openDeleteModal('{{ $publication->id }}')"
                                                class="text-danger"> Delete</a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="py-2"> {{ $publications->links() }}</div>

                </form>
            </div>

        </div>

        <!-- Include edit-modal.php -->
        @include('admin.publications.partials.edit-modal')
        <!-- Include delete-modal.php -->
        @include('admin.publications.partials.delete-modal')

        <!-- Bulk Action Confirmation Modal -->
        <div class="modal fade" id="bulkActionConfirmModal" tabindex="-1" role="dialog" aria-labelledby="bulkActionConfirmModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bulkActionConfirmModalLabel">Confirm Bulk Action</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <span id="bulkActionConfirmText">Are you sure you want to perform this action?</span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmBulkActionBtn">Yes, Proceed</button>
                    </div>
                </div>
            </div>
        </div>
    @endsection

@section('scripts')
<script>
let pendingBulkAction = null;
function submitBulkAction(action) {
    pendingBulkAction = action;
    let actionText = '';
    if (action === 'inactive') actionText = 'unpublish (set inactive)';
    else if (action === 'delete') actionText = 'delete';
    else if (action === 'featured') actionText = 'mark as featured';
    document.getElementById('bulkActionConfirmText').textContent = `Are you sure you want to ${actionText} the selected publications?`;
    $('#bulkActionConfirmModal').modal('show');
}

document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[name="selected_ids[]"]');
    const buttons = [
        document.querySelector('button[onclick*="inactive"]'),
        document.querySelector('button[onclick*="delete"]'),
        document.querySelector('button[onclick*="featured"]')
    ];
    function updateButtons() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        buttons.forEach(btn => btn.disabled = !anyChecked);
    }
    checkboxes.forEach(cb => cb.addEventListener('change', updateButtons));
    updateButtons();

    document.getElementById('confirmBulkActionBtn').addEventListener('click', function() {
        if (pendingBulkAction) {
            document.getElementById('bulk-action-type').value = pendingBulkAction;
            var form = document.getElementById('bulk-actions-form');
            form.action = '{{ url('admin/publications/bulk-action') }}';
            form.submit();
        }
    });
});
</script>
@endsection
