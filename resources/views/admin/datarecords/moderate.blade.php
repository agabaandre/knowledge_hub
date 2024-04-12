@extends('admin.layouts.main')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <h5>Publication Comments Moderation</h5>
                         <hr>
                    </div>
                    <div class="card-tools text-right">

                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">


                            <!-- if no publications -->
                            @if(count($publications)==0)
                                <div class="card text-center" style="margin: 0 auto;">
                                    <div class="card-body">
                                        <i class="fas fa-exclamation-triangle fa-5x greyed-out-icon mb-3" style="color: #BCBCBC"></i>
                                        <h4 class="card-title">No comments requiring moderation</h4>
                                        <p class="card-text">Sorry, there are currently no with new comments publications available.</p>
                                    </div>
                                </div>
                            @else
                                @foreach ($publications as $publication)

                                    <h4>{!! $publication->title !!}</h4>
                                    <p>{!! substr($publication->description, 0, 150) !!}...</p>
                                    <hr />

                                    <table class="table table-striped condensed">
                                        <thead>
                                            <tr>
                                                <th>Comment</th>
                                                <th>Created By</th>
                                                <th>Created At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($publication->pending_comments as $comment)
                                                <tr>
                                                    <td>{!! $comment->comment !!}</td>
                                                    <td>{{ $comment->user->name }}</td>
                                                    <td>{{ $comment->created_at }}</td>
                                                    <td>
                                                        <a href="{{ url('admin/publications/approve_comment')}}?id={{ $comment->id}}>" class="btn btn-success btn-sm approve_comment" id="approve_comment">Approve</a>
                                                        <a href="{{ url('admin/publications/reject_comment') }}?id={{$comment->id}}" class="btn btn-danger btn-sm reject_comment" id="reject_comment">Reject</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('scripts')

 @include('common.sweet-alert')

 
<script>
    $(document).on('click', '.approve_comment', function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        $.ajax({
            type: "get",
            url: url,
            success: function(response) {
                console.log(response);
                    swal( 'Success!','Comment approved ','success');
            }
        }).then((result) => {

            setTimeout(function(){
                location.reload();
            },3000);
            
        });
    });


    $(document).on('click', '.reject_comment', function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        $.ajax({
            type: "get",
            url: url,
            success: function(response) {
                    swal( 'Success!','Comment rejected ','success');
            }
        })
        .then((result) => {
                
            setTimeout(function(){
                location.reload();
            },2000);
            
        });
    });
</script>


@endsection