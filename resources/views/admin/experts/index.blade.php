@extends(admin_layout())

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
    <h1 class="page-title">Experts Management</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Experts</li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Filters Card -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="card-title mb-0">Filter Experts</h3>
                    <small class="text-muted">Search and filter experts</small>
                </div>
                <div>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#create-modal">
                        <i class="fa fa-plus"></i> Add Expert
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ url('admin/experts') }}" class="mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label>Country</label>
                                <select class="form-control" id="country" name="country">
                                    <option value="">All Countries</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" {{ (@$search->country == $country->id) ? 'selected' : '' }}>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label>Expert Type</label>
                                <select class="form-control" id="expert_type" name="expert_type_id">
                                    <option value="">All Types</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type->id }}" {{ (@$search->expert_type_id == $type->id) ? 'selected' : '' }}>{{ $type->type_name ?? $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label>Search</label>
                                <input type="text" name="term" id="filterTitle" class="form-control" placeholder="Search by name, email, or job title..." value="{{ @$search->term ?? ''}}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label>&nbsp;</label>
                                <div class="d-flex">
                                    <button type="submit" id="filterButton" class="btn btn-primary btn-sm mr-2">Filter</button>
                                    <a href="{{ url('admin/experts') }}" class="btn btn-secondary btn-sm">Clear</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Experts Table Card -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Experts</h3>
                    @if(count($experts) > 0)
                        <a href="?export=1" class="btn btn-success btn-sm"><i class="fa fa-file-excel"></i> Export to Excel</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                @if(session('message'))
                    <div class="alert alert-{{ session('status') == 'success' ? 'success' : 'danger' }}">
                        {{ session('message') }}
                    </div>
                @endif

                @if(count($experts) > 0)
                    <div class="table-responsive">
                        <table id="expertsTable" class="table table-striped table-bordered table-hover" style="border-radius: 0;">
                            <thead>
                                <tr>
                                    <th width="60px">#</th>
                                    <th>Name</th>
                                    <th>Job Title</th>
                                    <th>ISCO Classification</th>
                                    <th>Expert Type</th>
                                    <th>Country</th>
                                    <th>Email</th>
                                    <th width="120px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($experts as $idx => $expert)
                                    <tr>
                                        <td><span class="text-muted">{{ $experts->firstItem() + $idx }}</span></td>
                                        <td><strong>{{ $expert->first_name }} {{ $expert->last_name }}</strong></td>
                                        <td>{{ $expert->jobTitle->name ?? $expert->job_title ?? '-' }}</td>
                                        <td>{{ $expert->iscoClassification->name ?? '-' }}</td>
                                        <td>{{ $expert->type->type_name ?? '-' }}</td>
                                        <td>{{ $expert->country->name ?? '-' }}</td>
                                        <td>{{ $expert->email ?? '-' }}</td>
                                        <td>
                                            <a href="#create-modal{{ $expert->id }}" data-toggle="modal" class="btn btn-sm btn-outline-primary mr-1" title="Edit">
                                                <i class="fa fa-edit mr-1"></i> Edit
                                            </a>
                                            <a href="javascript:void(0);" onclick="openDeleteModal('{{ $expert->id }}')" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @include('admin.experts.partials.create-modal',['record'=>$expert, 'expert_types' => $types, 'types' => $types, 'isco_classifications' => $isco_classifications, 'jobs' => $jobs])
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $experts->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <p class="text-muted">No experts found</p>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#create-modal">
                            <i class="fa fa-plus"></i> Add Expert
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@include('admin.experts.partials.create-modal', ['isco_classifications' => $isco_classifications, 'jobs' => $jobs])
@include('admin.experts.partials.delete-modal')

@endsection

@section('scripts')
@include('common.select2')

<script>
var toDeleteRow = '';

function deleteRow () {
    let url = `{{ url('admin/experts/delete')}}?id=${toDeleteRow}`;
    fetch(url)
    .then(res => res.text())
    .then(res => {
        console.log(res)
        $('#delete-modal').modal('hide');
        window.location.reload();
    })
}

function openDeleteModal (row = 0) {
    toDeleteRow = row;
    $('#delete-modal').modal('show');
}

function loadJobTitles(selectElement) {
    var iscoSelect = $(selectElement);
    var selectedOption = iscoSelect.find('option:selected');
    var iscoId = selectedOption.data('isco-id');
    var classificationId = iscoSelect.val();
    var jobTitleSelect = iscoSelect.closest('.modal').find('#job_title_id');
    
    // Clear existing options
    jobTitleSelect.empty();
    jobTitleSelect.append('<option value="">Select Job Title</option>');
    
    if (!iscoId && !classificationId) {
        // Reinitialize Select2
        if (jobTitleSelect.hasClass('js-example-basic-single')) {
            jobTitleSelect.select2();
        }
        return;
    }
    
    // Fetch job titles for the selected ISCO classification
    $.ajax({
        url: '{{ url("admin/experts/job-titles-by-isco") }}',
        method: 'GET',
        data: { 
            isco_id: iscoId,
            classification_id: classificationId
        },
        success: function(response) {
            if (response && response.length > 0) {
                $.each(response, function(index, job) {
                    jobTitleSelect.append('<option value="' + job.id + '">' + job.name + '</option>');
                });
            }
            // Reinitialize Select2
            if (jobTitleSelect.hasClass('js-example-basic-single')) {
                jobTitleSelect.select2();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading job titles:', error);
        }
    });
}

// Initialize on page load
$(document).ready(function() {
    // When modal is opened, check if ISCO is already selected and load job titles
    $('.modal').on('shown.bs.modal', function() {
        var iscoSelect = $(this).find('#isco_classification_id');
        if (iscoSelect.val()) {
            loadJobTitles(iscoSelect[0]);
        }
    });
});
</script>
@endsection