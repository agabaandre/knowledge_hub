@extends(admin_layout())

@section('styles')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/tabs.css') }}">
  @include('account.partials.wizard_res')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit & Approve RSS Item</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('admin') }}">Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.rss_feeds.index') }}">RSS Feeds</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.rss_staging.index') }}">RSS Staging</a></li>
            <li class="breadcrumb-item active">Edit & Approve</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="card col-lg-12">
        <div class="card-header text-left">
            <h4 class="card-title float-left">From feed: {{ $staging->feed->name ?? 'RSS' }}</h4>
            <a href="{{ route('admin.rss_staging.index') }}" class="btn btn-secondary btn-sm">Back to Staging</a>
            <hr>
        </div>
        <div class="card-body text-left">
            <div class="container">
                <form action="{{ url('admin/publications/save') }}" id="publications-rss" class="publications">
                    @csrf
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="from_rss_staging_id" value="{{ $staging->id }}">
                    @include('account.wizard', ['row' => $row])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @include('common.select2')
    @include('account.partials.create_js')
    @include('account.partials.wizard_js')
    @include('partials.general.summernote')
<script>
$(function() {
    $('#publications-rss').off('submit').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = new FormData(form.get(0));
        var fileInput = $('#attachments, input[name="files"]')[0];
        if (fileInput && fileInput.files && fileInput.files.length > 0) {
            formData.delete('files'); formData.delete('files[]');
            Array.from(fileInput.files).forEach(function(file) { formData.append('files[]', file); });
        }
        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(response) {
                if (response.status == 'success') {
                    swal('Success!', response.message, 'success');
                    setTimeout(function() { window.location.assign("{{ route('admin.rss_staging.index') }}"); }, 2000);
                } else {
                    swal('Error!', response.message || 'Error', 'error');
                }
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'An error occurred.';
                swal('Error!', msg, 'error');
            }
        });
    });
});
</script>
@endsection
