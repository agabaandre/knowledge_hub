@extends(admin_layout())

@section('styles')
    @include('common.table')
    <link href="{{ asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="row">
        <div class="card col-lg-12">
            <div class="card-header text-left">
                <h3 class="card-title float-left">{{ $title ?? 'Tags' }}</h3>
                <hr>
            </div>

            @include('admin.tags.partials.ai-tools')

            <!-- Card Header With Form Filters -->
            <div class="card-header">
                <form class="container-fluid" method="get" action="{{ url('admin/tags') }}" id="tagsFilterForm">
                    <div class="row">

                        <div class="col-md-12 text-right">
                            <a href="#create-modal" data-toggle="modal" class="btn btn-outline-success float-right"><i class="fa fa-plus"></i> Add Tag</a>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="filterTitle">Search</label>
                                <input type="text" name="term" id="filterTitle" class="form-control"
                                       placeholder="Search tag name or description"
                                       value="{{ @$search->term ?? '' }}"
                                >
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
                <table id="tagsTable" class="table table-striped table-bordered" data-kh-datatable="client">
                    <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Tag</th>
                        <th>Health Topic</th>
                        <th>Health Emergency</th>
                        <th>Description</th>
                        <th style="width:100px;">Desc. status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($all_tags as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row->tag_text }}</td>
                            <td>{{ ($row->is_health_topic ?? 1) ? 'Yes':'No' }}</td>
                            <td>{{ ($row->is_health_emergency ?? 0) ? 'Yes':'No' }}</td>
                            @php
                                $overviewPlain = trim(preg_replace('/\s+/u', ' ', strip_tags($row->overview ?? '')));
                                $overviewLen = mb_strlen($overviewPlain);
                                $needsOverview = $overviewLen < 120;
                            @endphp
                            <td style="max-width:420px;" data-order="{{ $overviewLen }}">
                                @if($overviewLen > 0)
                                    <div class="small text-muted mb-1">{{ number_format($overviewLen) }} chars (plain text)</div>
                                    <div class="tag-desc-preview">{!! Str::limit(strip_tags($row->overview ?? ''), 160) !!}</div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td data-order="{{ $needsOverview ? 0 : ($overviewLen < 400 ? 1 : 2) }}">
                                @if($needsOverview)
                                    <span class="badge badge-warning">Missing</span>
                                @elseif($overviewLen < 400)
                                    <span class="badge badge-info">Short</span>
                                @else
                                    <span class="badge badge-success">OK</span>
                                @endif
                            </td>
                            <td style="white-space:nowrap;">
                                @if($needsOverview)
                                    <button type="button"
                                            class="btn btn-sm btn-success js-describe-tag"
                                            data-tag-id="{{ $row->id }}"
                                            data-tag-text="{{ $row->tag_text }}">
                                        WHO describe
                                    </button>
                                @else
                                    <button type="button"
                                            class="btn btn-sm btn-outline-success js-describe-tag"
                                            data-tag-id="{{ $row->id }}"
                                            data-tag-text="{{ $row->tag_text }}"
                                            title="Regenerate from WHO factsheet (apply only if longer)">
                                        Enhance
                                    </button>
                                @endif
                                <a href="#edit-tag-modal" data-toggle="modal" data-id="{{ $row->id }}" data-tag="{{ $row->tag_text }}" 
                                    data-is_health_topic="{{$row->is_health_topic ?? 1 }}" data-is_health_emergency="{{$row->is_health_emergency ?? 0 }}"
                                    data-overview="{{ e($row->overview ?? '') }}"
                                    class="btn btn-sm btn-primary ml-1">Edit</a>
                                @canany(['delete_publication_metadata', 'delete_meta_data'])
                                <a href="javascript:void(0);" class="btn btn-sm btn-danger ml-1"
                                   onclick="openDeleteModal({{ (int) $row->id }}, @json((string) $row->tag_text))">Delete</a>
                                @endcanany
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>

        </div>


        @include('admin.tags.partials.create-modal')
        @include('admin.tags.partials.edit-modal')
        @include('admin.tags.partials.delete-modal')
        @include('partials.general.summernote')


    </div>
@endsection

@section('scripts')
    @include('admin.partials.metadata_datatable')
    <script>
        $(function () {
            window.khInitMetadataTable('#tagsTable', {
                order: [[1, 'asc']],
                pageLength: 50
            });
        });
    </script>
@endsection
