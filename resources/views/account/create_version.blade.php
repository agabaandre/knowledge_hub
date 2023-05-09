@extends('layouts.plain')

@section('styles')

@endsection

@section('content')
<div class="row">
@include('layouts.partials.alerts')

    <div class="card col-lg-12">
        <div class="card-header text-left">
            <h4 class="card-title float-left">Publish resource version <br>Resource: <span class="text-dark"> {{$publication->title }} by {{ $publication->author->name}}</span></h4>
        </div>

        <div class="card-body text-left">
            @if(@$message)
             <div class="alert alert-danger">{{ $message }}</div>
            @endif
           <form method="POST" action="{{ route('account.publication') }}" id='publications' class='publications'>
            @csrf
            <div class="modal-body">
                <input type="hidden" name="original_id" id="id" value="{{$publication->id}}" class="newform">
                <div class="row">
                    
                <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="description">Version Number</label>
                                <input placeholder="Version Number"  class="form-control" 
                                value="{{ (old('version'))?old('version'):number_format(count($publication->versioning->toArray())+1,1)}}"  id="version" name="version" readonly/>
                            </div>
                 </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="publication">File Type</label>
                        @include('partials.publications.filetype_dropdown',['field'=>'file_type'])
                    </div>
                </div>

                 <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Version  Link</label>
                                <input type="text" placeholder="Version  Link" class="form-control url" id="publication" name="link" required>
                            </div>
                        </div>
                       

                <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="summernote">Version Description</label>
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
                                <label class="form-label" for="publication">Version Attachments</label>
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

    @include('account.partials.create_js')

@endsection
