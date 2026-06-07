@extends(admin_layout())

@section('styles')
<style>
    .ai-config-page { --ai-accent: var(--theme-color-primary, #119A48); }
    .ai-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a2f 55%, #14532d 100%);
        border-radius: 12px;
        color: #fff;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .ai-hero::after {
        content: '';
        position: absolute;
        right: -40px;
        top: -40px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(255,255,255,.06);
    }
    .ai-hero h1 { font-size: 1.5rem; font-weight: 700; margin: 0 0 .35rem; }
    .ai-hero p { margin: 0; opacity: .88; max-width: 52rem; }
    .ai-stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .ai-stat-card {
        background: #fff;
        border: 1px solid #e8edf2;
        border-radius: 10px;
        padding: 1rem 1.15rem;
        box-shadow: 0 1px 3px rgba(15,23,42,.04);
    }
    .ai-stat-card .label { font-size: .75rem; text-transform: uppercase; letter-spacing: .04em; color: #64748b; margin-bottom: .25rem; }
    .ai-stat-card .value { font-size: 1.35rem; font-weight: 700; color: #0f172a; }
    .ai-section {
        background: #fff;
        border: 1px solid #e8edf2;
        border-radius: 12px;
        margin-bottom: 1.25rem;
        overflow: hidden;
    }
    .ai-section-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #eef2f6;
        background: #fafbfc;
    }
    .ai-section-header h2 { font-size: 1rem; font-weight: 700; margin: 0; color: #0f172a; }
    .ai-section-header p { margin: .25rem 0 0; font-size: .875rem; color: #64748b; }
    .ai-section-body { padding: 1.25rem; }
    .ai-feature-row {
        display: grid;
        grid-template-columns: minmax(200px, 1.2fr) minmax(180px, 1fr) auto;
        gap: 1rem;
        align-items: center;
        padding: .85rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .ai-feature-row:last-child { border-bottom: none; padding-bottom: 0; }
    .ai-feature-title { font-weight: 600; color: #0f172a; margin-bottom: .15rem; }
    .ai-feature-desc { font-size: .8rem; color: #64748b; margin: 0; }
    .ai-provider-card {
        border: 1px solid #e8edf2;
        border-radius: 10px;
        margin-bottom: 1rem;
        overflow: hidden;
        background: #fff;
    }
    .ai-provider-card:last-child { margin-bottom: 0; }
    .ai-provider-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: .9rem 1.1rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .ai-provider-brand {
        display: flex;
        align-items: center;
        gap: .75rem;
        min-width: 0;
    }
    .ai-provider-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        flex-shrink: 0;
    }
    .ai-provider-name { font-weight: 700; color: #0f172a; }
    .ai-provider-desc { font-size: .8rem; color: #64748b; margin: 0; }
    .ai-status-pill {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .03em;
        padding: .25rem .55rem;
        border-radius: 999px;
    }
    .ai-status-pill.ready { background: #dcfce7; color: #166534; }
    .ai-status-pill.incomplete { background: #fef3c7; color: #92400e; }
    .ai-status-pill.disabled { background: #f1f5f9; color: #64748b; }
    .ai-status-pill.unavailable { background: #fee2e2; color: #991b1b; }
    .ai-cap-tags { display: flex; flex-wrap: wrap; gap: .35rem; margin-top: .5rem; }
    .ai-cap-tag {
        font-size: .7rem;
        padding: .15rem .45rem;
        border-radius: 4px;
        background: #f1f5f9;
        color: #475569;
    }
    .ai-integration-row {
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
        background: #fafbfc;
        position: relative;
    }
    .ai-integration-row .btn-remove {
        position: absolute;
        top: .65rem;
        right: .65rem;
    }
    .ai-save-bar {
        position: sticky;
        bottom: 0;
        z-index: 20;
        background: rgba(255,255,255,.96);
        border-top: 1px solid #e8edf2;
        padding: 1rem 0 0;
        margin-top: 1rem;
        backdrop-filter: blur(6px);
    }
    @media (max-width: 768px) {
        .ai-feature-row { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="container-fluid ai-config-page">
    @include('layouts.partials.alerts')
    @include('admin.courses.partials.learning_subnav', ['learningNav' => 'ai'])

    <div class="ai-hero">
        <h1><i class="fa fa-robot me-2"></i>{{ __('admin_nav.ai_config') }}</h1>
        <p>{{ __('admin_nav.ai_config_intro') }}</p>
    </div>

    @if(empty($aiPage))
        <div class="alert alert-warning">
            AI configuration columns are missing. Run <code>php artisan migrate</code> on this server.
        </div>
    @else
        <div class="ai-stat-grid">
            <div class="ai-stat-card">
                <div class="label">{{ __('admin_nav.ai_stat_ready_providers') }}</div>
                <div class="value">{{ $aiPage['stats']['ready_providers'] }}</div>
            </div>
            <div class="ai-stat-card">
                <div class="label">{{ __('admin_nav.ai_stat_features_ready') }}</div>
                <div class="value">{{ $aiPage['stats']['features_ready'] }} / {{ $aiPage['stats']['total_features'] }}</div>
            </div>
            <div class="ai-stat-card">
                <div class="label">{{ __('admin_nav.ai_stat_primary_chat') }}</div>
                <div class="value" style="font-size:1rem;">{{ $aiPage['stats']['primary_chat_label'] }}</div>
            </div>
        </div>

        <form method="post" action="{{ route('admin.courses.ai-config.save') }}" id="ai-config-form">
            @csrf
            @include('admin.courses.partials.ai_config_form', ['aiPage' => $aiPage])

            <div class="ai-save-bar d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save me-1"></i>{{ __('admin_nav.save_ai_config') }}
                </button>
                <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">{{ __('admin_nav.back_to_courses') }}</a>
            </div>
        </form>
    @endif
</div>
@endsection

@section('scripts')
@if(!empty($aiPage))
<script>
(function () {
    var container = document.getElementById('custom-integrations-list');
    var addBtn = document.getElementById('add-custom-integration');
    var template = document.getElementById('custom-integration-template');
    if (!container || !addBtn || !template) return;

    var index = container.querySelectorAll('.ai-integration-row').length;

    function reindexNames(row, idx) {
        row.querySelectorAll('[name]').forEach(function (el) {
            el.name = el.name.replace(/custom_integrations\[\d+\]/, 'custom_integrations[' + idx + ']');
        });
    }

    addBtn.addEventListener('click', function () {
        var clone = template.content.cloneNode(true);
        var row = clone.querySelector('.ai-integration-row');
        reindexNames(row, index);
        container.appendChild(clone);
        index++;
    });

    container.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-remove-integration');
        if (!btn) return;
        var row = btn.closest('.ai-integration-row');
        if (row) row.remove();
    });
})();
</script>
@endif
@endsection
