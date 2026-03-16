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
    <h1 class="page-title">Quotes Management</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Quotes</li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Filters Card -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="card-title mb-0">Filter Quotes</h3>
                    <small class="text-muted">Search and filter quotes</small>
                </div>
                <div>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#create-modal">
                        <i class="fa fa-plus"></i> Add Quote
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ url('admin/quotes') }}" class="mb-3">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group mb-0">
                                <input type="text" name="term" id="filterTitle" class="form-control" placeholder="Search by quote..." value="{{ @$search->term ?? ''}}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex">
                                <button type="submit" id="filterButton" class="btn btn-primary btn-sm mr-2">Filter</button>
                                <a href="{{ url('admin/quotes') }}" class="btn btn-secondary btn-sm">Clear</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Quotes Table Card -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Quotes</h3>
                </div>
            </div>
            <div class="card-body">
                @if(session('message'))
                    <div class="alert alert-{{ session('status') == 'success' ? 'success' : 'danger' }}">
                        {{ session('message') }}
                    </div>
                @endif

                @if(count($quotes) > 0)
                    <div class="table-responsive">
                        <table id="quotesTable" class="table table-striped table-bordered table-hover" style="border-radius: 0;">
                            <thead>
                                <tr>
                                    <th width="60px">#</th>
                                    <th>Quote</th>
                                    <th width="80px">Image</th>
                                    <th width="120px">Link</th>
                                    <th width="150px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quotes as $idx => $row)
                                    <tr>
                                        <td><span class="text-muted">{{ $quotes->firstItem() + $idx }}</span></td>
                                        <td>{{ Str::limit($row->quote, 80) }}</td>
                                        <td>
                                            @if($row->image ?? null)
                                                <img src="{{ $row->image_url }}" alt="" class="img-thumbnail" style="max-height: 40px; max-width: 60px; object-fit: cover;">
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($row->link_url ?? null)
                                                <a href="{{ $row->link_url }}" target="_blank" rel="noopener" class="small">View</a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
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
                        {{ $quotes->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <p class="text-muted">No quotes found</p>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#create-modal">
                            <i class="fa fa-plus"></i> Add Quote
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

	@include('admin.quotes.partials.create-modal')
	<!-- Include delete-modal.php -->
	@include('admin.quotes.partials.delete-modal')

    @endsection

	@section('scripts')

	<script>
        
        var toDeleteRow = '';

        function deleteRow () {
            let url = `{{ url('admin/quotes/delete')}}?id=${toDeleteRow}`;

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
                alert('Invalid Quote ID');
                return;
            }

            // Fetch quote data from the current page data
            var rows = JSON.parse(@json($quotes->toJson()));
            
            var target_row = rows.data.find(item => item.id === rowId);
            
            if (!target_row) {
                alert('Quote not found');
                return;
            }

            // Set form values
            $('#quote').val(target_row.quote);
            $('#id').val(target_row.id);
            $('#link_url').val(target_row.link_url || '');
            $('#quote_image').val('');
            $('#quote-image-preview').html(target_row.image ? '<img src="' + (target_row.image_url || ('{{ url("/") }}/storage/uploads/quotes/' + target_row.image)) + '" alt="" class="img-thumbnail" style="max-height:60px;">' : '');
            $('#title').text('Update Quote');
            $('#create-modal').modal('show');
        }

    </script>

	@endsection