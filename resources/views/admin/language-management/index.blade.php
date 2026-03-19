@extends(admin_layout())

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin_nav.language_management') }}</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.configure') }}">Settings</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('admin_nav.language_management') }}</li>
            </ol>
        </div>
    </div>

    @if (Session::has('alert-success') || Session::has('alert-danger'))
        <div class="alert alert-{{ Session::has('alert-success') ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
            {{ Session::get('alert-success') ?? Session::get('alert-danger') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('general.close') }}"></button>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <p class="text-muted mb-0">
                Edit UI strings for the public site navigation, admin sidebar, and static footer/account chrome.
                English is the source of keys; other locales fall back to English until you save a value here.
                Existing Laravel groups such as <code>general</code> and <code>auth</code> are unchanged.
            </p>
        </div>
    </div>

    <div class="row g-2 mb-3 align-items-end" id="lm-filters">
        <div class="col-md-4">
            <label class="form-label" for="lm-locale">{{ __('general.select') }} locale</label>
            <select id="lm-locale" name="locale" class="form-select no-select2" autocomplete="off">
                @foreach ($locales as $code)
                    <option value="{{ $code }}" @selected((string) $code === (string) $currentLocale)>
                        {{ $localeLabels[$code]['flag'] ?? '' }} {{ strtoupper($code) }}
                        — {{ $localeLabels[$code]['name'] ?? $code }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="lm-group">Section</label>
            <select id="lm-group" name="group" class="form-select no-select2" autocomplete="off">
                @foreach ($groups as $key => $label)
                    <option value="{{ $key }}" @selected((string) $key === (string) $currentGroup)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <span id="lm-loading" class="text-muted small d-none"><i class="fa fa-spinner fa-spin me-1"></i>Loading…</span>
        </div>
    </div>

    <form method="post" action="{{ route('admin.language-management.update') }}" id="lm-save-form">
        @csrf
        <div id="lm-translation-shell" class="position-relative">
            <div id="lm-panel-overlay" class="d-none position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-flex align-items-center justify-content-center" style="z-index: 5; min-height: 120px;">
                <span class="text-muted"><i class="fa fa-spinner fa-spin me-2"></i>Loading translations…</span>
            </div>
            <div id="lm-translation-inner">
                @include('admin.language-management.partials.translation-panel')
            </div>
        </div>
    </form>
@endsection

@section('scripts')
<script>
(function() {
    var gridUrl = @json(route('admin.language-management.grid'));
    var localeEl = document.getElementById('lm-locale');
    var groupEl = document.getElementById('lm-group');
    var inner = document.getElementById('lm-translation-inner');
    var overlay = document.getElementById('lm-panel-overlay');
    var loadingBadge = document.getElementById('lm-loading');

    if (!localeEl || !groupEl || !inner) return;

    function showLoading(show) {
        if (overlay) overlay.classList.toggle('d-none', !show);
        if (loadingBadge) loadingBadge.classList.toggle('d-none', !show);
    }

    function syncUrl(locale, group) {
        try {
            var url = new URL(window.location.href);
            url.searchParams.set('locale', locale);
            url.searchParams.set('group', group);
            window.history.replaceState({}, '', url.toString());
        } catch (e) { /* ignore */ }
    }

    function loadGrid() {
        var locale = localeEl.value;
        var group = groupEl.value;
        var url = gridUrl + '?locale=' + encodeURIComponent(locale) + '&group=' + encodeURIComponent(group);

        showLoading(true);

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(function(res) {
            if (!res.ok) throw new Error('Request failed');
            return res.json();
        })
        .then(function(data) {
            if (!data || !data.ok || !data.html) throw new Error('Invalid response');
            inner.innerHTML = data.html;
            localeEl.value = data.locale;
            groupEl.value = data.group;
            syncUrl(data.locale, data.group);
        })
        .catch(function() {
            alert('Could not load translations for this locale/section. Please refresh the page.');
        })
        .finally(function() {
            showLoading(false);
        });
    }

    localeEl.addEventListener('change', loadGrid);
    groupEl.addEventListener('change', loadGrid);
})();
</script>
@endsection
