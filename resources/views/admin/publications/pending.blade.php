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
        <h1 class="page-title">Pending Public Health Resources</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Publish</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pending Public Health Resource</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <!-- Filters Card -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="card-title mb-0">Filter Pending Resources</h3>
                        <small class="text-muted">Use quick filters or expand advanced filters for precise results</small>
                    </div>
                    <div>
                        <button class="btn btn-soft btn-sm" type="button" data-toggle="collapse" data-target="#advancedFilters" aria-expanded="false" aria-controls="advancedFilters">
                            <i class="fa fa-sliders mr-1"></i> Advanced Filters
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ url('admin/publications/pending') }}" class="mb-3">
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
                            <a href="{{ url('admin/publications/pending') }}" class="btn btn-secondary btn-sm"><i class="fa fa-rotate-left mr-1"></i> Clear</a>
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
                        <h3 class="card-title mb-0">Pending Publications</h3>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form id="bulk-approval-form" method="POST" action="{{ route('admin.publications.bulk-approval') }}">
                        @csrf
                        <input type="hidden" name="action" id="bulk-approval-action" value="">
                        <input type="hidden" name="rejected_reason" id="bulkRejectReasonHidden" value="">

                        <!-- Bulk actions toolbar -->
                        <div class="mb-3 d-flex align-items-center flex-wrap" style="gap:8px;">
                            <button type="button" class="btn btn-sm btn-success" id="bulkApproveBtn" disabled title="Select one or more publications first">
                                <i class="fa fa-check-circle mr-1"></i> Approve selected
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="bulkRejectBtn" disabled title="Select one or more publications first">
                                <i class="fa fa-times-circle mr-1"></i> Reject selected
                            </button>
                            <span class="text-muted small" id="bulkSelectionCount"></span>
                        </div>

                    <!-- Datatable -->
                    <div class="table-responsive">
                        <table id="publicationTable" class="table table-striped table-bordered table-hover" style="border-radius: 0;">
                            <thead>
                                <tr>
                                    <th style="width:40px;">
                                        <input type="checkbox" id="selectAllPending" title="Check/uncheck all on this page">
                                    </th>
                                    <th style="width:60px;">#</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th width="10%">Author</th>
                                    <th width="10%">Affiliation</th>
                                    <th width="10%">Member State</th>
                                    <th>Status</th>
                                    <th width="10%">Date Created</th>
                                    <th width="18%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($publications as $idx => $publication)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="publication_ids[]" value="{{ $publication->id }}" class="pending-pub-cb">
                                        </td>
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

    <!-- Bulk reject reason modal -->
    <div class="modal fade" id="bulkRejectModal" tabindex="-1" role="dialog" aria-labelledby="bulkRejectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkRejectModalLabel">Reject selected publications</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">Optionally provide a reason (will be sent to submitters).</p>
                    <textarea name="rejected_reason" id="bulkRejectReason" class="form-control" rows="3" placeholder="Reason for rejection (optional)"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm" id="bulkRejectConfirmBtn">
                        <i class="fa fa-times-circle mr-1"></i> Reject selected
                    </button>
                </div>
            </div>
        </div>
    </div>

        <!-- Include edit-modal.php -->
        @include('admin.publications.partials.edit-modal')
        <!-- Include delete-modal.php -->
        @include('admin.publications.partials.delete-modal')
    @endsection

@section('scripts')
    @parent
    @include('common.select2')
    <script>
(function() {
    var form = document.getElementById('bulk-approval-form');
    var selectAll = document.getElementById('selectAllPending');
    var checkboxes = document.querySelectorAll('.pending-pub-cb');
    var bulkApproveBtn = document.getElementById('bulkApproveBtn');
    var bulkRejectBtn = document.getElementById('bulkRejectBtn');
    var bulkRejectModal = document.getElementById('bulkRejectModal');
    var bulkRejectReason = document.getElementById('bulkRejectReason');
    var bulkRejectReasonHidden = document.getElementById('bulkRejectReasonHidden');
    var bulkRejectConfirmBtn = document.getElementById('bulkRejectConfirmBtn');
    var actionInput = document.getElementById('bulk-approval-action');
    var countEl = document.getElementById('bulkSelectionCount');

    function getSelectedIds() {
        return Array.prototype.slice.call(checkboxes).filter(function(cb) { return cb.checked; }).map(function(cb) { return cb.value; });
    }

    function updateState() {
        var ids = getSelectedIds();
        var n = ids.length;
        bulkApproveBtn.disabled = n === 0;
        bulkRejectBtn.disabled = n === 0;
        if (countEl) {
            countEl.textContent = n > 0 ? n + ' selected' : '';
        }
        if (selectAll) {
            selectAll.checked = n > 0 && n === checkboxes.length;
            selectAll.indeterminate = n > 0 && n < checkboxes.length;
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            Array.prototype.forEach.call(checkboxes, function(cb) { cb.checked = selectAll.checked; });
            updateState();
        });
    }

    Array.prototype.forEach.call(checkboxes, function(cb) {
        cb.addEventListener('change', updateState);
    });

    if (bulkApproveBtn) {
        bulkApproveBtn.addEventListener('click', function() {
            if (getSelectedIds().length === 0) return;
            if (!confirm('Approve the selected publication(s)?')) return;
            actionInput.value = 'approve';
            form.submit();
        });
    }

    if (bulkRejectBtn) {
        bulkRejectBtn.addEventListener('click', function() {
            if (getSelectedIds().length === 0) return;
            if (bulkRejectModal && typeof $ !== 'undefined' && $.fn.modal) {
                if (bulkRejectReason) bulkRejectReason.value = '';
                $(bulkRejectModal).modal('show');
            } else {
                if (confirm('Reject the selected publication(s)?')) {
                    actionInput.value = 'reject';
                    if (bulkRejectReasonHidden) bulkRejectReasonHidden.value = '';
                    form.submit();
                }
            }
        });
    }

    if (bulkRejectConfirmBtn && bulkRejectModal) {
        bulkRejectConfirmBtn.addEventListener('click', function() {
            actionInput.value = 'reject';
            if (bulkRejectReasonHidden && bulkRejectReason) bulkRejectReasonHidden.value = bulkRejectReason.value || '';
            $(bulkRejectModal).modal('hide');
            form.submit();
        });
    }

    updateState();
})();
    </script>
@endsection
