@extends('layouts.plain')

@section('styles')

@endsection

@section('content')
<div class="row">

    <div class="card col-lg-12">
        <div class="card-header">
            <h4 class="card-title mb-0">Create Forum</h4>
            <p class="mt-0">Start a new discussion, interested individuals will be alert and will join you!</p>
        </div>

        <div class="card-body text-left">
            <div class="container">
            @if(@$message)
             <div class="alert alert-danger">{{ $message }}</div>
            @endif
           <form method="POST" action="{{ route('forums.publish') }}" id='publications' class='publications' enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
               <div class="row">
                    
                 <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Forum Title</label>
                                <input type="text" placeholder="Forum Title" class="form-control url" id="title" name="title" required>
                            </div>
                  </div>

                  
                  <div class="form-group col-lg-12">
                    <label class="form-label" for="communities">Target Audience/Communities of Practice</label>
                      @include('partials.publications.publication_communities_dropdown',['field'=>'communities[]',
                        'selected'=>[]])
                  </div>
                       

                <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="summernote">Forum Story</label>
                                <textarea placeholder="Descripion" class="form-control newform" id="summernote" name="description" required></textarea>
                            </div>
                        </div>

                   
                        <div class="col-md-12" >
                            <div class="mb-3">
                                <label class="form-label" for="publication">Cover Image</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="image" id="image">
                                    <label class="custom-file-label" for="validatedCustomFile">Choose file...</label>
                                    <div class="cover_preview py-2"></div>
                                </div>
                            </div>
                        </div>

                        

                        <div class="col-md-12 justify-content-center video" style="display: none;">
                             <label class="form-label" for="publication">Video</label>
                            <div class="mb-3">
                                <iframe width="450" height="260"class="vid" src="">
                                </iframe>
                            </div>
                        </div>
                    

                </div>

            </div>
            <div class="modal-footer">

                <button class="btn btn-danger" data-dismiss="modal" type="button">Cancel</button>
                <button class="btn btn-primary" type="submit">Submit</button>
            </div>

           </form>

            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

@endsection

@section('scripts')

    @include('account.partials.create_js')
    @include('common.select2')

@endsection
