<link href="{{ asset('assets/plugins/summernote/dist/summernote.min.css') }}" rel="stylesheet">
<script src="{{ asset('assets/plugins/summernote/dist/summernote.min.js') }}"></script>

<script>

$(document).ready(function() {

    $('#summernote').summernote({
        placeholder: 'Discussion body here',
        tabsize: 2,
        height: 200
    });

});

</script>