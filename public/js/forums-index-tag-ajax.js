/**
 * AJAX tag filtering for /forums (smart URLs + fragment reload without full page navigation).
 */
(function () {
    var config = window.forumsTagAjaxConfig || {};
    var fragmentUrl = config.fragmentUrl;
    var loading = false;

    function getTagSlugFromPath() {
        var match = window.location.pathname.match(/\/forums\/tag\/([^/]+)/);
        return match ? decodeURIComponent(match[1]) : null;
    }

    function ensureTagInParams(params) {
        if (params.get('tag')) {
            return params;
        }

        var slug = getTagSlugFromPath();
        if (!slug) {
            return params;
        }

        var link = document.querySelector('a.js-forums-tag-ajax[data-tag-slug="' + slug.replace(/\\/g, '\\\\').replace(/"/g, '\\"') + '"]');
        if (link && link.getAttribute('data-tag-text')) {
            params.set('tag', link.getAttribute('data-tag-text'));
        }

        return params;
    }

    function formatStatus(loaded, total) {
        return 'Showing ' + Number(loaded).toLocaleString() + ' of ' + Number(total).toLocaleString() + ' discussions';
    }

    function updateSidebarActiveState(activeTag) {
        var slug = getTagSlugFromPath();
        var normalizedActive = activeTag ? String(activeTag).toLowerCase() : '';

        document.querySelectorAll('a.js-forums-tag-ajax[data-tag-text]').forEach(function (link) {
            var text = (link.getAttribute('data-tag-text') || '').toLowerCase();
            var linkSlug = link.getAttribute('data-tag-slug') || '';
            var isActive = false;
            if (normalizedActive && text === normalizedActive) {
                isActive = true;
            }
            if (slug && linkSlug === slug) {
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

    function applyFragment(data) {
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

        updateSidebarActiveState(data.active_tag || null);

        var canon = document.querySelector('link[rel="canonical"]');
        if (canon && data.canonical_url) {
            canon.setAttribute('href', data.canonical_url);
        }
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

        var params = new URLSearchParams(u.search);
        ensureTagInParams(params);
        params.delete('page');

        var qs = params.toString();
        var wrap = document.getElementById('forums-list-wrap');
        loading = true;
        if (wrap) {
            wrap.classList.add('is-loading');
        }

        fetch(fragmentUrl + (qs ? '?' + qs : ''), {
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
                applyFragment(data);
                if (push) {
                    history.pushState({ forumsTagAjax: 1 }, '', u.pathname + (qs ? '?' + qs : ''));
                }
            })
            .catch(function () {
                window.location.assign(u.pathname + (qs ? '?' + qs : ''));
            })
            .finally(function () {
                loading = false;
                if (wrap) {
                    wrap.classList.remove('is-loading');
                }
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
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
        if (params.get('tag')) {
            return params;
        }
        var slug = getTagSlugFromPath();
        if (!slug) {
            return params;
        }
        var link = document.querySelector('a.js-forums-tag-ajax[data-tag-slug="' + slug.replace(/\\/g, '\\\\').replace(/"/g, '\\"') + '"]');
        if (link && link.getAttribute('data-tag-text')) {
            params.set('tag', link.getAttribute('data-tag-text'));
        }
        return params;
    };
})();
