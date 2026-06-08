@extends(admin_layout())

@section('styles')
@include('admin.courses.partials.learning_admin_styles')
@endsection

@section('content')
<div class="container-fluid py-3 learning-admin-page">
    @include('layouts.partials.alerts')
    @include('admin.courses.partials.learning_subnav', ['learningNav' => 'ai'])

    <div class="card learning-hero shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h3><i class="fa fa-robot me-2" style="color: var(--la-primary);"></i>{{ __('admin_nav.ai_config') }}</h3>
                    <p>{{ __('admin_nav.ai_config_intro') }}</p>
                    @if(!empty($aiPage))
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <span class="learning-stat-pill">
                                <i class="fa fa-check-circle"></i>
                                {{ $aiPage['stats']['ready_providers'] }} {{ __('admin_nav.ai_stat_ready_providers') }}
                            </span>
                            <span class="learning-stat-pill">
                                <i class="fa fa-route"></i>
                                {{ $aiPage['stats']['features_ready'] }}/{{ $aiPage['stats']['total_features'] }} {{ __('admin_nav.ai_stat_features_ready') }}
                            </span>
                            <span class="learning-stat-pill">
                                <i class="fa fa-comments"></i>
                                {{ $aiPage['stats']['primary_chat_label'] }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(empty($aiPage))
        <div class="alert alert-warning">
            AI configuration columns are missing. Run <code>php artisan migrate</code> on this server.
        </div>
    @else
        <form method="post" action="{{ route('admin.courses.ai-config.save') }}" id="ai-config-form">
            @csrf
            @include('admin.courses.partials.ai_config_form', ['aiPage' => $aiPage])

            <div class="learning-save-bar">
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

    var index = container.querySelectorAll('.integration-row').length;

    function reindexNames(row, idx) {
        row.querySelectorAll('[name]').forEach(function (el) {
            el.name = el.name.replace(/custom_integrations\[\d+\]/, 'custom_integrations[' + idx + ']');
        });
    }

    addBtn.addEventListener('click', function () {
        var clone = template.content.cloneNode(true);
        var row = clone.querySelector('.integration-row');
        reindexNames(row, index);
        container.appendChild(clone);
        index++;
    });

    container.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-remove-integration');
        if (!btn) return;
        var row = btn.closest('.integration-row');
        if (row) row.remove();
    });

    var sitesContainer = document.getElementById('allowed-sites-list');
    var addSiteBtn = document.getElementById('add-allowed-site');
    var siteTemplate = document.getElementById('allowed-site-template');
    if (sitesContainer && addSiteBtn && siteTemplate) {
        var siteIndex = sitesContainer.querySelectorAll('.allowed-site-row').length;

        function reindexSiteNames(row, idx) {
            row.querySelectorAll('[name]').forEach(function (el) {
                el.name = el.name.replace(/ai_search_allowed_sites\[\d+\]/, 'ai_search_allowed_sites[' + idx + ']');
            });
            var enabled = row.querySelector('[id^="allowed_site_enabled_"]');
            if (enabled) {
                enabled.id = 'allowed_site_enabled_' + idx;
                var label = row.querySelector('label[for^="allowed_site_enabled_"]');
                if (label) label.setAttribute('for', 'allowed_site_enabled_' + idx);
            }
        }

        addSiteBtn.addEventListener('click', function () {
            var emptyMsg = document.getElementById('no-allowed-sites-msg');
            if (emptyMsg) emptyMsg.remove();
            var clone = siteTemplate.content.cloneNode(true);
            var row = clone.querySelector('.allowed-site-row');
            reindexSiteNames(row, siteIndex);
            sitesContainer.appendChild(clone);
            siteIndex++;
        });

        sitesContainer.addEventListener('click', function (e) {
            var btn = e.target.closest('.btn-remove-allowed-site');
            if (!btn) return;
            var row = btn.closest('.allowed-site-row');
            if (row) row.remove();
        });
    }
})();
</script>
@endif
@endsection
