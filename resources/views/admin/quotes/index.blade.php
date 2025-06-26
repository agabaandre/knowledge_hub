@extends('admin.layouts.main')

@section('styles')
 @include('common.table')
@endsection

@section('content')
<div class="row">
	<div class="card col-lg-12">
		<div class="card-header text-left">
			<h3 class="card-title float-left">{{ $title ?? 'Quotes' }}</h3>
			 <hr>
		</div>
		<!-- Card Header With Form Filters -->
		<div class="card-header">
			<form  class="container-fluid">
				  <div class="row">
				   
				    <div class="col-md-12 text-right">
					 <a href="#create-modal" data-toggle="modal" class="btn btn-outline-success float-right"><i class="fa fa-plus"></i> Add Quote</a>
					</div>

					<div class="col-md-12">
						<div class="form-group">
							<label for="title">Search</label>
							<input type="text" name="term" id="filterTitle" class="form-control"
								placeholder="Filter by question"
                                value="{{ @$search->term ?? ''}}"
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
			<!-- Datatable -->
			<table id="publicationTable" class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Quote</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>

					@php 
                    $i = 1;
                    @endphp

					@foreach($quotes as $row)
						<tr>
							<td>{{ $row->quote }}</td>
							<td>
							<a class="btn btn-sm text-primary mr-2" href="javascript:void(0);" onclick="openEditModal('{{ $row->id }}')" > Edit</a>
								<a class="btn btn-sm btn-danger ml-1" href="javascript:void(0);" onclick="openDeleteModal('{{ $row->id }}')" class="text-danger"> Delete</a>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>

            <div class="py-2"> {{$quotes->links() }}</div>

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

		function openEditModal (rowId = 0) {

			var rows = JSON.parse(@json($quotes->toJson()));

			console.log(rows);

			var target_row = rows.data.find(item=>item.id === parseInt(rowId));

			$('#quote').text(target_row.quote);
			$('#id').val(target_row.id);
			$('#title').html("Update Quote");
           
            $('#create-modal').modal('show');
        }

    </script>

	@endsection