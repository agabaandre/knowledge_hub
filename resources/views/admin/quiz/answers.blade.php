@extends('admin.layouts.main')

@section('styles')
 @include('common.table')
@endsection

@section('content')
<div class="row">
	<div class="card col-lg-12">
		<div class="card-header text-left">
			<h3 class="card-title float-left">{{ $title ?? 'Questions Answers' }}</h3>
			 <hr>
             <p class="card-title float-left">
                {{ $question->question_text }}
             </p>
		</div>
		<!-- Card Header With Form Filters -->
		<div class="card-header">
		
				  <div class="row">
				   
				    <div class="col-md-12 text-right">
					 <a href="#create-modal" data-toggle="modal" class="btn btn-outline-success float-right"><i class="fa fa-plus"></i> Add Answer</a>
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
							    <a class="btn btn-sm btn-info ml-1" href=""> Edit</a>
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

    <script  type="text/javascript">
      
    $(document).ready(function() {

    $('input[name="answer_type"]').on('change', function() {

        if ($(this).val() == 'correct') {
            $('.explanation').show();
        } else {
            $('.explanation').hide();
        }
    });

     });
    </script>

    @endsection