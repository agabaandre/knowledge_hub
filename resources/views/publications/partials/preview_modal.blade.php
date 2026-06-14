<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width:96vw;margin:0.75rem auto;">
        <div class="modal-content" style="border-radius:0.75rem;overflow:hidden;">
            <div class="modal-header py-2 px-3" style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                <h5 class="modal-title mb-0" id="previewModalLabel">
                    <i class="fa fa-file mr-2"></i>Attachment Preview
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" id="previewModalBody" style="min-height:70vh;background:#fff;"></div>
        </div>
    </div>
</div>
<script>
window.previewAttachmentClick = window.previewAttachmentClick || function (event, button) {
    event.preventDefault();
    event.stopPropagation();

    var fileUrl = button.getAttribute('data-file-url');
    var ext = (button.getAttribute('data-file-ext') || '').toLowerCase();
    var isOffice = (button.getAttribute('data-file-office') || '0') === '1';

    if (!fileUrl || typeof jQuery === 'undefined') {
        if (fileUrl) window.open(fileUrl, '_blank');
        return false;
    }

    var $ = jQuery;
    var modal = $('#previewModal');
    var modalBody = $('#previewModalBody');
    var modalTitle = $('#previewModalLabel');

    if (!modal.length) {
        window.open(fileUrl, '_blank');
        return false;
    }

    if (ext === 'pdf') {
        modalTitle.html('<i class="fa fa-file-pdf mr-2"></i>PDF Preview');
    } else if (['jpg','jpeg','png','gif','webp','svg'].indexOf(ext) !== -1) {
        modalTitle.html('<i class="fa fa-file-image mr-2"></i>Image Preview');
    } else if (isOffice) {
        modalTitle.html('<i class="fa fa-file-alt mr-2"></i>Document Preview');
    } else {
        modalTitle.html('<i class="fa fa-file mr-2"></i>Attachment Preview');
    }

    var content = '';
    if (['jpg','jpeg','png','gif','webp','svg'].indexOf(ext) !== -1) {
        content = '<div style="text-align:center;padding:2rem;"><img src="'+fileUrl+'" class="img-fluid" style="max-height:75vh;max-width:100%;margin:auto;display:block;"></div>';
    } else if (ext === 'pdf') {
        content = '<iframe src="'+fileUrl+'#toolbar=1&navpanes=0&scrollbar=1" style="width:100%;height:75vh;border:none;background:#fff;"></iframe>';
    } else if (isOffice) {
        content = '<iframe src="https://docs.google.com/viewer?url='+encodeURIComponent(fileUrl)+'&embedded=true" style="width:100%;height:75vh;border:none;background:#fff;"></iframe>';
    } else {
        content = '<div class="alert alert-info m-4 text-center">Preview not available.<br><a href="'+fileUrl+'" target="_blank" class="btn btn-primary btn-sm mt-2"><i class="fa fa-download mr-1"></i>Download</a></div>';
    }

    modalBody.html(content);
    modal.modal('show');
    return false;
};
</script>
