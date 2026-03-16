@extends(admin_layout())

@section('styles')
    @include('common.table')
    <style>
        .filter-chip { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border:1px solid #e2e8f0; border-radius:999px; background:#fff; }
        .btn-soft { border:1px solid #cbd5e1; background:#ffffff; }
        .btn-soft:hover { background:#f8fafc; }
        .form-label-sm { font-size:.875rem; font-weight:600; color:#334155; }
        .form-group { margin-bottom: 1.5rem; }
        #advancedFilters .form-group { margin-bottom: 1rem; }
        #advancedFilters .row { margin-left: -15px; margin-right: -15px; }
        .card { border: 1px solid #e2e8f0; border-radius: 0; margin-bottom: 1.5rem; }
        .card-header { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.5rem; }
        .card-body { padding: 1.5rem; }
    </style>
@endsection


@section('content')
    <div class="page-header">
        <div class="d-flex align-items-center justify-content-between">
            <h1 class="page-title">Manage Public Health Resources</h1>
            @if(isset($pending_publications_count) && $pending_publications_count > 0)
                <div class="dropdown nav-item">
                    <a class="nav-link position-relative" href="{{ url('admin/publications/pending') }}" title="Pending Publications">
                        <svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span class="badge badge-danger badge-pill" style="position:absolute;top:-4px;right:-6px;min-width:20px;">{{ $pending_publications_count }}</span>
                    </a>
                </div>
            @endif
        </div>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Publish</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manage Public Health Resource</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <!-- Filters Card -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="card-title mb-0">Filter Resources</h3>
                        <small class="text-muted">Use quick filters or expand advanced filters for precise results</small>
                    </div>
                    <div>
                        <button class="btn btn-soft btn-sm" type="button" data-toggle="collapse" data-target="#advancedFilters" aria-expanded="false" aria-controls="advancedFilters">
                            <i class="fa fa-sliders mr-1"></i> Advanced Filters
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ url('admin/publications') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label class="form-label-sm" for="title">Keyword</label>
                                    <input type="text" name="term" id="filterTitle" class="form-control" placeholder="Search by title or keyword" value="{{ @$search->term ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label class="form-label-sm" for="author">Source / Author</label>
                                    @include('partials.authors.dropdown', [ 'field' => 'author', 'selected' => @$search->author ])
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label class="form-label-sm" for="file_type">File Type</label>
                                    @include('partials.publications.filetype_dropdown', [ 'field' => 'file_type', 'selected' => @$search->file_type ])
                                </div>
                            </div>
                        </div>

                        <div id="advancedFilters" class="collapse mt-3">
                            <hr class="my-3" style="border-color: #e2e8f0;">
                            @include('partials.search.search_fields')
                        </div>

                        <div class="d-flex justify-content-end mt-2" style="gap:8px;">
                            <button type="submit" id="filterButton" class="btn btn-primary btn-sm"><i class="fa fa-filter mr-1"></i> Filter</button>
                            <a href="{{ url('admin/publications') }}" class="btn btn-secondary btn-sm"><i class="fa fa-rotate-left mr-1"></i> Clear</a>
                            <button type="button" id="exportButton" class="btn btn-success btn-sm"><i class="fa fa-download mr-1"></i> Export</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Publications Table Card -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Publications</h3>
                        <div>
                            <a href="{{ url('admin/publications/create') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> Add Publication
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="bulk-actions-form" method="POST" action="{{ url('admin/publications/bulk-action') }}">
                        @csrf
                        <input type="hidden" name="action" id="bulk-action-type" value="">

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <!-- Bulk Actions -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center">
                                <select name="action" id="bulkActionSelect" class="form-control mr-2" style="width: auto;" required>
                                    <option value="">Bulk Actions</option>
                                    <option value="inactive">Inactive/Unpublish Selected</option>
                                    <option value="delete">Delete Selected</option>
                                    <option value="featured">Mark as Featured</option>
                                </select>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="bulkActionButton" disabled>Apply</button>
                            </div>
                        </div>

                        <!-- Datatable -->
                        <div class="table-responsive">
                            <table id="publicationTable" class="table table-striped table-bordered table-hover" style="border-radius: 0;">
                                <thead>
                                    <tr>
                                        <th width="30">
                                            <input type="checkbox" id="selectAllPublications">
                                        </th>
                                        <th style="width:60px;">#</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th width="10%">Author</th>
                                        <th width="10%">Affiliation</th>
                                        <th width="10%">Member State</th>
                                        <th>Status</th>
                                        <th width="10%">Date Created</th>
                                        <th style="width:16%">Approved/Rejected By</th>
                                        <th width="18%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($publications as $idx => $publication)
                                        <tr>
                                            <td><input type="checkbox" name="selected_ids[]" value="{{ $publication->id }}" class="publication-checkbox"></td>
                                            <td><span class="text-muted">{{ $publications->firstItem() + $idx }}</span></td>
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
                                                {{ $publication->author_affiliation ?? '-' }}
                                            </td>
                                            <td>
                                                {{ $publication->country->name ?? '' }}
                                            </td>
                                            <td>
                                                {{ get_publication_state($publication->is_approved, $publication->is_rejected) }}
                                            </td>
                                            <td>
                                                @if($publication->date_created)
                                                    {{ \Carbon\Carbon::parse($publication->date_created)->format('M d, Y') }}
                                                @elseif($publication->created_at)
                                                    {{ \Carbon\Carbon::parse($publication->created_at)->format('M d, Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $name = '-';
                                                    if (!empty($publication->approved_by)) {
                                                        $u = \App\Models\User::find($publication->approved_by);
                                                        $name = $u ? ($u->name ?? '-') : '-';
                                                    } elseif (!empty($publication->rejected_by)) {
                                                        $u = \App\Models\User::find($publication->rejected_by);
                                                        $name = $u ? ($u->name ?? 'Rejected') : 'Rejected';
                                                    }
                                                @endphp
                                                <span class="text-muted">{{ $name }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ url('admin/publications/details') }}?id={{ $publication->id }}" class="btn btn-sm btn-outline-primary mr-1">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                @if ($publication->user_id == current_user()->id || is_admin())
                                                    <a href="{{ url('admin/publications/edit') }}?id={{ $publication->id }}" class="btn btn-sm btn-outline-dark mr-1">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endif
                                                @can('delete_publications')
                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="openDeleteModal('{{ $publication->id }}')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $publications->links() }}
                        </div>
                    </form>
                </div>
            </div>
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
@include('common.select2')
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
    // Select all checkbox functionality
    $('#selectAllPublications').on('change', function() {
        $('.publication-checkbox').prop('checked', this.checked);
        updateBulkActionButton();
    });

    const checkboxes = document.querySelectorAll('input[name="selected_ids[]"]');
    const bulkActionSelect = document.getElementById('bulkActionSelect');
    const bulkActionButton = document.getElementById('bulkActionButton');
    
    function updateBulkActionButton() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        const actionSelected = bulkActionSelect && bulkActionSelect.value;
        
        if (bulkActionButton) {
            bulkActionButton.disabled = !anyChecked || !actionSelected;
        }
    }
    
    checkboxes.forEach(cb => cb.addEventListener('change', updateBulkActionButton));
    if (bulkActionSelect) {
        bulkActionSelect.addEventListener('change', updateBulkActionButton);
    }
    updateBulkActionButton();

    // Bulk action form submission
    if (bulkActionButton) {
        bulkActionButton.addEventListener('click', function(e) {
            e.preventDefault();
            if (!$('input[name="selected_ids[]"]:checked').length) {
                alert('Please select at least one publication.');
                return false;
            }
            if (!bulkActionSelect.value) {
                alert('Please select a bulk action.');
                return false;
            }
            submitBulkAction(bulkActionSelect.value);
        });
    }

    document.getElementById('confirmBulkActionBtn').addEventListener('click', function() {
        if (pendingBulkAction) {
            document.getElementById('bulk-action-type').value = pendingBulkAction;
            var form = document.getElementById('bulk-actions-form');
            form.submit();
        }
    });
});

function submitBulkAction(action) {
    pendingBulkAction = action;
    let actionText = '';
    if (action === 'inactive') actionText = 'unpublish (set inactive)';
    else if (action === 'delete') actionText = 'delete';
    else if (action === 'featured') actionText = 'mark as featured';
    document.getElementById('bulkActionConfirmText').textContent = `Are you sure you want to ${actionText} the selected publications?`;
    $('#bulkActionConfirmModal').modal('show');
}
</script>
@endsection
