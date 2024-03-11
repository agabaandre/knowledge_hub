@extends('admin.layouts.main')

@section('styles')
 @include('common.table')
@endsection

@section('content')
<div class="row">
	<div class="card col-lg-12">
		<div class="card-header text-left">
			<h3 class="card-title float-left">{{ $title ?? 'Quiz Questions' }}</h3>
			 <hr>
		</div>
		<!-- Card Header With Form Filters -->
		<div class="card-header">
			<form  class="container-fluid">
				  <div class="row">
				   
				    <div class="col-md-12 text-right">
					 <a href="#create-modal" data-toggle="modal" class="btn btn-outline-success float-right"><i class="fa fa-plus"></i> Add Question</a>
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
						<th>Question</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>

					@php 
                    $i = 1;
                    @endphp

					@foreach($questions as $row)
						<tr>
							<td>{{ $row->question_text }}</td>
							<td>
							    <a class="btn btn-sm btn-primary ml-1" href="{{ url('admin/quiz/answers') }}?qn={{$row->id}}"  class="text-info"> Answers</a>
								<a class="btn btn-sm btn-danger mr-2" href="javascript:void(0);" onclick="openEditModal('{{ $row->id }}')" > Edit</a>
								<a class="btn btn-sm btn-danger ml-1" href="javascript:void(0);" onclick="openDeleteModal('{{ $row->id }}')" class="text-danger"> Delete</a>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>

            <div class="py-2"> {{$questions->links() }}</div>

		</div>

	</div>

	@include('admin.quiz.partials.create-modal')
	<!-- Include delete-modal.php -->
	@include('admin.quiz.partials.delete-modal')

    @endsection

	@section('scripts')

<script>
	
	var toDeleteRow = '';

	function deleteRow () {
		let url = `{{ url('admin/quiz/delete')}}?id=${toDeleteRow}`;

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

		var rows = JSON.parse(@json($questions->toJson()));

		console.log(rows);

		var target_row = rows.data.find(item=>item.id === parseInt(rowId));

		$('#question').text(target_row.question_text);
		$('#id').val(target_row.id);
		$('#title').html("Update Question");
	   
		$('#create-modal').modal('show');
	}

</script>

@endsection