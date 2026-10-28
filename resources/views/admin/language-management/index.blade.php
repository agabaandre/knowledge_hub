@extends(admin_layout())

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin_nav.language_management') }}</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('admin_nav.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.configure') }}">{{ __('admin_nav.settings') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('admin_nav.language_management') }}</li>
            </ol>
        </div>
    </div>

    <div id="lm-flash" class="alert alert-dismissible fade d-none" role="alert">
        <span id="lm-flash-text"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('general.close') }}"></button>
    </div>

    @if (Session::has('alert-success') || Session::has('alert-danger'))
        <div class="alert alert-{{ Session::has('alert-success') ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
            {{ Session::get('alert-success') ?? Session::get('alert-danger') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('general.close') }}"></button>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <p class="text-muted mb-0">{{ __('admin_language_management.intro') }}</p>
        </div>
    </div>

    <div class="row g-2 mb-3 align-items-end" id="lm-filters">
        <div class="col-md-4">
            <label class="form-label" for="lm-locale">{{ __('admin_language_management.select_locale') }}</label>
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
            <label class="form-label" for="lm-group">{{ __('admin_language_management.section') }}</label>
            <select id="lm-group" name="group" class="form-select no-select2" autocomplete="off">
                @foreach ($groups as $key => $label)
                    <option value="{{ $key }}" @selected((string) $key === (string) $currentGroup)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <span id="lm-loading" class="text-muted small d-none"><i class="fa fa-spinner fa-spin me-1"></i>{{ __('admin_language_management.loading') }}</span>
        </div>
    </div>

    <form method="post" action="{{ route('admin.language-management.update') }}" id="lm-save-form">
        @csrf
        <div id="lm-translation-shell" class="position-relative">
            {{-- Do not mix d-none and d-flex on the same node — both use display:!important and the overlay can stay visible forever. --}}
            <div id="lm-panel-overlay" class="d-none position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75" style="z-index: 5; min-height: 120px;">
                <div class="d-flex align-items-center justify-content-center w-100 h-100">
                    <span class="text-muted"><i class="fa fa-spinner fa-spin me-2"></i>{{ __('admin_language_management.loading_translations') }}</span>
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
    var updateUrl = @json(route('admin.language-management.update'));
    var aiTranslateUrl = @json(route('admin.language-management.ai-translate'));
    var copyEnglishUrl = @json(route('admin.language-management.copy-english'));
    var csrfToken = @json(csrf_token());
    var i18n = {
        loadFailed: @json(__('admin_language_management.load_failed')),
        saveFailed: @json(__('admin_language_management.save_failed')),
        saved: @json(__('admin_language_management.saved')),
        savedAfterAi: @json(__('admin_language_management.saved_after_ai')),
        saving: @json(__('admin_language_management.saving')),
        selectNonEnglishCopy: @json(__('admin_language_management.select_non_english_copy')),
        selectNonEnglishAi: @json(__('admin_language_management.select_non_english_ai')),
        confirmCopy: @json(__('admin_language_management.confirm_copy')),
        confirmAi: @json(__('admin_language_management.confirm_ai')),
        noFields: @json(__('admin_language_management.no_fields')),
        aiNoneUpdated: @json(__('admin_language_management.ai_none_updated')),
        aiTimeout: @json(__('admin_language_management.ai_timeout')),
        aiFailed: @json(__('admin_language_management.ai_failed')),
        copyFailed: @json(__('admin_language_management.copy_failed')),
        aiTranslate: @json(__('admin_language_management.ai_translate')),
        aiTranslating: @json(__('admin_language_management.ai_translating')),
        copyFromEnglish: @json(__('admin_language_management.copy_from_english'))
    };

    var localeEl = document.getElementById('lm-locale');
    var groupEl = document.getElementById('lm-group');
    var inner = document.getElementById('lm-translation-inner');
    var overlay = document.getElementById('lm-panel-overlay');
    var loadingBadge = document.getElementById('lm-loading');
    var form = document.getElementById('lm-save-form');
    var flashEl = document.getElementById('lm-flash');
    var flashText = document.getElementById('lm-flash-text');
    var saveInFlight = false;

    if (!localeEl || !groupEl || !inner || !form) return;

    function showFlash(message, type) {
        if (!flashEl || !flashText) {
            window.alert(message);
            return;
        }
        flashEl.classList.remove('d-none', 'alert-success', 'alert-danger', 'show');
        flashEl.classList.add(type === 'danger' ? 'alert-danger' : 'alert-success', 'show');
        flashText.textContent = message;
        try {
            flashEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } catch (e) { /* ignore */ }
        window.clearTimeout(showFlash._t);
        showFlash._t = window.setTimeout(function() {
            flashEl.classList.remove('show');
            flashEl.classList.add('d-none');
        }, 5000);
    }

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

    function collectTranslations() {
        var map = {};
        document.querySelectorAll('.lm-translation-input').forEach(function(inp) {
            var key = inp.getAttribute('data-key');
            if (key) map[key] = inp.value;
        });
        return map;
    }

    function parseJsonResponse(res) {
        return res.text().then(function(raw) {
            var data = null;
            try {
                data = raw ? JSON.parse(raw) : null;
            } catch (err) {
                var snippet = (raw || '').replace(/\s+/g, ' ').slice(0, 160);
                throw new Error(
                    res.status === 504 || res.status === 502
                        ? i18n.aiTimeout
                        : ('Server returned a non-JSON response (' + res.status + '). ' + snippet)
                );
            }
            return { ok: res.ok, status: res.status, data: data };
        });
    }

    function saveTranslations(opts) {
        opts = opts || {};
        if (saveInFlight) {
            return Promise.reject(new Error('save_in_flight'));
        }

        var locInput = document.getElementById('lm-input-locale');
        var grpInput = document.getElementById('lm-input-group');
        if (!locInput || !grpInput) {
            return Promise.reject(new Error(i18n.saveFailed));
        }

        var payload = {
            locale: locInput.value,
            group: grpInput.value,
            translations: collectTranslations()
        };

        saveInFlight = true;
        var saveBtns = form.querySelectorAll('button[type="submit"]');
        saveBtns.forEach(function(btn) {
            btn.disabled = true;
            if (!btn.dataset.lmOrigHtml) btn.dataset.lmOrigHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i>' + i18n.saving;
        });

        return fetch(updateUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'same-origin',
            body: JSON.stringify(payload)
        })
        .then(parseJsonResponse)
        .then(function(wrapped) {
            var data = wrapped.data;
            if (!wrapped.ok || !data || !data.ok) {
                var msg = (data && data.message) ? data.message : i18n.saveFailed;
                throw new Error(msg);
            }
            showFlash(opts.successMessage || data.message || i18n.saved, 'success');
            return data;
        })
        .finally(function() {
            saveInFlight = false;
            saveBtns.forEach(function(btn) {
                btn.disabled = false;
                if (btn.dataset.lmOrigHtml) btn.innerHTML = btn.dataset.lmOrigHtml;
            });
        });
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
            showFlash(i18n.loadFailed, 'danger');
        })
        .finally(function() {
            showLoading(false);
        });
    }

    localeEl.addEventListener('change', loadGrid);
    groupEl.addEventListener('change', loadGrid);

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        saveTranslations().catch(function(err) {
            if (err && err.message === 'save_in_flight') return;
            showFlash((err && err.message) || i18n.saveFailed, 'danger');
        });
    });

    document.addEventListener('click', function(e) {
        var copyBtn = e.target && e.target.closest ? e.target.closest('#lm-copy-english-btn') : null;
        if (copyBtn) {
            var locInput = document.getElementById('lm-input-locale');
            var grpInput = document.getElementById('lm-input-group');
            if (!locInput || !grpInput) return;

            var locale = locInput.value;
            var group = grpInput.value;
            if (!locale || locale === 'en') {
                showFlash(i18n.selectNonEnglishCopy, 'danger');
                return;
            }

            if (!confirm(i18n.confirmCopy)) {
                return;
            }

            copyBtn.disabled = true;
            var copyIcon = copyBtn.querySelector('i');
            var copyPrev = copyIcon ? copyIcon.className : '';
            if (copyIcon) {
                copyIcon.className = 'fa fa-spinner fa-spin me-1';
            }

            fetch(copyEnglishUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                credentials: 'same-origin',
                body: JSON.stringify({ locale: locale, group: group, overwrite: false })
            })
            .then(parseJsonResponse)
            .then(function(wrapped) {
                var data = wrapped.data;
                if (!wrapped.ok || !data || !data.ok) {
                    var msg = (data && data.message) ? data.message : i18n.copyFailed;
                    throw new Error(msg);
                }
                var map = data.translations || {};
                document.querySelectorAll('.lm-translation-input').forEach(function(inp) {
                    var key = inp.getAttribute('data-key');
                    if (key && Object.prototype.hasOwnProperty.call(map, key)) {
                        inp.value = map[key];
                    }
                });
            })
            .catch(function(err) {
                showFlash((err && err.message) || i18n.copyFailed, 'danger');
            })
            .finally(function() {
                copyBtn.disabled = false;
                if (copyIcon) copyIcon.className = copyPrev;
            });
            return;
        }

        var btn = e.target && e.target.closest ? e.target.closest('#lm-ai-translate-btn') : null;
        if (!btn) return;

        var locInput2 = document.getElementById('lm-input-locale');
        var grpInput2 = document.getElementById('lm-input-group');
        if (!locInput2 || !grpInput2) return;

        var locale2 = locInput2.value;
        var group2 = grpInput2.value;
        if (!locale2 || locale2 === 'en') {
            showFlash(i18n.selectNonEnglishAi, 'danger');
            return;
        }

        if (!confirm(i18n.confirmAi)) {
            return;
        }

        var inputs = Array.prototype.slice.call(document.querySelectorAll('.lm-translation-input'));
        var keys = inputs.map(function(inp) { return inp.getAttribute('data-key'); }).filter(Boolean);
        if (!keys.length) {
            showFlash(i18n.noFields, 'danger');
            return;
        }

        btn.disabled = true;
        var icon = btn.querySelector('i');
        var prevClass = icon ? icon.className : 'fa fa-magic me-1';
        if (icon) {
            icon.className = 'fa fa-spinner fa-spin me-1';
        }

        function setProgress(done, total) {
            var label = i18n.aiTranslating
                .replace(':done', String(done))
                .replace(':total', String(total));
            btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> ' + label;
        }

        function restoreBtn() {
            btn.disabled = false;
            btn.innerHTML = '<i class="' + prevClass + '"></i> ' + i18n.aiTranslate;
        }

        function applyMap(map) {
            var filled = 0;
            inputs.forEach(function(inp) {
                var key = inp.getAttribute('data-key');
                if (key && Object.prototype.hasOwnProperty.call(map, key)) {
                    inp.value = map[key];
                    filled++;
                }
            });
            return filled;
        }

        var chunkSize = 8;
        var chunks = [];
        for (var i = 0; i < keys.length; i += chunkSize) {
            chunks.push(keys.slice(i, i + chunkSize));
        }

        var filledTotal = 0;
        var chunkIndex = 0;

        function runNext() {
            if (chunkIndex >= chunks.length) {
                if (filledTotal === 0) {
                    showFlash(i18n.aiNoneUpdated, 'danger');
                    restoreBtn();
                    return Promise.resolve();
                }
                return saveTranslations({ successMessage: i18n.savedAfterAi })
                    .catch(function(err) {
                        showFlash((err && err.message) || i18n.saveFailed, 'danger');
                    })
                    .finally(restoreBtn);
            }

            setProgress(Math.min(chunkIndex * chunkSize, keys.length), keys.length);

            return fetch(aiTranslateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                credentials: 'same-origin',
                body: JSON.stringify({ locale: locale2, group: group2, keys: chunks[chunkIndex] })
            })
            .then(parseJsonResponse)
            .then(function(wrapped) {
                var data = wrapped.data;
                if (!wrapped.ok || !data || !data.ok) {
                    var msg = (data && data.message) ? data.message : ('Request failed (' + wrapped.status + ')');
                    throw new Error(msg);
                }
                filledTotal += applyMap(data.translations || {});
                chunkIndex++;
                setProgress(Math.min(chunkIndex * chunkSize, keys.length), keys.length);
                return runNext();
            });
        }

        runNext().catch(function(err) {
            showFlash((err && err.message) || i18n.aiFailed, 'danger');
            restoreBtn();
        });
    });
})();
</script>
@endsection
