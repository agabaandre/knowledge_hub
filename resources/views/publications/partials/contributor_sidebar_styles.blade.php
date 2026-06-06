@php $primary = settings()->primary_color ?? '#119A48'; @endphp
<style>
    .contributor-sidebar-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.25rem 1.35rem;
        box-shadow: 0 2px 12px rgba(15, 23, 42, 0.04);
    }
    .contributor-sidebar-panel__header {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        margin-bottom: 0.5rem;
    }
    .contributor-sidebar-panel__icon {
        width: 2rem;
        height: 2rem;
        border-radius: 8px;
        background: rgba(17, 154, 72, 0.1);
        color: {{ $primary }};
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
    }
    .contributor-sidebar-panel__title {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        line-height: 1.3;
    }
    .contributor-sidebar-panel__lead {
        font-size: 0.8125rem;
        color: #64748b;
        margin: 0 0 1rem;
        line-height: 1.5;
    }
    .contributor-communities-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .contributor-communities-item__link {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
        padding: 0.75rem 0.85rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        text-decoration: none;
        background: #f8fafc;
        transition: border-color 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
    }
    .contributor-communities-item__link:hover {
        border-color: rgba(17, 154, 72, 0.35);
        background: #fff;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
    }
    .contributor-communities-item__name {
        font-size: 0.9rem;
        font-weight: 600;
        color: #0f172a;
        line-height: 1.35;
    }
    .contributor-communities-item__meta {
        font-size: 0.75rem;
        color: #64748b;
    }
    .contributor-facts-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    .contributor-fact-card {
        padding: 0.9rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }
    .contributor-fact-card__title {
        font-size: 0.9rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 0.4rem;
        line-height: 1.35;
    }
    .contributor-fact-card__summary {
        font-size: 0.8125rem;
        color: #475569;
        margin: 0 0 0.65rem;
        line-height: 1.5;
    }
    .contributor-fact-card__link {
        font-size: 0.8125rem;
        font-weight: 600;
        color: {{ $primary }};
        text-decoration: none;
    }
    .contributor-fact-card__link:hover {
        text-decoration: underline;
    }
</style>
