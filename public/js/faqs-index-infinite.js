/**
 * Infinite scroll for the FAQs listing page (6 FAQs per batch).
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
        return 'Showing ' + Number(loaded).toLocaleString() + ' of ' + Number(total).toLocaleString() + ' FAQs';
    }

    function updateFooter(wrap, data) {
        var footer = document.getElementById('faqs-infinite-footer');
        if (!wrap || !footer) {
            return;
        }

        wrap.setAttribute('data-current-page', String(data.current_page || 1));
        wrap.setAttribute('data-last-page', String(data.last_page || 1));
        wrap.setAttribute('data-total', String(data.total || 0));
        wrap.setAttribute('data-loaded', String(data.loaded_count || 0));

        var status = footer.querySelector('#faqs-infinite-status');
        if (status) {
            status.textContent = formatStatus(data.loaded_count || 0, data.total || 0);
        }

        var countEl = document.getElementById('faqCount');
        if (countEl) {
            countEl.textContent = String(data.total || 0);
        }

        var loader = footer.querySelector('#faqs-infinite-loader');
        if (loader) {
            loader.classList.add('d-none');
        }

        var complete = footer.querySelector('#faqs-infinite-complete');
        var sentinel = footer.querySelector('#faqs-infinite-sentinel');

        if (data.has_more) {
            if (complete) {
                complete.remove();
            }
            if (!sentinel) {
                sentinel = document.createElement('div');
                sentinel.id = 'faqs-infinite-sentinel';
                sentinel.className = 'faqs-infinite-sentinel';
                sentinel.setAttribute('aria-hidden', 'true');
                footer.appendChild(sentinel);
            }
        } else {
            if (sentinel) {
                sentinel.remove();
            }
            if (!complete) {
                complete = document.createElement('p');
                complete.id = 'faqs-infinite-complete';
                complete.className = 'text-muted small mb-0 text-center';
                complete.textContent = window.FAQS_INFINITE_STATUS_COMPLETE || 'All FAQs loaded';
                footer.appendChild(complete);
            }
        }
    }

    function loadNextPage() {
        var config = window.faqsInfiniteScrollConfig || {};
        if (!config.enabled || infiniteLoading) {
            return;
        }

        var wrap = document.getElementById('faqs-list-wrap');
        if (!wrap || wrap.getAttribute('data-infinite-scroll') !== '1') {
            return;
        }

        var currentPage = parseInt(wrap.getAttribute('data-current-page') || '1', 10);
        var lastPage = parseInt(wrap.getAttribute('data-last-page') || '1', 10);
        if (currentPage >= lastPage) {
            return;
        }

        var footer = document.getElementById('faqs-infinite-footer');
        var loader = footer ? footer.querySelector('#faqs-infinite-loader') : null;
        var list = document.getElementById('faqsList');
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

                if (data.has_more) {
                    window.initFaqsInfiniteScroll();
                } else {
                    disconnectInfiniteScroll();
                }
            })
            .catch(function () {
                if (footer && !footer.querySelector('#faqs-infinite-retry')) {
                    var retry = document.createElement('button');
                    retry.type = 'button';
                    retry.id = 'faqs-infinite-retry';
                    retry.className = 'btn btn-sm btn-outline-secondary mt-2';
                    retry.textContent = window.FAQS_INFINITE_STATUS_ERROR || 'Could not load more. Tap to retry.';
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

    window.initFaqsInfiniteScroll = function () {
        var config = window.faqsInfiniteScrollConfig || {};
        if (!config.enabled) {
            disconnectInfiniteScroll();
            return;
        }

        disconnectInfiniteScroll();

        var wrap = document.getElementById('faqs-list-wrap');
        var sentinel = document.getElementById('faqs-infinite-sentinel');
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
        if ((window.faqsInfiniteScrollConfig || {}).enabled) {
            window.initFaqsInfiniteScroll();
        }
    });
})();
