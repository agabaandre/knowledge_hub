@extends('layouts.plain')

@section('styles')

@endsection

@section('content')
<div class="row">

@include('layouts.partials.alerts')

<div class="card col-lg-12">
    <div class="card-header text-left">
        <h4 class="card-title float-left">Create resource summary</h4>
    </div>

    <div class="card-body text-left">
        @if(@$message)
            <div class="alert alert-danger">{{ $message }}</div>
        @endif
        <form method="POST" action="{{ route('account.summary') }}" id='publications' class='publications'>
        @csrf
        <div class="modal-body">
            <input type="hidden" name="original_id" id="id" value="{{$publication->id}}" class="newform">
            <div class="row">
                
            <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="description">Title</label>
                            <input placeholder="Title"  class="form-control" 
                            value="{{ (old('title'))?old('title'):$publication->title}}"  id="version" name="title"/>
                        </div>
                </div>

               
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="summernote">Your summary</label>
                    <textarea placeholder="Your Summary" class="form-control newform" id="summernote" name="summary" required></textarea>
                </div>
            </div>


            <div class="col-md-4 attachment">
                <div class="mb-3">
                    <label class="form-label" for="publication">Attachment</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="file" id="attachments">
                        <label class="custom-file-label" for="validatedCustomFile">Choose file.</label>
                        <div class="preview py-2"></div>
                    </div>
                </div>
            </div>
                    
            </div>

        </div>
        <div class="modal-footer">

            <button class="btn btn-danger" data-dismiss="modal" type="button">Cancel</button>
            <button class="btn btn-primary" type="submit">Submit</button>
        </div>

        </form>

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>

@endsection

@section('scripts')

@include('account.partials.create_js')

@endsection
