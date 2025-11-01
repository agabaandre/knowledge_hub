@extends('admin.layouts.main')

@section('styles')
    @include('common.table')
    <style>
        .filter-chip { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border:1px solid #e2e8f0; border-radius:999px; background:#fff; }
        .btn-soft { border:1px solid #cbd5e1; background:#ffffff; }
        .btn-soft:hover { background:#f8fafc; }
        .form-label-sm { font-size:.875rem; font-weight:600; color:#334155; }
        .form-group { margin-bottom: 1.5rem; }
        #advancedFilters .form-group { margin-bottom: 1rem; }
        #advancedFilters .row { margin-left: -15px; margin-right: -15px; }
        .card { border: 1px solid #e2e8f0; border-radius: 0; margin-bottom: 1.5rem; }
        .card-header { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.5rem; }
        .card-body { padding: 1.5rem; }
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
        <!-- Filters Card -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="card-title mb-0">Filter Pending Resources</h3>
                        <small class="text-muted">Use quick filters or expand advanced filters for precise results</small>
                    </div>
                    <div>
                        <button class="btn btn-soft btn-sm" type="button" data-toggle="collapse" data-target="#advancedFilters" aria-expanded="false" aria-controls="advancedFilters">
                            <i class="fa fa-sliders mr-1"></i> Advanced Filters
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ url('admin/publications/pending') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label class="form-label-sm" for="title">Keyword</label>
                                    <input type="text" name="term" id="filterTitle" class="form-control" placeholder="Search by title or keyword" value="{{ @$search->term ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label class="form-label-sm" for="author">Source / Author</label>
                                    @include('partials.authors.dropdown', [ 'field' => 'author', 'selected' => @$search->author ])
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label class="form-label-sm" for="file_type">File Type</label>
                                    @include('partials.publications.filetype_dropdown', [ 'field' => 'file_type', 'selected' => @$search->file_type ])
                                </div>
                            </div>
                        </div>

                        <div id="advancedFilters" class="collapse mt-3">
                            <hr class="my-3" style="border-color: #e2e8f0;">
                            @include('partials.search.search_fields')
                        </div>

                        <div class="d-flex justify-content-end mt-2" style="gap:8px;">
                            <button type="submit" id="filterButton" class="btn btn-primary btn-sm"><i class="fa fa-filter mr-1"></i> Filter</button>
                            <a href="{{ url('admin/publications/pending') }}" class="btn btn-secondary btn-sm"><i class="fa fa-rotate-left mr-1"></i> Clear</a>
                            <button type="button" id="exportButton" class="btn btn-success btn-sm"><i class="fa fa-download mr-1"></i> Export</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Publications Table Card -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Pending Publications</h3>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <!-- Datatable -->
                    <div class="table-responsive">
                        <table id="publicationTable" class="table table-striped table-bordered table-hover" style="border-radius: 0;">
                            <thead>
                                <tr>
                                    <th style="width:60px;">#</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th width="10%">Author</th>
                                    <th width="10%">Affiliation</th>
                                    <th width="10%">Member State</th>
                                    <th>Status</th>
                                    <th width="10%">Date Created</th>
                                    <th width="18%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
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
                                            {{ $publication->author_affiliation ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $publication->country->name ?? '' }}
                                        </td>
                                        <td>
                                            {{ get_publication_state($publication->is_approved, $publication->is_rejected) }}
                                        </td>
                                        <td>
                                            @if($publication->date_created)
                                                {{ \Carbon\Carbon::parse($publication->date_created)->format('M d, Y') }}
                                            @elseif($publication->created_at)
                                                {{ \Carbon\Carbon::parse($publication->created_at)->format('M d, Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ url('admin/publications/details') }}?id={{ $publication->id }}" class="btn btn-sm btn-outline-primary mr-1">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            @if ($publication->user_id == current_user()->id || is_admin())
                                                <a href="{{ url('admin/publications/edit') }}?id={{ $publication->id }}" class="btn btn-sm btn-outline-dark mr-1">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            @endif
                                            @can('delete_publications')
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="openDeleteModal('{{ $publication->id }}')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $publications->links() }}
                    </div>
                </div>
            </div>
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
