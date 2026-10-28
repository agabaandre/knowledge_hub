@extends(admin_layout())

@section('styles')
    @include('common.table')
    <link href="{{ asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ $description ?? __('admin_nav.sub_thematic_area') }}</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('admin_nav.dropdown_lists') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('admin_nav.security_sub_themes') }}</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="card col-lg-12">
            <div class="card-header text-left">
                <h3 class="card-title float-left">{{ $title ?? __('admin_nav.sub_thematic_areas') }}</h3>
                <hr>
            </div>
            <!-- Card Header With Form Filters -->
            <div class="card-header">
                <form class="container-fluid" method="get" action="{{ url('admin/subthemes') }}" id="subthemesFilterForm">
                    <div class="row">

                        <div class="col-md-12 text-right">
                            <a href="#create-subtheme-modal" data-toggle="modal"
                                class="btn btn-outline-success float-right"><i class="fa fa-plus"></i> {{ ui_t('general.add', 'Add') }} {{ __('admin_nav.sub_thematic_area') }}</a>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="filterTitle">{{ ui_t('general.search', 'Search') }}</label>
                                <input type="text" name="term" id="filterTitle" class="form-control"
                                    placeholder="{{ __('admin_nav.filter_by_name') }}" value="{{ @$search->term ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="theme_id">{{ __('admin_nav.thematic_area') }}</label>
                                <select class="form-control select2" name="theme_id" id="theme_id">
                                    <option value="">{{ __('admin_nav.all_thematic_areas') }}</option>
                                    @foreach ($themes as $theme)
                                        <option value="{{ (int) $theme->id }}" @if(($selectedThemeId ?? 0) === (int) $theme->id) selected @endif>
                                            {{ $theme->description }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                    </div>

                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="submit" id="filterButton" class="btn btn-primary btn-sm">{{ ui_t('general.filter_data', 'Filter Data') }}</button>
                            <button type="button" id="reset" class="btn btn-secondary btn-sm">{{ ui_t('general.reset', 'Reset') }}</button>
                            <button type="button" id="exportButton" class="btn btn-success btn-sm">{{ ui_t('general.export_data', 'Export Data') }}</button>

                        </div>


                    </div>
                </form>
            </div>
            <div class="card-body text-left">
                <table id="subthemesTable" class="table table-striped table-bordered" data-kh-datatable="client">
                    <thead>
                        <tr>
                            <th style="width:3px;">#</th>
                            <th>{{ __('admin_nav.sub_thematic_area') }}</th>
                            <th>{{ ui_t('general.icon', 'Icon') }}</th>
                            <th>{{ __('admin_nav.thematic_area') }}</th>
                            <th>{{ ui_t('general.actions', 'Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subthemes as $row)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $row->description }}</td>
                                <td>{{ $row->icon }}</td>
                                <td>{{ $row->theme->description ?? '' }}</td>
                                <td>
                                    <a href="#edit-subtheme-modal" data-toggle="modal" data-id="{{ $row->id }}"
                                        data-description="{{ e((string) $row->description) }}" data-icon="{{ e((string) $row->icon) }}"
                                        data-detailed_description="{{ e((string) ($row->detailed_description ?? '')) }}"
                                        data-thematic_area_id="{{ $row->thematic_area_id }}"
                                        class="btn btn-sm btn-primary ml-1">{{ ui_t('general.edit', 'Edit') }}</a>
                                    @can('delete_publication_metadata')
                                    <a class="btn btn-sm btn-danger ml-1" href="javascript:void(0);"
                                        onclick='openDeleteModal({{ (int) $row->id }}, @json((string) $row->description))'>{{ ui_t('general.delete', 'Delete') }}</a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

        </div>

        @include('admin.subthemes.partials.create-modal', [
            'themes' => $themes,
            'selectedThemeId' => $selectedThemeId ?? 0,
        ])
        @include('admin.subthemes.partials.edit-modal', [
            'themes' => $themes,
        ])
        @include('admin.subthemes.partials.delete-modal')
@endsection

@section('scripts')
    @include('admin.partials.metadata_datatable')
    <script>
        $(function () {
            // Theme filter is server-side (narrows the dataset); search/sort are client-side.
            window.khInitMetadataTable('#subthemesTable', {
                order: [[1, 'asc']]
            });

            $('#filterButton').off('click.khMetaDt').on('click.khMetaDt', function (e) {
                // Allow full form submit when a thematic area is selected so the list reloads filtered.
                var themeId = $('#theme_id').val();
                if (themeId) {
                    return true;
                }
                e.preventDefault();
                var table = $('#subthemesTable').DataTable();
                table.search(String($('#filterTitle').val() || '')).draw();
            });

            $('#reset').off('click.khMetaDt').on('click.khMetaDt', function (e) {
                e.preventDefault();
                window.location.href = @json(url('admin/subthemes'));
            });
        });
    </script>
@endsection
