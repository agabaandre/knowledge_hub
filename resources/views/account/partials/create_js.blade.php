
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
                $('.video').hide();

            }else{

                $('.attachment').hide();
                $('.video').hide();
                isVideo = false;
            }
         
            //
        }

        const typeSelected = selectedType.name.toLowerCase();

        if(typeSelected.indexOf('video')>-1 || typeSelected.indexOf('web')>-1  ){
            $('.url').attr('required',true);
            $('.url_wrapper').show();
        
        }else{

            $('.url').removeAttr('required');
            $('.url_wrapper').hide();
        }
         
    });


</script>