@php $primary = settings()->primary_color ?? '#119A48'; @endphp
    .records-search-sidebar {
        position: sticky;
        top: 5.5rem;
        align-self: flex-start;
        max-height: calc(100vh - 5.5rem - 1.25rem);
        overflow-y: auto;
        overflow-x: hidden;
        overscroll-behavior: contain;
        scrollbar-width: thin;
        scrollbar-color: rgba(15, 23, 42, 0.22) transparent;
        padding-right: 0.2rem;
        padding-bottom: 1rem;
    }

    .records-search-sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .records-search-sidebar::-webkit-scrollbar-thumb {
        background: rgba(15, 23, 42, 0.2);
        border-radius: 999px;
    }

    .records-search-sidebar::-webkit-scrollbar-track {
        background: transparent;
    }

    .records-sidebar-card {
        background: #fff;
        border: 1px solid #e8edf2;
        border-radius: 1rem;
        padding: 1.1rem 1.15rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05), 0 8px 24px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }

    .records-sidebar-card__head {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        margin-bottom: 0.85rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #eef2f6;
    }

    .records-sidebar-card__icon {
        flex-shrink: 0;
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 0.65rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
    }

    .records-sidebar-card__icon--tags {
        background: linear-gradient(135deg, #dcfce7, #ecfdf5);
        color: #047857;
    }

    .records-sidebar-card__icon--filters {
        background: linear-gradient(135deg, #dbeafe, #eff6ff);
        color: #1d4ed8;
    }

    .records-sidebar-card__icon--forums {
        background: linear-gradient(135deg, #ffedd5, #fef3c7);
        color: #c2410c;
    }

    .records-sidebar-card__icon--latest {
        background: linear-gradient(135deg, #f3e8ff, #ede9fe);
        color: #6d28d9;
    }

    .records-sidebar-card__title {
        font-size: 0.9375rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.01em;
        margin: 0;
        line-height: 1.3;
    }

    .records-sidebar-card__hint {
        font-size: 0.75rem;
        color: #64748b;
        margin: 0.15rem 0 0;
        line-height: 1.4;
    }

    .records-sidebar-tags {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .records-sidebar-tags__link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.45rem 0.65rem;
        border-radius: 0.65rem;
        border: 1px solid #eef2f6;
        background: #f8fafc;
        text-decoration: none;
        transition: background 0.2s ease, border-color 0.2s ease, transform 0.15s ease;
    }

    .records-sidebar-tags__link:hover {
        background: #fff;
        border-color: rgba(17, 154, 72, 0.25);
        text-decoration: none;
        transform: translateX(2px);
    }

    .records-sidebar-tags__link.is-active {
        background: linear-gradient(135deg, #ecfdf5, #f0fdf4);
        border-color: rgba(17, 154, 72, 0.35);
        box-shadow: inset 0 0 0 1px rgba(17, 154, 72, 0.08);
    }

    .records-sidebar-tags__label {
        font-size: 0.8125rem;
        font-weight: 700;
        color: #0f172a;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .records-sidebar-tags__label-hash {
        color: {{ $primary }};
        margin-right: 0.15rem;
    }

    .records-sidebar-tags__count {
        flex-shrink: 0;
        min-width: 1.65rem;
        padding: 0.1rem 0.45rem;
        border-radius: 999px;
        background: #fff;
        border: 1px solid #e2e8f0;
        font-size: 0.6875rem;
        font-weight: 800;
        color: #64748b;
        text-align: center;
        font-variant-numeric: tabular-nums;
    }

    .records-sidebar-tags__link.is-active .records-sidebar-tags__count {
        background: {{ $primary }};
        border-color: {{ $primary }};
        color: #fff;
    }

    .records-sidebar-tags__clear {
        display: inline-block;
        margin-top: 0.65rem;
        color: {{ $primary }};
        font-weight: 600;
        font-size: 0.8125rem;
        text-decoration: none;
    }

    .records-sidebar-tags__clear:hover {
        text-decoration: underline;
    }

    .records-facet-group + .records-facet-group {
        margin-top: 1rem;
        padding-top: 0.85rem;
        border-top: 1px dashed #e2e8f0;
    }

    .records-facet-group__head {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.55rem;
    }

    .records-facet-group__icon {
        width: 1.65rem;
        height: 1.65rem;
        border-radius: 0.45rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.72rem;
        flex-shrink: 0;
    }

    .records-facet-group__icon--file {
        background: linear-gradient(135deg, #fee2e2, #ffedd5);
        color: #b45309;
    }

    .records-facet-group__icon--category {
        background: linear-gradient(135deg, #dcfce7, #ecfdf5);
        color: #047857;
    }

    .records-facet-group__icon--subcategory {
        background: linear-gradient(135deg, #e0e7ff, #eef2ff);
        color: #4338ca;
    }

    .records-facet-group__title {
        font-size: 0.8125rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        letter-spacing: -0.01em;
    }

    .records-facet-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
    }

    .records-facet-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.32rem 0.62rem;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        background: #fff;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 600;
        line-height: 1.25;
        cursor: pointer;
        transition: all 0.15s ease;
        margin: 0;
    }

    .records-facet-chip:hover {
        border-color: rgba(17, 154, 72, 0.28);
        background: #f8fafc;
    }

    .records-facet-chip input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
    }

    .records-facet-chip span {
        pointer-events: none;
    }

    .records-facet-chip:has(input:checked) {
        background: {{ $primary }};
        border-color: {{ $primary }};
        color: #fff;
        box-shadow: 0 2px 8px rgba(17, 154, 72, 0.22);
    }

    .records-search-sidebar .search-sidebar-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .records-search-sidebar .search-sidebar-list__link {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        padding: 0.75rem 0.85rem;
        border: 1px solid #eef2f6;
        border-radius: 0.75rem;
        text-decoration: none;
        background: #f8fafc;
        transition: border-color 0.15s ease, background 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
    }

    .records-search-sidebar .search-sidebar-list__link:hover {
        border-color: rgba(17, 154, 72, 0.28);
        background: linear-gradient(135deg, rgba(17, 154, 72, 0.05), rgba(59, 130, 246, 0.04));
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
        transform: translateX(2px);
        text-decoration: none;
    }

    .records-search-sidebar .search-sidebar-list__title {
        font-size: 0.8125rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.35;
    }

    .records-search-sidebar .search-sidebar-list__excerpt {
        font-size: 0.75rem;
        color: #64748b;
        line-height: 1.45;
    }

    .records-search-sidebar .search-sidebar-list__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem 0.75rem;
        font-size: 0.6875rem;
        color: #94a3b8;
    }

    .records-search-sidebar .search-sidebar-list__meta i {
        color: {{ $primary }};
        margin-right: 0.2rem;
    }

    .records-search-sidebar .search-sidebar-view-all {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        margin-top: 0.85rem;
        font-size: 0.8125rem;
        font-weight: 700;
        color: {{ $primary }};
        text-decoration: none;
    }

    .records-search-sidebar .search-sidebar-view-all:hover {
        text-decoration: underline;
    }

    @media (max-width: 991.98px) {
        .records-search-sidebar {
            position: static;
            top: auto;
            max-height: none;
            overflow: visible;
            padding-right: 0;
            padding-bottom: 0;
        }
    }
