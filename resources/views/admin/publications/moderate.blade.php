@extends(admin_layout())

@section('styles')
    @include('common.table')
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
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title mb-0">Filter comments</h3>
                    <small class="text-muted">Search by publication title or comment text</small>
                </div>
                <div class="card-body">
                    <form id="moderateFiltersForm" class="row align-items-end">
                        <div class="col-md-6">
                            <label class="form-label-sm">Keyword</label>
                            <input type="text" name="term" id="filterTitle" class="form-control" value="{{ @$search->term ?? '' }}" placeholder="Publication title or comment text">
                        </div>
                        <div class="col-md-6 text-md-end mt-2 mt-md-0">
                            <a href="{{ url('admin/publications/moderate') }}" class="btn btn-secondary btn-sm">Clear</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
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
        ],
        dom: '<"row mb-2"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"i>>rtip'
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
