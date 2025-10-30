<style>
    .table{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden}
    .table thead th{background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#0f172a;font-weight:600;padding:10px}
    .table tbody td{border-top:1px solid #f1f5f9;color:#334155;padding:10px;vertical-align:middle}
    .table.table-sm thead th,.table.table-sm tbody td{padding:8px}
    .table-hover tbody tr:hover{background:#f9fafb}
    .table-striped tbody tr:nth-of-type(odd){background-color:#fcfcfd}
    .dataTables_wrapper .dataTables_filter input{border:1px solid #e2e8f0;border-radius:8px;padding:6px 10px}
    .dataTables_wrapper .dataTables_length select{border:1px solid #e2e8f0;border-radius:8px;padding:6px 28px 6px 10px}
    .dataTables_wrapper .dataTables_info{padding:10px;color:#64748b}
    .dataTables_wrapper .dataTables_paginate{padding:10px; display:flex; align-items:center; gap:4px}
    .dataTables_wrapper .dataTables_paginate .paginate_button{
        border:1px solid #e5e7eb;
        border-radius:8px;
        margin:0 2px;
        background:#fff;
        color:#0f172a!important;
        padding:6px 12px;
        transition: all .2s ease;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover{
        background:#f1f5f9;
        border-color:#cbd5e1;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current{
        background: var(--theme-color-primary, #119A48);
        color:#fff!important;
        border-color: var(--theme-color-primary, #119A48);
        box-shadow: 0 1px 2px rgba(0,0,0,.06);
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover{
        opacity:.5; background:#fff; color:#94a3b8!important; border-color:#e2e8f0; cursor:not-allowed;
    }
</style>