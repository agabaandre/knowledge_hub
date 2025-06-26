
<script>

function deleteFile(index) {
    console.log(index);

    filelistall = $('#attachments').prop("files");

    var fileBuffer = [];
    Array.prototype.push.apply(fileBuffer, filelistall);
    fileBuffer.splice(index, 1);
    const dT = new ClipboardEvent('').clipboardData || new DataTransfer();
    for (let file of fileBuffer) { dT.items.add(file); }

    filelistall = $('#attachments').prop("files", dT.files);

    $('.preview_' + index).remove();
}

$(function() {

    // Multiple images preview in browser
    var imagesPreview = function(input, placeToInsertImagePreview) {

        if (input.files) {

            var current_files = $('#attachments').prop("files");
            newIndex = (current_files) ? current_files.length : 0;
            var filesCount = input.files.length;

            // Define allowed file types
            const allowedTypes = {
                'image': ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
                'pdf': ['application/pdf'],
                'powerpoint': ['application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'],
                'word': ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
                'excel': ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
                'audio': ['audio/mpeg', 'audio/wav', 'audio/ogg'],
                'video': ['video/mp4', 'video/webm', 'video/ogg']
            };

            // Flatten allowed types for easy checking
            const validTypes = Object.values(allowedTypes).flat();

            for (i = 0; i < filesCount; i++) {
                const file = input.files[i];
                console.log('file ' + i, file);

                if (!validTypes.includes(file.type)) {
                    alert(`File "${file.name}" is not allowed. Only images, PDFs, PowerPoint, Word, Excel, audio and video files are permitted.`);
                    // Clear the input
                    input.value = '';
                    $(placeToInsertImagePreview).empty();
                    return;
                }

                newIndex = (newIndex > 0) ? (newIndex - 1) : 0;
                var fileName = file.name;
                var reader = new FileReader();

                reader.onload = function(event) {
                    var my_file = event.target.result;
                    var htmlToAppend = "";

                    if (file.type.startsWith('image/')) {
                        htmlToAppend = $($.parseHTML('<span class="text-danger preview_' + newIndex + ' style="max-height:30px!important; margin-top:50px; cursor:pointer;" onclick="deleteFile(' + newIndex + ')">Remove</span> <h6 class="preview_' + newIndex + '">' + fileName + '</h6><img style="max-height:200px; max-width:230px" class="mt-2 rounded preview_' + newIndex + '">'))
                            .attr('src', my_file);

                        $(placeToInsertImagePreview).removeAttr('style');
                        $(placeToInsertImagePreview).html(htmlToAppend);
                        return;
                    } else {
                        // For non-image files show appropriate icon/preview
                        let icon = 'fa-file';
                        if (file.type.includes('pdf')) icon = 'fa-file-pdf';
                        else if (file.type.includes('powerpoint')) icon = 'fa-file-powerpoint'; 
                        else if (file.type.includes('word')) icon = 'fa-file-word';
                        else if (file.type.includes('excel')) icon = 'fa-file-excel';
                        else if (file.type.includes('audio')) icon = 'fa-file-audio';
                        else if (file.type.includes('video')) icon = 'fa-file-video';

                        htmlToAppend = $($.parseHTML('<span class="text-danger preview_' + newIndex + '" style="max-height:30px!important; margin-top:50px; cursor:pointer;" onclick="deleteFile(' + newIndex + ')">Remove</span><h6 class="preview_' + newIndex + '">' + fileName + '</h6><i class="fas ' + icon + ' fa-3x preview_' + newIndex + '"></i>'));
                    }

                    var wholeDiv = $($.parseHTML("<div class='col-lg-4 preview_" + newIndex + "'>"));
                    var divClose = $($.parseHTML('</div>'));
                    divClose.appendTo(htmlToAppend);
                    htmlToAppend.appendTo(wholeDiv);
                    wholeDiv.appendTo(placeToInsertImagePreview);
                }

                reader.readAsDataURL(file);
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