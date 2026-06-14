@php
    $pageTitle = $pageTitle ?? ('Search Resources & Discussions - ' . (settings()->site_name ?? 'Africa CDC Knowledge Hub'));
    $pageDescription = $pageDescription ?? 'Search publications, resources and discussion forums. Find public health content and join discussions across Africa.';
    $canonicalUrl = $canonicalUrl ?? url('records/search');
    $jsonLdFlags = $jsonLdFlags ?? (JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
@endphp
@extends('layouts.app')

@section('structured_data')
@if(!empty($searchJsonLd))
<script type="application/ld+json">{!! json_encode($searchJsonLd, $jsonLdFlags) !!}</script>
@endif
@endsection

@section('styles')
<style>
@include('partials.publications.publication_feed_card_styles')
@include('publications.partials.preview_modal_styles')
@include('publications.partials.records_search_sidebar_styles')
.records-search-main.is-loading { pointer-events: none; opacity: 0.55; transition: opacity .2s ease; }
</style>
@endsection

@section('content')
    <div class="gray py-4">
        <div class="container">
            <nav class="mb-3" aria-label="Breadcrumb">
                <ol class="breadcrumb mb-0" style="background: transparent; padding: 0; font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Browse resources</li>
                </ol>
            </nav>

            <div class="row">
                <div class="col-lg-8">
                    <div id="records-search-main" class="records-search-main position-relative">
                        <div id="records-search-heading">
                            @include('publications.partials.search_main_heading')
                        </div>
                        <div id="khub-search-ai-mount" class="khub-search-ai-mount">
                            @if(($searchAsyncLoad ?? false) && ($aiSearchEnabled ?? false) && mb_strlen(trim((string) request('term', ''))) >= 2)
                                @include('publications.partials.search_ai_async_loading')
                            @elseif(!($searchAsyncLoad ?? false) && ($aiSearchEnabled ?? false) && mb_strlen(trim((string) request('term', ''))) >= 2)
                                @include('publications.partials.ai_search_assistant')
                            @endif
                        </div>
                        <div id="records-search-body">
                            @include('publications.partials.search_main_body')
                        </div>
                    </div>
                    @if($searchAsyncLoad ?? false)
                        <script type="application/json" id="records-search-async-config">{!! json_encode([
                            'fragmentUrl' => url('records/search/fragment'),
                            'aiInsightsUrl' => route('records.search.ai-insights'),
                            'aiEnabled' => (bool) ($aiSearchEnabled ?? false),
                        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
                        <div id="records-search-async-root" aria-hidden="true"></div>
                    @endif
                </div>

                <div class="col-lg-4 records-search-sidebar" id="records-search-sidebar">
                    @include('publications.partials.records_search_sidebar_tags')
                    @include('partials.search.search_sidebar_facets')

                    <div id="records-search-sidebar-dynamic">
                        @include('publications.partials.search_sidebar_dynamic')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @auth
        @include('common.pdf-chat-modal')
    @endauth
@endsection

    @section('scripts')
        @include('common.select2')
        @auth
            @include('common.pdf-chat-js')
        @endauth
        @if((bool) (settings()->enable_ai_search ?? false))
        <script>
        window.KHUB_SEARCH_AI_STRINGS = {
            chatSend: @json(__('publications.search.ai_chat_send')),
            chatThinking: @json(__('publications.search.ai_chat_thinking')),
            chatError: @json(__('publications.search.ai_chat_error')),
            chatSubtitle: @json(__('publications.search.ai_chat_subtitle')),
            chatDocuments: @json(__('publications.search.ai_chat_documents'))
        };
        </script>
        <script src="{{ asset('js/khub-search-ai-init.js') }}?v={{ filemtime(public_path('js/khub-search-ai-init.js')) }}"></script>
        @endif
        <script>
        (function () {
            var FRAGMENT_URL = @json(url('records/search/fragment'));
            var AI_INSIGHTS_URL = @json(route('records.search.ai-insights'));
            var AI_SEARCH_ENABLED = @json((bool) (settings()->enable_ai_search ?? false));
            var SEARCH_ASYNC_LOAD = @json($searchAsyncLoad ?? false);
            var PUBLICATIONS_PAGE_URL = @json(route('records.search.publications-page'));
            var INFINITE_SCROLL_ENABLED = @json((settings()->search_pagination_mode ?? 'pagination') === 'infinite_scroll');
            var INFINITE_STATUS_LOADING = @json(__('publications.search.loading_more'));
            var INFINITE_STATUS_COMPLETE = @json(__('publications.search.all_results_loaded'));
            var INFINITE_STATUS_ERROR = @json(__('publications.search.load_more_error'));
            var RECORDS_SEARCH_TAG_ID = @json(request()->filled('tag') ? (string) request('tag') : null);
            var mainEl = document.getElementById('records-search-main');
            var sideDyn = document.getElementById('records-search-sidebar-dynamic');
            var infiniteObserver = null;
            var infiniteLoading = false;
            if (!mainEl) {
                return;
            }

            function facetArrayParamKeyRegex(base) {
                return new RegExp('^' + base.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\[(\\d+)\\]$');
            }

            function removeFacetParams(params) {
                var keys = Array.from(new Set(Array.from(params.keys())));
                keys.forEach(function (key) {
                    if (key === 'file_type' || key === 'category') {
                        params.delete(key);
                        return;
                    }
                    ['data_category_id', 'file_category_id', 'file_type_id'].forEach(function (root) {
                        if (key === root || key === root + '[]' || facetArrayParamKeyRegex(root).test(key)) {
                            params.delete(key);
                        }
                    });
                });
            }

            function normalizeFacetArrayQueryKeys(params) {
                ['file_type_id', 'data_category_id', 'file_category_id'].forEach(function (base) {
                    var re = facetArrayParamKeyRegex(base);
                    var entries = [];
                    params.forEach(function (val, key) {
                        var m = key.match(re);
                        if (m) {
                            entries.push({ idx: parseInt(m[1], 10), val: val, key: key });
                        }
                    });
                    if (!entries.length) {
                        return;
                    }
                    entries.sort(function (a, b) { return a.idx - b.idx; });
                    entries.forEach(function (e) {
                        params.delete(e.key);
                    });
                    entries.forEach(function (e) {
                        params.append(base + '[]', e.val);
                    });
                });
            }

            function updateStructuredData(data) {
                if (!data.structured_data) {
                    return;
                }
                var existing = document.getElementById('records-search-jsonld');
                if (existing) {
                    existing.remove();
                }
                var script = document.createElement('script');
                script.type = 'application/ld+json';
                script.id = 'records-search-jsonld';
                script.textContent = JSON.stringify(data.structured_data);
                document.head.appendChild(script);
            }

            function getMultiParam(params, base) {
                var bracketed = params.getAll(base + '[]');
                if (bracketed.length) {
                    return bracketed;
                }
                var indexed = [];
                params.forEach(function (val, key) {
                    if (facetArrayParamKeyRegex(base).test(key)) {
                        indexed.push({ key: key, val: val });
                    }
                });
                if (indexed.length) {
                    indexed.sort(function (a, b) {
                        var ai = parseInt(a.key.match(/\[(\d+)\]$/)[1], 10);
                        var bi = parseInt(b.key.match(/\[(\d+)\]$/)[1], 10);
                        return ai - bi;
                    });
                    return indexed.map(function (x) { return x.val; });
                }
                var single = params.get(base);
                if (single !== null && single !== '') {
                    return [single];
                }
                if (base === 'data_category_id') {
                    var c = params.get('category');
                    if (c !== null && c !== '') {
                        return [c];
                    }
                }
                if (base === 'file_type_id') {
                    var ft = params.get('file_type');
                    if (ft !== null && ft !== '') {
                        return [ft];
                    }
                }
                return null;
            }

            window.initSearchSidebarFacets = function () {
                var params = new URLSearchParams(window.location.search);
                ['file_type_id', 'data_category_id', 'file_category_id'].forEach(function (paramName) {
                    var selected = getMultiParam(params, paramName);
                    document.querySelectorAll('.search-facet-cb[data-param="' + paramName + '"]').forEach(function (cb) {
                        if (selected === null) {
                            cb.checked = false;
                        } else {
                            cb.checked = selected.indexOf(cb.value) !== -1 || selected.indexOf(String(cb.value)) !== -1;
                        }
                    });
                });
            };

            function getTagSlugFromPathname(pathname) {
                var path = pathname || window.location.pathname;
                var match = path.match(/\/records\/tag\/([^/]+)/);
                return match ? decodeURIComponent(match[1]) : null;
            }

            function tagIdFromSlug(slug) {
                if (!slug) {
                    return null;
                }
                var escaped = slug.replace(/\\/g, '\\\\').replace(/"/g, '\\"');
                var link = document.querySelector('a.records-sidebar-tags__link[data-tag-slug="' + escaped + '"]');
                return link && link.dataset.tagId ? String(link.dataset.tagId) : null;
            }

            function resolveRecordsTagApiParams(url) {
                var params = new URLSearchParams(url.search);
                var slug = getTagSlugFromPathname(url.pathname);
                if (slug) {
                    params.delete('tag');
                    var tagId = tagIdFromSlug(slug);
                    if (tagId) {
                        params.set('tag', tagId);
                    }
                }
                return params;
            }

            function buildRecordsBrowserUrl(url, apiParams) {
                var params = new URLSearchParams(apiParams.toString());
                if (getTagSlugFromPathname(url.pathname)) {
                    params.delete('tag');
                }
                normalizeFacetArrayQueryKeys(params);
                var qs = params.toString();
                return url.pathname + (qs ? '?' + qs : '');
            }

            function getActiveRecordsTagId() {
                var slug = getTagSlugFromPathname();
                if (slug) {
                    var fromSlug = tagIdFromSlug(slug);
                    if (fromSlug) {
                        return fromSlug;
                    }
                }
                var fromQuery = new URLSearchParams(window.location.search).get('tag');
                return fromQuery ? String(fromQuery) : (RECORDS_SEARCH_TAG_ID || null);
            }

            function updateFacetClearVisibility() {
                var wrap = document.getElementById('records-facet-clear-wrap');
                if (!wrap) {
                    return;
                }
                var anyChecked = document.querySelectorAll('.search-facet-cb:checked').length > 0;
                wrap.classList.toggle('d-none', !anyChecked);
            }

            function updateSidebarTagActiveState() {
                var curTagSlug = getTagSlugFromPathname();
                var curTagId = getActiveRecordsTagId();

                document.querySelectorAll('a.js-records-search-ajax.records-sidebar-tags__link, a.js-records-search-ajax.sidebar-tag-pill').forEach(function (a) {
                    try {
                        var linkTagId = a.dataset.tagId || null;
                        var linkSlug = a.dataset.tagSlug || null;
                        var u = new URL(a.getAttribute('href'), window.location.origin);
                        if (!linkTagId) {
                            linkTagId = u.searchParams.get('tag');
                        }
                        if (!linkSlug) {
                            var tagSlugMatch = u.pathname.match(/\/records\/tag\/([^/]+)/);
                            linkSlug = tagSlugMatch ? decodeURIComponent(tagSlugMatch[1]) : null;
                        }
                        var active = false;
                        if (curTagId && linkTagId && String(linkTagId) === String(curTagId)) {
                            active = true;
                        }
                        if (curTagSlug && linkSlug && String(linkSlug) === String(curTagSlug)) {
                            active = true;
                        }
                        if (a.classList.contains('records-sidebar-tags__link')) {
                            a.classList.toggle('is-active', active);
                        } else {
                            a.classList.toggle('sidebar-tag-pill--active', active);
                        }
                    } catch (e) { /* ignore */ }
                });

                document.querySelectorAll('.records-sidebar-card--tags .records-sidebar-clear-wrap').forEach(function (wrap) {
                    wrap.classList.toggle('d-none', !curTagId);
                });
            }

            function applyRecordsSearchFragment(data, opts) {
                opts = opts || {};
                var root = opts.mainEl || mainEl;
                if (!root || !data) {
                    return;
                }

                var headingEl = root.querySelector('#records-search-heading');
                var bodyEl = root.querySelector('#records-search-body');

                if (headingEl && data.heading_html) {
                    headingEl.innerHTML = data.heading_html;
                }
                if (bodyEl && data.body_html) {
                    bodyEl.innerHTML = data.body_html;
                } else if (bodyEl && data.main_html) {
                    bodyEl.innerHTML = data.main_html;
                } else if (data.main_html) {
                    root.innerHTML = data.main_html;
                }
                if (sideDyn && data.sidebar_html) {
                    sideDyn.innerHTML = data.sidebar_html;
                }
                if (data.page_title) {
                    document.title = data.page_title;
                }
                var md = document.querySelector('meta[name="description"]');
                if (md && data.meta_description) {
                    md.setAttribute('content', data.meta_description);
                }
                var cn = document.querySelector('link[rel="canonical"]');
                if (cn && data.canonical_url) {
                    cn.setAttribute('href', data.canonical_url);
                }
                var ogU = document.querySelector('meta[property="og:url"]');
                if (ogU && data.canonical_url) {
                    ogU.setAttribute('content', data.canonical_url);
                }
                var twU = document.querySelector('meta[name="twitter:url"]');
                if (twU && data.canonical_url) {
                    twU.setAttribute('content', data.canonical_url);
                }
                updateStructuredData(data);
                window.initSearchSidebarFacets();
                updateFacetClearVisibility();
                updateSidebarTagActiveState();
                if (typeof window.initKhubSearchAssistant === 'function') {
                    window.initKhubSearchAssistant(document);
                } else if (typeof window.initAiSearchChat === 'function') {
                    window.initAiSearchChat(root);
                }
                if (typeof window.initRecordsSearchInfiniteScroll === 'function') {
                    window.initRecordsSearchInfiniteScroll(bodyEl || root);
                }
            }

            window.applyRecordsSearchFragment = applyRecordsSearchFragment;

            function loadAiInsightsFragment(params, opts) {
                opts = opts || {};
                var aiMount = opts.aiMount || document.getElementById('khub-search-ai-mount');
                if (!aiMount || !AI_SEARCH_ENABLED || !AI_INSIGHTS_URL) {
                    return Promise.resolve(null);
                }
                var term = params.get('term');
                if (!term || String(term).trim().length < 2) {
                    aiMount.innerHTML = '';
                    return Promise.resolve(null);
                }

                var qs = params.toString();
                var url = AI_INSIGHTS_URL + (qs ? '?' + qs : '');

                return fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                    .then(function (r) {
                        if (!r.ok) {
                            return null;
                        }
                        return r.json();
                    })
                    .then(function (data) {
                        if (data && data.ok && data.assistant_html && String(data.assistant_html).trim() !== '') {
                            aiMount.innerHTML = data.assistant_html;
                            if (typeof window.initKhubSearchAssistant === 'function') {
                                window.initKhubSearchAssistant(document);
                            }
                        } else if (!data || !data.ok) {
                            aiMount.innerHTML = '';
                        }
                        return data;
                    })
                    .catch(function () {
                        aiMount.innerHTML = '';
                        return null;
                    });
            }

            window.loadAiInsightsFragment = loadAiInsightsFragment;

            function formatInfiniteStatus(loaded, total) {
                return 'Showing ' + Number(loaded).toLocaleString() + ' of ' + Number(total).toLocaleString() + ' publications';
            }

            function disconnectInfiniteScroll() {
                if (infiniteObserver) {
                    infiniteObserver.disconnect();
                    infiniteObserver = null;
                }
                infiniteLoading = false;
            }

            function updateInfiniteFooter(root, data) {
                var wrap = (root || mainEl).querySelector('#records-search-publications');
                var footer = (root || mainEl).querySelector('#records-search-infinite-footer');
                if (!wrap || !footer) {
                    return;
                }

                wrap.setAttribute('data-current-page', String(data.current_page || 1));
                wrap.setAttribute('data-last-page', String(data.last_page || 1));
                wrap.setAttribute('data-total', String(data.total || 0));
                wrap.setAttribute('data-loaded', String(data.loaded_count || 0));

                var status = footer.querySelector('#records-search-infinite-status');
                if (status) {
                    status.textContent = formatInfiniteStatus(data.loaded_count || 0, data.total || 0);
                }

                var loader = footer.querySelector('#records-search-infinite-loader');
                if (loader) {
                    loader.classList.add('d-none');
                }

                var complete = footer.querySelector('#records-search-infinite-complete');
                var sentinel = footer.querySelector('#records-search-infinite-sentinel');

                if (data.has_more) {
                    if (complete) {
                        complete.remove();
                    }
                    if (!sentinel) {
                        sentinel = document.createElement('div');
                        sentinel.id = 'records-search-infinite-sentinel';
                        sentinel.className = 'records-search-infinite-sentinel';
                        sentinel.setAttribute('aria-hidden', 'true');
                        footer.appendChild(sentinel);
                    }
                } else {
                    if (sentinel) {
                        sentinel.remove();
                    }
                    if (!complete) {
                        complete = document.createElement('p');
                        complete.id = 'records-search-infinite-complete';
                        complete.className = 'text-muted small mb-0';
                        complete.textContent = INFINITE_STATUS_COMPLETE;
                        footer.appendChild(complete);
                    }
                }
            }

            function loadNextSearchPage(root) {
                if (!INFINITE_SCROLL_ENABLED || infiniteLoading) {
                    return;
                }

                var scope = root || mainEl;
                var wrap = scope.querySelector('#records-search-publications');
                if (!wrap || wrap.getAttribute('data-infinite-scroll') !== '1') {
                    return;
                }

                var currentPage = parseInt(wrap.getAttribute('data-current-page') || '1', 10);
                var lastPage = parseInt(wrap.getAttribute('data-last-page') || '1', 10);
                if (currentPage >= lastPage) {
                    return;
                }

                var footer = scope.querySelector('#records-search-infinite-footer');
                var loader = footer ? footer.querySelector('#records-search-infinite-loader') : null;
                var list = scope.querySelector('#records-search-publications-list');
                if (!list) {
                    return;
                }

                infiniteLoading = true;
                if (loader) {
                    loader.classList.remove('d-none');
                }

                var params = resolveRecordsTagApiParams(new URL(window.location.href));
                params.delete('page');
                params.set('page', String(currentPage + 1));
                normalizeFacetArrayQueryKeys(params);

                fetch(PUBLICATIONS_PAGE_URL + '?' + params.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                    .then(function (r) {
                        if (!r.ok) {
                            throw new Error('page failed');
                        }
                        return r.json();
                    })
                    .then(function (data) {
                        if (!data.ok || !data.html) {
                            throw new Error('invalid payload');
                        }

                        list.insertAdjacentHTML('beforeend', data.html);
                        updateInfiniteFooter(scope, data);

                        if (data.has_more) {
                            window.initRecordsSearchInfiniteScroll(scope);
                        } else {
                            disconnectInfiniteScroll();
                        }
                    })
                    .catch(function () {
                        if (footer && !footer.querySelector('#records-search-infinite-retry')) {
                            var retry = document.createElement('button');
                            retry.type = 'button';
                            retry.id = 'records-search-infinite-retry';
                            retry.className = 'btn btn-sm btn-outline-secondary mt-2';
                            retry.textContent = INFINITE_STATUS_ERROR;
                            retry.addEventListener('click', function () {
                                retry.remove();
                                loadNextSearchPage(scope);
                            });
                            footer.appendChild(retry);
                        }
                    })
                    .finally(function () {
                        infiniteLoading = false;
                        if (loader) {
                            loader.classList.add('d-none');
                        }
                    });
            }

            window.initRecordsSearchInfiniteScroll = function (scope) {
                if (!INFINITE_SCROLL_ENABLED) {
                    disconnectInfiniteScroll();
                    return;
                }

                disconnectInfiniteScroll();

                var root = scope || mainEl;
                var wrap = root.querySelector('#records-search-publications');
                var sentinel = root.querySelector('#records-search-infinite-sentinel');
                if (!wrap || wrap.getAttribute('data-infinite-scroll') !== '1' || !sentinel) {
                    return;
                }

                if (!('IntersectionObserver' in window)) {
                    sentinel.addEventListener('click', function () {
                        loadNextSearchPage(root);
                    });
                    sentinel.style.height = '24px';
                    sentinel.style.cursor = 'pointer';
                    return;
                }

                infiniteObserver = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            loadNextSearchPage(root);
                        }
                    });
                }, { root: null, rootMargin: '240px 0px', threshold: 0.01 });

                infiniteObserver.observe(sentinel);
            };

            function runRecordsSearchAjax(fullUrl, opts) {
                opts = opts || {};
                var push = opts.push !== false;
                var u;
                try {
                    u = new URL(fullUrl, window.location.origin);
                } catch (e) {
                    window.location.assign(fullUrl);
                    return;
                }
                var fragParams = resolveRecordsTagApiParams(u);
                normalizeFacetArrayQueryKeys(fragParams);
                var apiQs = fragParams.toString();
                var browserUrl = buildRecordsBrowserUrl(u, fragParams);
                var fragUrl = FRAGMENT_URL + (apiQs ? '?' + apiQs : '');
                var aiMount = document.getElementById('khub-search-ai-mount');
                if (aiMount && AI_SEARCH_ENABLED && fragParams.get('term') && String(fragParams.get('term')).trim().length >= 2) {
                    aiMount.innerHTML = '<p class="text-muted small mb-0"><i class="fa fa-spinner fa-spin me-1" aria-hidden="true"></i>' + @json(__('publications.search.loading_ai')) + '</p>';
                } else if (aiMount) {
                    aiMount.innerHTML = '';
                }
                mainEl.classList.add('is-loading');
                disconnectInfiniteScroll();

                var fragmentPromise = fetch(fragUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                    .then(function (r) {
                        if (!r.ok) {
                            throw new Error('fragment failed');
                        }
                        return r.json();
                    });

                var aiPromise = loadAiInsightsFragment(fragParams, { aiMount: aiMount });

                Promise.all([fragmentPromise, aiPromise])
                    .then(function (results) {
                        var data = results[0];
                        applyRecordsSearchFragment(data, { mainEl: mainEl });
                        if (push) {
                            history.pushState({ recordsSearchAjax: 1 }, '', browserUrl);
                        }
                    })
                    .catch(function () {
                        window.location.assign(window.location.origin + browserUrl);
                    })
                    .finally(function () {
                        mainEl.classList.remove('is-loading');
                    });
            }

            var facetDebounceTimer = null;
            var FACET_DEBOUNCE_MS = 280;

            function applySearchSidebarFacets() {
                if (facetDebounceTimer) {
                    clearTimeout(facetDebounceTimer);
                    facetDebounceTimer = null;
                }
                var u = new URL(window.location.href);
                var params = resolveRecordsTagApiParams(u);
                removeFacetParams(params);
                params.delete('page');
                var groups = [
                    { param: 'file_type_id', selector: '.search-facet-cb[data-param="file_type_id"]' },
                    { param: 'data_category_id', selector: '.search-facet-cb[data-param="data_category_id"]' },
                    { param: 'file_category_id', selector: '.search-facet-cb[data-param="file_category_id"]' }
                ];
                groups.forEach(function (g) {
                    var boxes = document.querySelectorAll(g.selector);
                    if (!boxes.length) {
                        return;
                    }
                    var checked = document.querySelectorAll(g.selector + ':checked');
                    if (checked.length === 0 || checked.length === boxes.length) {
                        return;
                    }
                    checked.forEach(function (cb) {
                        params.append(g.param + '[]', cb.value);
                    });
                });
                var browserUrl = buildRecordsBrowserUrl(u, params);
                runRecordsSearchAjax(window.location.origin + browserUrl, { push: true });
            }

            function clearSearchSidebarFacets() {
                document.querySelectorAll('.search-facet-cb').forEach(function (cb) {
                    cb.checked = false;
                });
                var u = new URL(window.location.href);
                var params = resolveRecordsTagApiParams(u);
                removeFacetParams(params);
                params.delete('page');
                var browserUrl = buildRecordsBrowserUrl(u, params);
                runRecordsSearchAjax(window.location.origin + browserUrl, { push: true });
            }

            function scheduleApplySearchSidebarFacets() {
                if (facetDebounceTimer) {
                    clearTimeout(facetDebounceTimer);
                }
                facetDebounceTimer = setTimeout(function () {
                    facetDebounceTimer = null;
                    applySearchSidebarFacets();
                }, FACET_DEBOUNCE_MS);
            }

            document.addEventListener('DOMContentLoaded', function () {
                if (getTagSlugFromPathname() && window.location.search.indexOf('tag=') !== -1) {
                    var cleanUrl = buildRecordsBrowserUrl(
                        new URL(window.location.href),
                        resolveRecordsTagApiParams(new URL(window.location.href))
                    );
                    if (cleanUrl !== window.location.pathname + window.location.search) {
                        history.replaceState({ recordsSearchAjax: 1 }, '', cleanUrl);
                    }
                }
                window.initSearchSidebarFacets();
                updateFacetClearVisibility();
                updateSidebarTagActiveState();
                if (typeof window.initRecordsSearchInfiniteScroll === 'function') {
                    window.initRecordsSearchInfiniteScroll(mainEl);
                }
                var side = document.getElementById('records-search-sidebar');
                if (side) {
                    side.addEventListener('change', function (e) {
                        if (e.target && e.target.classList && e.target.classList.contains('search-facet-cb')) {
                            updateFacetClearVisibility();
                            scheduleApplySearchSidebarFacets();
                        }
                    });
                    side.addEventListener('click', function (e) {
                        if (e.target.closest('.js-records-clear-facets')) {
                            e.preventDefault();
                            clearSearchSidebarFacets();
                            return;
                        }
                        var a = e.target.closest('a.js-records-search-ajax');
                        if (!a) {
                            return;
                        }
                        if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey || e.button !== 0) {
                            return;
                        }
                        e.preventDefault();
                        var href = a.getAttribute('href');
                        if (href) {
                            var nu = new URL(href, window.location.origin);
                            nu.searchParams.delete('page');
                            runRecordsSearchAjax(nu.toString(), { push: true });
                        }
                    });
                }
            });

            window.normalizeRecordsSearchFacetParams = normalizeFacetArrayQueryKeys;

            window.addEventListener('popstate', function () {
                if (!document.getElementById('records-search-main')) {
                    return;
                }
                if (facetDebounceTimer) {
                    clearTimeout(facetDebounceTimer);
                    facetDebounceTimer = null;
                }
                runRecordsSearchAjax(window.location.href, { push: false });
            });
        })();
        </script>
        @if($searchAsyncLoad ?? false)
        <script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
        <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
        <script src="{{ asset('js/records-search-async.js') }}?v={{ filemtime(public_path('js/records-search-async.js')) }}"></script>
        @endif
        @include('common.attachment_js')
        @include('publications.partials.preview_modal')
        @include('partials.publications.publication_feed_card_scripts')
    @endsection
