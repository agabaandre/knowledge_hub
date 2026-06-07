<style>
    .learning-admin-page {
        --la-primary: var(--theme-color-primary, #119A48);
        --la-primary-soft: color-mix(in srgb, var(--la-primary) 12%, white);
        --la-border: #e2e8f0;
        --la-muted: #64748b;
        --la-text: #0f172a;
    }
    .learning-admin-page .learning-hero {
        background: linear-gradient(135deg, #f8fafc 0%, var(--la-primary-soft) 100%);
        border: 1px solid var(--la-border);
        border-radius: 0.75rem;
    }
    .learning-admin-page .learning-hero h3 {
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--la-text);
        margin: 0 0 0.5rem;
    }
    .learning-admin-page .learning-hero p {
        color: var(--la-muted);
        margin: 0;
        max-width: 52rem;
    }
    .learning-admin-page .learning-stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.8125rem;
        color: #475569;
        background: #fff;
        border: 1px solid var(--la-border);
        border-radius: 999px;
        padding: 0.3rem 0.7rem;
    }
    .learning-admin-page .learning-section-card {
        border: 1px solid var(--la-border);
        border-radius: 0.75rem;
        overflow: hidden;
        background: #fff;
        margin-bottom: 1.25rem;
    }
    .learning-admin-page .learning-section-card .section-head {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        background: #f8fafc;
    }
    .learning-admin-page .learning-section-card .section-head h4,
    .learning-admin-page .learning-section-card .section-head h2 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--la-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .learning-admin-page .section-head .platform-icon {
        width: 30px;
        height: 30px;
        font-size: 0.85rem;
        margin-right: 0;
    }
    .learning-admin-page .learning-section-card .section-head p {
        margin: 0.25rem 0 0;
        font-size: 0.8125rem;
        color: var(--la-muted);
    }
    .learning-admin-page .learning-section-card .section-body {
        padding: 1.25rem;
    }
    .learning-admin-page .learning-subnav .nav-link {
        font-weight: 600;
        color: #64748b;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 0.75rem 1rem;
    }
    .learning-admin-page .learning-subnav .nav-link:hover {
        color: var(--la-primary);
        border-bottom-color: color-mix(in srgb, var(--la-primary) 35%, transparent);
    }
    .learning-admin-page .learning-subnav .nav-link.active {
        color: var(--la-primary);
        background: transparent;
        border-bottom-color: var(--la-primary);
    }
    .learning-admin-page .platform-card {
        border: 1px solid var(--la-border);
        border-radius: 0.65rem;
        margin-bottom: 1rem;
        overflow: hidden;
        background: #fff;
    }
    .learning-admin-page .platform-card:last-child { margin-bottom: 0; }
    .learning-admin-page .platform-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.9rem 1.1rem;
        border-bottom: 1px solid #f1f5f9;
        background: #fff;
    }
    .learning-admin-page .platform-card-head h5 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--la-text);
    }
    .learning-admin-page .platform-icon {
        width: 38px;
        height: 38px;
        border-radius: 0.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        background: var(--la-primary);
        margin-right: 0.65rem;
    }
    .learning-admin-page .feature-row {
        display: grid;
        grid-template-columns: minmax(200px, 1.2fr) minmax(180px, 1fr) auto;
        gap: 1rem;
        align-items: center;
        padding: 0.85rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .learning-admin-page .feature-row:last-child { border-bottom: none; padding-bottom: 0; }
    .learning-admin-page .feature-title { font-weight: 600; color: var(--la-text); margin-bottom: 0.15rem; }
    .learning-admin-page .feature-desc { font-size: 0.8rem; color: var(--la-muted); margin: 0; }
    .learning-admin-page .status-pill {
        display: inline-flex;
        align-items: center;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 0.25rem 0.55rem;
        border-radius: 999px;
        white-space: nowrap;
    }
    .learning-admin-page .status-pill.ready { background: #dcfce7; color: #166534; }
    .learning-admin-page .status-pill.incomplete { background: #fef3c7; color: #92400e; }
    .learning-admin-page .status-pill.disabled { background: #f1f5f9; color: #64748b; }
    .learning-admin-page .status-pill.unavailable { background: #fee2e2; color: #991b1b; }
    .learning-admin-page .provider-icon {
        width: 40px;
        height: 40px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        flex-shrink: 0;
    }
    .learning-admin-page .cap-tag {
        font-size: 0.7rem;
        padding: 0.15rem 0.45rem;
        border-radius: 0.25rem;
        background: #f1f5f9;
        color: #475569;
    }
    .learning-admin-page .integration-row {
        border: 1px dashed #cbd5e1;
        border-radius: 0.65rem;
        padding: 1rem;
        margin-bottom: 1rem;
        background: #f8fafc;
        position: relative;
    }
    .learning-admin-page .integration-row .btn-remove {
        position: absolute;
        top: 0.65rem;
        right: 0.65rem;
    }
    .learning-admin-page .learning-save-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        padding-top: 1rem;
        margin-top: 0.5rem;
        border-top: 1px solid var(--la-border);
    }
    .learning-admin-page .form-label {
        font-weight: 600;
        font-size: 0.8125rem;
        color: #334155;
    }
    @media (max-width: 768px) {
        .learning-admin-page .feature-row { grid-template-columns: 1fr; }
    }
</style>
