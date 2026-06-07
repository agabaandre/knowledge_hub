<style>
    :root {
        --kh-table-primary: var(--theme-color-primary, #119A48);
        --kh-table-primary-soft: rgba(17, 154, 72, 0.08);
        --kh-table-primary-border: rgba(17, 154, 72, 0.18);
        --kh-table-surface: #ffffff;
        --kh-table-header: #f5f7fa;
        --kh-table-header-text: #475569;
        --kh-table-border: #e2e8f0;
        --kh-table-row-hover: rgba(17, 154, 72, 0.05);
        --kh-table-footer: #f8fafc;
        --kh-table-text: #334155;
        --kh-table-muted: #64748b;
    }

    /* Surface / card-like table container */
    .table,
    .dataTables_wrapper table.dataTable {
        background: var(--kh-table-surface);
        border: 1px solid var(--kh-table-border);
        border-top: 3px solid var(--kh-table-primary);
        border-radius: 0;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05), 0 4px 14px rgba(15, 23, 42, 0.04);
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 0;
    }

    .table thead th,
    .dataTables_wrapper table.dataTable thead th {
        background: linear-gradient(180deg, #fafbfc 0%, var(--kh-table-header) 100%);
        border-bottom: 1px solid var(--kh-table-border);
        box-shadow: inset 0 -2px 0 var(--kh-table-primary-border);
        color: var(--kh-table-header-text);
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        padding: 12px 14px;
        vertical-align: middle;
        white-space: normal;
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    .table tbody td,
    .dataTables_wrapper table.dataTable tbody td {
        border-top: 1px solid #eef2f7;
        color: var(--kh-table-text);
        padding: 12px 14px;
        vertical-align: top;
        font-size: 0.875rem;
        line-height: 1.45;
        transition: background-color 0.15s ease;
        white-space: normal !important;
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    /* DataTables adds nowrap in some modes — force wrapping on wrap-enabled tables */
    table.dataTable.nowrap th,
    table.dataTable.nowrap td,
    table.dataTable td.dt-nowrap,
    table.dataTable th.dt-nowrap {
        white-space: normal !important;
    }

    table.kh-table-wrap-cells.nowrap th,
    table.kh-table-wrap-cells.nowrap td {
        white-space: normal !important;
    }

    .table tbody tr:last-child td,
    .dataTables_wrapper table.dataTable tbody tr:last-child td {
        border-bottom: none;
    }

    .table.table-sm thead th,
    .table.table-sm tbody td,
    .dataTables_wrapper table.dataTable.table-sm thead th,
    .dataTables_wrapper table.dataTable.table-sm tbody td {
        padding: 10px 12px;
    }

    .table-hover tbody tr:hover,
    .dataTables_wrapper table.dataTable tbody tr:hover {
        background: var(--kh-table-row-hover) !important;
    }

    .table-striped tbody tr:nth-of-type(odd),
    .dataTables_wrapper table.dataTable.stripe tbody tr:nth-of-type(odd),
    .dataTables_wrapper table.dataTable.display tbody tr:nth-of-type(odd) {
        background-color: #fcfdfd;
    }

    .table-striped tbody tr:nth-of-type(even),
    .dataTables_wrapper table.dataTable.stripe tbody tr:nth-of-type(even),
    .dataTables_wrapper table.dataTable.display tbody tr:nth-of-type(even) {
        background-color: #ffffff;
    }

    .table-striped tbody tr:nth-of-type(odd):hover,
    .dataTables_wrapper table.dataTable.display tbody tr:nth-of-type(odd):hover {
        background-color: var(--kh-table-row-hover) !important;
    }

    /* Selection checkboxes */
    .table input[type="checkbox"],
    .dataTables_wrapper input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: var(--kh-table-primary);
        cursor: pointer;
        vertical-align: middle;
    }

    /* Hide DataTables default pseudo-element arrows (theme uses background-image icons) */
    .dataTables_wrapper table.dataTable thead > tr > th.sorting:before,
    .dataTables_wrapper table.dataTable thead > tr > th.sorting:after,
    .dataTables_wrapper table.dataTable thead > tr > th.sorting_asc:before,
    .dataTables_wrapper table.dataTable thead > tr > th.sorting_asc:after,
    .dataTables_wrapper table.dataTable thead > tr > th.sorting_desc:before,
    .dataTables_wrapper table.dataTable thead > tr > th.sorting_desc:after,
    .dataTables_wrapper table.dataTable thead > tr > th.sorting_asc_disabled:before,
    .dataTables_wrapper table.dataTable thead > tr > th.sorting_asc_disabled:after,
    .dataTables_wrapper table.dataTable thead > tr > th.sorting_desc_disabled:before,
    .dataTables_wrapper table.dataTable thead > tr > th.sorting_desc_disabled:after {
        display: none !important;
        content: none !important;
    }

    /* Sortable headers */
    .dataTables_wrapper table.dataTable thead .sorting,
    .dataTables_wrapper table.dataTable thead .sorting_asc,
    .dataTables_wrapper table.dataTable thead .sorting_desc,
    .dataTables_wrapper table.dataTable thead .sorting_asc_disabled,
    .dataTables_wrapper table.dataTable thead .sorting_desc_disabled {
        cursor: pointer;
        padding-right: 28px;
        background-repeat: no-repeat;
        background-position: center right 10px;
        background-size: 14px 14px;
    }

    .dataTables_wrapper table.dataTable thead .sorting {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m7 15 5 5 5-5'/%3E%3Cpath d='m7 9 5-5 5 5'/%3E%3C/svg%3E");
    }

    .dataTables_wrapper table.dataTable thead .sorting_asc {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23119A48' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m18 15-6-6-6 6'/%3E%3C/svg%3E");
    }

    .dataTables_wrapper table.dataTable thead .sorting_desc {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23119A48' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
    }

    /* DataTables wrapper layout */
    .dataTables_wrapper {
        color: var(--kh-table-text);
    }

    .kh-dt-toolbar {
        padding: 4px 2px 0;
    }

    .kh-dt-toolbar .dataTables_length label {
        margin-bottom: 0;
    }

    .dataTables_wrapper .row {
        margin-left: 0;
        margin-right: 0;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        padding: 12px 4px;
    }

    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_filter label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 0;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--kh-table-muted);
    }

    .dataTables_wrapper .dataTables_filter input,
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        background: #fff;
        color: var(--kh-table-text);
        font-size: 0.875rem;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .dataTables_wrapper .dataTables_filter input {
        padding: 8px 12px;
        min-width: 220px;
    }

    .dataTables_wrapper .dataTables_filter input:focus,
    .dataTables_wrapper .dataTables_length select:focus {
        outline: none;
        border-color: var(--kh-table-primary);
        box-shadow: 0 0 0 3px var(--kh-table-primary-soft);
    }

    .dataTables_wrapper .dataTables_length select {
        padding: 7px 34px 7px 12px;
        margin: 0 4px;
    }

    /* Footer bar: info + pagination (Vuetify footer style) */
    .dataTables_wrapper .dataTables_info {
        padding: 14px 16px;
        color: var(--kh-table-muted);
        font-size: 0.8125rem;
        font-weight: 500;
        float: none;
        display: inline-flex;
        align-items: center;
    }

    .dataTables_wrapper .dataTables_paginate {
        padding: 10px 12px;
        display: inline-flex;
        align-items: center;
        justify-content: flex-end;
        gap: 4px;
        float: none;
        flex-wrap: wrap;
    }

    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        background: var(--kh-table-footer);
        border-top: 1px solid var(--kh-table-border);
    }

    /* Unified footer row when info + paginate are siblings */
    .dataTables_wrapper > .dataTables_info + .dataTables_paginate,
    .dataTables_wrapper > .dataTables_paginate {
        margin-top: 0;
    }

    .dataTables_wrapper::after {
        content: "";
        display: block;
        clear: both;
    }

    /* Pagination buttons */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border: 1px solid transparent !important;
        border-radius: 6px !important;
        margin: 0 !important;
        min-width: 36px;
        height: 36px;
        line-height: 1;
        padding: 0 12px !important;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        background: transparent !important;
        color: var(--kh-table-muted) !important;
        font-size: 0.8125rem;
        font-weight: 600;
        transition: background-color 0.15s ease, color 0.15s ease, box-shadow 0.15s ease;
        box-sizing: border-box;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled):not(.current) {
        background: var(--kh-table-primary-soft) !important;
        color: var(--kh-table-primary) !important;
        border-color: var(--kh-table-primary-border) !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: var(--kh-table-primary) !important;
        color: #fff !important;
        border-color: var(--kh-table-primary) !important;
        box-shadow: 0 2px 6px rgba(17, 154, 72, 0.28);
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        opacity: 0.45 !important;
        background: transparent !important;
        color: #94a3b8 !important;
        border-color: transparent !important;
        cursor: not-allowed !important;
        box-shadow: none !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.previous,
    .dataTables_wrapper .dataTables_paginate .paginate_button.next {
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    /* Processing overlay */
    .dataTables_wrapper .dataTables_processing {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: auto;
        min-width: 180px;
        margin: 0;
        padding: 14px 22px;
        background: rgba(255, 255, 255, 0.96);
        border: 1px solid var(--kh-table-border);
        border-radius: 0;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
        color: var(--kh-table-primary);
        font-size: 0.875rem;
        font-weight: 600;
        z-index: 10;
    }

    /* Empty / zero records */
    .dataTables_wrapper .dataTables_empty {
        padding: 36px 16px !important;
        text-align: center !important;
        color: var(--kh-table-muted) !important;
        font-size: 0.9375rem;
        background: #fafbfc;
    }

    /* Action buttons inside tables */
    .table .btn-sm,
    .dataTables_wrapper .btn-sm {
        border-radius: 6px;
        font-weight: 600;
        padding: 0.25rem 0.5rem;
    }

    .table .btn-outline-primary,
    .dataTables_wrapper .btn-outline-primary {
        border-color: var(--kh-table-primary-border);
        color: var(--kh-table-primary);
    }

    .table .btn-outline-primary:hover,
    .dataTables_wrapper .btn-outline-primary:hover {
        background: var(--kh-table-primary-soft);
        border-color: var(--kh-table-primary);
        color: var(--kh-table-primary);
    }

    .kh-table-wrap-cells {
        table-layout: fixed !important;
        width: 100% !important;
    }

    .kh-table-wrap-cells thead th,
    .kh-table-wrap-cells tbody td {
        white-space: normal !important;
        word-break: break-word;
        overflow-wrap: anywhere;
        hyphens: auto;
        vertical-align: top;
    }

    .kh-table-wrap-cells tbody td > *,
    .kh-table-wrap-cells tbody td a {
        white-space: normal !important;
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    .kh-table-wrap-cells .pub-col-title,
    .kh-table-wrap-cells .pub-col-description,
    .kh-table-wrap-cells .pub-col-author,
    .kh-table-wrap-cells .pub-col-affiliation,
    .kh-table-wrap-cells .pub-col-moderator {
        max-width: 1px;
    }

    .kh-table-wrap-cells .kh-col-actions,
    .kh-table-wrap-cells .pub-col-actions,
    .kh-table-wrap-cells .pub-col-checkbox,
    .kh-table-wrap-cells .pub-col-index,
    .kh-table-wrap-cells .pub-col-status,
    .kh-table-wrap-cells .pub-col-date,
    .kh-table-wrap-cells .pub-col-member-state {
        max-width: none;
        white-space: nowrap !important;
        vertical-align: middle;
    }

    .pub-cell-wrap {
        display: block;
        white-space: normal !important;
        word-break: break-word;
        overflow-wrap: anywhere;
        line-height: 1.4;
    }

    /* Responsive wrapper */
    .table-responsive,
    .publication-table-wrap,
    .kh-table-wrap {
        overflow-x: visible;
        max-width: 100%;
    }

    .table-responsive > .table,
    .kh-table-wrap > .table,
    .kh-table-wrap > .dataTables_wrapper > table {
        border-radius: 0;
    }

    /* Footer toolbar helper used by server-side tables */
    .kh-dt-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
        background: var(--kh-table-footer);
        border: 1px solid var(--kh-table-border);
        border-top: none;
        border-radius: 0;
        padding: 4px 8px;
    }

    .kh-dt-footer .dataTables_info,
    .kh-dt-footer .dataTables_paginate {
        background: transparent;
        border-top: none;
        padding: 8px;
    }

    @media (max-width: 767px) {
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            width: 100%;
            text-align: left;
        }

        .dataTables_wrapper .dataTables_filter input {
            min-width: 0;
            width: 100%;
        }

        .dataTables_wrapper .dataTables_paginate {
            justify-content: center;
            width: 100%;
        }

        .dataTables_wrapper .dataTables_info {
            justify-content: center;
            width: 100%;
            text-align: center;
        }
    }

    /* Mobile card layout for wide admin/account tables (class kh-table-mobile-cards) */
    .kh-table-mobile-hint {
        display: none;
        font-size: 0.75rem;
        color: var(--kh-table-muted);
        margin-bottom: 0.5rem;
    }

    @media (min-width: 768px) {
        .kh-table-mobile-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table.kh-table-mobile-cards {
            table-layout: auto !important;
            width: 100% !important;
            min-width: 900px;
        }

        table.kh-table-mobile-cards.kh-table-mobile-cards--compact {
            min-width: 640px;
        }

        table.kh-table-mobile-cards.kh-table-mobile-cards--medium {
            min-width: 960px;
        }

        table.kh-table-mobile-cards.kh-table-mobile-cards--wide {
            min-width: 1200px;
        }

        table.kh-table-mobile-cards thead th {
            white-space: nowrap !important;
            word-break: normal !important;
            overflow-wrap: normal !important;
        }

        table.kh-table-mobile-cards tbody td {
            word-break: normal !important;
            overflow-wrap: break-word !important;
        }
    }

    @media (max-width: 767.98px) {
        .kh-table-mobile-hint {
            display: block;
        }

        .kh-table-mobile-scroll .dataTables_wrapper,
        .kh-table-mobile-scroll .dataTables_scroll,
        .kh-table-mobile-scroll .dataTables_scrollBody,
        .publication-table-wrap.kh-table-mobile-scroll .dataTables_wrapper,
        .publication-table-wrap.kh-table-mobile-scroll .dataTables_scroll,
        .publication-table-wrap.kh-table-mobile-scroll .dataTables_scrollBody {
            overflow: visible !important;
        }

        table.kh-table-mobile-cards {
            table-layout: auto !important;
            width: 100% !important;
            min-width: 0 !important;
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
        }

        table.kh-table-mobile-cards colgroup {
            display: none;
        }

        table.kh-table-mobile-cards thead {
            display: none;
        }

        table.kh-table-mobile-cards tbody {
            display: block;
        }

        table.kh-table-mobile-cards tbody tr {
            display: block;
            margin-bottom: 0.85rem;
            padding: 0.85rem 0.9rem;
            border: 1px solid var(--kh-table-border) !important;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
        }

        table.kh-table-mobile-cards tbody tr.pub-row-featured,
        table.kh-table-mobile-cards tbody tr.pub-row-inactive {
            border-radius: 12px;
        }

        table.kh-table-mobile-cards tbody tr td {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.75rem;
            border: none !important;
            padding: 0.4rem 0 !important;
            white-space: normal !important;
            word-break: normal !important;
            overflow-wrap: break-word !important;
        }

        table.kh-table-mobile-cards tbody tr td,
        table.kh-table-mobile-cards tbody tr td * {
            word-break: normal !important;
            overflow-wrap: break-word !important;
        }

        table.kh-table-mobile-cards tbody tr td::before {
            content: attr(data-label);
            flex: 0 0 38%;
            max-width: 38%;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--kh-table-muted);
            line-height: 1.35;
            padding-top: 0.15rem;
        }

        table.kh-table-mobile-cards tbody tr td > * {
            flex: 1 1 auto;
            min-width: 0;
            text-align: right;
        }

        table.kh-table-mobile-cards tbody tr td.kh-mcard-hide {
            display: none !important;
        }

        table.kh-table-mobile-cards tbody tr td.kh-mcard-primary {
            display: block;
            padding-bottom: 0.55rem !important;
            margin-bottom: 0.35rem;
            border-bottom: 1px solid #eef2f7 !important;
        }

        table.kh-table-mobile-cards tbody tr td.kh-mcard-primary::before {
            display: none;
        }

        table.kh-table-mobile-cards tbody tr td.kh-mcard-primary > * {
            text-align: left;
        }

        table.kh-table-mobile-cards tbody tr td.kh-mcard-select {
            align-items: center;
            padding-bottom: 0.5rem !important;
            margin-bottom: 0.25rem;
            border-bottom: 1px solid #eef2f7 !important;
        }

        table.kh-table-mobile-cards tbody tr td.kh-mcard-select > * {
            text-align: left;
        }

        table.kh-table-mobile-cards tbody tr td.kh-mcard-actions {
            flex-direction: column;
            align-items: stretch;
        }

        table.kh-table-mobile-cards tbody tr td.kh-mcard-actions::before {
            margin-bottom: 0.25rem;
        }

        table.kh-table-mobile-cards tbody tr td.kh-mcard-actions .btn-group,
        table.kh-table-mobile-cards tbody tr td.kh-mcard-actions .pub-actions-group {
            display: flex;
            flex-direction: column;
            width: 100%;
            gap: 0.35rem;
            justify-content: stretch;
        }

        table.kh-table-mobile-cards tbody tr td.kh-mcard-actions .btn {
            width: 100%;
            text-align: center;
        }

        table.kh-table-mobile-cards .pub-title-link,
        table.kh-table-mobile-cards .pub-desc-preview {
            white-space: normal !important;
            word-break: normal !important;
            overflow-wrap: break-word !important;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            font-size: 0.8rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.25rem 0.55rem !important;
        }
    }
</style>
