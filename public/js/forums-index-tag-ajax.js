/**
 * AJAX tag filtering for /forums (smart URLs + fragment reload without full page navigation).
 */
(function () {
    var config = window.forumsTagAjaxConfig || {};
    var fragmentUrl = config.fragmentUrl;
    var loading = false;

    function getTagSlugFromPathname(pathname) {
        var path = pathname || window.location.pathname;
        var match = path.match(/\/forums\/tag\/([^/]+)/);
        return match ? decodeURIComponent(match[1]) : null;
    }

    function tagTextFromSlug(slug) {
        if (!slug) {
            return null;
        }
        var escaped = slug.replace(/\\/g, '\\\\').replace(/"/g, '\\"');
        var link = document.querySelector('a.js-forums-tag-ajax[data-tag-slug="' + escaped + '"]');
        if (link && link.getAttribute('data-tag-text')) {
            return link.getAttribute('data-tag-text');
        }
        return null;
    }

    /**
     * Build API query params for the fragment endpoint (always includes tag text when filtered).
     */
    function resolveTagApiParams(url) {
        var params = new URLSearchParams(url.search);
        var slug = getTagSlugFromPathname(url.pathname);

        if (slug) {
            params.delete('tag');
            var text = tagTextFromSlug(slug);
            if (text) {
                params.set('tag', text);
            }
        }

        params.delete('page');
        return params;
    }

    /**
     * Browser URL: smart tag paths never carry a redundant ?tag= query param.
     */
    function buildBrowserUrl(url, apiParams) {
        var params = new URLSearchParams(apiParams.toString());
        if (getTagSlugFromPathname(url.pathname)) {
            params.delete('tag');
        }
        var qs = params.toString();
        return url.pathname + (qs ? '?' + qs : '');
    }

    function formatStatus(loaded, total) {
        return 'Showing ' + Number(loaded).toLocaleString() + ' of ' + Number(total).toLocaleString() + ' discussions';
    }

    function updateSidebarActiveState(activeTag) {
        var slug = getTagSlugFromPathname();
        var normalizedActive = activeTag ? String(activeTag).toLowerCase() : '';

        document.querySelectorAll('a.js-forums-tag-ajax[data-tag-text]').forEach(function (link) {
            var text = (link.getAttribute('data-tag-text') || '').toLowerCase();
            var linkSlug = link.getAttribute('data-tag-slug') || '';
            var isActive = false;
            if (normalizedActive && text === normalizedActive) {
                isActive = true;
            } else if (!normalizedActive && slug && linkSlug === slug) {
                isActive = true;
            }
            link.classList.toggle('is-active', isActive);
        });

        document.querySelectorAll('.forums-sidebar-clear-wrap').forEach(function (wrap) {
            wrap.classList.toggle('d-none', !activeTag);
        });
    }

    function updateInfiniteFooter(wrap, data) {
        var footer = document.getElementById('forums-infinite-footer');
        if (!footer) {
            return;
        }

        var status = footer.querySelector('#forums-infinite-status');
        if (status) {
            status.textContent = formatStatus(data.loaded_count || 0, data.total || 0);
        }

        var loader = footer.querySelector('#forums-infinite-loader');
        if (loader) {
            loader.classList.add('d-none');
        }

        var complete = footer.querySelector('#forums-infinite-complete');
        var sentinel = footer.querySelector('#forums-infinite-sentinel');

        if (data.has_more) {
            if (complete) {
                complete.remove();
            }
            if (!sentinel) {
                sentinel = document.createElement('div');
                sentinel.id = 'forums-infinite-sentinel';
                sentinel.className = 'forums-infinite-sentinel';
                sentinel.setAttribute('aria-hidden', 'true');
                footer.appendChild(sentinel);
            }
        } else {
            if (sentinel) {
                sentinel.remove();
            }
            if (!complete) {
                complete = document.createElement('p');
                complete.id = 'forums-infinite-complete';
                complete.className = 'text-muted small mb-0';
                complete.textContent = window.FORUMS_INFINITE_STATUS_COMPLETE || 'All discussions loaded';
                footer.appendChild(complete);
            }
        }

        if (wrap) {
            if ((data.total || 0) > 0 && config.infiniteScrollEnabled) {
                wrap.setAttribute('data-infinite-scroll', '1');
            } else {
                wrap.removeAttribute('data-infinite-scroll');
            }
        }
    }

    function applyFragment(data, browserUrl) {
        var list = document.getElementById('forums-list');
        var wrap = document.getElementById('forums-list-wrap');
        if (!list || !data) {
            return;
        }

        list.innerHTML = data.list_html || '';

        if (wrap) {
            wrap.setAttribute('data-current-page', String(data.current_page || 1));
            wrap.setAttribute('data-last-page', String(data.last_page || 1));
            wrap.setAttribute('data-total', String(data.total || 0));
            wrap.setAttribute('data-loaded', String(data.loaded_count || 0));
        }

        updateInfiniteFooter(wrap, data);

        if (typeof window.initForumListingInlineFileUploads === 'function') {
            window.initForumListingInlineFileUploads();
        }
        if (window.ForumsIndexFilters && typeof window.ForumsIndexFilters.refreshCards === 'function') {
            window.ForumsIndexFilters.refreshCards();
        }
        if (typeof window.initForumContributorCarousels === 'function') {
            window.initForumContributorCarousels(list);
        }
        if (typeof window.initForumsInfiniteScroll === 'function') {
            window.initForumsInfiniteScroll();
        }

        window.forumsIndexForumIds = data.forum_ids || [];
        if (window.khubAiChat) {
            window.khubAiChat.forum_ids = data.forum_ids || [];
        }

        if (browserUrl) {
            var canon = document.querySelector('link[rel="canonical"]');
            if (canon && data.canonical_url) {
                canon.setAttribute('href', data.canonical_url);
            }
        }

        updateSidebarActiveState(data.active_tag || null);
    }

    function runForumsTagAjax(fullUrl, opts) {
        opts = opts || {};
        if (!fragmentUrl || loading) {
            return;
        }

        var push = opts.push !== false;
        var u;
        try {
            u = new URL(fullUrl, window.location.origin);
        } catch (e) {
            window.location.assign(fullUrl);
            return;
        }

        var apiParams = resolveTagApiParams(u);
        var apiQs = apiParams.toString();
        var browserUrl = buildBrowserUrl(u, apiParams);
        var wrap = document.getElementById('forums-list-wrap');
        loading = true;
        if (wrap) {
            wrap.classList.add('is-loading');
        }

        fetch(fragmentUrl + (apiQs ? '?' + apiQs : ''), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('fragment failed');
                }
                return response.json();
            })
            .then(function (data) {
                if (!data.ok) {
                    throw new Error('invalid payload');
                }
                if (push) {
                    history.pushState({ forumsTagAjax: 1 }, '', browserUrl);
                }
                applyFragment(data, browserUrl);
            })
            .catch(function () {
                window.location.assign(browserUrl);
            })
            .finally(function () {
                loading = false;
                if (wrap) {
                    wrap.classList.remove('is-loading');
                }
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var initialSlug = getTagSlugFromPathname();
        if (initialSlug && window.location.search.indexOf('tag=') !== -1) {
            var cleanUrl = buildBrowserUrl(window.location, resolveTagApiParams(window.location));
            if (cleanUrl !== window.location.pathname + window.location.search) {
                history.replaceState({ forumsTagAjax: 1 }, '', cleanUrl);
            }
        }
        updateSidebarActiveState(config.activeTag || null);

        var sidebar = document.querySelector('.forums-sidebar');
        if (sidebar) {
            sidebar.addEventListener('click', function (e) {
                var link = e.target.closest('a.js-forums-tag-ajax');
                if (!link) {
                    return;
                }
                if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey || e.button !== 0) {
                    return;
                }
                e.preventDefault();
                runForumsTagAjax(link.href, { push: true });
            });
        }
    });

    window.addEventListener('popstate', function () {
        if (!fragmentUrl) {
            return;
        }
        runForumsTagAjax(window.location.href, { push: false });
    });

    window.runForumsTagAjax = runForumsTagAjax;

    window.ensureForumTagInParams = function (params) {
        if (!params) {
            params = new URLSearchParams(window.location.search);
        }
        var resolved = resolveTagApiParams(new URL(window.location.href));
        params.delete('tag');
        params.delete('page');
        resolved.forEach(function (value, key) {
            params.set(key, value);
        });
        return params;
    };
})();
