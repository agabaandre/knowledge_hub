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
                    <div id="records-search-main" class="records-search-main position-relative">
                        @include('publications.partials.search_main_column')
                    </div>
                </div>

                <div class="col-lg-4" id="records-search-sidebar">
                    @php
                        $recordsSearchTagQuery = request()->except('page');
                        $sidebarTagsList = (isset($tags) && count($tags) > 0) ? $tags->take(10) : \App\Models\Tag::query()->orderBy('tag_text', 'asc')->limit(10)->get();
                    @endphp
                    <style>
                        .sidebar-content{background:#fff;border:1px solid #e2e8f0;border-radius:0.5rem;padding:18px;box-shadow:0 2px 8px rgba(0,0,0,.04);margin-bottom:20px}
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
                        <h5 class="popular-tags-title">{{ __('Popular Tags') }}</h5>
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
    @endsection

    @section('scripts')
        @include('common.select2')
        <script>
        (function () {
            var FRAGMENT_URL = @json(url('records/search/fragment'));
            var mainEl = document.getElementById('records-search-main');
            var sideDyn = document.getElementById('records-search-sidebar-dynamic');
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
                mainEl.classList.add('is-loading');
                fetch(fragUrl, {
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
                    })
                    .then(function (data) {
                        mainEl.innerHTML = data.main_html;
                        if (sideDyn) {
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
                        if (push) {
                            history.pushState({ recordsSearchAjax: 1 }, '', u.pathname + (qs ? '?' + qs : ''));
                        }
                        window.initSearchSidebarFacets();
                        updateSidebarTagActiveState();
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
                window.initSearchSidebarFacets();
                updateSidebarTagActiveState();
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
    @endsection
