
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

            console.log(selectedType);

            if(parseInt(selectedType.is_downloadable) === 1){

                $('.attachment').show();

            }else{

                $('.attachment').hide();
                $('.video').hide();
                isVideo = false;
            }
         
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