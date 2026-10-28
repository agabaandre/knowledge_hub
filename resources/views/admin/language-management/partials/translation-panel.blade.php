{{-- Translation table + hiddens (replaced via AJAX when locale/section changes) --}}
<input type="hidden" name="locale" id="lm-input-locale" value="{{ $currentLocale }}">
<input type="hidden" name="group" id="lm-input-group" value="{{ $currentGroup }}">

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span id="lm-panel-title">{{ $groups[$currentGroup] ?? $currentGroup }}</span>
        <div class="d-flex align-items-center flex-wrap gap-1">
            @if ($currentLocale !== 'en')
                <button type="button" class="btn btn-outline-primary btn-sm" id="lm-copy-english-btn"
                        title="{{ __('admin_language_management.copy_from_english_title') }}">
                    <i class="fa fa-copy me-1"></i> {{ __('admin_language_management.copy_from_english') }}
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="lm-ai-translate-btn"
                        title="{{ __('admin_language_management.ai_translate_title') }}">
                    <i class="fa fa-magic me-1"></i> {{ __('admin_language_management.ai_translate') }}
                </button>
            @endif
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa fa-save me-1"></i>{{ __('general.save') }}
            </button>
        </div>
    </div>
    <div class="card-body border-bottom py-2">
        <div class="row g-2 align-items-center">
            <div class="col-md-8 col-lg-6">
                <label class="visually-hidden sr-only" for="lm-search-english">{{ __('admin_language_management.search_english') }}</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fa fa-search" aria-hidden="true"></i></span>
                    <input type="search"
                           id="lm-search-english"
                           class="form-control"
                           placeholder="{{ __('admin_language_management.search_english_placeholder') }}"
                           autocomplete="off">
                    <button type="button" class="btn btn-outline-secondary" id="lm-search-clear" title="{{ __('admin_language_management.search_clear') }}">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-4 col-lg-6">
                <span id="lm-search-count" class="small text-muted"></span>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle" id="lm-translation-table">
                <thead class="table-light">
                    <tr>
                        <th class="text-center text-muted" style="width:3.5rem">#</th>
                        <th style="width:20%">{{ __('admin_language_management.col_key') }}</th>
                        <th style="width:38%">{{ __('admin_language_management.col_english') }}</th>
                        <th style="width:38%">{{ __('admin_language_management.col_translation', ['locale' => strtoupper($currentLocale)]) }}</th>
                    </tr>
                </thead>
                <tbody id="lm-translation-tbody">
                    @foreach ($lines as $key => $value)
                        @php
                            $englishText = (string) ($english[$key] ?? '');
                        @endphp
                        <tr class="lm-translation-row"
                            data-key="{{ $key }}"
                            data-english="{{ $englishText }}">
                            <td class="text-center text-muted small lm-row-num">{{ $loop->iteration }}</td>
                            <td><code class="small">{{ $key }}</code></td>
                            <td class="small text-muted lm-english-text">{{ $englishText }}</td>
                            <td>
                                <input type="text"
                                       name="translations[{{ $key }}]"
                                       value="{{ old('translations.'.$key, $value) }}"
                                       class="form-control form-control-sm lm-translation-input"
                                       autocomplete="off"
                                       data-key="{{ $key }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div id="lm-search-empty" class="d-none text-center text-muted small py-4">
            {{ __('admin_language_management.search_no_results') }}
        </div>
    </div>
    <div class="card-footer text-end">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save me-1"></i>{{ __('general.save') }}
        </button>
    </div>
</div>
