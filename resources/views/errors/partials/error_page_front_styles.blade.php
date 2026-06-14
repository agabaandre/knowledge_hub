<style>
    .front-error-page {
        background: #f4f5f7;
        padding: 2rem 0 3rem;
        min-height: 50vh;
    }
    .front-error-card {
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        background: #fff;
        box-shadow: 0 4px 24px rgba(15, 23, 42, 0.06);
        max-width: 720px;
        margin: 0 auto;
    }
    .front-error-card .card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f0f7f4 100%);
        border-bottom: 1px solid #e2e8f0;
        padding: 1.25rem 1.5rem;
    }
    .front-error-card .card-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    .front-error-card .card-body {
        padding: 2rem 1.5rem;
        text-align: center;
    }
    .front-error-icon {
        font-size: 3rem;
        color: var(--theme-color-primary, {{ settings()->au_corporate_green ?? '#1A5632' }});
        margin-bottom: 1rem;
    }
    .front-error-message {
        font-size: 1.05rem;
        color: {{ settings()->au_grey_text ?? '#58595B' }};
        margin-bottom: 0.75rem;
        line-height: 1.6;
    }
    .front-error-hint {
        font-size: 0.9rem;
        color: #64748b;
        margin-bottom: 1.5rem;
    }
    .front-error-hint a {
        color: var(--theme-color-primary, {{ settings()->au_corporate_green ?? '#1A5632' }});
    }
    .front-error-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        justify-content: center;
    }
    .front-error-actions .btn-primary {
        background: var(--theme-color-primary, {{ settings()->au_corporate_green ?? '#1A5632' }});
        border-color: var(--theme-color-primary, {{ settings()->au_corporate_green ?? '#1A5632' }});
    }
    .front-error-actions .btn-primary:hover {
        opacity: 0.92;
    }
</style>
