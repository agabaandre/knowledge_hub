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

    <div class="card mb-3" id="lm-bulk-card">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-2">
                <div>
                    <h2 class="h6 mb-1">{{ __('admin_language_management.bulk_title') }}</h2>
                    <p class="text-muted small mb-0">{{ __('admin_language_management.bulk_intro') }}</p>
                </div>
                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" id="lm-bulk-only-missing" checked>
                    <label class="form-check-label small" for="lm-bulk-only-missing">
                        {{ __('admin_language_management.bulk_only_missing') }}
                    </label>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm" id="lm-bulk-sections-btn"
                        title="{{ __('admin_language_management.bulk_sections_title') }}">
                    <i class="fa fa-layer-group me-1"></i>{{ __('admin_language_management.bulk_sections') }}
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="lm-bulk-all-langs-btn"
                        title="{{ __('admin_language_management.bulk_all_languages_title') }}">
                    <i class="fa fa-globe me-1"></i>{{ __('admin_language_management.bulk_all_languages') }}
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm d-none" id="lm-bulk-cancel-btn">
                    <i class="fa fa-stop me-1"></i>{{ __('admin_language_management.bulk_cancel') }}
                </button>
                <button type="button" class="btn btn-outline-warning btn-sm" id="lm-clear-english-btn"
                        title="{{ __('admin_language_management.clear_english_title') }}">
                    <i class="fa fa-eraser me-1"></i>{{ __('admin_language_management.clear_english') }}
                </button>
            </div>
            <div id="lm-bulk-progress" class="d-none mt-3">
                <div class="d-flex justify-content-between small mb-1">
                    <span id="lm-bulk-status" class="text-muted"></span>
                    <span id="lm-bulk-counts" class="text-muted"></span>
                </div>
                <div class="progress" style="height: 0.55rem;">
                    <div id="lm-bulk-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                         role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <pre id="lm-bulk-log" class="small bg-light border rounded p-2 mt-2 mb-0"
                     style="max-height: 10rem; overflow: auto; white-space: pre-wrap;"></pre>
            </div>
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
    var aiTranslateSaveUrl = @json(route('admin.language-management.ai-translate-save'));
    var bulkPlanUrl = @json(route('admin.language-management.bulk-plan'));
    var clearEnglishUrl = @json(route('admin.language-management.clear-english-overrides'));
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
        copyFromEnglish: @json(__('admin_language_management.copy_from_english')),
        confirmBulkSections: @json(__('admin_language_management.confirm_bulk_sections')),
        confirmBulkAllLanguages: @json(__('admin_language_management.confirm_bulk_all_languages')),
        bulkPlanning: @json(__('admin_language_management.bulk_planning')),
        bulkNothingTodo: @json(__('admin_language_management.bulk_nothing_todo')),
        bulkRunning: @json(__('admin_language_management.bulk_running')),
        bulkJobStatus: @json(__('admin_language_management.bulk_job_status')),
        bulkDone: @json(__('admin_language_management.bulk_done')),
        bulkCancelled: @json(__('admin_language_management.bulk_cancelled')),
        bulkFailed: @json(__('admin_language_management.bulk_failed')),
        bulkPlanFailed: @json(__('admin_language_management.bulk_plan_failed')),
        searchShowing: @json(__('admin_language_management.search_showing')),
        searchNoResults: @json(__('admin_language_management.search_no_results')),
        confirmClearEnglish: @json(__('admin_language_management.confirm_clear_english')),
        clearEnglishFailed: @json(__('admin_language_management.clear_english_failed')),
        clearEnglish: @json(__('admin_language_management.clear_english'))
    };

    // Small AI payloads keep each request fast and avoid gateway / PHP timeouts.
    var AI_CHUNK_SIZE = 4;
    var AI_CHUNK_PAUSE_MS = 120;

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

    function wait(ms) {
        return new Promise(function(resolve) {
            window.setTimeout(resolve, ms);
        });
    }

    function chunkKeys(keys, size) {
        var out = [];
        for (var i = 0; i < keys.length; i += size) {
            out.push(keys.slice(i, i + size));
        }
        return out;
    }

    function bindEnglishSearch() {
        if (typeof window.jQuery === 'undefined' || !window.jQuery) {
            return;
        }
        var $ = window.jQuery;
        var $root = $(inner);
        var $input = $root.find('#lm-search-english');
        var $clear = $root.find('#lm-search-clear');
        var $count = $root.find('#lm-search-count');
        var $empty = $root.find('#lm-search-empty');
        var $rows = $root.find('#lm-translation-tbody tr.lm-translation-row');

        function applyFilter() {
            var q = String($input.val() || '').toLowerCase().trim();
            var visible = 0;
            $rows.each(function() {
                var $row = $(this);
                var hay = (
                    String($row.attr('data-key') || '') + ' ' +
                    String($row.attr('data-english') || '') + ' ' +
                    String($row.find('.lm-english-text').text() || '') + ' ' +
                    String($row.find('.lm-translation-input').val() || '')
                ).toLowerCase();
                var show = !q || hay.indexOf(q) !== -1;
                $row.toggle(show);
                if (show) visible++;
            });
            if ($count.length) {
                $count.text(
                    i18n.searchShowing
                        .replace(':visible', String(visible))
                        .replace(':total', String($rows.length))
                );
            }
            if ($empty.length) {
                if (q && visible === 0) $empty.removeClass('d-none');
                else $empty.addClass('d-none');
            }
        }

        $input.off('.lmSearch').on('input.lmSearch keyup.lmSearch search.lmSearch', applyFilter);
        $clear.off('.lmSearch').on('click.lmSearch', function() {
            $input.val('');
            applyFilter();
            $input.trigger('focus');
        });
        applyFilter();
    }

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
            bindEnglishSearch();
        })
        .catch(function() {
            showFlash(i18n.loadFailed, 'danger');
        })
        .finally(function() {
            showLoading(false);
        });
    }

    bindEnglishSearch();

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
                if (!key || !Object.prototype.hasOwnProperty.call(map, key)) return;
                var next = String(map[key] == null ? '' : map[key]).trim();
                if (!next) return;
                var row = inp.closest('tr');
                var english = row ? String(row.getAttribute('data-english') || '').trim() : '';
                // Skip English fallbacks so Save does not override packaged locale files.
                if (english && next === english) return;
                inp.value = next;
                filled++;
            });
            return filled;
        }

        var chunkSize = AI_CHUNK_SIZE;
        var chunks = chunkKeys(keys, chunkSize);

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
                // Yield between chunks so the UI stays responsive during long AI runs.
                return wait(AI_CHUNK_PAUSE_MS).then(runNext);
            });
        }

        runNext().catch(function(err) {
            showFlash((err && err.message) || i18n.aiFailed, 'danger');
            restoreBtn();
        });
    });

    // ---- Bulk async AI translate (all sections / all languages) ----
    var bulkSectionsBtn = document.getElementById('lm-bulk-sections-btn');
    var bulkAllLangsBtn = document.getElementById('lm-bulk-all-langs-btn');
    var bulkCancelBtn = document.getElementById('lm-bulk-cancel-btn');
    var bulkOnlyMissingEl = document.getElementById('lm-bulk-only-missing');
    var bulkProgress = document.getElementById('lm-bulk-progress');
    var bulkStatus = document.getElementById('lm-bulk-status');
    var bulkCounts = document.getElementById('lm-bulk-counts');
    var bulkBar = document.getElementById('lm-bulk-bar');
    var bulkLog = document.getElementById('lm-bulk-log');
    var bulkCancelRequested = false;
    var bulkRunning = false;

    function setBulkUiRunning(running) {
        bulkRunning = running;
        if (bulkSectionsBtn) bulkSectionsBtn.disabled = running;
        if (bulkAllLangsBtn) bulkAllLangsBtn.disabled = running;
        if (bulkCancelBtn) {
            if (running) bulkCancelBtn.classList.remove('d-none');
            else bulkCancelBtn.classList.add('d-none');
            bulkCancelBtn.disabled = !running;
        }
        if (localeEl) localeEl.disabled = running;
        if (groupEl) groupEl.disabled = running;
    }

    function appendBulkLog(line) {
        if (!bulkLog) return;
        bulkLog.textContent += (bulkLog.textContent ? '\n' : '') + line;
        bulkLog.scrollTop = bulkLog.scrollHeight;
    }

    function updateBulkProgress(doneKeys, totalKeys, statusText) {
        if (bulkStatus) bulkStatus.textContent = statusText || '';
        if (bulkCounts) {
            bulkCounts.textContent = String(doneKeys) + ' / ' + String(totalKeys);
        }
        if (bulkBar) {
            var pct = totalKeys > 0 ? Math.min(100, Math.round((doneKeys / totalKeys) * 100)) : 0;
            bulkBar.style.width = pct + '%';
            bulkBar.setAttribute('aria-valuenow', String(pct));
            if (!bulkRunning) {
                bulkBar.classList.remove('progress-bar-animated');
            } else {
                bulkBar.classList.add('progress-bar-animated');
            }
        }
    }

    function fetchBulkPlan(scope) {
        var onlyMissing = bulkOnlyMissingEl ? !!bulkOnlyMissingEl.checked : true;
        var params = new URLSearchParams();
        params.set('scope', scope);
        params.set('only_missing', onlyMissing ? '1' : '0');
        if (scope === 'sections') {
            params.set('locale', localeEl.value);
        }
        return fetch(bulkPlanUrl + '?' + params.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        }).then(parseJsonResponse);
    }

    function translateSaveChunk(locale, group, keys, onlyMissing) {
        return fetch(aiTranslateSaveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                locale: locale,
                group: group,
                keys: keys,
                only_missing: onlyMissing
            })
        }).then(parseJsonResponse);
    }

    function runBulk(scope) {
        if (bulkRunning) return;

        if (scope === 'sections' && (!localeEl.value || localeEl.value === 'en')) {
            showFlash(i18n.selectNonEnglishAi, 'danger');
            return;
        }

        var confirmMsg = scope === 'all_languages'
            ? i18n.confirmBulkAllLanguages
            : i18n.confirmBulkSections;
        if (!confirm(confirmMsg)) return;

        bulkCancelRequested = false;
        setBulkUiRunning(true);
        if (bulkProgress) bulkProgress.classList.remove('d-none');
        if (bulkLog) bulkLog.textContent = '';
        updateBulkProgress(0, 0, i18n.bulkPlanning);
        appendBulkLog(i18n.bulkPlanning);

        var onlyMissing = bulkOnlyMissingEl ? !!bulkOnlyMissingEl.checked : true;
        var savedTotal = 0;
        var doneKeys = 0;
        var totalKeys = 0;
        var failed = false;

        fetchBulkPlan(scope)
            .then(function(wrapped) {
                var data = wrapped.data;
                if (!wrapped.ok || !data || !data.ok) {
                    throw new Error((data && data.message) || i18n.bulkPlanFailed);
                }
                var jobs = data.jobs || [];
                totalKeys = data.total_keys || 0;
                if (!jobs.length || totalKeys === 0) {
                    updateBulkProgress(0, 0, i18n.bulkNothingTodo);
                    appendBulkLog(i18n.bulkNothingTodo);
                    showFlash(i18n.bulkNothingTodo, 'success');
                    return null;
                }

                updateBulkProgress(0, totalKeys, i18n.bulkRunning
                    .replace(':jobs', String(jobs.length))
                    .replace(':keys', String(totalKeys)));
                appendBulkLog(i18n.bulkRunning
                    .replace(':jobs', String(jobs.length))
                    .replace(':keys', String(totalKeys)));

                var jobIndex = 0;

                function runJob() {
                    if (bulkCancelRequested) {
                        return Promise.resolve('cancelled');
                    }
                    if (jobIndex >= jobs.length) {
                        return Promise.resolve('done');
                    }

                    var job = jobs[jobIndex];
                    var chunks = chunkKeys(job.keys || [], AI_CHUNK_SIZE);
                    var chunkIndex = 0;
                    var jobLabel = (job.locale || '').toUpperCase() + ' / ' + (job.group_label || job.group);

                    appendBulkLog(i18n.bulkJobStatus
                        .replace(':locale', String(job.locale || '').toUpperCase())
                        .replace(':group', String(job.group_label || job.group))
                        .replace(':keys', String(job.key_count || (job.keys || []).length)));

                    function runChunk() {
                        if (bulkCancelRequested) {
                            return Promise.resolve('cancelled');
                        }
                        if (chunkIndex >= chunks.length) {
                            jobIndex++;
                            return runJob();
                        }

                        var keys = chunks[chunkIndex];
                        updateBulkProgress(
                            doneKeys,
                            totalKeys,
                            i18n.aiTranslating
                                .replace(':done', String(doneKeys))
                                .replace(':total', String(totalKeys)) + ' — ' + jobLabel
                        );

                        return translateSaveChunk(job.locale, job.group, keys, onlyMissing)
                            .then(function(wrapped2) {
                                var data2 = wrapped2.data;
                                if (!wrapped2.ok || !data2 || !data2.ok) {
                                    throw new Error((data2 && data2.message) || i18n.aiFailed);
                                }
                                var saved = data2.saved || data2.translated || 0;
                                savedTotal += saved;
                                doneKeys += keys.length;
                                chunkIndex++;
                                updateBulkProgress(
                                    doneKeys,
                                    totalKeys,
                                    i18n.aiTranslating
                                        .replace(':done', String(Math.min(doneKeys, totalKeys)))
                                        .replace(':total', String(totalKeys)) + ' — ' + jobLabel
                                );
                                return wait(AI_CHUNK_PAUSE_MS).then(runChunk);
                            });
                    }

                    return runChunk();
                }

                return runJob();
            })
            .then(function(result) {
                if (result === null) return;
                if (result === 'cancelled' || bulkCancelRequested) {
                    appendBulkLog(i18n.bulkCancelled);
                    updateBulkProgress(doneKeys, totalKeys, i18n.bulkCancelled);
                    showFlash(i18n.bulkCancelled, 'danger');
                    return;
                }
                var doneMsg = i18n.bulkDone
                    .replace(':saved', String(savedTotal))
                    .replace(':keys', String(totalKeys));
                appendBulkLog(doneMsg);
                updateBulkProgress(totalKeys, totalKeys, doneMsg);
                showFlash(doneMsg, 'success');
                // Refresh the visible editor so it reflects newly saved values.
                loadGrid();
            })
            .catch(function(err) {
                failed = true;
                var msg = (err && err.message) || i18n.bulkFailed;
                appendBulkLog(msg);
                updateBulkProgress(doneKeys, totalKeys || doneKeys, msg);
                showFlash(msg, 'danger');
            })
            .finally(function() {
                setBulkUiRunning(false);
                if (bulkBar && !failed && !bulkCancelRequested) {
                    bulkBar.classList.remove('progress-bar-animated');
                }
            });
    }

    if (bulkSectionsBtn) {
        bulkSectionsBtn.addEventListener('click', function() {
            runBulk('sections');
        });
    }
    if (bulkAllLangsBtn) {
        bulkAllLangsBtn.addEventListener('click', function() {
            runBulk('all_languages');
        });
    }
    if (bulkCancelBtn) {
        bulkCancelBtn.addEventListener('click', function() {
            bulkCancelRequested = true;
            bulkCancelBtn.disabled = true;
            if (bulkStatus) bulkStatus.textContent = i18n.bulkCancelled;
        });
    }

    var clearEnglishBtn = document.getElementById('lm-clear-english-btn');
    if (clearEnglishBtn) {
        clearEnglishBtn.addEventListener('click', function() {
            var locale = localeEl.value;
            if (!locale || locale === 'en') {
                showFlash(i18n.selectNonEnglishAi, 'danger');
                return;
            }
            if (!confirm(i18n.confirmClearEnglish)) {
                return;
            }

            clearEnglishBtn.disabled = true;
            var prevHtml = clearEnglishBtn.innerHTML;
            clearEnglishBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i>' + i18n.clearEnglish;

            fetch(clearEnglishUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                credentials: 'same-origin',
                body: JSON.stringify({ locale: locale, scope: 'locale' })
            })
            .then(parseJsonResponse)
            .then(function(wrapped) {
                var data = wrapped.data;
                if (!wrapped.ok || !data || !data.ok) {
                    throw new Error((data && data.message) || i18n.clearEnglishFailed);
                }
                showFlash(data.message || i18n.clearEnglish, 'success');
                loadGrid();
            })
            .catch(function(err) {
                showFlash((err && err.message) || i18n.clearEnglishFailed, 'danger');
            })
            .finally(function() {
                clearEnglishBtn.disabled = false;
                clearEnglishBtn.innerHTML = prevHtml;
            });
        });
    }
})();
</script>
@endsection
