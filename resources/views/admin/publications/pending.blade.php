@extends('admin.layouts.main')

@section('styles')
    @include('common.table')
    <style>
        .filter-card { background:#fff; border:1px solid #e2e8f0; border-radius:10px; }
        .filter-card .card-header { background:#f8fafc; border-bottom:1px solid #e2e8f0; }
        .btn-soft { border:1px solid #cbd5e1; background:#ffffff; }
        .btn-soft:hover { background:#f8fafc; }
        .form-label-sm { font-size:.875rem; font-weight:600; color:#334155; }
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

        <div class="card col-lg-12">
            <div class="filter-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <strong>Filter Pending Resources</strong>
                        <small class="text-muted d-block">Quick filters with collapsible advanced filters</small>
                    </div>
                    <div>
                        <button class="btn btn-soft btn-sm" type="button" data-toggle="collapse" data-target="#advPending" aria-expanded="false" aria-controls="advPending">
                            <i class="fa fa-sliders mr-1"></i> Advanced Filters
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form class="container-fluid">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label-sm" for="title">Keyword</label>
                                    <input type="text" name="term" id="filterTitle" class="form-control" placeholder="Search by title or keyword" value="{{ @$search->term ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label-sm">Source / Author</label>
                                    @include('partials.authors.dropdown',[ 'field'=>'author','selected'=>@$search->author])
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label-sm">File Type</label>
                                    @include('partials.publications.filetype_dropdown',[ 'field'=>'file_type','selected'=>@$search->file_type])
                                </div>
                            </div>
                        </div>
                        <div id="advPending" class="collapse mt-2">
                            <div class="row">@include('partials.search.search_fields')</div>
                        </div>
                        <div class="d-flex justify-content-end mt-2" style="gap:8px;">
                            <button type="submit" id="filterButton" class="btn btn-dark btn-sm"><i class="fa fa-filter mr-1"></i> Apply</button>
                            <button type="button" id="reset" class="btn btn-soft btn-sm"><i class="fa fa-rotate-left mr-1"></i> Reset</button>
                            <button type="button" id="exportButton" class="btn btn-success btn-sm"><i class="fa fa-download mr-1"></i> Export</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body text-left">
                <!-- Datatable -->
                <div class="table-responsive mt-3">
                <table id="publicationTable" class="table table-striped table-hover table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:60px;">#</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th width="10%">Author</th>
                            <th width="10%">Member State</th>
                            <th>Status</th>
                            <th width="18%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        @php
                            $i = 1;
                        @endphp

                        @foreach ($publications as $idx => $publication)
                            <tr>
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
                                    {{ $publication->country->name ?? '' }}
                                </td>
                                <td>
                                    {{ get_publication_state($publication->is_approved, $publication->is_rejected) }}
                                </td>
                                <td>
                                    <a href="{{ url('admin/publications/details') }}?id={{ $publication->id }}" class="btn btn-sm btn-outline-primary mr-1">
                                        <i class="fa fa-eye mr-1"></i> Details
                                    </a>
                                    @if ($publication->user_id == current_user()->id || is_admin())
                                        <a href="{{ url('admin/publications/edit') }}?id={{ $publication->id }}" class="btn btn-sm btn-outline-dark mr-1">
                                            <i class="fa fa-edit mr-1"></i> Edit
                                        </a>
                                    @endif
                                    @can('delete_publications')
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="openDeleteModal('{{ $publication->id }}')">
                                            <i class="fa fa-trash mr-1"></i> Delete
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>

                <div class="py-2"> {{ $publications->links() }}</div>

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
@endsection
