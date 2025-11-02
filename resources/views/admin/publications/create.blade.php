<!--  Extra Large modal example -->
@extends('admin.layouts.main')

@section('styles')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/tabs.css') }}">
  @include('account.partials.wizard_res')
@endsection
  
@section('content')
<div class="page-header">
    <h1 class="page-title">New Public Health Resource</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Publish</a></li>
            <li class="breadcrumb-item active" aria-current="page">New Public Health Resource</li>
        </ol>
    </div>
</div>
<div class="row">

@php
//dd($publication->accessgroups);
@endphp



    <div class="card col-lg-12">
        <div class="card-header text-left">
            <h4 class="card-title float-left">{{ $title ?? '' }}</h4>
             @if(current_user()->author_id)
                <a href="#import-modal" data-toggle="modal" class="btn btn-dark"><i class="fa fa-upload"></i> Import Resources</a>
                @include('admin.publications.partials.import-modal',['action'=>url('admin/publications/import')])
             @endif
             
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
            <div class="container">
            @if(current_user()->author_id)
            
            <form action="{{ url('admin/publications/save') }}" id='publications' class='publications'>
                @csrf
            <input type="hidden" value="{{ form_edit('id',$publication,'id') }}" name="id" />

            @include('account.wizard',['row'=>$publication])

            </form>
            @else

            <div class="alert alert-danger">
                <p>No Author Account is associated to the logged in account.</p>
            </div>

            @endif
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

@endsection

@section('scripts')

    @include('common.select2')
    @include('account.partials.create_js')
    @include('account.partials.wizard_js')

<script>

    $('input[name="upload_type"]').on('change', function() {
        if ($(this).val() == 'upload') {
            $('#file-input').show();
            $('#link-input').hide();
        } else {
            $('#file-input').hide();
            $('#link-input').show();
        }
    });

    // Ensure this handler runs first and prevents wizard handler from executing
    $('#publications').off('submit').on('submit', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation(); // Prevent other handlers from executing

        var form = $(this);

        // Get the form data
        var formData = new FormData(form.get(0));
        
        // Manually add files if they exist (handles DataTransfer-set files)
        // Check both possible file input IDs/names
        var fileInput = $('#attachments');
        if (!fileInput.length || !fileInput[0].files || fileInput[0].files.length === 0) {
            // Try alternative selector
            fileInput = $('input[name="files"]');
        }
        
        if (fileInput.length && fileInput[0].files && fileInput[0].files.length > 0) {
            console.log('Admin form: Files detected before submission:', fileInput[0].files.length);
            
            // Clear existing files from FormData and add our files manually
            formData.delete('files'); // Remove any existing files entry
            formData.delete('files[]'); // Remove any existing files[] entry
            
            // Add each file individually
            Array.from(fileInput[0].files).forEach(function(file, index) {
                formData.append('files[]', file);
                console.log('Admin form: Added file to FormData:', file.name, '(' + (file.size / 1024).toFixed(2) + ' KB)');
            });
        } else {
            console.log('Admin form: No files detected in input');
        }

        var url = form.attr('action');

        $.ajax({
            url: url,
            type: 'post',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Admin form: Submission response:', response);
                if (response.status == 'success') {
                    swal( 'Success!',response.message,'success');

                    setTimeout(function(){
                        window.location.assign("<?php echo url('admin/publications') ?>")
                    },2000);

                } else {
                    swal( 'Error!',response.message,'Error')
                }
            },
            error: function(xhr) {
                console.error('Admin form submission error:', xhr);
                var errorMsg = 'An error occurred while submitting the form.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                swal('Error!', errorMsg, 'error');
            }
        });



    });
</script>


@include('partials.general.summernote')
@include('common.select2')

@endsection