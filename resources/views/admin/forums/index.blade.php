@extends(admin_layout())

@section('styles')
<link href="{{ asset('assets/plugins/summernote/dist/summernote.min.css') }}" rel="stylesheet">
@include('common.table')
@include('admin.publications.partials.filter_styles')
<style>
    .forum-stat-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 0;
        border-top: 3px solid var(--theme-color-primary, #119A48);
    }
    .forum-stat-card--pending { border-top-color: #d97706; }
    .forum-stat-card--rejected { border-top-color: #dc3545; }
    .forum-stat-card__label {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 0.35rem;
    }
    .forum-stat-card__value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
    }
    #forumsTable_wrapper table.dataTable { table-layout: fixed !important; }
    #forumsTable .forum-col-index { width: 3rem; min-width: 3rem; }
    #forumsTable .forum-col-title { width: 18%; }
    #forumsTable .forum-col-description { width: 22%; }
    #forumsTable .forum-col-author { width: 10%; }
    #forumsTable .forum-col-created { width: 9%; }
    #forumsTable .forum-col-moderator { width: 14%; }
    #forumsTable .forum-col-actions { width: 11rem; min-width: 11rem; }
    .pub-actions-group { display: inline-flex; flex-wrap: wrap; gap: 4px; }
    .pub-actions-group .btn { padding: 0.25rem 0.45rem; }
</style>
@endsection

@section('content')
@php
    $queue = $forum_admin_queue ?? 'pending';
    $ajaxUrl = match ($queue) {
        'approved' => url('admin/forums/approved'),
        'rejected' => url('admin/forums/rejected'),
        default => url('admin/forums'),
    };
    $forumStats = $forum_stats ?? ['approved' => 0, 'pending' => 0, 'rejected' => 0];
@endphp

<div class="row mb-3">
    <div class="col-md-4 col-sm-6 mb-2">
        <a href="{{ url('admin/forums/approved') }}" class="card forum-stat-card h-100 shadow-sm text-decoration-none">
            <div class="card-body py-3">
                <div class="forum-stat-card__label">Approved Forums</div>
                <div class="forum-stat-card__value">{{ number_format((int) ($forumStats['approved'] ?? 0)) }}</div>
            </div>
        </a>
    </div>
    <div class="col-md-4 col-sm-6 mb-2">
        <a href="{{ url('admin/forums') }}" class="card forum-stat-card forum-stat-card--pending h-100 shadow-sm text-decoration-none">
            <div class="card-body py-3">
                <div class="forum-stat-card__label">Pending Review</div>
                <div class="forum-stat-card__value">{{ number_format((int) ($forumStats['pending'] ?? 0)) }}</div>
            </div>
        </a>
    </div>
    <div class="col-md-4 col-sm-6 mb-2">
        <a href="{{ url('admin/forums/rejected') }}" class="card forum-stat-card forum-stat-card--rejected h-100 shadow-sm text-decoration-none">
            <div class="card-body py-3">
                <div class="forum-stat-card__label">Rejected</div>
                <div class="forum-stat-card__value">{{ number_format((int) ($forumStats['rejected'] ?? 0)) }}</div>
            </div>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="pub-filters-card mb-3">
            <div class="pub-filters-card__header">
                <div class="pub-filters-card__heading">
                    <span class="pub-filters-card__icon"><i class="fa fa-filter"></i></span>
                    <div>
                        <h3 class="pub-filters-card__title">{{ $title ?? 'Forums' }}</h3>
                        <p class="pub-filters-card__subtitle">{{ $forum_list_subtitle ?? 'Search and manage discussion threads' }}</p>
                    </div>
                </div>
                <div class="d-flex align-items-center flex-wrap" style="gap:8px;">
                    <div class="btn-group btn-group-sm" role="group" aria-label="Forum queues">
                        <a href="{{ url('admin/forums') }}" class="btn {{ $queue === 'pending' ? 'btn-dark' : 'btn-outline-secondary' }}">Pending</a>
                        <a href="{{ url('admin/forums/approved') }}" class="btn {{ $queue === 'approved' ? 'btn-dark' : 'btn-outline-secondary' }}">Approved</a>
                        <a href="{{ url('admin/forums/rejected') }}" class="btn {{ $queue === 'rejected' ? 'btn-dark' : 'btn-outline-secondary' }}">Rejected</a>
                    </div>
                    @if(isset($pending_forums_count) && $pending_forums_count > 0)
                        <a class="nav-link position-relative d-inline-flex p-0" href="{{ url('admin/forums/moderate') }}" title="Pending Forums">
                            <svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:22px;height:22px;">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                            </svg>
                            <span class="badge badge-danger badge-pill" style="position:absolute;top:-6px;right:-8px;min-width:18px;">{{ $pending_forums_count }}</span>
                        </a>
                    @endif
                </div>
            </div>
            <div class="pub-filters-card__body">
                <form id="forumsFiltersForm" method="GET" action="{{ $ajaxUrl }}" class="mb-0">
                    <div class="pub-filters-grid pub-filters-grid--single">
                        <div class="pub-filter-field">
                            <label class="pub-filter-label" for="filterForumTerm">Keyword</label>
                            <input type="text" name="term" id="filterForumTerm" class="form-control pub-filter-input" placeholder="Filter by title, description, or author" value="{{ @$search->term ?? '' }}">
                        </div>
                    </div>
                    <div class="pub-filters-actions">
                        <a href="{{ $ajaxUrl }}" class="pub-filters-btn pub-filters-btn--clear" id="clearForumFilters">
                            <i class="fa fa-rotate-left"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card pub-list-card">
            <div class="card-header">
                <h3 class="card-title mb-0">{{ $title ?? 'Forums' }}</h3>
            </div>
            <div class="card-body text-left">
                @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

                <div class="publication-table-wrap">
                    <table id="forumsTable" data-kh-datatable="custom" class="table table-striped table-bordered table-hover w-100 kh-table-wrap-cells">
                        <thead>
                            <tr>
                                <th class="forum-col-index">#</th>
                                <th class="forum-col-title">Forum Title</th>
                                <th class="forum-col-description">Description</th>
                                <th class="forum-col-author">Author</th>
                                <th class="forum-col-created">Created</th>
                                <th class="forum-col-moderator">Approved/Rejected By</th>
                                <th class="forum-col-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.forums.partials.delete-modal')
@endsection

@section('scripts')
@include('admin.publications.partials.datatable_assets')
<script>
let forumsTable = null;
let forumsFilterTimer = null;

function collectForumFilterParams() {
    const params = {};
    const term = $('#filterForumTerm').val();
    if (term !== null && term !== '') {
        params.term = term;
    }
    return params;
}

function reloadForumsTable() {
    if (forumsTable) forumsTable.ajax.reload();
}

function scheduleForumFilterReload() {
    clearTimeout(forumsFilterTimer);
    forumsFilterTimer = setTimeout(reloadForumsTable, 350);
}

$(function () {
    forumsTable = $('#forumsTable').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        autoWidth: false,
        scrollX: false,
        pageLength: 20,
        lengthMenu: [[10, 20, 50, 100], [10, 20, 50, 100]],
        order: [[1, 'desc']],
        ajax: {
            url: '{{ $ajaxUrl }}',
            data: function (d) {
                d.datatable = 1;
                return Object.assign(d, collectForumFilterParams());
            }
        },
        columns: [
            { data: 'index', orderable: false, searchable: false, className: 'forum-col-index text-center' },
            { data: 'title', orderable: true, className: 'forum-col-title' },
            { data: 'description', orderable: true, className: 'forum-col-description' },
            { data: 'author', orderable: false, className: 'forum-col-author' },
            { data: 'created_at', orderable: true, className: 'forum-col-created' },
            { data: 'moderator', orderable: false, className: 'forum-col-moderator' },
            { data: 'actions', orderable: false, searchable: false, className: 'forum-col-actions text-nowrap' }
        ],
        columnDefs: [
            { targets: [0, 6], orderable: false }
        ],
        language: {
            processing: '<i class="fa fa-spinner fa-spin"></i> Loading forums...',
            emptyTable: 'No forums match your filters.',
            zeroRecords: 'No matching forums found.'
        }
    });

    $('#filterForumTerm').on('input', scheduleForumFilterReload);

    $('#forumsFiltersForm').on('submit', function (e) {
        e.preventDefault();
        reloadForumsTable();
    });

    $('#clearForumFilters').on('click', function (e) {
        e.preventDefault();
        window.location.href = '{{ $ajaxUrl }}';
    });
});
</script>
@endsection
