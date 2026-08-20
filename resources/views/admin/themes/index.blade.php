@extends(admin_layout())

@section('styles')
    @include('common.table')
    <link href="{{ asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ $description ?? 'Thematic Area' }}</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Dropdown Lists</a></li>
            <li class="breadcrumb-item active" aria-current="page">Security Themes</li>
        </ol>
    </div>
</div>
    <div class="row">
        <div class="card col-lg-12">
           
            <!-- Card Header With Form Filters -->
            <div class="card-header">
                <form class="container-fluid" method="get" action="{{ url('admin/themes') }}" id="themesFilterForm">
                    <div class="row">

                        <div class="col-md-12 text-right">
                            <a href="#create-modal" data-toggle="modal" class="btn btn-outline-success float-right"><i
                                    class="fa fa-plus"></i> Add Thematic Area</a>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="filterTitle">Search</label>
                                <input type="text" name="term" id="filterTitle" class="form-control"
                                    placeholder="Filter by name" value="{{ @$search->term ?? '' }}">
                            </div>
                        </div>


                    </div>

                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="submit" id="filterButton" class="btn btn-primary btn-sm">Filter Data</button>
                            <button type="button" id="reset" class="btn btn-secondary btn-sm">Reset</button>
                            <button type="button" id="exportButton" class="btn btn-success btn-sm">Export Data</button>

                        </div>


                    </div>
                </form>
            </div>
            <div class="card-body text-left">
                @include('layouts.partials.alerts')
                <table id="themesTable" class="table table-striped table-bordered" data-kh-datatable="client">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Thematic Area</th>
                            <th>Display Order</th>
                            <th>Icon</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($themes as $row)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $row->description }}</td>
                                <td>{{ (int) ($row->display_order ?? 0) }}</td>
                                <td>{{ $row->icon }}</td>
                                <td>
                                    <a href="{{ url('admin/subthemes') }}?theme_id={{ (int) $row->id }}"
                                        class="btn btn-sm btn-info mr-1">View Subthemes</a>
                                    <a href="#edit-theme-modal" class="btn btn-sm btn-success mr-1" data-toggle="modal"
                                        data-id="{{ $row->id }}"
                                        data-description="{{ e((string) $row->description) }}"
                                        data-detailed_description="{{ e((string) ($row->detailed_description ?? '')) }}"
                                        data-icon="{{ e((string) $row->icon) }}"
                                        data-display_order="{{ (int) ($row->display_order ?? 0) }}">Edit
                                        Theme</a>

                                    @can('delete_publication_metadata')
                                    <a class="btn btn-sm btn-danger ml-1" href="javascript:void(0);"
                                        onclick='openDeleteModal({{ (int) $row->id }}, @json((string) $row->description))'>Delete</a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

        </div>

        @include('admin.themes.partials.create-modal')
        @include('admin.themes.partials.edit-modal')
        @include('admin.themes.partials.delete-modal')
@endsection

@section('scripts')
    @include('admin.partials.metadata_datatable')
    <script>
        $(function () {
            var table = window.khInitMetadataTable('#themesTable', {
                order: [[2, 'asc'], [1, 'asc']]
            });
            $('#exportButton').on('click', function () {
                if (table) {
                    // Fallback: copy visible rows as CSV-ish via browser print of table search filter
                    table.search($('#filterTitle').val() || '').draw();
                }
                window.print();
            });
        });
    </script>
@endsection
