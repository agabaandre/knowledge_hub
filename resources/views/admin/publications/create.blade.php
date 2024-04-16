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