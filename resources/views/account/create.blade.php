@extends('layouts.plain')

@section('styles')

@endsection

@section('content')
<div class="row">

    <div class="card col-lg-12">
        <div class="card-header text-left">
            <h4 class="card-title float-left">Publish a resource</h4>
        </div>

        <div class="card-body text-left">
            <div class="row justify-content-center">

                <!-- toast -->
                <div id="toast-3s" class="toast toast-3s fade hide" role="alert" aria-live="assertive" data-delay="3000" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="mr-auto">Message</strong>
                        <small class="text-muted"></small>
                        <button type="button" class="m-l-5 mb-1 mt-1 close" data-dismiss="toast" aria-label="Close">
                            <span></span>
                        </button>
                    </div>
                    <div class="notification">

                    </div>
                </div>
                <!-- end toast -->
            </div>
           <form method="POST" action="{{ route('account.publication') }}" id='publications' class='publications'>
            @csrf
            <div class="modal-body">
                <input type="hidden" name="id" id="id" class="newform">
                <div class="row">
                    

                <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="description">Publication Title</label>
                                <input placeholder="Title"  class="form-control newform"  id="title" name="title" required/>
                            </div>
                 </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label" for="publication">File Type</label>
                        @include('partials.publications.filetype_dropdown',['field'=>'file_type'])
                    </div>
                </div>

                <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Sub Theme</label>
                                @include('partials.publications.subtheme_dropdown',['field'=>'sub_theme','class'=>'select2'])
                            </div>
                 </div>

                 <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Geographical Coverage</label>
                                @include('partials.publications.area_dropdown',['class'=>'select2'])
                            </div>
                </div>

                 
                 <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Publication URL Link</label>
                                <input type="text" placeholder="URL Link" class="form-control url" id="publication" name="link" required>
                            </div>
                        </div>
                       
                        
                 
                <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="summernote">Publication Description</label>
                                <textarea placeholder="Descripion" class="form-control newform" id="summernote" name="description" required></textarea>
                            </div>
                        </div>

                   
                        <div class="col-md-6" >
                            <div class="mb-3">
                                <label class="form-label" for="publication">Cover Image</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="cover" id="cover">
                                    <label class="custom-file-label" for="validatedCustomFile">Choose file...</label>
                                    <div class="cover_preview py-2"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 attachment" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Publication Attachments</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="files" id="attachments">
                                    <label class="custom-file-label" for="validatedCustomFile">Choose file.s..</label>
                                    <div class="preview py-2"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 justify-content-center video" style="display: none;">
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

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

@endsection

@section('scripts')
    @include('common.select2')
    @include('account.partials.create_js')
@endsection
