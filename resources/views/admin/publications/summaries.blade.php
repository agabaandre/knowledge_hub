@extends(admin_layout())

@section('styles')
    @include('common.table')
    @include('admin.publications.partials.filter_styles')
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
        <div class="pub-filters-card">
            <div class="pub-filters-card__header">
                <div class="pub-filters-card__heading">
                    <span class="pub-filters-card__icon"><i class="fa fa-filter"></i></span>
                    <div>
                        <h3 class="pub-filters-card__title">Filter Summaries</h3>
                        <p class="pub-filters-card__subtitle">Filters apply automatically as you type or change selections</p>
                    </div>
                </div>
            </div>
            <div class="pub-filters-card__body">
                <form id="summaryFiltersForm">
                    <div class="pub-filters-grid pub-filters-grid--duo">
                        <div class="pub-filter-field">
                            <label class="pub-filter-label" for="filterTitle">Keyword</label>
                            <input type="text" name="term" id="filterTitle" class="form-control pub-filter-input" value="{{ @$search->term ?? '' }}" placeholder="Title or keyword">
                        </div>
                        <div class="pub-filter-field">
                            <label class="pub-filter-label" for="author">Source / Author</label>
                            @include('partials.authors.dropdown', [
                                'field' => 'author',
                                'selected' => @$search->author,
                                'class' => 'pub-filter-input select2 form-control',
                            ])
                        </div>
                    </div>
                    <div class="pub-filters-actions">
                        <a href="{{ url('admin/publications/summaries') }}" class="pub-filters-btn pub-filters-btn--clear">
                            <i class="fa fa-rotate-left"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card pub-list-card">
            <div class="card-header">
                <h3 class="card-title mb-0">Publication Summaries</h3>
            </div>
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
        ]
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
