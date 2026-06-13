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
                    <div id="khub-search-ai-mount" class="khub-search-ai-mount">
                        @if(($searchAsyncLoad ?? false) && ($aiSearchEnabled ?? false) && mb_strlen(trim((string) request('term', ''))) >= 2)
                            @include('publications.partials.search_ai_async_loading')
                        @endif
                    </div>
                    <div id="records-search-main" class="records-search-main position-relative">
                        @include('publications.partials.search_main_column')
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

                <div class="col-lg-4" id="records-search-sidebar">
                    @php
                        $recordsSearchTagQuery = request()->except('page');
                        $sidebarTagsList = (isset($tags) && count($tags) > 0) ? $tags->take(10) : \App\Models\Tag::query()->orderBy('tag_text', 'asc')->limit(10)->get();
                    @endphp
                    @include('publications.partials.contributor_sidebar_styles')
                    <style>
                        .sidebar-content{background:#fff;border:1px solid #e2e8f0;border-radius:0.5rem;padding:18px;box-shadow:0 2px 8px rgba(0,0,0,.04);margin-bottom:20px}
                        .search-sidebar-panel{margin-bottom:1.25rem}
                        .search-sidebar-list{display:flex;flex-direction:column;gap:0.5rem}
                        .search-sidebar-list__link{display:flex;flex-direction:column;gap:0.25rem;padding:0.75rem 0.85rem;border:1px solid #e2e8f0;border-radius:8px;text-decoration:none;background:#f8fafc;transition:border-color .15s ease,background .15s ease,box-shadow .15s ease}
                        .search-sidebar-list__link:hover{border-color:rgba(17,154,72,.35);background:#fff;box-shadow:0 2px 8px rgba(15,23,42,.05)}
                        .search-sidebar-list__title{font-size:0.9rem;font-weight:600;color:#0f172a;line-height:1.35}
                        .search-sidebar-list__excerpt{font-size:0.8rem;color:#64748b;line-height:1.45}
                        .search-sidebar-list__meta{display:flex;flex-wrap:wrap;gap:0.5rem 0.75rem;font-size:0.75rem;color:#94a3b8}
                        .search-sidebar-list__meta i{color:var(--theme-color-primary,#119A48);margin-right:0.2rem}
                        .search-sidebar-view-all{display:inline-block;margin-top:0.85rem;font-size:0.8125rem;font-weight:600;color:var(--theme-color-primary,#119A48);text-decoration:none}
                        .search-sidebar-view-all:hover{text-decoration:underline}
                        .sidebar-content h5.popular-tags-title{margin-bottom:10px;font-size:15px;font-weight:500;text-transform:capitalize;color:#2d3748}
                        .search-facet-filters h5.popular-tags-title{font-size:15px;font-weight:500;margin-bottom:10px;color:#2d3748}
                        .search-facet-filters h5.facet-subheading{font-size:15px;font-weight:500;margin:0 0 8px 0;color:#2d3748;text-transform:none}
                        .sidebar-content .btn-primary{background-color:var(--theme-color-primary, #119A48);border-color:var(--theme-color-primary, #119A48);font-weight:500;border-radius:0.375rem}
                        .sidebar-content .btn-primary:hover{background-color:var(--theme-color-primary, #0d7a38);border-color:var(--theme-color-primary, #0d7a38);filter:brightness(1.05)}
                        .sidebar-content .btn-secondary{font-weight:500;border-radius:0.375rem}
                        .sidebar-tags{display:flex;flex-wrap:wrap;gap:0.5rem}
                        .sidebar-tag-pill{display:inline-block;padding:0.3rem 0.7rem;font-size:0.8rem;font-weight:500;color:#ffffff !important;text-decoration:none;border-radius:0.25rem;transition:all 0.2s ease;white-space:nowrap;background-color:var(--theme-color-primary, #119A48) !important;border:1px solid rgba(17,154,72,0.3)}
                        .sidebar-tag-pill:hover{transform:translateY(-2px);box-shadow:0 2px 6px rgba(17,154,72,0.3);color:#ffffff !important;text-decoration:none;background-color:var(--theme-color-primary, #119A48) !important}
                        .sidebar-tag-pill.sidebar-tag-pill--active{box-shadow:0 0 0 2px #fff,0 0 0 4px var(--theme-color-primary, #119A48)}
                        .records-search-main.is-loading{pointer-events:none;opacity:0.55;transition:opacity .2s ease}
                        .search-facet-filters .facet-checkbox-column{display:flex;flex-direction:column;align-items:flex-start;gap:0}
                        .search-facet-filters .facet-checkbox-column--2col{display:grid;grid-template-columns:1fr 1fr;gap:0.2rem 0.45rem;align-items:start;width:100%}
                        .search-facet-filters .facet-checkbox-column--2col .facet-checkbox-label{margin-bottom:0}
                        .search-facet-filters .facet-checkbox-label{display:flex;align-items:flex-start;font-size:0.62rem;line-height:1.3;margin-bottom:0.28rem;cursor:pointer;color:#334155;padding:0.18rem 0.32rem;border-radius:0.25rem;transition:background-color .15s ease,color .15s ease}
                        .search-facet-filters .facet-checkbox-label input{margin-top:0.12rem;margin-right:0.3rem;flex-shrink:0;width:0.85rem;height:0.85rem;accent-color:var(--theme-color-primary, #119A48)}
                        .search-facet-filters .facet-checkbox-label span{font-size:0.62rem}
                        .search-facet-filters .facet-checkbox-label:has(input:checked){background-color:var(--theme-color-primary, #119A48);color:#fff}
                        .search-facet-filters .facet-checkbox-label:has(input:checked) span{color:#fff}
                    </style>

                    @if($sidebarTagsList->count() > 0)
                    <div class="sidebar-content">
                        <h5 class="popular-tags-title">{{ __('ui_body.footer_popular_tags') }}</h5>
                      <div class="sidebar-tags">
                            @foreach($sidebarTagsList as $tag)
                                @php
                                    $tagHref = tag_records_url($tag, true, $recordsSearchTagQuery);
                                    $tagActive = request('tag') !== null && request('tag') !== '' && (string) request('tag') === (string) $tag->id;
                    @endphp
                                <a href="{{ $tagHref }}"
                                   class="sidebar-tag-pill js-records-search-ajax{{ $tagActive ? ' sidebar-tag-pill--active' : '' }}"
                                   title="{{ $tag->tag_text }}">
                                    {{ truncate($tag->tag_text, 15) }}
                        </a>
                        @endforeach
                      </div>
                    </div>
                    @endif

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

            function updateSidebarTagActiveState() {
                var cur = new URLSearchParams(window.location.search).get('tag');
                document.querySelectorAll('a.js-records-search-ajax.sidebar-tag-pill').forEach(function (a) {
                    try {
                        var u = new URL(a.getAttribute('href'), window.location.origin);
                        var t = u.searchParams.get('tag');
                        a.classList.toggle('sidebar-tag-pill--active', cur !== null && cur !== '' && String(t) === String(cur));
                    } catch (e) { /* ignore */ }
                });
            }

            function placeAiMountAfterHeading(root) {
                var aiMount = document.getElementById('khub-search-ai-mount');
                var h1 = root && root.querySelector('h1');
                if (!aiMount || !h1 || !h1.parentNode) {
                    return;
                }
                h1.insertAdjacentElement('afterend', aiMount);
            }

            function applyRecordsSearchFragment(data, opts) {
                opts = opts || {};
                var root = opts.mainEl || mainEl;
                if (!root || !data) {
                    return;
                }
                root.innerHTML = data.main_html;
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
                updateSidebarTagActiveState();
                if (typeof window.initKhubSearchAssistant === 'function') {
                    window.initKhubSearchAssistant(document);
                } else if (typeof window.initAiSearchChat === 'function') {
                    window.initAiSearchChat(root);
                }
                if (typeof window.initRecordsSearchInfiniteScroll === 'function') {
                    window.initRecordsSearchInfiniteScroll(root);
                }
                placeAiMountAfterHeading(root);
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
                        if (data && data.ok && data.assistant_html) {
                            aiMount.innerHTML = data.assistant_html;
                            if (typeof window.initKhubSearchAssistant === 'function') {
                                window.initKhubSearchAssistant(document);
                            }
                        } else {
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

                var params = new URLSearchParams(window.location.search);
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
                var fragParams = new URLSearchParams(u.search);
                normalizeFacetArrayQueryKeys(fragParams);
                var qs = fragParams.toString();
                var fragUrl = FRAGMENT_URL + (qs ? '?' + qs : '');
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
                            history.pushState({ recordsSearchAjax: 1 }, '', u.pathname + (qs ? '?' + qs : ''));
                        }
                    })
                    .catch(function () {
                        window.location.assign(u.href);
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
                var params = new URLSearchParams(window.location.search);
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
                var q = params.toString();
                var url = window.location.origin + window.location.pathname + (q ? '?' + q : '');
                runRecordsSearchAjax(url, { push: true });
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
                if (SEARCH_ASYNC_LOAD) {
                    placeAiMountAfterHeading(mainEl);
                }
                window.initSearchSidebarFacets();
                updateSidebarTagActiveState();
                if (typeof window.initRecordsSearchInfiniteScroll === 'function') {
                    window.initRecordsSearchInfiniteScroll(mainEl);
                }
                var side = document.getElementById('records-search-sidebar');
                if (side) {
                    side.addEventListener('change', function (e) {
                        if (e.target && e.target.classList && e.target.classList.contains('search-facet-cb')) {
                            scheduleApplySearchSidebarFacets();
                        }
                    });
                    side.addEventListener('click', function (e) {
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
    @endsection
