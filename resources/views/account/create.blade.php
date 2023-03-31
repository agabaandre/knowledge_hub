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

                    
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="publication">File Type</label>
                        @include('partials.publications.filetype_dropdown',['field'=>'file_type'])
                    </div>
                </div>

                <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Sub Theme</label>
                                @include('partials.publications.subtheme_dropdown',['field'=>'sub_theme'])
                            </div>
                 </div>
                 
                 <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Publication URL Link</label>
                                <input type="text" placeholder="URL Link" class="form-control url" id="publication" name="link" required>
                            </div>
                        </div>
                       
                        
                 <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Geographical Coverage</label>
                                @include('partials.publications.area_dropdown')
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

                        <div class="col-md-6 attachment" >
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

@include('partials.general.summernote')
@include('common.attachment_js')
@include('common.sweet-alert')

<script>

    var isVideo=false;

    $('.url').on('blur',function(e) {

        const url = e.target.value;
        if(isVideo)
         $('.vid').attr('src',url);
    });

    $('#file_type').on('change',function(e) {

        const alltypes = JSON.parse('<?php echo json_encode($file_types); ?>');
        console.log(alltypes);

        const selectedType = alltypes.find(item=>item.id === parseInt(e.target.value));

        if(selectedType.name.toLowerCase().indexOf('video')>-1){
            $('.attachment').hide();
            $('.video').show();
            isVideo = true;
            $('.vid').attr('src',$('.url').val());

        }else{

            $('.attachment').show();
            $('.video').hide();
            isVideo = true;
            //
        }
         
    });


      $('#publications00').submit(function(e) {
        e.preventDefault();

        var form = $(this);

        // Get the form data.]
        var formData = new FormData(form.get(0));
        var url = form.attr('action');

        $.ajax({
            url: url,
            type: 'post',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                
                if(response.status == 200) {
                    
                $('#publications').trigger("reset");
                   swal('Successful', response.data.message,'success');
            
                } else {
                    swal('Error!', response.data.message,'error');
                }
            },
            error:function(error){
                swal('Error!', error?.message,'error');
            }
        });


    });
</script>
@endsection
