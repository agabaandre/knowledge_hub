<link href="{{ asset('assets/plugins/summernote/dist/summernote.min.css') }}" rel="stylesheet">
<script src="{{ asset('assets/plugins/summernote/dist/summernote.min.js') }}"></script>

<script>

$(document).ready(function() {

    function uploadImage(file, editor) {
        var data = new FormData();
        data.append('file', file);
        data.append('_token', '{{ csrf_token() }}');

        $.ajax({
            url: '{{ route("image.upload") }}',
            type: 'POST',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                var imageUrl = response.url || response;
                $(editor).summernote('insertImage', imageUrl);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Image upload failed: ' + textStatus + ' ' + errorThrown);
            }
        });
    }

    $('.summernote').summernote({
        placeholder: 'Content here',
        tabsize: 2,
        height: 200,
        callbacks: {
            onImageUpload: function(files) {
                if (files && files.length > 0) {
                    uploadImage(files[0], this);
                }
            }
        }
    });

    $('#summernote').summernote({
        placeholder: 'Content here',
        tabsize: 2,
        height: 300,
        callbacks: {
            onImageUpload: function(files) {
                if (files && files.length > 0) {
                    uploadImage(files[0], this);
                }
            }
        }
    });

    $('.summernote-lg').summernote({
        placeholder: 'Content here',
        tabsize: 2,
        height: 700,
        callbacks: {
            onImageUpload: function(files) {
                if (files && files.length > 0) {
                    uploadImage(files[0], this);
                }
            }
        }
    });

    $('.summernote-sm').summernote({
        placeholder: 'Content here',
        tabsize: 2,
        height: 100,
        callbacks: {
            onImageUpload: function(files) {
                if (files && files.length > 0) {
                    uploadImage(files[0], this);
                }
            }
        }
    });

});

</script>