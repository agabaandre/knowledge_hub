@extends('admin.layouts.main')

@section('styles')
 @include('common.table')
 <style>
    .card { border: 1px solid #e2e8f0; border-radius: 0; margin-bottom: 1.5rem; }
    .card-header { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.5rem; }
    .card-body { padding: 1.5rem; }
    .badge-correct { background-color: #22c55e; color: white; }
    .badge-incorrect { background-color: #ef4444; color: white; }
 </style>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Question Answers</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('admin/quiz') }}">Quiz Questions</a></li>
            <li class="breadcrumb-item active" aria-current="page">Answers</li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Question Info Card -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="card-title mb-0">Question</h3>
                </div>
                <div>
                    <a href="{{ url('admin/quiz') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left mr-1"></i> Back to Questions
                    </a>
                    <button type="button" class="btn btn-primary btn-sm ml-2" data-toggle="modal" data-target="#create-modal">
                        <i class="fa fa-plus"></i> Add Answer
                    </button>
                </div>
            </div>
            <div class="card-body">
                <h4 class="text-primary mb-0">{{ $question->question_text }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Answers Table Card -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Answers</h3>
                </div>
            </div>
            <div class="card-body">
                @if(session('message'))
                    <div class="alert alert-{{ session('status') == 'success' ? 'success' : 'danger' }}">
                        {{ session('message') }}
                    </div>
                @endif

                @if(count($answers) > 0)
                    <div class="table-responsive">
                        <table id="answersTable" class="table table-striped table-bordered table-hover" style="border-radius: 0;">
                            <thead>
                                <tr>
                                    <th width="60px">#</th>
                                    <th>Answer</th>
                                    <th width="120px">Status</th>
                                    <th>Explanation</th>
                                    <th width="150px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($answers as $idx => $row)
                                    <tr>
                                        <td><span class="text-muted">{{ $answers->firstItem() + $idx }}</span></td>
                                        <td><strong>{{ $row->answer_text }}</strong></td>
                                        <td>
                                            @if($row->is_correct)
                                                <span class="badge badge-correct">
                                                    <i class="fa fa-check-circle mr-1"></i> Correct
                                                </span>
                                            @else
                                                <span class="badge badge-incorrect">
                                                    <i class="fa fa-times-circle mr-1"></i> Incorrect
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($row->is_correct && $row->answer_explanation)
                                                <small class="text-muted">{{ Str::limit(strip_tags($row->answer_explanation), 80) }}</small>
                                            @else
                                                <span class="text-muted">-</span>
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
                        {{ $answers->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <p class="text-muted">No answers found for this question</p>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#create-modal">
                            <i class="fa fa-plus"></i> Add Answer
                        </button>
                    </div>
                @endif
            </div>
        </div>
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

function openEditModal (rowId) {
    // Convert to integer to ensure proper type
    rowId = parseInt(rowId);
    
    if (!rowId || isNaN(rowId)) {
        alert('Invalid Answer ID');
        return;
    }

    // Fetch answer data from the current page data
    var rows = JSON.parse(@json($answers->toJson()));
    
    var target_row = rows.data.find(item => item.id === rowId);
    
    if (!target_row) {
        alert('Answer not found');
        return;
    }

    // Set form values
    $('#answer').val(target_row.answer_text);
    $('#id').val(target_row.id);
    
    if(parseInt(target_row.is_correct) === 0){
        $('#wrong_answer').prop('checked', true);
        $('.explanation').hide();
        $('#explanation').val('');
    } else {
        $('#right_answer').prop('checked', true);
        $('.explanation').show();
        $('#explanation').val(target_row.answer_explanation || '');
    }

    $('#title').html("Update Answer");
   
    $('#create-modal').modal('show');
}
</script>

@endsection