@if(($aiSearchEnabled ?? false) && mb_strlen(trim((string) request('term', ''))) >= 2)
@php
    $insights = $aiSearchInsights ?? null;
    $hasInsights = \App\Services\AiSearchInsightsService::isDisplayable($insights);
    $searchTerm = trim((string) ($insights['query'] ?? request('term', '')));
    $hubMatches = (int) ($insights['hub_matches'] ?? ($publications->total() ?? 0));
    $showViewResources = $hubMatches > 10;
    $overviewTeaser = $hasInsights ? Str::limit(trim((string) ($insights['overview'] ?? '')), 160) : '';
    $aiChatQuery = request()->except('page');
@endphp

<style>
    .khub-search-ai {
        border: 1px solid #dbeafe;
        border-radius: 0.5rem;
        background: linear-gradient(180deg, #f8fbff 0%, #fff 100%);
        margin-bottom: 1rem;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
    }
    .khub-search-ai__banner {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.85rem;
        padding: 0.85rem 1rem;
        flex-wrap: wrap;
    }
    .khub-search-ai__brand {
        display: flex;
        gap: 0.65rem;
        min-width: 0;
        flex: 1 1 16rem;
    }
    .khub-search-ai__icon {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 0.45rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--theme-color-primary, #119A48);
        color: #fff;
        flex-shrink: 0;
        font-size: 0.95rem;
    }
    .khub-search-ai__title {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 800;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: #0f172a;
        line-height: 1.25;
    }
    .khub-search-ai__subtitle {
        margin: 0.2rem 0 0;
        font-size: 0.78rem;
        color: #64748b;
        line-height: 1.45;
    }
    .khub-search-ai__teaser {
        margin: 0.35rem 0 0;
        font-size: 0.8rem;
        color: #475569;
        line-height: 1.45;
    }
    .khub-search-ai__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.3rem;
        margin-top: 0.45rem;
    }
    .khub-search-ai__chip {
        font-size: 0.65rem;
        font-weight: 600;
        color: #64748b;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        padding: 0.12rem 0.45rem;
    }
    .khub-search-ai__chip--accent {
        color: var(--theme-color-primary, #119A48);
        border-color: color-mix(in srgb, var(--theme-color-primary, #119A48) 25%, #e2e8f0);
        background: #f0fdf4;
    }
    .khub-search-ai__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        align-items: center;
        justify-content: flex-end;
    }
    .khub-search-ai__toggle-icon {
        transition: transform 0.2s ease;
    }
    .khub-search-ai.is-open .khub-search-ai__toggle-icon {
        transform: rotate(180deg);
    }
    .khub-search-ai__panel {
        display: none;
        border-top: 1px solid #e2e8f0;
    }
    .khub-search-ai.is-open .khub-search-ai__panel {
        display: block;
    }
    .khub-search-ai__panel-inner {
        padding: 0.75rem 1rem 1rem;
    }
    .khub-search-ai__suggestions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        margin: 0 0 0.75rem;
        padding-top: 0.15rem;
    }
    .khub-search-ai__suggestion {
        border: 1px solid #dbeafe;
        background: #fff;
        color: #334155;
        border-radius: 999px;
        padding: 0.28rem 0.65rem;
        font-size: 0.72rem;
        cursor: pointer;
        transition: border-color 0.15s ease, color 0.15s ease, background 0.15s ease;
    }
    .khub-search-ai__suggestion:hover {
        border-color: var(--theme-color-primary, #119A48);
        color: var(--theme-color-primary, #119A48);
        background: #f0fdf4;
    }
    .khub-search-ai .ai-brief {
        margin-bottom: 0.75rem !important;
        border-radius: 0.35rem;
    }
    .khub-search-ai .ai-search-chat {
        margin-bottom: 0;
        border-radius: 0.35rem;
    }
    .khub-search-ai__resources-anchor:target {
        scroll-margin-top: 6rem;
    }
    @media (max-width: 575.98px) {
        .khub-search-ai__banner { flex-direction: column; }
        .khub-search-ai__actions { width: 100%; justify-content: stretch; }
        .khub-search-ai__actions .btn { flex: 1 1 auto; }
    }
</style>

<div class="khub-search-ai" id="khubSearchAiAssistant"
     data-chat-url="{{ route('records.search.ai-chat') }}"
     data-reset-url="{{ route('records.search.ai-chat.reset') }}"
     data-term="{{ e($searchTerm) }}"
     data-query='@json($aiChatQuery)'>
    <div class="khub-search-ai__banner">
        <div class="khub-search-ai__brand">
            <span class="khub-search-ai__icon" aria-hidden="true"><i class="fa-solid fa-microchip"></i></span>
            <div>
                <h2 class="khub-search-ai__title">{{ __('publications.search.ai_assistant_title') }}</h2>
                <p class="khub-search-ai__subtitle">{{ __('publications.search.ai_assistant_subtitle') }}</p>
                @if($overviewTeaser !== '')
                    <p class="khub-search-ai__teaser">{{ $overviewTeaser }}</p>
                @endif
                <div class="khub-search-ai__meta">
                    @if($searchTerm !== '')
                        <span class="khub-search-ai__chip">{{ $searchTerm }}</span>
                    @endif
                    @if($hubMatches > 0)
                        <span class="khub-search-ai__chip khub-search-ai__chip--accent">
                            {{ $hubMatches === 1 ? __('publications.search.ai_hub_matches_one') : __('publications.search.ai_hub_matches', ['count' => number_format($hubMatches)]) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="khub-search-ai__actions">
            @if($showViewResources)
                <button type="button" class="btn btn-sm btn-outline-success" id="khubSearchAiViewResources" data-open-target="resources">
                    <i class="fa fa-file-lines me-1"></i>{{ __('publications.search.ai_view_resources') }}
                </button>
            @endif
            <button type="button" class="btn btn-sm theme-bg text-white" id="khubSearchAiToggle" aria-expanded="false" aria-controls="khubSearchAiPanel">
                <i class="fa-solid fa-microchip me-1"></i>{{ __('publications.search.ai_ask_button') }}
                <i class="fa fa-chevron-down ms-1 khub-search-ai__toggle-icon" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <div class="khub-search-ai__panel" id="khubSearchAiPanel" hidden>
        <div class="khub-search-ai__panel-inner">
            <div class="khub-search-ai__suggestions" aria-label="{{ __('publications.search.ai_suggestions_label') }}">
                <button type="button" class="khub-search-ai__suggestion js-khub-search-ai-chip" data-prompt="{{ __('publications.search.ai_suggestion_summary', ['term' => $searchTerm]) }}">{{ __('publications.search.ai_suggestion_summary_short') }}</button>
                <button type="button" class="khub-search-ai__suggestion js-khub-search-ai-chip" data-prompt="{{ __('publications.search.ai_suggestion_policies') }}">{{ __('publications.search.ai_suggestion_policies_short') }}</button>
                <button type="button" class="khub-search-ai__suggestion js-khub-search-ai-chip" data-prompt="{{ __('publications.search.ai_suggestion_documents', ['term' => $searchTerm]) }}">{{ __('publications.search.ai_suggestion_documents_short') }}</button>
                <button type="button" class="khub-search-ai__suggestion js-khub-search-ai-chip" data-prompt="{{ __('publications.search.ai_suggestion_evidence') }}">{{ __('publications.search.ai_suggestion_evidence_short') }}</button>
            </div>

            @if($hasInsights)
                <div id="khubSearchAiResources" class="khub-search-ai__resources-anchor">
                    @include('publications.partials.ai_search_insights', ['embedded' => true, 'limitDocuments' => $showViewResources ? 5 : null])
                </div>
            @endif

            @include('publications.partials.ai_search_chat', ['embedded' => true])
        </div>
    </div>
</div>

<script>
(function () {
    function initKhubSearchAssistant(root) {
        if (!root || root.dataset.initialized === '1') return;
        root.dataset.initialized = '1';

        var panel = root.querySelector('#khubSearchAiPanel');
        var toggleBtn = root.querySelector('#khubSearchAiToggle');
        var viewResourcesBtn = root.querySelector('#khubSearchAiViewResources');
        var chatInput = root.querySelector('#aiSearchChatInput');

        function setOpen(open, focusTarget) {
            root.classList.toggle('is-open', open);
            if (panel) {
                panel.hidden = !open;
            }
            if (toggleBtn) {
                toggleBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            }
            if (open && focusTarget === 'chat' && chatInput) {
                setTimeout(function () { chatInput.focus(); }, 120);
            }
            if (open && focusTarget === 'resources') {
                var anchor = root.querySelector('#khubSearchAiResources');
                if (anchor) {
                    setTimeout(function () { anchor.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 120);
                }
            }
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                var willOpen = !root.classList.contains('is-open');
                setOpen(willOpen, willOpen ? 'chat' : null);
            });
        }

        if (viewResourcesBtn) {
            viewResourcesBtn.addEventListener('click', function () {
                setOpen(true, 'resources');
                var showAllBtn = root.querySelector('#khubSearchAiShowAllDocs');
                if (showAllBtn) showAllBtn.click();
            });
        }

        root.querySelectorAll('.js-khub-search-ai-chip').forEach(function (chip) {
            chip.addEventListener('click', function () {
                var prompt = chip.getAttribute('data-prompt') || '';
                setOpen(true, 'chat');
                if (chatInput && prompt) {
                    chatInput.value = prompt;
                    chatInput.focus();
                }
            });
        });

        if (typeof window.initAiSearchChat === 'function') {
            window.initAiSearchChat(root);
        }
    }

    window.initKhubSearchAssistant = function (scope) {
        var root = (scope || document).querySelector('#khubSearchAiAssistant');
        initKhubSearchAssistant(root);
    };

    document.addEventListener('DOMContentLoaded', function () {
        window.initKhubSearchAssistant(document);
    });
})();
</script>
@endif
