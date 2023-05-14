<link href="{{ asset('assets/plugins/summernote/dist/summernote.min.css') }}" rel="stylesheet">
<script src="{{ asset('assets/plugins/summernote/dist/summernote.min.js') }}"></script>

<script>

$(document).ready(function() {

    $('.summernote').summernote({
        placeholder: 'Content here',
        tabsize: 2,
        height: 300
    });

    $('#summernote').summernote({
        placeholder: 'Content here',
        tabsize: 2,
        height: 300
    });

    $('.summernote-lg').summernote({
        placeholder: 'Content here',
        tabsize: 2,
        height: 700
    });

    $('.summernote-sm').summernote({
        placeholder: 'Content here',
        tabsize: 2,
        height: 100
    });

});

</script>