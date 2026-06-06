@extends(admin_layout())

@section('styles')
    @include('common.table')
    <style>
        .filter-card { background:#fff; border:1px solid #e2e8f0; }
        .filter-card .card-header { background:#f8fafc; border-bottom:1px solid #e2e8f0; }
        .form-label-sm { font-size:.875rem; font-weight:600; color:#334155; }
    </style>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Publication Summaries</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Publish</a></li>
            <li class="breadcrumb-item active" aria-current="page">Publication Summaries</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card filter-card mb-3">
            <div class="card-header">
                <strong>Filter Summaries</strong>
                <small class="text-muted d-block">Filters apply automatically</small>
            </div>
            <div class="card-body">
                <form id="summaryFiltersForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-sm">Keyword</label>
                                <input type="text" name="term" id="filterTitle" class="form-control" value="{{ @$search->term ?? '' }}" placeholder="Title or keyword">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-sm">Source / Author</label>
                                @include('partials.authors.dropdown', ['field' => 'author', 'selected' => @$search->author, 'class' => 'select2 form-control'])
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ url('admin/publications/summaries') }}" class="btn btn-soft btn-sm"><i class="fa fa-rotate-left mr-1"></i> Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="summariesTable" class="table table-striped table-hover table-bordered w-100">
                        <thead>
                            <tr>
                                <th width="60">#</th>
                                <th>Title</th>
                                <th>Content</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th width="180">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('common.select2')
@include('admin.publications.partials.datatable_assets')
<script>
let summariesTable = null;
let filterReloadTimer = null;

function collectFilterParams() {
    const params = {};
    $('#summaryFiltersForm').find('input, select').each(function () {
        const name = $(this).attr('name');
        const value = $(this).val();
        if (name && value !== null && value !== '' && value !== 'all') params[name] = value;
    });
    return params;
}

$(function () {
    summariesTable = $('#summariesTable').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        pageLength: 15,
        order: [[0, 'desc']],
        ajax: {
            url: '{{ url('admin/publications/summaries') }}',
            data: function (d) {
                d.datatable = 1;
                return Object.assign(d, collectFilterParams());
            }
        },
        columns: [
            { data: 'index', orderable: true },
            { data: 'title' },
            { data: 'content', orderable: false },
            { data: 'author', orderable: false },
            { data: 'status', orderable: true },
            { data: 'actions', orderable: false }
        ],
        dom: '<"row mb-2"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"i>>rtip'
    });

    $('#filterTitle').on('input', function () {
        clearTimeout(filterReloadTimer);
        filterReloadTimer = setTimeout(function () { summariesTable.ajax.reload(); }, 350);
    });
    $('#summaryFiltersForm').on('change', 'select', function () { summariesTable.ajax.reload(); });
    if ($.fn.select2) $('#summaryFiltersForm select.select2').on('change.select2', function () { summariesTable.ajax.reload(); });
});
</script>
@endsection
