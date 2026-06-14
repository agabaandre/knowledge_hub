    #previewModal .modal-content,
    #previewModal .modal-header {
        border-radius: 0 !important;
    }

    #previewModal .modal-dialog {
        max-width: 100vw;
        width: 100vw;
        margin: 0;
        padding: 0;
        transition: all 0.3s ease;
    }

    #previewModal.fullscreen .modal-dialog {
        max-width: 100vw;
        width: 100vw;
        height: 100vh;
        margin: 0;
        padding: 0;
    }

    #previewModal.fullscreen .modal-content {
        height: 100vh;
        border-radius: 0;
    }

    #previewModal.fullscreen .modal-body {
        max-height: calc(100vh - 60px);
        height: calc(100vh - 60px);
    }

    @media (min-width: 1200px) {
        #previewModal:not(.fullscreen) .modal-dialog {
            max-width: 100vw;
            width: 100vw;
        }
    }

    #previewModal .modal-content {
        border-radius: 0;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        background: #ffffff;
        width: 100%;
        height: 100vh;
    }

    #previewModal .modal-header {
        background: linear-gradient(135deg, #119A48 0%, #0e7a3a 100%);
        color: white;
        border-radius: 0;
        border: none;
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
    }

    #previewModal .modal-header .header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex: 1;
        min-width: 0;
    }

    #previewModal .modal-header .header-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-shrink: 0;
    }

    #previewModal .btn-fullscreen {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 0.4rem 0.6rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    #previewModal .btn-fullscreen:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
    }

    #previewModal .modal-title {
        font-weight: 600;
        font-size: 1.1rem;
        color: white;
        margin: 0;
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    #previewModal .close {
        color: white;
        opacity: 0.9;
        text-shadow: none;
        font-weight: 300;
        font-size: 1.5rem;
        padding: 0.5rem;
        line-height: 1;
    }

    #previewModal .close:hover {
        opacity: 1;
        color: white;
    }

    #previewModal .close:focus {
        outline: none;
    }

    #previewModal .modal-body {
        min-height: calc(100vh - 60px);
        height: calc(100vh - 60px);
        max-height: calc(100vh - 60px);
        overflow: hidden;
        padding: 0;
        background: #f8fafc;
        display: flex;
        align-items: stretch;
        justify-content: center;
        position: relative;
        width: 100%;
    }

    #previewModalBody {
        width: 100%;
        height: 100%;
        min-height: calc(100vh - 60px);
        background: #ffffff;
        display: flex;
        align-items: stretch;
        justify-content: center;
        position: relative;
        opacity: 1;
        transition: opacity 0.15s ease-in-out;
    }

    #previewModalBody iframe {
        width: 100%;
        height: 100%;
        min-height: calc(100vh - 60px);
        border: none;
        border-radius: 0;
        background: #ffffff;
        display: block;
    }

    #previewModalBody img {
        max-width: 100%;
        max-height: 100%;
        height: auto;
        border-radius: 0.25rem;
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        margin: auto;
        display: block;
    }

    #previewModal.fullscreen #previewModalBody img {
        max-height: calc(100vh - 120px);
    }

    #previewModal.fullscreen #previewModalBody iframe {
        min-height: calc(100vh - 120px);
        height: calc(100vh - 120px);
    }

    #previewModalBody .alert {
        margin: 2rem;
        text-align: center;
    }

    #previewModalBody .text-center {
        padding: 3rem;
    }

    /* Mobile: scroll all PDF pages via PDF.js canvas stack */
    #previewModal.pdf-js-preview .modal-body,
    #previewModal.pdf-js-preview #previewModalBody {
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        display: block;
        height: calc(100vh - 56px);
        max-height: calc(100vh - 56px);
        min-height: calc(100vh - 56px);
    }

    .pdf-js-scroll-preview {
        width: 100%;
        padding: 0.5rem 0 1rem;
        background: #525659;
    }

    .pdf-js-scroll-preview__page {
        display: block;
        width: 100% !important;
        height: auto !important;
        max-width: 100%;
        margin: 0 auto 0.75rem;
        background: #fff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.18);
    }

    .pdf-js-scroll-preview__hint {
        color: rgba(255, 255, 255, 0.85) !important;
        padding: 0.5rem 1rem 1rem;
    }

    @media (max-width: 768px) {
        #previewModal .modal-dialog {
            max-width: 100vw;
            width: 100vw;
            margin: 0;
        }

        #previewModal:not(.pdf-js-preview) .modal-body,
        #previewModal:not(.pdf-js-preview) #previewModalBody {
            min-height: calc(100vh - 56px);
            height: calc(100vh - 56px);
            max-height: calc(100vh - 56px);
        }

        #previewModal:not(.pdf-js-preview) #previewModalBody iframe {
            min-height: calc(100vh - 56px);
            height: calc(100vh - 56px);
        }
    }
