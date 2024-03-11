
       
<script>

function deleteFile(index)  {

    console.log(index);

    filelistall = $('#attachments').prop("files");

    var fileBuffer=[];
    Array.prototype.push.apply( fileBuffer, filelistall );
    fileBuffer.splice(index, 1);
    const dT = new ClipboardEvent('').clipboardData || new DataTransfer(); 
    for (let file of fileBuffer) { dT.items.add(file); }

    filelistall = $('#attachments').prop("files",dT.files);

    $('.preview_'+index).remove();
    
    }


    $(function() {

    // Multiple images preview in browser
    var imagesPreview = function(input, placeToInsertImagePreview) {

    if (input.files) {

        var current_files = $('#attachments').prop("files");

        newIndex = current_files.length;

        var filesCount = input.files.length;

        for (i = 0; i < filesCount; i++) {

        console.log('file '+i,input.files[i]);
        
        
            newIndex = (newIndex>0)?(newIndex-1):0;

            var fileName= input.files[newIndex].name;

            var reader = new FileReader();

            reader.onload = function(event) {
                var my_file = event.target.result;
                //  console.log(my_file);
                var htmlToAppend = "";

                if(my_file.indexOf('image/png')>-1 || my_file.indexOf('image/jpeg')>-1){
                
                    htmlToAppend = $($.parseHTML('<span class="text-danger preview_'+newIndex+' style="max-height:30px!important; margin-top:50px; cursor:pointer;" onclick="deleteFile('+newIndex+')">Remove</span> <h6 class="preview_'+newIndex+'">'+fileName+'</h6><img width="300px" class="mt-2 rounded preview_'+newIndex+'">')).attr('src',my_file);
                    
                    $(placeToInsertImagePreview).removeAttr('style');
                    $(placeToInsertImagePreview).html(htmlToAppend);
                    return;

                }else{

                htmlToAppend = $($.parseHTML('<span class="text-danger  preview_'+newIndex+'" style="max-height:30px!important; margin-top:50px; cursor:pointer;" onclick="deleteFile('+newIndex+')">Remove</span><h6 class="preview_'+newIndex+'">'+fileName+'</h6><iframe width="500px" class="mt-2 preview_'+newIndex+'" style="width:400px; height:400px; margin-right:10px;!important" frameborder="0">')).attr('src',my_file);
                    //console.log(my_file);
                }

                var wholeDiv = $($.parseHTML("<div class='col-lg-4 preview_"+newIndex+"'>"));
                var divCLose = $($.parseHTML('</div>'));
                divCLose.appendTo(htmlToAppend);
                htmlToAppend.appendTo(wholeDiv);

                wholeDiv.appendTo(placeToInsertImagePreview);
                
            }

            reader.readAsDataURL(input.files[i]);
        }
    }

};

    $('#attachments').on('change', function() {
        imagesPreview(this, 'div.preview');
    });

    $('#cover').on('change', function() {
        imagesPreview(this, 'div.cover_preview');
    });
      $('#favicon').on('change', function() {
        imagesPreview(this, 'div.favicon_preview');
    });
     $('#spotlight').on('change', function() {
        imagesPreview(this, 'div.spotlight_preview');
    });
  

});

</script>