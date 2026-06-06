<style>
    .pub-filters-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-top: 3px solid var(--theme-color-primary, #119A48);
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
        margin-bottom: 1.5rem;
    }

    .pub-filters-card__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        padding: 1rem 1.25rem;
        background: linear-gradient(180deg, #fafbfc 0%, #f4f7f6 100%);
        border-bottom: 1px solid #e2e8f0;
    }

    .pub-filters-card__heading {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        min-width: 0;
    }

    .pub-filters-card__icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(17, 154, 72, 0.12);
        color: var(--theme-color-primary, #119A48);
        flex-shrink: 0;
        font-size: 1rem;
    }

    .pub-filters-card__title {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
    }

    .pub-filters-card__subtitle {
        margin: 0.2rem 0 0;
        font-size: 0.8125rem;
        color: #64748b;
    }

    .pub-filters-advanced-toggle {
        border: 1px solid #cbd5e1;
        background: #fff;
        color: #334155;
        font-size: 0.8125rem;
        font-weight: 600;
        padding: 0.45rem 0.9rem;
        border-radius: 999px;
        transition: all 0.15s ease;
        white-space: nowrap;
    }

    .pub-filters-advanced-toggle:hover,
    .pub-filters-advanced-toggle[aria-expanded="true"] {
        background: rgba(17, 154, 72, 0.08);
        border-color: rgba(17, 154, 72, 0.35);
        color: var(--theme-color-primary, #119A48);
    }

    .pub-filters-card__body {
        padding: 1.25rem;
    }

    .pub-filters-grid {
        display: grid;
        gap: 1rem;
    }

    .pub-filters-grid--primary {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .pub-filters-grid--duo {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .pub-filters-grid--single {
        grid-template-columns: minmax(0, 1fr);
    }

    @media (max-width: 991px) {
        .pub-filters-grid--primary,
        .pub-filters-grid--duo {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575px) {
        .pub-filters-grid--primary,
        .pub-filters-grid--duo {
            grid-template-columns: 1fr;
        }
    }

    .pub-filter-field {
        margin: 0;
    }

    .pub-filter-label {
        display: block;
        margin-bottom: 0.35rem;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #475569;
    }

    .pub-filters-card .form-control,
    .pub-filters-card .select2-container--default .select2-selection--single {
        min-height: 42px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #fff;
        font-size: 0.875rem;
        color: #334155;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .pub-filters-card .form-control {
        padding: 0.5rem 0.85rem;
    }

    .pub-filters-card .form-control:focus {
        border-color: var(--theme-color-primary, #119A48);
        box-shadow: 0 0 0 3px rgba(17, 154, 72, 0.12);
    }

    .pub-filters-card .form-control::placeholder {
        color: #94a3b8;
    }

    .pub-filters-card .select2-container--default .select2-selection--single {
        display: flex;
        align-items: center;
        padding: 0 0.5rem;
    }

    .pub-filters-card .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #334155;
        line-height: 40px;
        padding-left: 0.35rem;
    }

    .pub-filters-card .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 6px;
    }

    .pub-filters-card .select2-container--default.select2-container--focus .select2-selection--single,
    .pub-filters-card .select2-container--default.select2-container--open .select2-selection--single {
        border-color: var(--theme-color-primary, #119A48);
        box-shadow: 0 0 0 3px rgba(17, 154, 72, 0.12);
    }

    .pub-filters-advanced {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px dashed #dbe3ea;
    }

    .pub-filters-advanced__label {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        margin-bottom: 0.85rem;
        padding: 0.25rem 0.65rem;
        border-radius: 999px;
        background: rgba(17, 154, 72, 0.08);
        color: var(--theme-color-primary, #119A48);
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }

    .pub-filters-advanced .row {
        margin-left: -0.5rem;
        margin-right: -0.5rem;
    }

    .pub-filters-advanced .row > [class*="col-"] {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }

    .pub-filters-advanced .form-group {
        margin-bottom: 0.75rem;
    }

    .pub-filters-advanced .form-label-sm,
    .pub-filters-advanced label small {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #475569;
    }

    .pub-filters-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #eef2f7;
    }

    .pub-filters-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.8125rem;
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        border: 1px solid transparent;
        transition: all 0.15s ease;
    }

    .pub-filters-btn--clear {
        background: #fff;
        border-color: #cbd5e1;
        color: #475569;
    }

    .pub-filters-btn--clear:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
        text-decoration: none;
    }

    .pub-filters-btn--export {
        background: var(--theme-color-primary, #119A48);
        border-color: var(--theme-color-primary, #119A48);
        color: #fff;
    }

    .pub-filters-btn--export:hover {
        background: #0d7d3a;
        border-color: #0d7d3a;
        color: #fff;
    }

    .pub-list-card {
        border: 1px solid #e2e8f0;
        border-top: 3px solid var(--theme-color-primary, #119A48);
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
        margin-bottom: 1.5rem;
    }

    .pub-list-card .card-header {
        background: linear-gradient(180deg, #fafbfc 0%, #f4f7f6 100%);
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 1.25rem;
    }
</style>
