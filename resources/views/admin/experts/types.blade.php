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
    <h1 class="page-title">Expert Types Management</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Expert Types</li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Filters Card -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="card-title mb-0">Filter Expert Types</h3>
                    <small class="text-muted">Search and filter expert types</small>
                </div>
                <div>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#create-modal">
                        <i class="fa fa-plus"></i> Add Expert Type
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ url('admin/experts/types') }}" class="mb-3">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group mb-0">
                                <input type="text" name="term" id="filterTitle" class="form-control" placeholder="Search by type name..." value="{{ @$search->term ?? ''}}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex">
                                <button type="submit" id="filterButton" class="btn btn-primary btn-sm mr-2">Filter</button>
                                <a href="{{ url('admin/experts/types') }}" class="btn btn-secondary btn-sm">Clear</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Expert Types Table Card -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Expert Types</h3>
                </div>
            </div>
            <div class="card-body">
                @if(session('message'))
                    <div class="alert alert-{{ session('status') == 'success' ? 'success' : 'danger' }}">
                        {{ session('message') }}
                    </div>
                @endif

                @if(count($types) > 0)
                    <div class="table-responsive">
                        <table id="expertTypesTable" class="table table-striped table-bordered table-hover" style="border-radius: 0;">
                            <thead>
                                <tr>
                                    <th width="60px">#</th>
                                    <th>Type Name</th>
                                    <th>Description</th>
                                    <th width="150px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($types as $idx => $row)
                                    <tr>
                                        <td><span class="text-muted">{{ $types->firstItem() + $idx }}</span></td>
                                        <td><strong>{{ $row->type_name }}</strong></td>
                                        <td>{{ Str::limit($row->type_desc ?? '-', 100) }}</td>
                                        <td>
                                            <a href="javascript:void(0);" onclick="openEditModal({{ $row->id }})" class="btn btn-sm btn-outline-primary mr-1" title="Edit">
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
                        {{ $types->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <p class="text-muted">No expert types found</p>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#create-modal">
                            <i class="fa fa-plus"></i> Add Expert Type
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@include('admin.experts.partials.add-type')
@include('admin.experts.partials.delete-type')

@endsection

@section('scripts')
<script>
	var toDeleteRow = '';

	function deleteRow () {
		let url = `{{ url('admin/experts/types/delete')}}?id=${toDeleteRow}`;

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

	function openEditModal (rowId) {
		// Convert to integer to ensure proper type
		rowId = parseInt(rowId);
		
		if (!rowId || isNaN(rowId)) {
			alert('Invalid Expert Type ID');
			return;
		}

		// Fetch expert type data from the current page data
		var rows = JSON.parse(@json($types->toJson()));
		
		var target_row = rows.data.find(item => item.id === rowId);
		
		if (!target_row) {
			alert('Expert type not found');
			return;
		}

		// Set form values
		$('#type').val(target_row.type_name || '');
		$('#description').val(target_row.type_desc || '');
		$('#id').val(target_row.id);
		$('#title').html("Update Expert Type");
	   
		$('#create-modal').modal('show');
	}

	// Reset modal on close
	$('#create-modal').on('hidden.bs.modal', function () {
		$('#id').val('');
		$('#type').val('');
		$('#description').val('');
		$('#title').html("Create Expert Type");
	});
</script>
@endsection