@extends('admin.layouts.main')

@section('styles')
    @include('common.table')
    <style>
        .card { border: 1px solid #e2e8f0; border-radius: 0; margin-bottom: 1.5rem; }
        .card-header { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.5rem; }
        .card-body { padding: 1.5rem; }
        .form-group { margin-bottom: 1.5rem; }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">KPI Indicators</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">KPI Indicators</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <!-- Filters Card -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="card-title mb-0">Filter Indicators</h3>
                        <small class="text-muted">Search and filter indicators</small>
                    </div>
                    <div>
                        <a href="#create-modal" data-toggle="modal" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus"></i> Add Indicator
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ url('admin/kpi') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group mb-0">
                                    <input type="text" name="term" id="filterTitle" class="form-control" placeholder="Search by name or description" value="{{ @$search->term ?? ''}}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex">
                                    <button type="submit" id="filterButton" class="btn btn-primary btn-sm mr-2">Filter</button>
                                    <a href="{{ url('admin/kpi') }}" class="btn btn-secondary btn-sm">Clear</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Indicators Table Card -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Indicators</h3>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('alert-success'))
                        <div class="alert alert-success">{{ session('alert-success') }}</div>
                    @endif
                    @if(session('alert-danger'))
                        <div class="alert alert-danger">{{ session('alert-danger') }}</div>
                    @endif

                    <!-- Indicators Table -->
                    <div class="table-responsive">
                        <table id="indicatorsTable" class="table table-striped table-bordered table-hover" style="border-radius: 0;">
                            <thead>
                                <tr>
                                    <th width="60px">#</th>
                                    <th>Indicator</th>
                                    <th>Description</th>
                                    <th width="15%">Subject Area</th>
                                    <th width="10%">Frequency</th>
                                    <th width="150px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($indicators as $idx => $row)
                                <tr>
                                    <td><span class="text-muted">{{ $indicators->firstItem() + $idx }}</span></td>
                                    <td><strong>{{ $row->name ?? 'N/A' }}</strong></td>
                                    <td>{!! Str::limit(strip_tags($row->description ?? ''), 80) !!}</td>
                                    <td>{{ $row->subjectArea ? $row->subjectArea->name : 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $row->frequency ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" onclick="openEditModal('{{ $row->id }}')" class="btn btn-sm btn-outline-primary mr-1" title="Edit">
                                            <i class="fa fa-edit"></i>
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
                        {{ $indicators->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include delete-modal.php -->
    @include('admin.kpi.partials.delete-modal')

    @include('admin.kpi.partials.create-modal', [
    'subject_areas' => $subject_areas])
    
    @include('admin.kpi.partials.edit-modal', [
    'subject_areas' => $subject_areas])
    @endsection

    @section('scripts')
    @include('common.select2')
    <script>
        // Initialize Select2 for create modal
        $('#create-modal').on('shown.bs.modal', function () {
            $('#subject_area, #frequency').select2({
                dropdownParent: $('#create-modal')
            });
        });
        
        // Initialize Select2 for edit modal
        $('#edit-modal').on('shown.bs.modal', function () {
            $('#edit_subject_area, #edit_frequency').select2({
                dropdownParent: $('#edit-modal')
            });
        });
    </script>
    @endsection