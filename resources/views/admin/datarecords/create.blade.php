<!--  Extra Large modal example -->
@extends('admin.layouts.main')

@section('content')
<div class="row">

    <div class="card col-lg-12">
        <div class="card-header text-left">
            <h4 class="card-title float-left">{{ $title ?? '' }}</h4>
             <hr>
        </div>

        <div class="card-body text-left">
            <div class="row justify-content-center">

                <!-- toast -->
                <div id="toast-3s" class="toast toast-3s fade hide" role="alert" aria-live="assertive" data-delay="3000" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="mr-auto">Message</strong>
                        <small class="text-muted"></small>
                        <button type="button" class="m-l-5 mb-1 mt-1 close" data-dismiss="toast" aria-label="Close">
                            <span>×</span>
                        </button>
                    </div>
                    <div class="notification">

                    </div>
                </div>
                <!-- end toast -->
            </div>
            <form action="{{ url('admin/datarecords/save') }}" id='publications' class='publications'>
                @csrf
                <input type="hidden" value="{{ form_edit('id',$record,'id') }}" name="id" />
            <div class="modal-body">
               <div class="row">

                    <div class="col-md-6">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="description">Title</label>
                                <input type="text" placeholder="Title"  class="form-control" id="title" 
                                name="title" 
                                required value="{{form_edit('title',$record,'title') }}" />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="description"> Description</label>
                                <textarea id="description" placeholder="Descripion" class="form-control summernote" name="description" style="min-height: 400px;">{{ form_edit('description',$record,'description') }}</textarea>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6">

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">COuntry</label>
                                @include('partials.countries.dropdown',['field'=>'country_id','required'=>'required', 'selected'=>form_edit('country_id',$record,'country_id')])
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Category</label>
                                @include('partials.datarecords.categories_dropdown',['field'=>'data_category_id','required'=>'required','exclude_special'=>true])
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Sub Category</label>
                                <select name="data_sub_category_id" class="form-control sub_category">
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <!-- Section Label -->
                            <label class="form-label mb-3" for="publication">Content Type</label>
                            <div class="mb-3">
                                <label class="form-check-inline">
                                    <input type="radio" name="upload_type" value="upload" checked class="form-check-input"> Attachment
                                </label>
                                <label class="form-check-inline">
                                    <input type="radio" name="upload_type" value="link" class="form-check-input">External Link
                                </label>

                                <div id="file-input">
                                    <!-- <label class="form-label" for="publication">Publication Doc</label> -->
                                    <input placeholder="Attach publication document" type="file" name="files" class="form-control" multiple>
                                </div>

                                <div id="link-input" style="display:none;">
                                    <!-- <label class="form-label" for="publication">Publication URL Link</label> -->
                                    <input placeholder="Add link" type="text" name="link" class="form-control">
                                </div>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">File Type</label>
                                @include('partials.publications.filetype_dropdown',['field'=>'file_type_id','required'=>'required','selected'=>form_edit('file_type',$record,'file_type_id')])
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Cover Image</label>
                                <div class="custom-file">
                                    <input type="file" class="form-control" name="cover" id="">

                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="publication">Status</label>
                                <select class="form-control select2" name="is_active" required>
                                    <option value="" selected disabled>Choose</option>
                                    <option value="Active" {{ (form_edit('is_active',$record,'is_active')=='Active')?'selected':''}}>Active</option>
                                    <option value="In-Active" {{ (form_edit('is_active',$record,'is_active')=='In-Active')?'selected':''}}>Inactive</option>

                                </select>
                            </div>
                        </div>

                        <!-- <div  class="col-md-4"> -->
                        <!-- </div> -->
                    </div>

                </div>
            </div>
            <div class="modal-footer">

                <button class="btn btn-danger" data-dismiss="modal" type="button">Cancel</button>
                <button class="btn btn-primary" type="submit">Save Record</button>
            </div>

            </form>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

@endsection

@section('scripts')

<script>

    $('.data_category').on('change',function(event){

        var data_categories = JSON.parse('<?php echo json_encode($data_categories); ?>');
     
        const categoryValue = $(this).val();
        var selectedCategory = data_categories.find((item)=>parseInt(item.id)==parseInt(categoryValue));
        var sub_categories   = selectedCategory.sub_categories;

        console.log(selectedCategory)

        console.log(sub_categories)

        $('.sub_category').html('<option value="" selected>Choose</option>');

        sub_categories.forEach(function(item){

            $('.sub_category').append(`<option value="${item.id}">${item.sub_catgeory_name}</optional>`)

        })

    });

    $('input[name="upload_type"]').on('change', function() {
        if ($(this).val() == 'upload') {
            $('#file-input').show();
            $('#link-input').hide();
        } else {
            $('#file-input').hide();
            $('#link-input').show();
        }
    });

    $('#publications').submit(function(e) {
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
                if (response.status == 'success') {
                    swal( 'Success!',response.message,'success');

                    setTimeout(function(){
                        window.location.assign("<?php echo url('admin/publications') ?>")
                    },2000);

                } else {
                    swal( 'Error!',response.message,'Error')
                }
            }
        });



    });
</script>


@include('partials.general.summernote')
@include('common.select2')

@endsection