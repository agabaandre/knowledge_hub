@extends('admin.layouts.main')

@section('styles')
 @include('common.table')
@endsection

@section('content')
<div class="row">
	<div class="card col-lg-12">
		
		<div class="card-header">
		
				  <div class="row">

				  <div class="col-log-8">
				    <h3 class="card-title float-left">{{ $title ?? 'Questions Answers' }}</h3>
					<br>
					<h4 class="card-title float-left text-primary mt-3">
						{{ $question->question_text }}
					</h4>
				  </div>
				   
				    <div class="col-md-6 text-right">
					 <a href="#create-modal" data-toggle="modal" class="btn btn-outline-success btn-small float-right"><i class="fa fa-plus"></i> Add Answer</a>
					</div>
					
				</div>

		</div>
		<div class="card-body text-left">
			<!-- Datatable -->
			<table id="publicationTable" class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Answer</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>

					@php 
                    $i = 1;
                    @endphp

					@foreach($answers as $row)
						<tr>
							<td>{{ $row->answer_text }}</td>
							<td>
							    <a class="btn btn-sm btn-primary mr-2" href="javascript:void(0);" onclick="openEditModal('{{ $row->id }}')" > Edit</a>
								<a class="btn btn-sm btn-danger ml-1" href="javascript:void(0);" onclick="openDeleteModal('{{ $row->id }}')" class="text-danger"> Delete</a>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>

            <div class="py-2"> {{$answers->links() }}</div>

		</div>

	</div>

    @include('partials.general.summernote')
    @include('admin.quiz.partials.create-answer-modal')
	@include('admin.quiz.partials.delete-modal')


    @endsection

	@section('scripts')

<script>

$(document).ready(function() {

   
$('input[name="answer_type"]').on('change', function() {

if ($(this).val() == 'correct') {
	$('.explanation').show();
} else {
	$('.explanation').hide();
}
});



});

	var toDeleteRow = '';

	function deleteRow () {
		let url = `{{ url('admin/quiz/answer/delete')}}?id=${toDeleteRow}`;

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

		var rows = JSON.parse(@json($answers->toJson()));

		console.log(rows);

		var target_row = rows.data.find(item=>item.id === parseInt(rowId));

		$('#answer').val(target_row.answer_text);
		$('#id').val(target_row.id);
		
		if(parseInt(target_row.is_correct) === 0){
			$('#wrong_answer').attr('checked',true);
			$('.explanation').hide();
		}
		else{
			$('#right_answer').attr('checked',true);
			$('.explanation').show();
			$('#explanation').text(target_row.answer_explanation);

		}

		$('#title').html("Update Answer");
	   
		$('#create-modal').modal('show');
	}

</script>

@endsection