<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 100vw; width: 100vw; margin: 0; padding: 0;">
        <div class="modal-content">
            <div class="modal-header">
                <div class="header-left">
                    <button type="button" class="btn-fullscreen" id="toggleFullscreen" title="Toggle fullscreen">
                        <i class="fa fa-expand" id="fullscreenIcon"></i>
                    </button>
                    <h5 class="modal-title" id="previewModalLabel">
                        <i class="fa fa-file mr-2"></i>Attachment Preview
                    </h5>
                </div>
                <div class="header-actions">
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; font-size: 1.5rem; opacity: 0.9; cursor: pointer; padding: 0.5rem; color: white;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body" id="previewModalBody" style="padding: 0; min-height: calc(100vh - 60px); height: calc(100vh - 60px);">
                <div class="text-center w-100" style="padding: 3rem;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading preview...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading preview...</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
(function () {
    var PDFJS_CDN = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
    var PDFJS_WORKER = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    function previewLoadingHtml() {
        return '<div class="text-center w-100" style="padding: 3rem;"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading preview...</span></div><p class="mt-3 text-muted">Loading preview...</p></div>';
    }

    function shouldUsePdfJsPreview() {
        return window.matchMedia('(max-width: 768px)').matches
            || (window.matchMedia('(pointer: coarse)').matches && window.innerWidth < 1024);
    }

    function ensurePdfJsLoaded() {
        return new Promise(function (resolve, reject) {
            if (typeof pdfjsLib !== 'undefined') {
                if (!pdfjsLib.GlobalWorkerOptions.workerSrc) {
                    pdfjsLib.GlobalWorkerOptions.workerSrc = PDFJS_WORKER;
                }
                resolve();
                return;
            }

            var existing = document.getElementById('pdfjs-preview-lib');
            if (existing) {
                existing.addEventListener('load', function () {
                    pdfjsLib.GlobalWorkerOptions.workerSrc = PDFJS_WORKER;
                    resolve();
                }, { once: true });
                existing.addEventListener('error', reject, { once: true });
                return;
            }

            var script = document.createElement('script');
            script.id = 'pdfjs-preview-lib';
            script.src = PDFJS_CDN;
            script.onload = function () {
                pdfjsLib.GlobalWorkerOptions.workerSrc = PDFJS_WORKER;
                resolve();
            };
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    async function renderPdfJsScrollPreview(fileUrl, containerEl) {
        await ensurePdfJsLoaded();

        containerEl.classList.add('pdf-js-scroll-preview');
        containerEl.innerHTML = previewLoadingHtml();

        var pdf = await pdfjsLib.getDocument({ url: fileUrl, withCredentials: true }).promise;
        containerEl.innerHTML = '';

        var maxWidth = Math.max(280, containerEl.clientWidth || window.innerWidth) - 16;

        for (var pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
            var page = await pdf.getPage(pageNum);
            var unscaled = page.getViewport({ scale: 1 });
            var scale = Math.min(maxWidth / unscaled.width, 2);
            var pixelRatio = window.devicePixelRatio || 1;
            var viewport = page.getViewport({ scale: scale * pixelRatio });
            var canvas = document.createElement('canvas');
            canvas.className = 'pdf-js-scroll-preview__page';
            canvas.setAttribute('data-page', String(pageNum));
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            canvas.style.width = Math.floor(viewport.width / pixelRatio) + 'px';
            canvas.style.height = Math.floor(viewport.height / pixelRatio) + 'px';
            await page.render({ canvasContext: canvas.getContext('2d'), viewport: viewport }).promise;
            containerEl.appendChild(canvas);
        }

        var hint = document.createElement('p');
        hint.className = 'pdf-js-scroll-preview__hint small text-center mb-0';
        hint.textContent = pdf.numPages + ' page' + (pdf.numPages === 1 ? '' : 's') + ' — scroll to view all';
        containerEl.appendChild(hint);
    }

    function buildIframePreview(fileUrl, ext, isOffice) {
        if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].indexOf(ext) !== -1) {
            return '<div style="text-align:center;padding:2rem;"><img src="' + fileUrl + '" class="img-fluid" style="max-height:calc(100vh - 120px);max-width:100%;margin:auto;display:block;" alt="Preview"></div>';
        }
        if (ext === 'pdf') {
            return '<iframe src="' + fileUrl + '#toolbar=1&navpanes=0&scrollbar=1&view=FitH" style="width:100%;height:100%;min-height:calc(100vh - 60px);border:none;background:#ffffff;" title="PDF preview"></iframe>';
        }
        if (isOffice) {
            var gdocs = 'https://docs.google.com/viewer?url=' + encodeURIComponent(fileUrl) + '&embedded=true';
            return '<iframe src="' + gdocs + '" style="width:100%;height:100%;min-height:calc(100vh - 60px);border:none;background:#ffffff;" title="Document preview"></iframe>';
        }
        return '<div class="alert alert-info" style="margin:2rem;text-align:center;"><i class="fa fa-info-circle mr-2"></i>Preview not available for this file type.<br><a href="' + fileUrl + '" target="_blank" rel="noopener" class="btn btn-primary btn-sm mt-2"><i class="fa fa-download mr-1"></i>Download file</a></div>';
    }

    function setPreviewTitle(modalTitle, ext, isOffice) {
        if (ext === 'pdf') {
            modalTitle.html('<i class="fa fa-file-pdf mr-2"></i>PDF Preview');
        } else if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].indexOf(ext) !== -1) {
            modalTitle.html('<i class="fa fa-file-image mr-2"></i>Image Preview');
        } else if (isOffice) {
            modalTitle.html('<i class="fa fa-file-alt mr-2"></i>Document Preview');
        } else {
            modalTitle.html('<i class="fa fa-file mr-2"></i>Attachment Preview');
        }
    }

    window.previewAttachmentClick = function (event, button) {
        event.preventDefault();
        event.stopPropagation();

        var fileUrl = button.getAttribute('data-file-url');
        var ext = (button.getAttribute('data-file-ext') || '').toLowerCase();
        var isOffice = (button.getAttribute('data-file-office') || '0') === '1';

        if (!fileUrl) {
            return false;
        }

        if (typeof jQuery === 'undefined' || typeof jQuery.fn.modal === 'undefined') {
            window.open(fileUrl, '_blank');
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

        modal.removeClass('fullscreen pdf-js-preview');
        $('#fullscreenIcon').removeClass('fa-compress').addClass('fa-expand');
        setPreviewTitle(modalTitle, ext, isOffice);
        modalBody.removeClass('pdf-js-scroll-preview').css('opacity', '1');
        modalBody.html(previewLoadingHtml());
        modal.modal('show');

        modal.off('shown.bs.modal').one('shown.bs.modal', function () {
            var usePdfJs = ext === 'pdf' && shouldUsePdfJsPreview();

            if (usePdfJs) {
                modal.addClass('pdf-js-preview');
                renderPdfJsScrollPreview(fileUrl, modalBody[0]).catch(function () {
                    modal.removeClass('pdf-js-preview');
                    modalBody.html(buildIframePreview(fileUrl, ext, isOffice));
                });
                return;
            }

            modal.removeClass('pdf-js-preview');
            modalBody.html(buildIframePreview(fileUrl, ext, isOffice));
        });

        return false;
    };

    function initFullscreenToggle() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initFullscreenToggle, 100);
            return;
        }

        var $ = jQuery;

        $(document).on('click', '#toggleFullscreen', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var modal = $('#previewModal');
            var icon = $('#fullscreenIcon');

            if (modal.hasClass('pdf-js-preview')) {
                return;
            }

            if (modal.hasClass('fullscreen')) {
                modal.removeClass('fullscreen');
                icon.removeClass('fa-compress').addClass('fa-expand');
            } else {
                modal.addClass('fullscreen');
                icon.removeClass('fa-expand').addClass('fa-compress');
            }
        });

        $('#previewModal').on('hidden.bs.modal', function () {
            var modal = $(this);
            modal.removeClass('fullscreen pdf-js-preview');
            $('#fullscreenIcon').removeClass('fa-compress').addClass('fa-expand');
            $('#previewModalBody').html(previewLoadingHtml());
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFullscreenToggle);
    } else {
        initFullscreenToggle();
    }
})();
</script>
