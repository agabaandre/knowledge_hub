/**
 * Infinite scroll for the forums listing page (10 threads per batch).
 */
(function () {
    var infiniteObserver = null;
    var infiniteLoading = false;

    function disconnectInfiniteScroll() {
        if (infiniteObserver) {
            infiniteObserver.disconnect();
            infiniteObserver = null;
        }
        infiniteLoading = false;
    }

    function formatStatus(loaded, total) {
        return 'Showing ' + Number(loaded).toLocaleString() + ' of ' + Number(total).toLocaleString() + ' discussions';
    }

    function mergeForumIds(newIds) {
        if (!Array.isArray(newIds) || !newIds.length) {
            return;
        }
        if (Array.isArray(window.forumsIndexForumIds)) {
            newIds.forEach(function (id) {
                if (window.forumsIndexForumIds.indexOf(id) === -1) {
                    window.forumsIndexForumIds.push(id);
                }
            });
        }
        if (window.khubAiChat && Array.isArray(window.khubAiChat.forum_ids)) {
            newIds.forEach(function (id) {
                if (window.khubAiChat.forum_ids.indexOf(id) === -1) {
                    window.khubAiChat.forum_ids.push(id);
                }
            });
        }
    }

    function updateFooter(wrap, data) {
        var footer = document.getElementById('forums-infinite-footer');
        if (!wrap || !footer) {
            return;
        }

        wrap.setAttribute('data-current-page', String(data.current_page || 1));
        wrap.setAttribute('data-last-page', String(data.last_page || 1));
        wrap.setAttribute('data-total', String(data.total || 0));
        wrap.setAttribute('data-loaded', String(data.loaded_count || 0));

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
    }

    function afterAppend(data) {
        if (typeof window.initForumListingInlineFileUploads === 'function') {
            window.initForumListingInlineFileUploads();
        }
        if (window.ForumsIndexFilters && typeof window.ForumsIndexFilters.refreshCards === 'function') {
            window.ForumsIndexFilters.refreshCards();
        }
        if (typeof window.initForumContributorCarousels === 'function') {
            window.initForumContributorCarousels(list);
        }
        mergeForumIds(data.forum_ids || []);
    }

    function loadNextPage() {
        var config = window.forumsInfiniteScrollConfig || {};
        if (!config.enabled || infiniteLoading) {
            return;
        }

        var wrap = document.getElementById('forums-list-wrap');
        if (!wrap || wrap.getAttribute('data-infinite-scroll') !== '1') {
            return;
        }

        var currentPage = parseInt(wrap.getAttribute('data-current-page') || '1', 10);
        var lastPage = parseInt(wrap.getAttribute('data-last-page') || '1', 10);
        if (currentPage >= lastPage) {
            return;
        }

        var footer = document.getElementById('forums-infinite-footer');
        var loader = footer ? footer.querySelector('#forums-infinite-loader') : null;
        var list = document.getElementById('forums-list');
        if (!list || !config.pageUrl) {
            return;
        }

        infiniteLoading = true;
        if (loader) {
            loader.classList.remove('d-none');
        }

        var params = new URLSearchParams(window.location.search);
        params.delete('page');
        params.set('page', String(currentPage + 1));

        fetch(config.pageUrl + '?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('page failed');
                }
                return response.json();
            })
            .then(function (data) {
                if (!data.ok || !data.html) {
                    throw new Error('invalid payload');
                }

                list.insertAdjacentHTML('beforeend', data.html);
                updateFooter(wrap, data);
                afterAppend(data);

                if (data.has_more) {
                    window.initForumsInfiniteScroll();
                } else {
                    disconnectInfiniteScroll();
                }
            })
            .catch(function () {
                if (footer && !footer.querySelector('#forums-infinite-retry')) {
                    var retry = document.createElement('button');
                    retry.type = 'button';
                    retry.id = 'forums-infinite-retry';
                    retry.className = 'btn btn-sm btn-outline-secondary mt-2';
                    retry.textContent = window.FORUMS_INFINITE_STATUS_ERROR || 'Could not load more. Tap to retry.';
                    retry.addEventListener('click', function () {
                        retry.remove();
                        loadNextPage();
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

    window.initForumsInfiniteScroll = function () {
        var config = window.forumsInfiniteScrollConfig || {};
        if (!config.enabled) {
            disconnectInfiniteScroll();
            return;
        }

        disconnectInfiniteScroll();

        var wrap = document.getElementById('forums-list-wrap');
        var sentinel = document.getElementById('forums-infinite-sentinel');
        if (!wrap || wrap.getAttribute('data-infinite-scroll') !== '1' || !sentinel) {
            return;
        }

        if (!('IntersectionObserver' in window)) {
            sentinel.addEventListener('click', loadNextPage);
            sentinel.style.height = '24px';
            sentinel.style.cursor = 'pointer';
            return;
        }

        infiniteObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    loadNextPage();
                }
            });
        }, { root: null, rootMargin: '240px 0px', threshold: 0.01 });

        infiniteObserver.observe(sentinel);
    };

    document.addEventListener('DOMContentLoaded', function () {
        if ((window.forumsInfiniteScrollConfig || {}).enabled) {
            window.initForumsInfiniteScroll();
        }
    });
})();
