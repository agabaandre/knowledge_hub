<style>
    .error-page-card {
        border: 1px solid #e2e8f0;
        border-radius: 0;
        margin-bottom: 1.5rem;
    }
    .error-page-card .card-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 1.5rem;
    }
    .error-page-card .card-body {
        padding: 2rem 1.5rem;
    }
    .error-page-body {
        text-align: center;
        max-width: 640px;
        margin: 0 auto;
    }
    .error-page-icon {
        font-size: 3rem;
        color: var(--theme-color-primary, {{ settings()->au_corporate_green ?? '#1A5632' }});
        margin-bottom: 1rem;
    }
    .error-page-message {
        font-size: 1.05rem;
        color: {{ settings()->au_grey_text ?? '#58595B' }};
        margin-bottom: 0.75rem;
        line-height: 1.6;
    }
    .error-page-hint {
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
    }
    .error-page-hint a {
        color: var(--theme-color-primary, {{ settings()->au_corporate_green ?? '#1A5632' }});
    }
    .error-page-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        justify-content: center;
    }
    .error-page-actions .btn-primary {
        background: var(--theme-color-primary, {{ settings()->au_corporate_green ?? '#1A5632' }});
        border-color: var(--theme-color-primary, {{ settings()->au_corporate_green ?? '#1A5632' }});
    }
    .error-page-actions .btn-primary:hover {
        opacity: 0.92;
    }
</style>
