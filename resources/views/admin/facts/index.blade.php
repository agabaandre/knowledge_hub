@extends('admin.layouts.main')

@section('styles')
    @include('common.table')
    <style>
        .card { border: 1px solid #e2e8f0; border-radius: 0; margin-bottom: 1.5rem; }
        .card-header { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.5rem; }
        .card-body { padding: 1.5rem; }
    </style>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Facts Management</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Facts</li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Filters Card -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="card-title mb-0">Filter Facts</h3>
                    <small class="text-muted">Search and filter facts</small>
                </div>
                <div>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#create-modal">
                        <i class="fa fa-plus"></i> Add Fact
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ url('admin/facts') }}" class="mb-3">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group mb-0">
                                <input type="text" name="term" id="filterTitle" class="form-control" placeholder="Search by title or summary..." value="{{ @$search->term ?? ''}}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex">
                                <button type="submit" id="filterButton" class="btn btn-primary btn-sm mr-2">Filter</button>
                                <a href="{{ url('admin/facts') }}" class="btn btn-secondary btn-sm">Clear</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Facts Table Card -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Facts</h3>
                </div>
            </div>
            <div class="card-body">
                @if(session('message'))
                    <div class="alert alert-{{ session('status') == 'success' ? 'success' : 'danger' }}">
                        {{ session('message') }}
                    </div>
                @endif

                @if(count($facts) > 0)
                    <div class="table-responsive">
                        <table id="factsTable" class="table table-striped table-bordered table-hover" style="border-radius: 0;">
                            <thead>
                                <tr>
                                    <th width="60px">#</th>
                                    <th>Title</th>
                                    <th>Summary</th>
                                    <th width="180px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($facts as $idx => $row)
                                    <tr>
                                        <td><span class="text-muted">{{ $facts->firstItem() + $idx }}</span></td>
                                        <td><strong>{{ $row->fact_title }}</strong></td>
                                        <td>{!! Str::limit(strip_tags($row->fact_summary), 100) !!}</td>
                                        <td>
                                            <a href="#edit-fact-modal" class="btn btn-sm btn-outline-primary mr-1" data-toggle="modal"
                                                data-id="{{ $row->id }}" data-title="{{ $row->fact_title }}"
                                                data-summary="{{ $row->fact_summary }}"
                                                data-description="{!! htmlspecialchars($row->fact_description, ENT_QUOTES) !!}" title="Edit">
                                                <i class="fa fa-edit mr-1"></i> Edit
                                            </a>
                                            <a href="javascript:void(0);" onclick="openDeleteModal('{{ $row->id }}')" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $facts->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <p class="text-muted">No facts found</p>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#create-modal">
                            <i class="fa fa-plus"></i> Add Fact
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Include modals -->
@include('admin.facts.partials.delete-modal')
@include('admin.facts.partials.create-modal')
@include('admin.facts.partials.edit-modal')
@endsection
