<style>
    .publication-list-card,
    .publication-list-card a,
    .publication-list-card .publication-image-link { pointer-events: auto !important; }
    .publication-list-card a { cursor: pointer; }
    .publication-list-card .publication-content-col a:not(.btn) {
        color: var(--default-font-color, #212529) !important;
        text-decoration: none !important;
    }
    .publication-list-card .publication-content-col a:not(.btn):hover {
        color: var(--theme-color-primary, #119A48) !important;
        text-decoration: underline !important;
    }
    .publication-list-card .publication-title-desktop a,
    .publication-list-card .publication-title-mobile a {
        color: var(--default-font-color, #212529) !important;
        text-decoration: none !important;
    }
    .publication-list-card .publication-title-desktop a:hover,
    .publication-list-card .publication-title-mobile a:hover {
        color: var(--theme-color-primary, #119A48) !important;
        text-decoration: underline !important;
    }
    @media (max-width: 767.98px) {
        .publication-card-row {
            display: block !important;
            flex-direction: unset !important;
            flex-wrap: unset !important;
        }
        .publication-title-mobile { display: none !important; }
        .publication-image-col {
            width: 120px !important;
            height: 120px !important;
            flex: none !important;
            max-width: 120px !important;
            padding-right: 12px !important;
            padding-left: 0 !important;
            margin-right: 12px !important;
            margin-bottom: 8px !important;
            float: left !important;
            border: none !important;
            overflow: hidden !important;
            background: transparent !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            position: relative !important;
            shape-outside: margin-box !important;
        }
        .publication-image-link { width: 100% !important; height: 100% !important; display: block !important; }
        .publication-image {
            min-height: 120px !important;
            height: 120px !important;
            width: 100% !important;
            object-fit: contain !important;
            object-position: center !important;
            background: transparent !important;
        }
        .publication-content-col {
            width: auto !important;
            flex: none !important;
            max-width: 100% !important;
            padding-left: 0 !important;
            display: block !important;
            overflow: visible !important;
            text-align: justify !important;
        }
        .publication-title-desktop {
            display: block !important;
            text-align: left !important;
            overflow-wrap: break-word !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
            white-space: normal !important;
            line-height: 1.4 !important;
            font-size: 1rem !important;
            margin-bottom: 0.5rem !important;
        }
        .publication-card-row::after { content: ""; display: table; clear: both; }
    }
    @media (min-width: 768px) {
        .publication-title-mobile { display: none !important; }
        .publication-title-desktop { display: block; }
    }
</style>
