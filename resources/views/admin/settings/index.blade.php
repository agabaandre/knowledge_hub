@extends('admin.layouts.main')
@section('styles')
    <!-- Bootstrap Colorpicker CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.2.0/css/bootstrap-colorpicker.min.css" rel="stylesheet">

<!-- Bootstrap Colorpicker JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.2.0/js/bootstrap-colorpicker.min.js"></script>

@endsection
@section('content')

  <!-- PAGE-HEADER -->
  <div class="page-header">
        <h1 class="page-title">System Configuration</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Dashaboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">System Configuration</li>
            </ol>
        </div>
    </div>
   <!-- PAGE-HEADER END -->
  <form action="{{ route('admin.config.save') }}" method="post" enctype="multipart/form-data">
    <div class="row bg-white bg-white py-4 px-3 rounded">
             @csrf
            <div class="col-lg-4">
                    <div class="form-group">
                        <label class="text-muted">Title </label>
                        <input value="{{$settings->title}}" name="title" class="form-control" placeholder="">
                    </div>
                    <div class="form-group">
                        <label class="text-muted">Site name </label>
                        <input value="{{$settings->site_name}}" name="site_name" class="form-control" placeholder="">
                    </div>
                    <div class="form-group">
                        <label class="text-muted">Site description </label>
                        <textarea name="site_description" rows="5" class="form-control" placeholder="">{{$settings->site_description}}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="text-muted">Seo keywords </label>
                        <textarea name="seo_keywords" class="form-control" placeholder="">{{$settings->seo_keywords}}</textarea>
                    </div>
            </div>

            <div class="col-lg-4">

                <div class="form-group">
                    <label class="text-muted">Address </label>
                    <textarea value="" name="address" class="form-control" placeholder="Enter Site Address">{{ $settings->address }}</textarea>
                </div>
                <div class="form-group">
                    <label class="text-muted">Email </label>
                    <input value="{{ $settings->email }}" name="email" class="form-control" placeholder="">
                </div>
                <div class="form-group">
                    <label class="text-muted">Phone </label>
                    <input value="{{ $settings->phone }}" name="phone" class="form-control" placeholder="">
                </div>
                <div class="form-group">
                    <label class="text-muted">Language </label>
                    <input value="{{ $settings->language }}" name="language" class="form-control" placeholder="">
                </div>
                <div class="form-group">
                    <label class="text-muted">Timezone </label>
                    @include('partials.general.timezones',['selected'=>$settings->timezone])
                </div>
            </div>

            <div class="col-lg-4">
                
            <div class="row mt-3">
                <label>Primary Site Color:</label>
                <div class="input-group colorPicker">
                    <div class="input-group-append">
                    <span class="input-group-text color-preview"></span>
                    </div>
                    <input type="text" name="primary_color" value="{{ $settings->primary_color }}" class="form-control" />
                </div>
            </div>

            <div class="row mt-3">
                <label>Secondary Site Color:</label>
                <div class="input-group colorPicker">
                    <div class="input-group-append">
                    <span class="input-group-text color-preview"></span>
                    </div>
                    <input type="text"  name="secondary_color" value="{{ $settings->secondary_color }}" class="form-control" />
                </div>
            </div>

            <div class="row mt-3">
                <label>Site Logo:</label>
                <div class="form-group">
                    <input type="file" name="logo" id="attachments">
                    <div class="preview" style="max-width: 150px;"></div>
                </div>
            </div>

            <div class="row mt-3 px-2">
                <input type="submit" class="btn btn-dark" value="Save Changes">
            </div>
              
            </div>
        </div>

</form>

@endsection

@section('scripts')

	@include('common.select2')
    @include('common.attachment_js')

<script>
  $(function () {
  $('.colorPicker').each(function () {
    var colorPickerInput = $(this).find('input');
    
    $(this).colorpicker().on('colorpickerChange colorpickerCreate', function (e) {
      // Find the closest color preview related to this color picker
      var colorPreview = $(e.currentTarget).find('.color-preview');
      colorPreview.css('background-color', e.color.toString());
    });
  });
});
</script>


@endsection


    