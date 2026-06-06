@extends(admin_layout())

@section('styles')
    @include('common.table')
    @include('admin.publications.partials.filter_styles')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Moderate Comments</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Publish</a></li>
            <li class="breadcrumb-item active" aria-current="page">Moderate Comments</li>
        </ol>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="pub-filters-card mb-3">
                <div class="pub-filters-card__header">
                    <div class="pub-filters-card__heading">
                        <span class="pub-filters-card__icon"><i class="fa fa-filter"></i></span>
                        <div>
                            <h3 class="pub-filters-card__title">Filter Comments</h3>
                            <p class="pub-filters-card__subtitle">Search by publication title or comment text</p>
                        </div>
                    </div>
                </div>
                <div class="pub-filters-card__body">
                    <form id="moderateFiltersForm">
                        <div class="pub-filters-grid pub-filters-grid--single">
                            <div class="pub-filter-field">
                                <label class="pub-filter-label" for="filterTitle">Keyword</label>
                                <input type="text" name="term" id="filterTitle" class="form-control pub-filter-input" value="{{ @$search->term ?? '' }}" placeholder="Publication title or comment text">
                            </div>
                        </div>
                        <div class="pub-filters-actions">
                            <a href="{{ url('admin/publications/moderate') }}" class="pub-filters-btn pub-filters-btn--clear">
                                <i class="fa fa-rotate-left"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card pub-list-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="moderateCommentsTable" class="table table-striped table-hover table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>Publication</th>
                                    <th>Comment</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
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
</div>
@endsection

@section('scripts')
@include('common.sweet-alert')
@include('admin.publications.partials.datatable_assets')
<script>
let moderateTable = null;
let filterReloadTimer = null;

$(function () {
    moderateTable = $('#moderateCommentsTable').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        pageLength: 20,
        order: [[3, 'desc']],
        ajax: {
            url: '{{ url('admin/publications/moderate') }}',
            data: function (d) {
                d.datatable = 1;
                d.term = $('#filterTitle').val() || '';
                return d;
            }
        },
        columns: [
            { data: 'publication' },
            { data: 'comment' },
            { data: 'created_by', orderable: false },
            { data: 'created_at' },
            { data: 'actions', orderable: false }
        ]
    });

    $('#filterTitle').on('input', function () {
        clearTimeout(filterReloadTimer);
        filterReloadTimer = setTimeout(function () { moderateTable.ajax.reload(); }, 350);
    });

    $(document).on('click', '.approve_comment', function (event) {
        event.preventDefault();
        var url = $(this).attr('href');
        $.get(url, function () {
            swal('Success!', 'Comment approved', 'success');
            moderateTable.ajax.reload(null, false);
        });
    });

    $(document).on('click', '.reject_comment', function (event) {
        event.preventDefault();
        var url = $(this).attr('href');
        $.get(url, function () {
            swal('Success!', 'Comment rejected', 'success');
            moderateTable.ajax.reload(null, false);
        });
    });
});
</script>
@endsection
