@extends(admin_layout())

@section('styles')
    @include('common.table')
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
                <form class="container-fluid">
                    <div class="row">

                        <div class="col-md-12 text-right">
                            <a href="#create-modal" data-toggle="modal" class="btn btn-outline-success float-right"><i class="fa fa-plus"></i> Add Tag</a>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="title">Search</label>
                                <input type="text" name="term" id="filterTitle" class="form-control"
                                       placeholder="Filter by name"
                                       value="{{ @$search->term ?? '' }}"
                                >
                            </div>
                        </div>


                    </div>

                    <div class="row">
                        <div class="col-md-12 text-right">
                            <!-- Export Button -->
                            <button type="submit" id="filterButton" class="btn btn-primary btn-sm">Filter Data</button>
                            <button type="button" id="reset" class="btn btn-secondary btn-sm">Reset</button>
                            <button type="button" id="exportButton" class="btn btn-success btn-sm">Export Data</button>

                        </div>


                    </div>
                </form>
            </div>
            <div class="card-body text-left">
                @include('layouts.partials.alerts')
                <!-- Datatable -->
                <table id="publicationTable" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Tag</th>
                        <th>Health Topic</th>
                        <th>Health Emergency</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>

                    @php
                        $i = 1;
                    @endphp

                    @foreach($all_tags as $row)
                        <tr>
                            <td>{{ $all_tags->firstItem() + $loop->index }}</td>
                            <td>{{ $row->tag_text }}</td>
                            <td>{{ ($row->is_health_topic ?? 1) ? 'Yes':'No' }}</td>
                            <td>{{ ($row->is_health_emergency ?? 0) ? 'Yes':'No' }}</td>
                            <td style="max-width:420px;">
                                {!! Str::limit(strip_tags($row->overview ?? ''), 140) ?: '<span class="text-muted">—</span>' !!}
                            </td>
                            <td>
                                <a href="#edit-tag-modal" data-toggle="modal" data-id="{{ $row->id }}" data-tag="{{ $row->tag_text }}" 
                                    data-is_health_topic="{{$row->is_health_topic ?? 1 }}" data-is_health_emergency="{{$row->is_health_emergency ?? 0 }}"
                                    data-overview="{{$row->overview }}"
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

                <div class="py-2"> {{ $all_tags->links() }}</div>

            </div>

        </div>


        @include('admin.tags.partials.create-modal')
        <!-- Include edit-modal.php -->
        @include('admin.tags.partials.edit-modal')
        <!-- Include delete-modal.php -->
        @include('admin.tags.partials.delete-modal')

        
        @include('partials.general.summernote')


    </div>
@endsection
