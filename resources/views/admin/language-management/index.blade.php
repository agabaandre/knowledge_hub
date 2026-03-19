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
            {{-- Do not mix d-none and d-flex on the same node — both use display:!important and the overlay can stay visible forever. --}}
            <div id="lm-panel-overlay" class="d-none position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75" style="z-index: 5; min-height: 120px;">
                <div class="d-flex align-items-center justify-content-center w-100 h-100">
                    <span class="text-muted"><i class="fa fa-spinner fa-spin me-2"></i>Loading translations…</span>
                </div>
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
    var aiTranslateUrl = @json(route('admin.language-management.ai-translate'));
    var csrfToken = @json(csrf_token());
    var localeEl = document.getElementById('lm-locale');
    var groupEl = document.getElementById('lm-group');
    var inner = document.getElementById('lm-translation-inner');
    var overlay = document.getElementById('lm-panel-overlay');
    var loadingBadge = document.getElementById('lm-loading');

    if (!localeEl || !groupEl || !inner) return;

    function showLoading(show) {
        if (overlay) {
            if (show) overlay.classList.remove('d-none');
            else overlay.classList.add('d-none');
        }
        if (loadingBadge) {
            if (show) loadingBadge.classList.remove('d-none');
            else loadingBadge.classList.add('d-none');
        }
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

    document.addEventListener('click', function(e) {
        var btn = e.target && e.target.closest ? e.target.closest('#lm-ai-translate-btn') : null;
        if (!btn) return;

        var locInput = document.getElementById('lm-input-locale');
        var grpInput = document.getElementById('lm-input-group');
        if (!locInput || !grpInput) return;

        var locale = locInput.value;
        var group = grpInput.value;
        if (!locale || locale === 'en') {
            alert('Select a non-English locale to use AI translate.');
            return;
        }

        if (!confirm('Fill all translation fields from English using OpenAI? You can edit before saving.')) {
            return;
        }

        btn.disabled = true;
        var icon = btn.querySelector('i');
        var prevClass = icon ? icon.className : '';
        if (icon) {
            icon.className = 'fa fa-spinner fa-spin me-1';
        }

        fetch(aiTranslateUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'same-origin',
            body: JSON.stringify({ locale: locale, group: group })
        })
        .then(function(res) {
            return res.json().then(function(data) {
                return { ok: res.ok, status: res.status, data: data };
            });
        })
        .then(function(wrapped) {
            var data = wrapped.data;
            if (!wrapped.ok || !data || !data.ok) {
                var msg = (data && data.message) ? data.message : ('Request failed (' + wrapped.status + ')');
                throw new Error(msg);
            }
            var map = data.translations || {};
            var inputs = document.querySelectorAll('.lm-translation-input');
            var filled = 0;
            inputs.forEach(function(inp) {
                var key = inp.getAttribute('data-key');
                if (key && Object.prototype.hasOwnProperty.call(map, key)) {
                    inp.value = map[key];
                    filled++;
                }
            });
            if (filled === 0) {
                alert('No fields were updated. Try again or check the browser console.');
            }
        })
        .catch(function(err) {
            alert(err.message || 'AI translate failed.');
        })
        .finally(function() {
            btn.disabled = false;
            if (icon) icon.className = prevClass;
        });
    });
})();
</script>
@endsection
